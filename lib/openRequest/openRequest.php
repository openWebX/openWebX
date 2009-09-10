<?php
// ########################################################################
// # File: $Id$
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
// # Author: $Author$
// ########################################################################
// # Revision: $Revision$
// ########################################################################
/**
* openRequest
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openSystem.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openRequest
* @uses openWebX
*/
class openRequest extends openWebX {
	
	public static function parse() {
		if (isset($_GET['request']) && $_GET['request']!='') {
			$strReq = $_GET['request'];
			$arrReq = explode('/',openFilter::filterAction('clean','string',$_GET['request']));
			$actCommand = '';
			$arrCommand = array();
			foreach ($arrReq as $val) {
				if (substr($val,0,1)!=':') {
					$actCommand = $val;
				} else {
					$arrCommand[$actCommand][] = substr($val,1);
				}
			}
			openSystem::sysSetValue('request',$arrCommand);
		}
	}
	
	public static function get($strCommand='') {
		if ($arrReq = openSystem::sysGetValue('request')) {
			if ($strCommand=='') {
				return $arrReq;
			} else {
				foreach ($arrReq as $key=>$val) {
					if ($key==$strCommand) {
						return $val;
					}
				}
			}
		} else {
			return null;
		}
	}
	
	public static function set($strCommand,$arrParams=array()) {
		if ($arrReq = openSystem::sysGetValue('request')) {
			
		} else {
			$arrCommand[$strCommand] = array();
		}
	}
	
}
?>
