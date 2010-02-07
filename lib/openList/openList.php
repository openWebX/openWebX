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

	private $dbObject 			= NULL;
	private $fileObject			= NULL;
	public $listArray			= array();
	public $listItemArray		= array();
	
	public function __construct() {
		$this->registerSlots();
	}
	
	public function __destruct() {	
	}
	
	
	public function handleSignal($strSignalName, $mixedParams) {
      	switch(strtolower($strSignalName)) {
        	case 'list':
				$this->listProcess($mixedParams);
            	break;
      	}
    }
	
	
	public function listGetListsByType($strType) {
		$this->dbObject = new openDB();
		$this->dbObject->dbSetStatement(SQL_openList_getList_ByType,array('type'=>$strType));
		$this->dbObject->dbFetchArray();
		$this->listArray = $this->dbObject->dbResultArray;
		unset($this->dbObject);
	}
	
	public function listGetListItemsByListType($strListType) {
		$this->dbObject = new openDB();
		$this->dbObject->dbSetStatement(SQL_openList_getListItems_ByListType,array('type'=>$strListType));
		$this->dbObject->dbFetchArray();
		$this->listItemArray = $this->dbObject->dbResultArray;
		unset($this->dbObject);
		
	}
	
	
	private function listAdd($strTitle,$strType,$strFolder,$strElements = 0) {
		$this->listArray[] = array(
			'title' 	=> openFilter::filterAction('clean','string',$strTitle),
			'hash' 		=> md5(openFilter::filterAction('clean','string',$strFolder)),
			'type' 		=> openFilter::filterAction('clean','string',$strType),
			'folder' 	=> openFilter::filterAction('clean','string',$strFolder),
			'elements' 	=> intval($strElements),
		);
	}
	
	private function listStore() {
		$this->dbObject = new openDB();
		$this->dbObject->dbBulkInsert(SQL_openList_addList,$this->listArray);
		unset($this->dbObject);	
	}
	
	private function listAddItem($strTitle,$strType,$strFolder,$strFile) {
		$this->listItemArray[] = array(
			'title' 	=> openFilter::filterAction('clean','string',$strTitle),
			'hash' 		=> md5(openFilter::filterAction('clean','string',$strFolder).'/'.openFilter::filterAction('clean','string',$strFile)),
			'type' 		=> openFilter::filterAction('clean','string',$strType),
			'folder' 	=> openFilter::filterAction('clean','string',$strFolder),
			'file' 		=> openFilter::filterAction('clean','string',$strFile),
		);
	}

	private function listStoreItems() {
		$this->dbObject = new openDB();
		$this->dbObject->dbBulkInsert(SQL_openList_addListItem,$this->listItemArray);
		unset($this->dbObject);	
			
	}
	
	public function listBuildFromDirectory($strDirectory,$strType,$strItemType) {
		if (file_exists($strDirectory)) {
			
			$itemCount = array();
			
			// First profile the directory...
			$this->fileObject = new openFilesystem();
			$dirContents = $this->fileObject->fileProfileDir($strDirectory);
			
			
			// ...then scan the results for usable files...
			$allowedExts = Settings::get('file_types_'.$strItemType);		
			foreach($dirContents['files'] as $key=>$val) {
				$directory	= dirname($val);
				$file		= basename($val);
				$extension	= $this->fileObject->fileGetFileExtension($val);
				$arrTmp = explode('/',$directory);
				if (substr($arrTmp[count($arrTmp)-1],0,1)!='.' && substr($file,0,1)!='.' && in_array(strtolower($extension),$allowedExts)) {
					(isset($itemCount[$directory])) ? $itemCount[$directory]++ : $itemCount[$directory] = 1;
					$this->listAddItem($file,$strItemType,$directory,$file);	
				}
			}
			unset($this->fileObject);

			// ...then add the directories...
			foreach($dirContents['directories'] as $key=>$val) {
				$arrTmp = explode('/',$val);
				if (substr($arrTmp[count($arrTmp)-1],0,1)!='.') {
					$this->listAdd($arrTmp[count($arrTmp)-1],$strType,$val,(isset($itemCount[$val]) ? $itemCount[$val] : 0));	
				}
			}
			
			// ... and finally put the data to the database!
			$this->listStore();
			$this->listStoreItems();
		}
	}
	
	
	private function listProcess($arrParams) {
			
	}
	
	private function registerSlots() {
		openWebX::registerSlot($this,'list',0);	
	}
	
}


?>