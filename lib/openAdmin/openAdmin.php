<?php
// ########################################################################
// # File: $Id$
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
// # Autor: $Author$
// ########################################################################
// # Revision: $Revision$
// ########################################################################
/**
* openAdmin
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id$
* @package openWebX
* @subpackage openAdmin
* @uses openWebX
*/
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
		
		echo 'im Admin-Bereich!';
	
	}
			
}
?>