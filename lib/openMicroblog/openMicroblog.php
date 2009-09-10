<?php
// ########################################################################
// # File: $Id: openMicroblog.php 217 2009-08-14 13:56:19Z jens $
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
// # Autor: $Author: jens $
// ########################################################################
// # Revision: $Revision: 217 $
// ########################################################################
// # LastChange: $LastChangedDate: 2009-08-14 15:56:19 +0200 (Fri, 14 Aug 2009) $
// ########################################################################
/**
* openMicroblog
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openMicroblog.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openMicroblog
* @uses openWebX
*/
class openMicroblog extends openWebX implements openObject {

	public $data = array();
	
	private $mbEntries = array();

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
	  	if (in_array('refresh',$mixedParams)) {
	  		if ($strSignalName == 'microblog') {
	  			$this->mbGetNew('twitter');
	  			$this->mbGetNew('laconica');
	  		} else {
	  			$this->mbGetNew($strSignalName);	
	  		}
	  	}
	  	$this->mbGet($strSignalName);
	  	if (in_array('show',$mixedParams)) {
	  		$this->mbShow();	
	  	}
	}

	private function mbShow() {
		foreach ($this->mbEntries as $entry) {
			$myTag = new openHTML_Tag('p');
			$myTag->class = 'openMicroblog_entry';
			
			echo '<p class="openMicroblog_entry"><img src="/share/images/icons/openMicroblog/'.$entry['type'].'.png" style="float:left;" /><p class="openMicroblog_title">'.$entry['author'].' ('.date('d.m.Y H:i:s',strtotime($entry['created'])).')</p><p class="openMicroblog_content">'.openString::strConvertLinks($entry['content']).'</p></p>';
			unset ($myTag);
		}
	}

	private function mbGet($strType,$iLimit=5) {
		$arrParams = array ('type'=>$strType);
		$myDB = new openDB();
		($strType=='microblog') ? $strSQL = SQL_openMicroblog_getEntries : $strSQL = SQL_openMicroblog_getEntries; 
		$myDB->dbSetStatement($strSQL.' LIMIT '.$iLimit,$arrParams);
		$myDB->dbFetchArray();
		$this->mbEntries = $myDB->dbResultArray;
		unset($myDB);
	}

	private function mbGetNew($strType='laconica') {
		$strType = strtolower(openFilter::filterAction('clean','string',$strType));
	    $this->mbBuildRequestURI($strType);
		$this->mbGetEntries();
		$this->mbPrepareSave($strType);
		$this->mbSave();
		
	}

	private function mbSave() {
		$myDB = new openDB();
		foreach ($this->mbEntries as $elements) {
			$myDB->dbSetStatement(SQL_openMicroblog_storeEntry,$elements);
			$myDB->dbExecute();	
		}
		unset ($myDB);
	}

	private function mbPrepareSave($strType) {
		$myArray = array();
		foreach ($this->mbEntries->status as $val) {
			$arrVal 	= (array)$val;
			$arrUser	= (array)$arrVal['user'];
			$item = array (
				'created' => date('Y-m-d H:i:s',strtotime($arrVal['created_at'])),
				'author' => $arrUser['name'],
				'type'	=> $strType,
				'content' => $arrVal['text']
			);
			$hash = md5(serialize($item));
			$item['hash'] = $hash;
			$myArray[] = $item;
		}
		$this->mbEntries = $myArray;
	}

	private function mbGetEntries() {
		$buffer             = file_get_contents($this->mbRequestURI);
        $xml            = new SimpleXMLElement($buffer);
        $this->mbEntries= $xml;
        unset($xml);
	}

	private function mbBuildRequestURI($strType) {
		switch ($strType) {
			case 'twitter':
				$this->mbRequestURI = openFilter::filterAction('sanitize','url','http://twitter.com/statuses/user_timeline/'.Settings::get('twitter_user').'.xml');
				break;
			case 'laconica':
				$this->mbRequestURI = openFilter::filterAction('sanitize','url',Settings::get('laconica_url').'statuses/user_timeline/'.Settings::get('laconica_user').'.xml');
				break;
		}

	}

	private function registerSlots() {
		openWebX::registerSlot($this,'update',0);
		openWebX::registerSlot($this,'twitter',0);
		openWebX::registerSlot($this,'laconica',0);
		openWebX::registerSlot($this,'microblog',0);
	}
}
?>