<?php
// ########################################################################
// # File: $Id: openHTML_Body_List.php 217 2009-08-14 13:56:19Z jens $
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

class openHTML_Body_List extends openWebX implements openObject {

    protected $data = array();

    public function __construct($strID) {
        $this->id       = openFilter::filterAction('sanitize','string',$strID);
        $this->type     = 'u';
        $this->content  = '';
        return $this;
    }

    public function __destruct() {

    }

    public function addItem($strID,$strContent) {
        $strID = openFilter::filterAction('sanitize','string',$strID);
        $this->$strID = new openHTML_Body_List_Element($strID);
        $this->$strID->content = $strContent;
    }

    public function build() {
    	$myList 		= new openHTML_Tag($this->data['type'].'l');
    	$myList->id		= $this->data['id'];
    	unset($this->data['id'],$this->data['type']);
    	$retVal			= '';
        foreach ($this->data as $key => $val) {
            if (is_a($val,'openHTML_Body_List_Element')) {
                $retVal.=$val->build();
                unset($this->data[$key]);
            } else {
            	$myList->$key = $val;
            }
        }
        $myList->content = $retVal;
        $retVal = $myList->build();
        unset ($myList);
        $this->isBuilt = true;
        return $retVal;
    }

}

class openHTML_Body_List_Element extends openWebX implements openObject {

    public $data = array();

    public function __construct($strID) {
    	$this->id       = openFilter::filterAction('sanitize','string',$strID);
        $this->content  = '';
    }

    public function __destruct() {
    }


    public function build() {
    	$myID 		= $this->data['id'];
    	$myContent	= $this->data['content'];
    	unset ($this->data['id'],$this->data['content']);
    	$myTag 		= new openHTML_Tag('li');
    	$myTag->id	= $myID;
        foreach ($this->data as $key=>$val) {
        	$key = openFilter::filterAction('clean','string',$key);
        	$val = openFilter::filterAction('clean','string',$val);
        	$myTag->$key = $val;
		}
		$myTag->content = $myContent;
		$retVal = $myTag->build();
		unset ($myTag);
		$this->isBuilt = true;
		return ($retVal);
    }

}
?>