<?php
// ########################################################################
// # File: $Id$
// ########################################################################
// # This program is free software; you can redistribute it and/or modify
// # it under the terms of the GNU General Public License V3
// #
// # This program is subject to the GPL license, that is bundled with
// # this package in the file /share/LICENSE.TXT.
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
* openBlog
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id$
* @package openWebX
* @subpackage openBlog
* @uses openWebX
*/
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
				$this->blogEntry = new openDocument($mixedParams[1],'blogentry'); 
				break;
		}
	}
	
}
?>
