<?php
// ########################################################################
// # File: $Id: openFeeds.php 217 2009-08-14 13:56:19Z jens $
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
* openList
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openFeeds.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openFeeds
* @uses openWebX
*/
class openList extends openWebX implements openObject {

	private $docObject 	= NULL;
	private $listItems 	= array();
	private $listID		= '';
	
	public function __construct($strTitle) {
		$this->registerSlots();
		$this->listID	 = md5($strTitle);
		$this->docObject = new openDocument($this->listID,'list');	
	}
	
	public function __destruct() {
		$this->docObject->save();
		unset ($this->docObject);	
	}
	
	public function listAddItem($strTitle) {
		$this->listItems[] = new openListItem(count($this->listItems), $this->listID, $strTitle);	
	}
	
	public function handleSignal($strSignalName, $mixedParams) {
      	switch(strtolower($strSignalName)) {
        	case 'slot':
				$this->listProcess($mixedParams);
            	break;
      	}
    }
	
	private function listProcess($arrParams) {
			
	}
	
	private function registerSlots() {
		openWebX::registerSlot($this,'list',0);	
	}
	
}

class openListItem extends openWebX implements openObject {
	
	private $docObject = NULL;
	
	public function __construct($intPos, $idList, $strTitle) {
		$this->docObject = new openDocument(md5($strTitle),'list_item');
		$this->docObject->listid 	= $idList;
		$this->docObject->position 	= $intPos;
	}
	
	public function __destruct() {
		$this->docObject->save();
		unset ($this->docObject);	
	}
	
}

?>