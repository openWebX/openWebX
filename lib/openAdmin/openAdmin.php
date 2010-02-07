<?php
/**
* openAdmin
*
* Part of the openWebX-API
* This class is a stub
* 
* @package openWebX
* @subpackage openAdmin
* @uses openWebX 
* 
* @license http://www.gnu.org/licenses/gpl.html GPLv3
* @author Jens Reinemuth <jens@reinemuth.info>
* @version 0.00
**/
class openAdmin extends openWebX implements openObject {
	
	public function __construct() {
		$this->registerSlots();	
	}
	
	public function handleSignal($strSignalName,$mixedParams) {
      switch(strtolower($strSignalName)) {
        case 'admin':
			$this->adminMainpage();
            break;
      }
    }
	
	private function registerSlots() {
		openWebX::registerSlot($this,'admin',0);
	}
	
	private function adminMainpage() {
		
		// TODO: build up admin-pages
	
	}
			
}
?>