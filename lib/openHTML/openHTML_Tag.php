<?php
// ########################################################################
// # File: $Id: openHTML_Tag.php 217 2009-08-14 13:56:19Z jens $
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


class openHTML_Tag extends openWebX implements openObject {

	public $data = array();

	public function __construct($strTagName,$bShortClose=false,$bOmitClose=false) {
		$this->tag 			= strtolower(openFilter::filterAction('sanitize','string',$strTagName));
		$this->shortclose	= $bShortClose;
        $this->omitclose    = $bOmitClose;
		$this->content	= '';
        return $this;
    }

	public function __destruct() {

	}

	public function build() {
		$myTag 		= $this->data['tag'];
		$mySC		= $this->data['shortclose'];
        $myOC       = $this->data['omitclose'];
		if ($myTag!='meta') {
            $myContent	= $this->data['content'];
            unset ($this->data['content']);
        } else {
            $myContent = '';
        }
		unset($this->data['tag'],$this->data['shortclose'],$this->data['omitclose']);
		$retVal = '<'.$myTag;
		foreach ($this->data as $key => $val) {
            if ($key=='xmllang') $key='xml:lang';
			$retVal.= ' '.str_replace('_','-',$key).'="'.$val.'"';
		}
		if ($mySC && !$myOC) $retVal.=' /';
		$retVal.='>'.$myContent;
		if (!$mySC && !$myOC) $retVal.=(($myTag!='script')?CRLF:'').'</'.$myTag.'>';
		return CRLF . $retVal;
	}
}
?>