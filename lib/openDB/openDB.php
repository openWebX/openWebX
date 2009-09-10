<?php

abstract class openDB_Abstract extends openWebX implements openObject {
	
	public abstract function dbCreateStructure();
	public abstract function dbCreateView($strViewName,$strSQL);
	public abstract function dbExecute();
	public abstract function dbFetchArray();
	public abstract function dbFetchObject();
	public abstract function dbSetStatement($strStatement,$arrParams=null);
	
	public abstract function dbStore($strType,$arrContent);
	
	
	
}

require_once(str_replace('openDB.php','openDB_'.strtolower(Settings::get('database_type')).'.php',__FILE__));

?>
