<?php
// ########################################################################
// # File: $Id: openDB_couchdb.php 235 2009-09-10 06:03:02Z jens $
// ########################################################################
// # This program is free software; you can redistribute it and/or modify
// # it under the terms of the GNU General Public License V3
// #
// # This program is subject to the GPL license, that is bundled with
// # this package in the file /share/LICENSE.TXT.
// # If you did not receive a copy of the GNU General Public License
// # along with this program write to the Free Software Foundation, Inc.,
// # 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
// #
// # This program is distributed in the hope that it will be useful,
// # but WITHOUT ANY WARRANTY; without even the implied warranty of
// # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// # GNU General Public License for more details.
// #
// ########################################################################
// # Autor: $Author: jens $
// ########################################################################
// # Revision: $Revision: 235 $
// ########################################################################
// # Date: $Date: 2009-09-10 08:03:02 +0200 (Thu, 10 Sep 2009) $
// ########################################################################
/**
* openDB_couchDB
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openDB_couchdb.php 235 2009-09-10 06:03:02Z jens $
* @package openWebX
* @subpackage openDB
* @uses openWebX
*/
class openDB extends openDB_Abstract {
	
	/**
	 * overloaded property
	 * 
	 * @access public 
	 */
	public $data 			= array();
	
	private $dbObject		= null;
	
	public function __construct() {
		$this->dbSetVariables();
		$this->dbConnect();
	}
	
	public function __destruct() {
		$this->dbDisconnect();
	}
	
	
	public function dbCreateStructure() {
		if (!$this->dbObject->databaseExists($this->data['dbDatabase'])) {
			$this->dbObject->createDatabase($this->data['dbDatabase']);
		}
		foreach ($_SESSION['openWebX']['views'] as $key=>$val) {
			if (!$this->dbGetByID($key)) {
				$myDoc = new StdClass();
				$myDoc->_id=$key;
				$myDoc->language='javascript';
				$myDoc->views = json_decode($val);
				$this->dbStore($myDoc);
			}	
		}
	}
	
	public function dbCreateView($strViewName,$strSQL) {
		
	}
	public function dbExecute() {
		
	}
	public function dbFetchArray() {
		
	}
	public function dbFetchObject() {
		
	}
	public function dbSetStatement($strStatement,$arrParams=null) {
		'Trying to set:'.$strStatement;
	}
	
	public function dbStore($objContent) {
		openDebug::dbgVar($objContent);
		if (!is_object($objContent)) {
			throw new InvalidArgumentException ("Content should be an object");
		}
		if (!$this->dbObject->getDoc($objContent->_id)) {
			$this->dbObject->storeDoc($objContent);
		}
	}
	
	public function dbGetByID($strID) {
		return ($myDoc = $this->dbObject->getDoc($strID)) ? $myDoc : null; 
	}
	
	public function dbGetByField($strField,$mixedValue) {
		
	}
	
	private function dbSetVariables() {
	    $this->dbType      = Settings::get('database_type');
	    $this->dbServer    = Settings::get('database_server');
	    $this->dbPort	   = Settings::get('database_port');
	    $this->dbUser      = Settings::get('database_username');
	    $this->dbPassword  = Settings::get('database_password');
	    $this->dbDatabase  = Settings::get('database_name');
	}
	
	private function dbConnect() {
		$this->dbObject = new couchClient($this->data['dbServer'],$this->data['dbPort'],$this->data['dbDatabase']);
		//if (!$this->dbObject->databaseExists($this->data['dbDatabase'])) {
			$this->dbCreateStructure();
		//}
	}
	
	private function dbDisconnect() {
		
	}
	
	private function dbTestServer() {
		
	}

}









































class couchBasic {

 	/**
	* @var string database server hostname
	*/
	protected $hostname = '';
	/**
	* @var integer database server TCP port
	*/
	protected $port = 0;
	/**
	* @var array allowed HTTP methods for REST dialog
	*/
	protected $HTTP_METHODS = array('GET','POST','PUT','DELETE','COPY');
	/**
	* @var resource HTTP servfer socket
	* @see _connect()
	*/
	protected $socket = NULL;
 
	/**
	* class constructor
	*
	* @param string $hostname CouchDB server host
	* @param integer $port CouchDB server port
	*/
	public function __construct ($hostname, $port) {
		$this->hostname = $hostname;
		$this->port 	= $port;
	}
 
