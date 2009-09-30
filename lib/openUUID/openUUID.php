<?php
class openUUID {
	 const MD5  	= 3;
	 const SHA1 	= 5;
	 const clearVer = 15;  // 00001111  Clears all bites of version byte with AND
	 const clearVar = 63;  // 00111111  Clears all relevant bits of variant byte with AND
	 const varRes   = 224; // 11100000  Variant reserved for future use
	 const varMS    = 192; // 11000000  Microsft GUID variant
	 const varRFC   = 128; // 10000000  The RFC 4122 variant (this variant)
	 const varNCS   = 0;   // 00000000  The NCS compatibility variant
	 const version1 = 16;  // 00010000
	 const version3 = 48;  // 00110000
	 const version4 = 64;  // 01000000
	 const version5 = 80;  // 01010000
	 const interval = 0x01b21dd213814000; // Time (in 100ns steps) between the start of the UTC and Unix epochs
	 const nsDNS  	= "6ba7b810-9dad-11d1-80b4-00c04fd430c8";
	 const nsURL  	= "6ba7b811-9dad-11d1-80b4-00c04fd430c8";
	 const nsOID  	= "6ba7b812-9dad-11d1-80b4-00c04fd430c8";
	 const nsX500 	= "6ba7b814-9dad-11d1-80b4-00c04fd430c8";
	 
	 protected $bytes;
	 protected $hex;
	 protected $string;
	 protected $urn;
	 protected $version;
	 protected $variant;
	 protected $node;
	 protected $time;
 
	 public static function uuidMint($ver = 1, $node = NULL, $ns = NULL) {
		  /* Create a new UUID based on provided data. */
		  switch((int) $ver) {
			   case 1:
			    	return new self(self::mintTime($node));
			   case 2:
			    // Version 2 is not supported 
			    	throw new UUIDException("Version 2 is unsupported.");
			   case 3:
			    	return new self(self::mintName(self::MD5, $node, $ns));
			   case 4:
			    	return new self(self::mintRand());
			   case 5:
			    	return new self(self::mintName(self::SHA1, $node, $ns));
			   default:
			    	throw new UUIDException("Selected version is invalid or unsupported.");
		  }
		 }

	public static function import($uuid) {
		return new self(makeBin($uuid, 16));
	}   
	
	public static function compare($a, $b) {
		return ((self::makeBin($a)==self::makeBin($b)) ? true : false);
	}
	
	public function __toString() {
	  	return $this->string;
	}
	
	public function __get($var) {
		switch($var) {
			case "bytes":
	    		return $this->bytes;
	   		case "hex":
	    		return bin2hex($this->bytes);
	   		case "string":
	    		return $this->__toString();
	   		case "urn":
	    		return "urn:uuid:".$this->__toString();
	   		case "version":
	    		return ord($this->bytes[6]) >> 4;
	   		case "variant":
	    		$byte = ord($this->bytes[8]);
	    		if ($byte >= self::varRes)
	     			return 3;
	    		if ($byte >= self::varMS)
	     			return 2;
	    		if ($byte >= self::varRFC)
	     			return 1;
	    		else
	     			return 0;
	   		case "node":
	    		if (ord($this->bytes[6])>>4==1)
	     			return bin2hex(substr($this->bytes,10));
	   		case "time":
	    		if (ord($this->bytes[6])>>4==1) {
	     			$time = bin2hex($this->bytes[6].$this->bytes[7].$this->bytes[4].$this->bytes[5].$this->bytes[0].$this->bytes[1].$this->bytes[2].$this->bytes[3]);
	     			// Clear version flag
	     			$time[0] = "0"; 
	     			// Do some reverse arithmetic to get a Unix timestamp
	     			$time = (hexdec($time) - self::interval) / 10000000;
	     			return $time;
	    		} else {
	     			return NULL;
	    		}
	   		default:
	    		return NULL;
	  	}
	}	 
	
