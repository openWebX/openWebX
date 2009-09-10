<?php
/**
* This is the default include
*
* @author Jens Reinemuth
* @version $Id: openWebX.php 235 2009-09-10 06:03:02Z jens $
* @package openWebX
*/
/**
 * Start the session...
 */
session_start();
/**
 * class Settings
 *
 * simple wrapper to access the system-settings
 */
class Settings extends openWebX {
  /**
   * get
   *
   * get the value of the given settings-variable
   *
   * @access static
   * @access public
   * @param string strName name of the settings-variable
   * @return mixed value of the settings-variable
   */
  static function get($strName) {
    return ((isset($_SESSION['openWebX']['settings'][$strName]) ? $_SESSION['openWebX']['settings'][$strName] : NULL));
  }
}

/**
 * include globals, settings and sql-statements
 */
require_once('globals.php');
require_once('settings.php');
require_once('statements_'.strtolower(Settings::get('database_type')).'.php');


/**
* __autoload
*
* overload function to automatically include the needed files if an object is initialized
*
* @param string $strLibName Name of the class-file to autoinclude
*/
function __autoload($strLibName) {
    $strLibName = strval(trim($strLibName));
    if ($strLibName!='openWebX') {
        $loadLib = Settings::get('path_lib').$strLibName.'/'.$strLibName.'.php';
        //echo 'Trying to load '.$loadLib;
        require($loadLib);
    }
}

/**
 * Set unique-id for page
 */
openSystem::sysSetValue('pageID',md5($_GET.$_POST));
//openCache::cacheCheck();
//openCache::cacheStart();

/**
 * Get all get-requests and pass them through...
 */
openRequest::parse();


/**
 * prepare the object-preinitialization
 */
// Fill the object-cache
//if (is_null(openSystem::sysGetValue('object_cache'))) {
//    openSystem::sysObjectCache();
//}


//#########################################################################################################
// one class to rule them all... ;-)
// Define the base class with overloading functions
//#########################################################################################################
class openWebX {

    protected $data = array();

    static function init($strObject,$mixedParams='') {
        try {
	        if (!$myClass = openSystem::sysGetValue($strObject)) {
	            $myClass = new $strObject($mixedParams);
	        	  openSystem::sysSetValue($strObject,$myClass);
	        }
        	return($myClass);
        } catch (Exception $e) {
        	$e->errHandling();
        }
    }

    public function __get($strVariable) {
        try {
            if (array_key_exists($strVariable,$this->data)) {
                return $this->data[$strVariable];
            } else {
                throw new openException (EXCEPTION_OBJECT_GETERROR,'GET: Variable is not defined: '.$strVariable);
            }
        } catch (Exception $e) {
            $e->errHandling();
        }
    }

    public function __set($strVariable,$mixedValue) {
        try {
            $this->data[$strVariable] = $mixedValue;
        } catch (Exception $e) {
            $e->errHandling();
        }
    }

    public function __isset($strVariable) {
        try {
            return (isset($this->data[$strVariable]));
        } catch (Exception $e) {
            $e->errHandling();
        }
    }

    final public function __unset($strVariable) {
        try {
        	 unset($this->data[$strVariable]);
        } catch (Exception $e) {
        	 $e->errHandling();
        }
    }

    final public function __call($strFunction,$arrParams) {
      try {
        throw new openException (EXCEPTION_OBJECT_CALLERROR, 'CALL: Function does not exist or is not public: '. $strFunction."(".implode(", ", $arrParams).")");
      } catch (Exception $e) {
        $e->errHandling();
      }
    }

    final public function __toString() {
    	return self::build();
	  }

	protected function build() {
		return get_class(self);
	}

  	final public function registerSlot($objRegistrar,$strSlotName,$iPriority=0) {
  		$myHash = md5(get_class($objRegistrar).$strSlotName);
  		$slot = new StdClass();
  		$slot->_id 		=  $myHash;
  		$slot->type		= 'slot';
  		$slot->object	= get_class($objRegistrar);
  		$slot->slot		= $strSlotName;
  		$slot->priority	= $iPriority;
  		$myDB = new openDB();
  		$myDB->dbStore('slot',$slot);
  		unset($myDB);
  	}

  	final static function sendSignal($strSignalName,$mixedParams) {
  	  $strSignalName = openFilter::filterAction('clean','string',$strSignalName);
      $myArr = self::getSlots($strSignalName);
      foreach ($myArr as $key=>$val) {
        $myObj = self::init($val['object']);
        $myObj->handleSignal($strSignalName,$mixedParams);
      }
  	}

  	final static function showSlots() {
	}

    final static function getSlots($strSlotName) {
      $retVal = false;
      return ($retVal);
    }

}

interface openObject {
    //public function registerSlots();
    //public function handleSignal($strSignalName,$mixedParams);
}

?>
