<?php
// ########################################################################
// # File: $Id: openHTML_Body.php 229 2009-08-19 04:40:11Z jens $
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
// # Revision: $Revision: 229 $
// ########################################################################
class openHTML_Body extends openWebX implements openObject {

	protected $data 	= array();
	protected $elements = array();

	public function __construct() {
	}

	public function __destruct() {
	}

	public function add($strType,$strID,$strOptional='') {
		$strID = openFilter::filterAction('clean','string',$strID);
		switch (strtolower($strType)) {
			case 'div':
				$this->elements[$strID] = new openHTML_Body_Div($strID);
				break;
            case 'list':
                $this->elements[$strID] = new openHTML_Body_List($strID);
                break;
            case 'img':
            case 'image':
            	$this->elements[$strID] = new openHTML_Body_Image($strID);
            	break;
            case 'form':
            	$this->elements[$strID] = new openHTML_Body_Form($strID);
            	break;
            case 'fieldset':
            	$this->elements[$strID] = new openHTML_Body_Fieldset($strID,$strOptional);
            	break;
            case 'table':
            	$this->elements[$strID] = new openHTML_Body_Table($strID,$strOptional);
            	break;
            case 'link':
            	$this->elements[$strID] = new openHTML_Body_Link($strID,$strOptional);
            	break;
		}
        return $this->elements[$strID];
	}

	public function build() {
		$myBody = new openHTML_Tag('body');
		$myBody->id = 'body';
		foreach ($this->elements as $key => $val) {
			if (!isset($val->data['isBuilt'])) $myBody->content.=$val->build();
		}
        $retVal = $myBody->build();
		return $retVal;
	}

}
?>
