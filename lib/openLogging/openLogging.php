<?php
// ########################################################################
// # File: $Id: openLogging.php 217 2009-08-14 13:56:19Z jens $
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
// # Revision: $Revision: 217 $
// ########################################################################
/**
* openLogging
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openLogging.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openLogging
* @uses openWebX
*/
class openLogging extends openWebX implements openObject {

	/**
	 * __construct
	 *
	 * Constructor of the class
	 *
	 * registers the slots available for this class...
	 */
	public function __construct() {
		$this->registerSlots();
	}

	public function __destruct() {

	}

	public function handleSignal($strSignalName,$mixedParams) {
	  switch(strtolower($strSignalName)) {
	    case 'log':
	    	if (openFilter::filterAction('check','string',$mixedParams)) $this->logEvent(openFilter::filterAction('clean','string',$mixedParams));
	    	break;
	  }
	}

	private function registerSlots() {
		openWebX::registerSlot($this,'log',0);
	}



	private function logEvent($strLine) {
		$strLine = openFilter::filterAction('clean','string',$strLine);
    	switch (Settings::get('log_type')) {
      		case LOG_2_SCREEN:
        		self::log2Screen($strLine);
        		break;
      		case LOG_2_FILE:
      			default:
        		self::log2File($strLine);
        		break;
    	}
  	}

  	private function log2Screen($strLine) {
    	echo '<br/>'.date('d.m.Y H:i:s').':<br/>&nbsp;&nbsp;'.$strLine;
  	}

  	private function log2File($strLine) {
  		$myFS = openWebX::init('openFilesystem');
    	$myFile = Settings::get('path_log').date('Y-m-d').'.log';
    	$myFS->fileAppendText("\n".date('d.m.Y H:i:s').': '.$strLine);
    	unset($myFS);
  	}

}
?>
