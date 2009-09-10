<?php
// ########################################################################
// # File: $Id: openFilter.php 217 2009-08-14 13:56:19Z jens $
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
* @version $Id: openFilter.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openFilter
* @uses openWebX
*/
class openFilter extends openWebX {


	public static function filterAction($strAction,$strType,$mixedOrig) {
		$retVal = false;
		try {
			$filterType 	= '';
			$filterAction 	= '';
			switch (strtolower(strval($strType))) {
				case 'boolean':
				case 'bool':
					$filterType = 'BOOLEAN';
					break;
				case 'email':
				case 'mail':
					$filterType = 'EMAIL';
					break;
				case 'float':
					$filterType = 'FLOAT';
					break;
				case 'ip':
					$filterType = 'IP';
					break;
				case 'url':
					$filterType = 'URL';
					break;
				case 'integer':
				case 'int':
					$filterType = 'INT';
					break;
				case 'string':
				case 'str':
					$filterType = 'STRING';
					break;
				case 'regex':
				case 'regexp':
					$filterType = 'REGEXP';
					break;
				default:
					throw new openException(EXCEPTION_FILTER_UNKNOWN_TYPE,'The type "'.$strType.'" is not handled by the filter system.');
					break;	
			}
			switch (strtoupper(strval($strAction))) {
				case 'CHECK':
				case 'VALIDATE':
					$filterAction = 'VALIDATE';
					break;
				case 'CLEAN':
				case 'SANITIZE':
					$filterAction = 'SANITIZE';
					break;
				default:
					throw new openException(EXCEPTION_FILTER_UNKNOWN_ACTION,'The action "'.$strAction.'" is not handled by the filter system.');
					break;	
			}
			($filterAction == 'VALIDATE' && $filterType == 'STRING') ? $myFilter = 'FILTER_DEFAULT' : $myFilter = 'FILTER_'.$filterAction.'_'.$filterType;
			$retVal = filter_var($mixedOrig,constant($myFilter));
		} catch (Exception $e) {
			$e->errHandling();
		}
		return ($retVal);
	}
}
?>