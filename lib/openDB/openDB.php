<?php

abstract class openDB_Abstract extends openWebX implements openObject {
	
	public abstract function dbCreateStructure();
	public abstract function dbGetByID($strID);
	public abstract function dbGetByType($strType);
	public abstract function dbStore($objContent);
	
	
	
}

require_once(str_replace('openDB.php','openDB_'.strtolower(Settings::get('database_type')).'.php',__FILE__));

?>
