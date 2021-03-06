<?php
// ########################################################################
// # File: $Id: openHTML_Body_Div.php 217 2009-08-14 13:56:19Z jens $
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
class openHTML_Body_Div extends openWebX implements openObject {

	public $data = array();

	public function __construct($strID) {
		$this->id 		= openFilter::filterAction('clean','string',$strID);
		$this->content	= '';
        return $this;
    }

	public function __destruct() {

	}

	public function build() {
		$myDiv 			= new openHTML_Tag('div');
    	$myDiv->id		= $this->data['id'];
    	$myDiv->content	= $this->data['content'];
    	unset($this->data['id'],$this->data['content']);
        foreach ($this->data as $key => $val) {
        	$myDiv->$key = $val;
        }
        $retVal = $myDiv->build();
        unset ($myDiv);
        $this->isBuilt		= true;
        return $retVal;
	}
}
?>