	protected function __construct($uuid) {
		if (strlen($uuid) != 16)
	   		throw new UUIDException("Input must be a 128-bit integer.");
	  		$this->bytes  = $uuid;
	  		// Optimize the most common use
	  		$this->string = 
	   			bin2hex(substr($uuid,0,4))."-".
	   			bin2hex(substr($uuid,4,2))."-".
	   			bin2hex(substr($uuid,6,2))."-".
	   			bin2hex(substr($uuid,8,2))."-".
	   			bin2hex(substr($uuid,10,6));
	 }
	
	 protected static function mintTime($node = NULL) {
	  	$time = microtime(1) * 10000000 + self::interval;
	  $time = sprintf("%F", $time);
	  preg_match("/^\d+/", $time, $time); //strip decimal point
	  // And now to a 64-bit binary representation
	  $time = base_convert($time[0], 10, 16);
	  $time = pack("H*", str_pad($time, 16, "0", STR_PAD_LEFT));
	  // Reorder bytes to their proper locations in the UUID
	  $uuid  = $time[4].$time[5].$time[6].$time[7].$time[2].$time[3].$time[0].$time[1];
	  // Generate a random clock sequence
	  $uuid .= self::randomBytes(2);
	  // set variant
	  $uuid[8] = chr(ord($uuid[8]) & self::clearVar | self::varRFC);
	  // set version
	  $uuid[6] = chr(ord($uuid[6]) & self::clearVer | self::version1);
	  // Set the final 'node' parameter, a MAC address
	  if ($node) 
	   $node = self::makeBin($node, 6);
	  if (!$node) { 
	    // If no node was provided or if the node was invalid, 
	    //  generate a random MAC address and set the multicast bit
	   $node = self::randomBytes(6);
	   $node[0] = pack("C", ord($node[0]) | 1);
	  }
	  $uuid .= $node;
	  return $uuid;
	 }
	
	 protected static function mintRand() {
	  /* Generate a Version 4 UUID.  
	     These are derived soly from random numbers. */
	  // generate random fields
	  $uuid = self::randomBytes(16);
	  // set variant
	  $uuid[8] = chr(ord($uuid[8]) & self::clearVar | self::varRFC);
	  // set version
	  $uuid[6] = chr(ord($uuid[6]) & self::clearVer | self::version4);
	  return $uuid;
	 }
	
	 protected static function mintName($ver, $node, $ns) {
	  /* Generates a Version 3 or Version 5 UUID.
	     These are derived from a hash of a name and its namespace, in binary form. */
	  if (!$node)
	   throw new UUIDException("A name-string is required for Version 3 or 5 UUIDs.");
	  // if the namespace UUID isn't binary, make it so
	  $ns = self::makeBin($ns, 16);
	  if (!$ns)
	   throw new UUIDException("A binary namespace is required for Version 3 or 5 UUIDs.");
	  switch($ver) {
	   case self::MD5: 
	    $version = self::version3;
	    $uuid = md5($ns.$node,1);
	    break;
	   case self::SHA1:
	    $version = self::version5;
	    $uuid = substr(sha1($ns.$node,1),0, 16);
	    break;
	  }
	  // set variant
	  $uuid[8] = chr(ord($uuid[8]) & self::clearVar | self::varRFC);
	  // set version
	  $uuid[6] = chr(ord($uuid[6]) & self::clearVer | $version);
	  return ($uuid);
	 }
	
protected static function makeBin($str, $len) {
	if ($str instanceof self) {
		return $str->bytes;	
	}
	if (strlen($str)==$len) {
		return $str;
	} else {
		$str = preg_replace("/[^a-f0-9]/is", "", $str);
	}
	if (strlen($str) != ($len * 2)) {
		return false;
	} else {
		return pack("H*", $str);
	}
}

	public static function uuidRandomBytes($bytes) {
		$source = @fopen('/dev/urandom','rb');
		if ($source===false) {
   			$source = @fopen('/dev/egd-pool','rb');
		} 
		if  ($source !== FALSE) { // Unix-like
   			$rand = fread($source,$bytes);
   			fclose($source);
  		} else { // mt_rand() fallback
   			$rand = "";
   			for ($a = 0; $a < $bytes; $a++) {
    			$rand .= chr(mt_rand(0, 255));
   			}
  		} 
  		return $rand;
 	}
}

class UUIDException extends Exception {
}