	/**
	* parse a CouchDB server response and sends back an array
	* the array contains keys :
	* status_code : the HTTP status code returned by the server
	* status_message : the HTTP message related to the status code
	* body : the response body (if any). If CouchDB server response Content-Type is application/json
	* the body will by json_decode()d
	*
	* @static
	* @param string $raw_data data sent back by the server
	* @param boolean $json_as_array is true, the json response will be decoded as an array. Is false, it's decoded as an object
	* @return array CouchDB response
	*/
	public static function parseRawResponse($raw_data, $json_as_array = FALSE) {
		if ( !strlen($raw_data) ) {
			throw new InvalidArgumentException("no data to parse");
		}
		$response = array();
		list($headers, $body) = explode("\r\n\r\n", $raw_data);
		$status_line = reset(explode("\n",$headers));
		$status_array = explode(' ',$status_line,3);
		$response['status_code'] = trim($status_array[1]);
		$response['status_message'] = trim($status_array[2]);
		if ( strlen($body) ) {
			if ( preg_match('@Content-Type:\s+application/json@',$headers) ) {
				$response['body'] = json_decode($body,$json_as_array);
			} else {
				$response['body'] = $body;
			}
		} else {
			$response['body'] = null;
		}
		return $response;
	}
 
	/**
	* build HTTP request to send to the server
	*
	* @param string $method HTTP method to use
	* @param string $url the request URL
	* @param string|object|array $data the request body. If it's an array or an object, $data is json_encode()d
	* @return string HTTP request
	*/
	protected function _buildRequest($method,$url,$data) {
		if ( is_object($data) OR is_array($data) ) {
			$data = json_encode($data);
		}
		$req = "$method $url HTTP/1.0\r\nHost: {$this->hostname}\r\n";
		$req.= "Accept: application/json,text/html,text/plain,*/*\r\n";
		if ( $method == 'COPY') {
		     $req .= 'Destination: '.$data."\r\n\r\n";
		} elseif ($data) {
			$req .= 'Content-Length: '.strlen($data)."\r\n";
			$req .= 'Content-Type: application/json'."\r\n\r\n";
			$req .= $data."\r\n";
		} else {
			$req .= "\r\n";
		}
		return $req;
	}
	 
	/**
	*open the connection to the CouchDB server
	*
	*This function can throw an Exception if it fails
	*
	* @return boolean wheter the connection is successful
	*/
	protected function _connect() {
		$this->socket = @fsockopen($this->hostname, $this->port, $err_num, $err_string);
		if(!$this->socket) {
			throw new Exception('Could not open connection to '.$this->hostname.':'.$this->port.': '.$err_string.' ('.$err_num.')');
			return FALSE;
		}
		return TRUE;
	}
	 
	/**
	*send the HTTP request to the server and read the response
	*
	* @param string $request HTTP request to send
	* @return string $response HTTP response from the CouchDB server
	*/
	protected function _execute($request) {
		fwrite($this->socket, $request);
		$response = '';
		while(!feof($this->socket)) {
			$response .= fgets($this->socket);
		}
		return $response;
	}
	 
	/**
	*closes the connection to the server
	*
	*
	*/
	protected function _disconnect() {
		@fclose($this->socket);
		$this->socket = NULL;
	}
	 
	/**
	*send a query to the CouchDB server
	*
	* @param string $method HTTP method to use (GET, POST, ...)
	* @param string $url URL to fetch
	* @param array $parameters additionnal parameters to send with the request
	* @param string|array|object $data request body
	*
	* @return string|false server response on success, false on error
	*/
	public function query ( $method, $url, $parameters = array() , $data = NULL ) {
		if ( !in_array($method, $this->HTTP_METHODS ) ) {
			throw new Exception("Bad HTTP method: $method");
		}
		if ( is_array($parameters) AND count($parameters) ) {	
			$url = $url.'?'.http_build_query($parameters);
		}
		$request = $this->_buildRequest($method,$url,$data);
		if ( !$this->_connect() ) {
			return FALSE;
		}
		$raw_response = $this->_execute($request);
		$this->_disconnect();
	    //log_message('debug',"COUCH : Executed query $method $url");
	    //log_message('debug',"COUCH : ".$raw_response);
		return $raw_response;
	}
	 
