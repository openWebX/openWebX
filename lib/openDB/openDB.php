<?php

abstract class openDB_Abstract extends openWebX implements openObject {
	
	
}

require_once(str_replace('openDB.php','openDB_'.strtolower(Settings::get('database_type')).'.php',__FILE__));

?>
