<?php
// ########################################################################
// # File: $Id: openHTML_Body_Fieldset.php 217 2009-08-14 13:56:19Z jens $
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
class openHTML_Body_Fieldset extends openWebX implements openObject {

	public $data = array();

	public function __construct($strID,$strLegend) {
		$this->id 		= openFilter::filterAction('clean','string',$strID);
		$this->legend	= openFilter::filterAction('clean','string',$strLegend);
		$this->content	= '';
        return $this;
    }

	public function __destruct() {

	}

	public function build() {
		$myFS 				= new openHTML_Tag('fieldset');
    	$myFS->id			= $this->data['id'];
    	$myLegend			= new openHTML_Tag('legend');
    	$myLegend->content	= $this->data['legend'];
    	$myFS->content		= $myLegend->build();
    	unset($myLegend);
    	$myFS->content		.= $this->data['content'];
    	unset($this->data['id'],$this->data['content'],$this->data['legend']);
        foreach ($this->data as $key => $val) {
        	$myFS->$key 	= $val;
        }
        $retVal 			= $myFS->build();
        unset ($myFS);
        $this->isBuilt		= true;
        return $retVal;
	}
}
?>