	/**
	* record a file located on the disk as a CouchDB attachment
	*
	* @param string $url CouchDB URL to store the file to
	* @param string $file path to the on-disk file
	* @param string $content_type attachment content_type
	*
	* @return string server response
	*/
	public function storeFile ( $url, $file, $content_type ) {
		if ( !strlen($url) ) {
			throw new InvalidArgumentException("Attachment URL can't be empty");
		}
		if ( !strlen($file) OR !is_file($file) OR !is_readable($file) ) {
			throw new InvalidArgumentException("Attachment file does not exist or is not readable");
		}
		if ( !strlen($content_type) ) {
			throw new InvalidArgumentException("Attachment Content Type can't be empty");
		}
	    $req = "PUT $url HTTP/1.0\r\nHost: {$this->hostname}\r\n";
		$req .= "Accept: application/json,text/html,text/plain,*/*\r\n";
	   	$req .= 'Content-Length: '.filesize($file)."\r\n";
		$req .= 'Content-Type: '.$content_type."\r\n\r\n";
	    $fstream=fopen($file,'r');
	    $this->_connect();
	    fwrite($this->socket, $req);
	    stream_copy_to_stream($fstream,$this->socket);
	    $response = '';
	    while(!feof($this->socket))
		$response .= fgets($this->socket);
	    $this->_disconnect();
	    fclose($fstream);
	    return $response;
	  }
	 
	/**
	* store some data as a CouchDB attachment
	*
	* @param string $url CouchDB URL to store the file to
	* @param string $data data to send as the attachment content
	* @param string $content_type attachment content_type
	*
	* @return string server response
	*/
	public function storeAsFile($url,$data,$content_type) {
		if ( !strlen($url) ) {
			throw new InvalidArgumentException("Attachment URL can't be empty");
		}
		if ( !strlen($content_type) ) {
			throw new InvalidArgumentException("Attachment Content Type can't be empty");
		}
	    $req = "PUT $url HTTP/1.0\r\nHost: {$this->hostname}\r\n";
		$req .= "Accept: application/json,text/html,text/plain,*/*\r\n";
	   	$req .= 'Content-Length: '.strlen($data)."\r\n";
		$req .= 'Content-Type: '.$content_type."\r\n\r\n";
	    $this->_connect();
	    fwrite($this->socket, $req);
	    fwrite($this->socket, $data);
	    $response = '';
	    while(!feof($this->socket)) {
			$response .= fgets($this->socket);
	    }
	    $this->_disconnect();
	    return $response;
	}
}

/**
* CouchDB client class
*
* This class implements all required methods to use with a
* CouchDB server
*
*
*/
class couchClient extends couchBasic {
	/**
	* @var string database server hostname
	*/
	protected $dbname = '';
	/**
	* @var array CouchDB view query options
	*/
	protected $view_query = array(); 
	/**
	* @var bool option to return couchdb view results as couchDocuments objects
	*/
	protected $results_as_cd = false;
    /**
	* @var array list of properties beginning with '_' and allowed in CouchDB objects
	*/
	public static $allowed_underscored_properties = array('_id','_rev','_attachments');
	 
	/**
	* class constructor
	*
	* @param string $hostname CouchDB server host
	* @param integer $port CouchDB server port
	* @param string $dbname CouchDB database name
	*/
	public function __construct($hostname, $port, $dbname) {
		if ( !strlen($dbname) ) {
			throw new InvalidArgumentException("Database name can't be empty");
		}
	    parent::__construct($hostname,$port);
	    $this->dbname = $dbname;
	  }
	 
	/**
	* helper method to execute the following algorithm :
	*
	* query the couchdb server
	* test the status_code
	* return the response body on success, throw an exception on failure
	*
	* @param string $method HTTP method (GET, POST, ...)
	* @param string $url URL to fetch
	* @param $array $allowed_status_code the list of HTTP response status codes that prove a successful request
	* @param array $parameters additionnal parameters to send with the request
	* @param string|object|array $data the request body. If it's an array or an object, $data is json_encode()d
	*/
	protected function _queryAndTest ( $method, $url,$allowed_status_codes, $parameters = array(),$data = NULL ) {
	    $raw = $this->query($method,$url,$parameters,$data);
	    $response = $this->parseRawResponse($raw);
	    if ( in_array($response['status_code'], $allowed_status_codes) ) {
	      return $response['body'];
	    }
	    return FALSE;
	}
	 
