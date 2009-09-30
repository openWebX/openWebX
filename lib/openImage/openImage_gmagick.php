<?php
// TODO implement this, when pecl-gmagick is here...

class openImage_gmagick extends openWebX implements openObject {

	private $docObject = null;
	private $imgObject = null;

	public function __construct() {
		$this->registerSlots();	
	}
	
	public function __destruct() {
		
	}
	
}


?>
