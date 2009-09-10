<?php
// ########################################################################
// # File: $Id: openHTML_Body_Link.php 222 2009-08-16 08:36:44Z jens $
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
// # Revision: $Revision: 222 $
// ########################################################################
class openHTML_Body_Link extends openWebX implements openObject {
    
    public $data  = array();
    
	public function __construct($strID,$strType='internal') {
		$this->id 	= openFilter::filterAction('clean','string',$strID);
		$this->type	= openFilter::filterAction('clean','string',$strType);
        return $this;
    }

	public function __destruct() {

	}

	public function build() {
		$myLink 		= new openHTML_Tag('a');
    	$myLink->id		= $this->data['id'];
    	$myType			= $this->data['type'];
    	unset($this->data['id'],$this->data['type']);
        foreach ($this->data as $key => $val) {
        	$myLink->$key = $val;
        }
        $myLink->content.=$this->addIcon($myType);
        $retVal = $myLink->build();
        unset ($myLink);
        return $retVal;
	}
	
	private function addIcon($strType) {
		$retVal = '';
		$arrType = explode('::',$strType);
		switch ($arrType[0]) {
			case 'external':
			case 'mailto':
				$myImg 		= new openHTML_Tag ('img',true);
				$myImg->id 	= md5(microtime());
				$myImg->src	= Settings::get('web_icons').'openHTML/openHTML_Body_Link/'.$strType.'.png';
				$retVal = $myImg->build();
				unset($myImg);
				break;
			case 'icon':
				$myImg 		= new openHTML_Tag ('img',true);
				$myImg->id 	= md5(microtime());
				$myImg->src	= Settings::get('web_icons').$arrType[1];
				$retVal = $myImg->build();
				unset($myImg);
				break;		
		}
		return $retVal;
	}

}
?>