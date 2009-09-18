<?php
// ########################################################################
// # File: $Id: openHTML.php 217 2009-08-14 13:56:19Z jens $
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
require_once(str_replace('openHTML.php','openHTML_Tag.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Head.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Body.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Body_Table.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Body_Fieldset.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Body_Div.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Body_Form.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Body_List.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Body_Image.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Body_Link.php',__FILE__));
require_once(str_replace('openHTML.php','openHTML_Foot.php',__FILE__));


/**
* openHTML
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openHTML.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openHTML
* @uses openWebX
*/
class openHTML extends openWebX implements openObject {

  	protected $data = array();
  	private $isGetted = false;
  	private $isShown = false;

  	public function __construct() {
    	$this->head = new openHTML_Head();
    	$this->body = new openHTML_Body();
    	$this->foot = new openHTML_Foot();
  	}
  	public function __destruct() {
    	if (!$this->isGetted && !$this->isShown) $this->show();
    	unset($this->head,$this->body,$this->foot);
  	}

	/**
	 * Sleep & Wakeup
	 */
	public function __sleep() {
		  $this->isGetted = true;
		  $this->isShown = true;
		  return array();
	}
	public function __wakeup() {

  	}

  	public function getHeader() {
    	return($this->head->build());
  	}
  	public function getFooter() {
    	return($this->foot->build());
  	}
  	public function getBody() {
    	return($this->body->build());
  	}

  	public function build() {
	    $myBody   = $this->getBody();
	    $myHeader = $this->getHeader();
	    $myFooter = $this->getFooter();
        $retVal=$myHeader.$myBody.$myFooter;
	    $this->isGetted = true;
	    return($retVal);
	}

  	public function show() {
    	$this->isShown = true;
    	echo $this->build();
  	}
}
?>