	/**
	*list all databases on the CouchDB server
	*
	* @return object databases list
	*/
	public function listDatabases ( ) {
	    return $this->_queryAndTest ('GET', '/_all_dbs', array(200));
	}
	 
	/**
	*create the database
	*
	* @return object creation infos
	*/
	public function createDatabase ( ) {
	    return $this->_queryAndTest ('PUT', '/'.urlencode($this->dbname), array(201));
	}
	 
	/**
	*delete the database
	*
	* @return object creation infos
	*/
	public function deleteDatabase ( ) {
	      return $this->_queryAndTest ('DELETE', '/'.urlencode($this->dbname), array(200));
	}
	 
	/**
	*get database infos
	*
	* @return object database infos
	*/
	public function getDatabaseInfos ( ) {
	    return $this->_queryAndTest ('GET', '/'.urlencode($this->dbname), array(200));
	}
	 
	/**
	*return database uri
	*
	* example : couch.server.com:5984/mydb
	*
	* @return string database URI
	*/
	public function getDatabaseUri() {
		return $this->hostname.':'.$this->port.'/'.$this->dbname;
	}
	 
	/**
	* test if the database already exists
	*
	* @return boolean wether or not the database exist
	*/
	public function databaseExists () {
	    try {
	      	$back = $this->getDatabaseInfos();
	      	return TRUE;
	    } catch ( Exception $e ) {
	      	// if status code = 404 database does not exist
	      	if ( $e->getCode() == 404 ) return FALSE;
	      	// we met another exception so we throw it
	      	throw $e;
	    }
	}
	 
	/**
	* fetch a CouchDB document
	*
	* @param string $id document id
	* @return object CouchDB document
	*/
	public function getDoc ($id) {
		if ( !strlen($id) ) {
			throw new InvalidArgumentException ("Document ID is empty");
		}
	    if ( preg_match('/^_design/',$id) ) {
	      $url = '/'.urlencode($this->dbname).'/_design/'.urlencode(str_replace('_design/','',$id));
	    } else {
	      $url = '/'.urlencode($this->dbname).'/'.urlencode($id);
	    }
	    return $this->_queryAndTest ('GET', $url, array(200));
	}
	 
	/**
	* store a CouchDB document
	*
	* @param object $doc document to store
	* @return object CouchDB document storage response
	*/
	public function storeDoc ( $doc ) {
		if ( !is_object($doc) ) throw new InvalidArgumentException ("Document should be an object");
		foreach ( array_keys(get_object_vars($doc)) as $key ) {
			if ( substr($key,0,1) == '_' AND !in_array($key,couchClient::$allowed_underscored_properties) )
			throw new InvalidArgumentException("Property $key can't begin with an underscore");
		}
	    $method = 'POST';
	    $url = '/'.urlencode($this->dbname);
	    if ( !empty($doc->_id) ) {
	      $method = 'PUT';
	      $url.='/'.urlencode($doc->_id);
	    }
	    return $this->_queryAndTest ($method, $url, array(200,201),array(),$doc);
	}
	 
	/**
	* copy a CouchDB document
	*
	* @param string $id id of the document to copy
	* @param string $new_id id of the new document
	* @return object CouchDB document storage response
	*/
	public function copyDoc($id,$new_id) {
		if ( !strlen($id) )
			throw new InvalidArgumentException ("Document ID is empty");
		if ( !strlen($new_id) )
			throw new InvalidArgumentException ("New document ID is empty");
	 
	    $method = 'COPY';
	    $url = '/'.urlencode($this->dbname);
	    $url.='/'.urlencode($id);
	    return $this->_queryAndTest ($method, $url, array(200,201),array(),$new_id);
	}
	 
	/**
	* store a CouchDB attachment
	*
	* in this case the attachment content is in a PHP variable
	*
	* @param object $doc doc to store the attachment in
	* @param string $data attachment content
	* @param string $filename attachment name
	* @param string $content_type attachment content type
	* @return object CouchDB attachment storage response
	*/
	public function storeAsAttachment ($doc,$data,$filename,$content_type = 'application/octet-stream') {
		if ( !is_object($doc) ) throw new InvalidArgumentException ("Document should be an object");
	    if ( !$doc->_id ) throw new InvalidArgumentException ("Document should have an ID");
	    $url = '/'.urlencode($this->dbname).'/'.urlencode($doc->_id).'/'.urlencode($filename);
	    if ( $doc->_rev ) $url.='?rev='.$doc->_rev;
	    $raw = $this->store_as_file($url,$data,$content_type);
	    $response = $this->parse_raw_response($raw);
	    return $response['body'];
	}
	 
