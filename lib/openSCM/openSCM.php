<?php
// ########################################################################
// # File: $Id: openSCM.php 217 2009-08-14 13:56:19Z jens $
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

/**
 * include the child-elements
 */
require_once(str_replace('openSCM.php','openSCM_'.Settings::get('scm_module').'.php',__FILE__));

class openSCM extends openWebX implements openObject {
	
	private $scmObject = NULL;
	
	public function __construct() {
		try {
			if (!($this->scmObject = new openSCM_Module())) throw new openException('Module '.Settings::get('scm_module').' could not be loaded!',-1);
		} catch (exception $e) {
			$e->errHandling();
		}
	}
	
	public function __destruct() {
		
		
	}
	
}

?>
