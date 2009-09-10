<?php
// ########################################################################
// # File: $Id: openXML.php 217 2009-08-14 13:56:19Z jens $
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
/**
* openFilter
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openXML.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openXML
* @uses openWebX
*/
class openXML extends openWebX implements openObject {
	
	public function __construct() {
	}

	public function __destruct() {

	}

	public function set($strProperty,$mixedValue) {
		$this->$strProperty = $mixedValue;
	}

	public function get($strProperty) {
		return $this->$strProperty;
	}

	
	private function xmlGetHeader() {
		return '<?xml version="1.0" encoding="'.Settings::get('sys_encoding').'"?>';
	}
	
	
}
?>