	/**
	* store a CouchDB attachment
	*
	* in this case the attachment is a file on the harddrive
	*
	* @param object $doc doc to store the attachment in
	* @param string $file file to attach (complete path on the harddrive)
	* @param string $filename attachment name
	* @param string $content_type attachment content type
	* @return object CouchDB attachment storage response
	*/
	public function storeAttachment ($doc,$file,$content_type = 'application/octet-stream',$filename = null) {
		if ( !is_object($doc) ) throw new InvalidArgumentException ("Document should be an object");
	    if ( !$doc->_id ) throw new InvalidArgumentException ("Document should have an ID");
	    if ( !is_file($file) ) throw new InvalidArgumentException ("File $file does not exist");
	    $url = '/'.urlencode($this->dbname).'/'.urlencode($doc->_id).'/';
	    $url .= empty($filename) ? basename($file) : $filename ;
	    if ( $doc->_rev ) $url.='?rev='.$doc->_rev;
	    $raw = $this->storeFile($url,$file,$content_type);
	    $response = $this->parseRawResponse($raw);
	    return $response['body'];
	  }
	 
	/**
	* delete a CouchDB attachment from a document
	*
	* @param object $doc CouchDB document
	* @param string $attachment_name name of the attachment to delete
	* @return object CouchDB attachment removal response
	*/
	  public function deleteAttachment ($doc,$attachment_name ) {
	if ( !is_object($doc) ) throw new InvalidArgumentException ("Document should be an object");
	    if ( !$doc->_id ) throw new InvalidArgumentException ("Document should have an ID");
	    if ( !strlen($attachment_name) ) throw new InvalidArgumentException ("Attachment name not set");
	    $url = '/'.urlencode($this->dbname).
	            '/'.urlencode($doc->_id).
	            '/'.urlencode($attachment_name);
	    $raw = $this->query('DELETE',$url,array("rev"=>$doc->_rev));
	    $response = $this->parseRawResponse($raw);
	    return $response['body'];
	  }
	 
