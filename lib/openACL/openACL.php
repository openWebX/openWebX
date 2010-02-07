<?php
/**
* openACL
*
* Part of the openWebX-API
* This class is a stub
* 
* @package openWebX
* @subpackage openACL
* @uses openWebX
* 
* @license http://www.gnu.org/licenses/gpl.html GPLv3
* @author Jens Reinemuth <jens@reinemuth.info>
* @version 0.00
**/
class openACL extends openWebX implements openObject {
   public function __construct() {
        $this->registerSlots();
    }

    public function __destruct() {

    }

    public function handleSignal($strSignalName,$mixedParams) {

    }

    private function registerSlots() {
    }
}
?>