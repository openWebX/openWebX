<?php
/**
* openBlog
*
* Part of the openWebX-API
* This class is a stub
* 
* @package openWebX
* @subpackage openBlog
* @uses openWebX
* 
* @license http://www.gnu.org/licenses/gpl.html GPLv3
* @author Jens Reinemuth <jens@reinemuth.info>
* @version 0.00
**/
class openBlog extends openWebX {
	
	private $blogEntry 	= null;
	private $blogFields	= null;
	
	public function __construct() {
		$this->registerSlots();	
	}
	
	public function handleSignal($strSignalName,$mixedParams) {
      switch(strtolower($strSignalName)) {
        case 'blog':
			$this->blogProcess($mixedParams);
            break;
      }
    }
	
	private function registerSlots() {
		openWebX::registerSlot($this,'blog',0);
	}
	
	private function blogProcess($mixedParams) {
		$strAction = strtolower(openFilter::filterAction('clean','string',$mixedParams[0]));
		switch ($strAction) {
			case 'get':
				
				break;
		}
	}
	
}
?>
