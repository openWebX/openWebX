<?php
// ########################################################################
// # File: $Id: openDB_mysqli.php 235 2009-09-10 06:03:02Z jens $
// ########################################################################
// # This program is free software; you can redistribute it and/or modify
// # it under the terms of the GNU General Public License V3
// #
// # This program is subject to the GPL license, that is bundled with
// # this package in the file /doc/GPL-3.
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
/**
* openDB_mysqli
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openDB_mysqli.php 235 2009-09-10 06:03:02Z jens $
* @package openWebX
* @subpackage openDB
* @uses openWebX
*/
class openDB extends openDB_Abstract {
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// public properties
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	/**
	 * anything else
	 * 
	 * overloaded 
	 * @var unknown_type
	 */
	public $data 			= array();
    public $dbResultArray 	= array();
    public $dbResultObject	= null;
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// private properties
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    private $dbObject 		= NULL;
    private $dbStatement	= NULL;
    
    /**
    * Constructor & Destructor
    */
 	/**
 	 * constructor
 	 * 
 	 * sets the needed connection-variables and connects to db
 	 */
    public function __construct() {
        $this->dbSetVariables();
        $this->dbConnect();
	}
	/**
	 * destructor
	 * 
	 * closes connection to db
	 */
	public function __destruct() {
        $this->dbDisconnect();
	}
	/**
	* Sleep & Wakeup
	*/
	public function __sleep() {
		//$this->dbDisconnect();
		unset($this->dbObject,$this->dbStatement,$this->dbResultArray);
		return array('data');
	}
	public function __wakeup() {
      	$this->dbSetVariables();
		$this->dbConnect();
	}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Private functions
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	private function dbSetVariables() {
	    $this->dbType      = Settings::get('database_type');
	    $this->dbServer    = Settings::get('database_server');
	    $this->dbUser      = Settings::get('database_username');
	    $this->dbPassword  = Settings::get('database_password');
	    $this->dbDatabase  = Settings::get('database_name');
	}
	//#########################################################################################################
	private function dbBuildDSN() {
	//#########################################################################################################
		  if ($this->dbType=='mysqli') $this->dbType='mysql';
		  $this->dbDSN = $this->dbType.':dbname='.$this->dbDatabase.';host='.$this->dbServer;
	}
	//#########################################################################################################
	private function dbConnect() {
	//#########################################################################################################
		$this->dbBuildDSN();
		try {
			$this->dbObject = new PDO($this->dbDSN,$this->dbUser,$this->dbPassword);
		} catch (PDOException $e) {
          	echo $e->getMessage();
 		}
	}
	//#########################################################################################################
	private function dbDisconnect() {
	//#########################################################################################################
    	unset($this->dbStatement,$this->dbObject);
	}
	//#########################################################################################################
	private function dbQuery() {
	//#########################################################################################################
		try {
			$this->dbResult = $this->dbStatement->query();
			if (stripos($this->dbStatement,'insert')!==false || stripos($this->dbStatement,'update')!==false) {
            	$this->dbInsertID = $this->dbObject->lastInsertId();
            	$this->dbUsedID   = $this->dbInsertID();
        	}
		} catch (PDOException $e) {
          	echo $e->getMessage();
 		} catch (Exception $e) {
        	$e->errHandling();
      	}
        
	}
	//#########################################################################################################
	private function dbExec() {
	//#########################################################################################################
		$this->dbRowCount = $this->dbObject->exec($this->dbStatement);
	}
	//#########################################################################################################
	private function dbGetStructure($strTable) {
    	$this->dbSetStatement(SQL_openDB_dbStructure,array(':table'=>$strTable));
		$this->dbFetchArray();
		$this->dbTableStructure = $this->dbResultArray;
	}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Public Methods
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	//#########################################################################################################
	public function dbSetStatement($strSQL,$arrParams=null) {
	//#########################################################################################################
  		try {
  			openDebug::dbgVar($strSQL,'SQL');
  			openDebug::dbgVar($arrParams,'Params');
    		$this->dbStatement = $this->dbObject->prepare($strSQL);
    		if ($arrParams && is_array($arrParams)) {
    			foreach ($arrParams as $key=>$val) {
    		    	$this->dbStatement->bindValue($key,openFilter::filterAction('sanitize','string',$val));
    			}
    		}
    		openDebug::dbgVar($this->Statement);
    		return true;
  		} catch (PDOException $e) {
  			echo $e->getMessage();
  			return false;
  		} catch (Exception $e) {
        	$e->errHandling();
        	return false;
      	}
	}
	