	/**
	* remove a document from the database
	*
	* @param object $doc document to remove
	* @return object CouchDB document removal response
	*/
	  public function deleteDoc ( $doc ) {
	if ( !is_object($doc) ) throw new InvalidArgumentException ("Document should be an object");
	    if ( empty($doc->_id) OR empty($doc->_rev) ) {
	      throw new Exception("Document should contain _id and _rev");
	      return FALSE;
	    }
	 
	    $url = '/'.urlencode($this->dbname).'/'.urlencode($doc->_id).'?rev='.urlencode($doc->_rev);
	    return $this->_queryAndTest ('DELETE', $url, array(200));
	  }
	 
	 
	/*
	 
	CouchDB views : Please read http://wiki.apache.org/couchdb/HTTP_view_API
	 
	This class provides method chaining for query options. As an example :
	 
	$view_response = $couchClient->limit(50)->include_docs(TRUE)->getView('blog_posts','order_by_date');
	 
	 
	 
	*/
	 
	 
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param mixed $value any json encodable thing
	* @return couchClient $this
	*/
	  public function key($value) {
	    $this->view_query['key'] = json_encode($value);
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param mixed $value any json encodable thing
	* @return couchClient $this
	*/
	  public function startkey($value) {
	    $this->view_query['startkey'] = json_encode($value);
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param mixed $value any json encodable thing
	* @return couchClient $this
	*/
	  public function endkey($value) {
	    $this->view_query['endkey'] = json_encode($value);
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param string $value document id
	* @return couchClient $this
	*/
	  public function startkey_docid($value) {
	    $this->view_query['startkey_docid'] = (string)$value;
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param string $value document id
	* @return couchClient $this
	*/
	  public function endkey_docid($value) {
	    $this->view_query['endkey_docid'] = (string)$value;
	    return $this;
	  }
	 
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param ineteger $value maximum number of items to fetch
	* @return couchClient $this
	*/
	  public function limit($value) {
	    $this->view_query['limit'] = (int)$value;
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param string $value has to be 'ok'
	* @return couchClient $this
	*/
	  public function stale($value) {
	    if ( $value == 'ok' )
	      $this->view_query['stale'] = $value;
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param boolean $value order in descending
	* @return couchClient $this
	*/
	  public function descending($value) {
	    $this->view_query['descending'] = json_encode((boolean)$value);
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param int $value number of items to skip
	* @return couchClient $this
	*/
	  public function skip($value) {
	    $this->view_query['skip'] = (int)$value;
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param boolean $value whether to group the results
	* @return couchClient $this
	*/
	  public function group($value) {
	    $this->view_query['group'] = json_encode((boolean)$value);
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param boolean $value whether to execute the reduce function (if any)
	* @return couchClient $this
	*/
	  public function reduce($value) {
	    $this->view_query['reduce'] = json_encode((boolean)$value);
	    return $this;
	  }
	 
	/**
	* CouchDB query option
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param boolean $value whether to include complete documents in the response
	* @return couchClient $this
	*/
	  public function include_docs($value) {
	    $this->view_query['include_docs'] = json_encode((boolean)$value);
	    return $this;
	  }
	 
	/**
	* returns couchDB view results as couchDocuments objects
	*
	* implies include_docs(true)
	*
	* when view result is parsed, view documents are translated to objects and sent back
	*
	* @view results_as_couchDocuments()
	* @return couchClient $this
	*
	*/
	public function asCouchDocuments() {
		$this->results_as_cd = true;
		return $this;
	}
	 
	/**
	* request a view from the CouchDB server
	*
	* @link http://wiki.apache.org/couchdb/HTTP_view_API
	* @param string $id design document name (without _design)
	* @param string $name view name
	* @return object CouchDB view query response
	*/
	public function getView ( $id, $name ) {
		if ( !$id OR !$name ) throw new InvalidArgumentException("You should specify view id and name");
		$url = '/'.urlencode($this->dbname).'/_design/'.urlencode($id).'/_view/'.urlencode($name);
		if ( $this->results_as_cd ) $this->include_docs(true);
		$view_query = $this->view_query;
		$results_as_cd = $this->results_as_cd;
		$this->view_query = array();
		$this->results_as_cd = false;
		if ( ! $results_as_cd )
		return $this->_queryAndTest ('GET', $url, array(200),$view_query);
		return $this->resultsToCouchDocuments (
			$this->_queryAndTest ('GET', $url, array(200),$view_query)
		);
	}
	/**
	* returns couchDB view results as couchDocuments objects
	*
	* - for string view keys, the object is found on "view key" index
	* ex : view returns
	* <code>[ "client" : null , "client2" : null ]</code>
	* is translated to :
	* array ( 'client' => array(couchDocument) , 'client2' => array(couchDocument) )
	*
	* - for array view keys, the object key in the result array is the last key of the view
	* ex : view returns
	* <code>[ [ "#44556643", "client" ] : null , [ "#65745767566","client2" : null ]</code>
	* is translated to :
	* array ( 'client' => array(couchDocument) , 'client2' => array(couchDocument) )
	*
	* @param stdClass couchDb view resultset
	* @return array array of couchDocument objects
	*/
	public function resultsToCouchDocuments ( $results ) {
	if ( !$results->rows or !is_array($results->rows) ) return FALSE;
	$back = array();
	foreach ( $results->rows as $row ) { // should have $row->key & $row->doc
	if ( !$row->key or !$row->doc ) return false;
	// create couchDocument
	$cd = new couchDocument($this);
	$cd->loadFromObject($row->doc);
	 
	// set key name
	if ( is_string($row->key) ) $key = $row->key;
	elseif ( is_array($row->key) ) {
	if ( !is_array(end($row->key)) && !is_object(end($row->key)) )
	$key = end($row->key);
	else
	continue;
	}
	 
	// set value in result array
	if ( isset($back[$key]) ) {
	if ( is_array($back[$key]) ) $back[$key][] = $cd;
	else $back[$key] = array($back[$key],$cd);
	} else {
	$back[$key] = $cd;
	}
	}
	return $back;
	}
	 
	/**
	* request a list from the CouchDB server
	*
	* @link http://wiki.apache.org/couchdb/Formatting_with_Show_and_List
	* @param string $id design document name (without _design)
	* @param string $name list name
	* @param string $view_name view name
	* @return object CouchDB list query response
	*/
	  public function getList ( $id, $name, $view_name ) {
	if ( !$id OR !$name ) throw new InvalidArgumentException("You should specify list id and name");
	if ( !$view_name ) throw new InvalidArgumentException("You should specify view name");
	$url = '/'.urlencode($this->dbname).'/_design/'.urlencode($id).'/_list/'.urlencode($name).'/'.urlencode($view_name);
	$view_query = $this->view_query;
	$this->results_as_cd = false;
	$this->view_query = array();
	    return $this->_queryAndTest ('GET', $url, array(200),$view_query);
	}
	 
	/**
	* returns all documents contained in the database
	*
	* If $keys is set, a POST request is sent and only documents whose ids are in $keys are sent back
	*
	* @param array $keys list of ids to retrieve
	*
	* @return object CouchDB _all_docs response
	*/
	public function getAllDocs ( $keys = array() ) {
	$url = '/'.urlencode($this->dbname).'/_all_docs';
	$view_query = $this->view_query;
	$this->view_query = array();
	$method = 'GET';
	$data = null;
	if ( count($keys) ) {
	$method = 'POST';
	$data = json_encode(array('keys'=>$keys));
	}
	    return $this->_queryAndTest ($method, $url, array(200),$view_query,$data);
	}
	 
	/**
	* returns all documents contained associated wityh a sequence number
	*
	* @return object CouchDB _all_docs_by_seq response
	*/
	public function getAllDocsBySeq () {
	$url = '/'.urlencode($this->dbname).'/_all_docs_by_seq';
	$view_query = $this->view_query;
	$this->view_query = array();
	    return $this->_queryAndTest ('GET', $url, array(200),$view_query);
	}
	}
	 
/**
* customized Exception class for CouchDB errors
*
* this class uses : the Exception message to store the HTTP message sent by the server
* the Exception code to store the HTTP status code sent by the server
* and adds a method getBody() to fetch the body sent by the server (if any)
*
*/
class couchException extends Exception {
    // couchDB response once parsed
    protected $couch_response = array();
	 
    /**
	*class constructor
	*
	* @param string $raw_response HTTP response from the CouchDB server
	*/
    function __construct($raw_response) {
        $this->couch_response = couchBasic::parseRawResponse($raw_response);
        parent::__construct($this->couch_response['status_message'],$this->couch_response['status_code']);
    }
	 
	/**
	* returns CouchDB server response body (if any)
	*
	* if the response's "Content-Type" is set to "application/json", the
	* body is json_decode()d
	*
	* @return string|object|null CouchDB server response
	*/
    function getBody() {
        return $this->couch_response['body'];
    }
}
	
class couchDocument {
	 
	/**
	* @var stdClass object internal data
	*/
	protected $__couch_data = NULL;
	 
	/**
	*class constructor
	*
	* @param couchClient $client couchClient connection object
	*
	*/
	function __construct(couchClient $client) {
		$this->__couch_data->client = $client;
		$this->__couch_data->fields = new stdClass();
	}
	 
	/**
	* load a CouchDB document from the CouchDB server
	*
	* @param string $id CouchDB document ID
	* @return couchDocument $this
	*/
	public function load ( $id ) {
		if ( !strlen($id) ) {
			throw new InvalidArgumentException("No id given");
		}
		$this->__couch_data->fields = $this->__couch_data->client->getDoc($id);
		return $this;
	}
	 
	/**
	* load a CouchDB document from a PHP object
	*
	* note that this method clones the object given in argument
	*
	* @param object $doc CouchDB document (should have $doc->_id , $doc->_rev, ...)
	* @return couchDocument $this
	*/
	public function loadFromObject($doc) {
		$this->__couch_data->fields = clone $doc;
		return $this;
	}
	 
	/**
	* load a document in a couchDocument object and return it
	*
	* @static
	* @param couchClient $client couchClient instance
	* @param string $id id of the document to load
	* @return couchDocument couch document loaded with data of document $id
	*/
	public static function getInstance(couchClient $client,$id) {
		$back = new couchDocument($client);
		return $back->load($id);
	}
	 
	/**
	* returns all defined keys in this document
	*
	* @return array list of keys available in this document
	*/
	public function getKeys ( ) {
		return array_keys(get_object_vars($this->__couch_data->fields));
	}
	 
	/**
	* returns all fields (key => values) of this document
	*
	* @return object all fields of the document
	*/
	public function getFields () {
		return clone $this->__couch_data->fields;
	}
	 
	/**
	* returns document URI
	*
	* example : couch.server.com:5984/mydb/thisdoc
	*
	* @return string document URI
	*/
	public function getUri() {
		return $this->__couch_data->client->getDatabaseUri().'/'.$this->id();
	}
	 
	/**
	* returns document id (or null)
	*
	* @return string document id
	*/
	public function id() {
		return $this->get('_id');
	}
	 
	/**
	* returns value of field $key
	*
	* @param string $key field name
	* @return mixed field value (or null)
	*/
	public function get ( $key ) {
	    //echo "get for $key\n";
		$key = (string)$key;
		if (!strlen($key) ) {
			throw new InvalidArgumentException("No key given");
		}
		return property_exists( $this->__couch_data->fields,$key ) ? $this->__couch_data->fields->$key : NULL;
	}
	 
	/**
	* PHP magic method : getter
	*
	* @see get()
	*/
	public function __get ( $key ) {
		return $this->get($key);
	}
	 
	/**
	* set one field to a value
	*
	* does not update the database
	*
	* @param string $key field name
	* @param mixed $value field value
	* @return boolean TRUE
	*/
	protected function setOne ($key, $value ) {
		$key = (string)$key;
		if ( !strlen($key) ) throw new InvalidArgumentException("property name can't be empty");
		if ( $key == '_rev' ) throw new InvalidArgumentException("Can't set _rev field");
		if ( $key == '_id' AND $this->get('_id') ) throw new InvalidArgumentException("Can't set _id field because it's already set");
		if ( substr($key,0,1) == '_' AND !in_array($key,couchClient::$allowed_underscored_properties) )
		throw new InvalidArgumentException("Property $key can't begin with an underscore");
		//echo "setting $key to ".print_r($value,TRUE)."<BR>\n";
		$this->__couch_data->fields->$key = $value;
		return TRUE;
	}
	 
	/**
	* record the object to the database
	*
	*
	*/
	protected function record() {
		$response = $this->__couch_data->client->storeDoc($this->__couch_data->fields);
		$this->__couch_data->fields->_id = $response->id;
		$this->__couch_data->fields->_rev = $response->rev;
	}
	 
	/**
	* set document fields
	*
	* this method store the object in the database !
	*
	* there is 2 ways to use it. Set one field :
	* <code>
	* $this->set('some_field','some value');
	* </code>
	*
	* or set multiple fields in one go :
	* <code>
	* $this->set( array('some_field'=>'some value','some_other_field'=>'another value') );
	* </code>
	*
	* @param string|array $key
	* @param mixed $value
	*
	*/
	public function set ( $key , $value = NULL ) {
		if ( func_num_args() == 1 ) {
			if ( !is_array($key) AND !is_object($key) ) throw new InvalidArgumentException("When second argument is null, first argument should ba an array or an object");
			foreach ( $key as $one_key => $one_value ) {
				$this->setOne($one_key,$one_value);
			}
		} else {
			$this->setOne($key,$value);
		}
		$this->record();
		return TRUE;
	}
	 
	/**
	* PHP automagic setter
	*
	* modify a document property and store to the Server
	*
	* @link http://php.net/__set
	*
	* @param string $key name of the property to set
	* @param mixed $value property value
	*/
	public function __set( $key , $value ) {
		return $this->set($key,$value);
	}
	 
	/**
	* PHP automagic isset'er
	*
	*
	* @link http://php.net/__isset
	*
	* @param string $key name of the property to test
	*/
	public function __isset($key) {
		return property_exists($this->__couch_data->fields,$key);
	}
	 
	/**
	* deletes a document property
	*
	* @param string $key the key to remove
	* @return boolean whether the removal process ran successfully
	*/
	public function remove($key) {
		$key = (string)$key;
		if ( !strlen($key) ) throw new InvalidArgumentException("Can't remove a key without name");
		if ( $key == '_id' OR $key == '_rev' ) return FALSE;
		if ( isset($this->$key) ) {
			unset($this->__couch_data->fields->$key);
			$this->record();
		}
		return TRUE;
	}
	 
	/**
	* PHP automagic unset'er
	*
	* @see remove()
	* @param string $key the property to delete
	* @return boolean whether the removal process ran successfully
	*/
	public function __unset($key) {
		return $this->remove($key);
	}
}
?>

