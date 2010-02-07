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
* openGallery
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openFeeds.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openGallery
* @uses openWebX
*/
class openGallery extends openWebX implements openObject {
	
	private $fileObject = null;
	private $listObject = null;
	
	public $galleryArray 		= array();
	public $galleryItemArray	= array();
	
	public function __construct() {
		$this->registerSlots();
	}
	
	public function __destruct() {	
	}
	
	public function galleryGetAll() {
		$this->listObject = new openList();	
		$this->listObject->listGetListsByType('gallery');
		$this->galleryArray = $this->listObject->listArray;
		unset($this->listObject);
	}
	
	public function galleryGetItems($strGallery='') {
		$this->listObject = new openList();	
		$this->listObject->listGetListItemsByListType('gallery');
		$this->galleryItemArray = $this->listObject->listItemArray;
		unset($this->listObject);
	}
	
	public function galleryBuildPiles($pileSize=9) {
		$this->galleryGetItems();
		//openDebug::dbgVar($this->galleryItemArray);
		$actContainer = '';
		$actID = '';
		$retVal = '';
		$iCounter = 1;
		foreach ($this->galleryItemArray as $val) {
			if ($actContainer!=$val['list_title']) {
				if ($actContainer!='') {
					$retVal .='
					<div id="gallery_'.$actID.'_title" class="gallery_container_title">'.$actContainer.'</div>
					</div>
					';
				}
				$iCounter = 1;
				$actContainer = $val['list_title'];
				$actID = $val['list_id'];	
				$retVal .= '
				<div id="gallery_'.$actID.'" class="gallery_container">
				';
			}
			if ($iCounter<=$pileSize) {
				$retVal .= '
				<img id="gallery_'.$actID.'_image_'.$val['hash'].'" class="gallery_image" src="'.Settings::get('web_cache').$val['hash'].'.png" />
				';
				$iCounter++;
			}
				
				
		}
		return $retVal;
	}
	
	public function galleryBuildFromDirectory($strDirectory) {
		$arrImages = array();
		$this->listObject = new openList();
		$this->listObject->listBuildFromDirectory($strDirectory,'gallery','images');
		foreach ($this->listObject->listItemArray as $val) {
			$arrImages[] = $val['folder'].'/'.$val['file'];
		}
		unset($this->listObject);
		$this->galleryBuildThumbnails($arrImages);
	}	
	
	private function galleryBuildThumbnails($arrImages) {
		foreach ($arrImages as $val) {
			$myFile = Settings::get('path_webcache').md5($val).'.png';
			if (!file_exists($myFile)) {
				$myImg = new openImage('resize/75x75/save',$val);
				unset($myImg);
			} 
		}	
	}
	
	private function registerSlots() {
		openWebX::registerSlot($this,'gallery',0);	
	}
	
	public function handleSignal($strSignalName, $mixedParams) {
      	switch(strtolower($strSignalName)) {
        	case 'gallery':
				$this->galleryProcess($mixedParams);
            	break;
      	}
    }
	
	private function galleryProcess($arrParams) {
		
	}
}


?>