	/**
	* dbFetchArray
	*
	* execute the prepared statement and fills variable "dbResultArray" with the results
	*
	* @param	void
	* @return	void
	*/
	public function dbFetchArray() {
        try {
            $this->dbResultArray = array();
		    if (!$this->dbStatement) {
		    	throw new openException(EXCEPTION_DB_QUERYEMPTY,'Statement can not be empty while executing!');
		    } else {
			    $this->dbStatement->execute();
			    $this->dbStatement->setFetchMode(PDO::FETCH_ASSOC);
			    $this->dbResultArray=$this->dbStatement->fetchAll();
		    }
	   	} catch (PDOException $e) {
    	    echo 'Connection failed: ' . $e->getMessage();
 	   	} catch (Exception $e) {
 	     	$e->errHandling();
 	   	}
	}
	/**
	* dbFetchObject
	*
	* execute the prepared statement and fills variable "dbResultArray" with the results
	*
	* @param	void
	* @return	void
	*/
	public function dbFetchObject() {
    	try {
        	$this->dbResultObject = null;
        	if (!$this->dbStatement) {
          		throw new openException(EXCEPTION_DB_QUERYEMPTY,'Statement can not be empty while executing!');
        	} else {
        		$this->dbStatement->execute();
          		$this->dbStatement->setFetchMode(PDO::FETCH_ASSOC);
          		$this->dbResultArray=$this->dbStatement->fetchAll();
        	}
      	} catch (PDOException $e) {
        	echo 'Connection failed: ' . $e->getMessage();
      	} catch (Exception $e) {
        	$e->errHandling();
      	}
  }

  	/**
   	* dbExecute
   	*
   	* 
   	*/
	public function dbExecute() {
	    try {
	      	if (!$this->dbStatement) {
	        	throw new openException(EXCEPTION_DB_QUERYEMPTY,'Statement can not be empty while executing!');
			} else {
	        	if (!$this->dbStatement->execute()) {
	          		throw new openException(EXCEPTION_DB_QUERYERROR,'Error while executing prepared statement!');
	        	}
	      	}
	    } catch (PDOException $e) {
	      	echo 'Connection failed: ' . $e->getMessage();
	    } catch (Exception $e) {
	      	$e->errHandling();
	    }
	  }

  	public function dbCreateView($strViewName,$strSQL) {
		$strSQL = 'CREATE OR REPLACE VIEW `'.$strViewName.'` AS '.$strSQL;
		$this->dbSetStatement($strSQL);
		$this->dbQuery();
	}
	public function dbEmptyTable($strTable) {
		$this->dbSetStatement('TRUNCATE TABLE `'.$strTable.'`');
		$this->dbExec();
		$this->dbOptimizeTable($strTable);
	}
	public function dbRepairTable($strTable) {
		$this->dbSetStatement('REPAIR TABLE `'.$strTable.'`');
		$this->dbExec();
	}
	public function dbOptimizeTable($strTable) {
		$this->dbSetStatement('OPTIMIZE TABLE `'.$strTable.'`');
		$this->dbExec();
	}
	public function dbInsert($strTable,$strFields,$strValues) {
		$mySQL = 'INSERT INTO `'.$strTable.'` ('.$strFields.') VALUES ('.$strValues.')';
		$this->dbSetStatement($mySQL);
		$this->dbExec();
		$this->dbInsertID = $this->dbObject->lastInsertId();
	}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
}

?>
