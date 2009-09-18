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
// # Autor: $Author$
// ########################################################################
// # Revision: $Revision$
// ########################################################################
/**
* openDocument
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id$
* @package openWebX
* @subpackage openDocument
* @uses openWebX
*/
class openDocument extends openWebX {
	
	/**
	 * overloaded property to simply build up an Doc-Object
	 * 
	 * @access public
	 */
	public 	$data 		= array();
	/**
	 * the document object
	 * 
	 * @access private
	 */
	private $docObject	= null;
	/**
	 * the database object
	 * 
	 * @access private
	 */
	private $dbObject	= null;
	
	
	
	
	/**
	 * loads the given document (id and type) from database or initializes a new one.
	 * 
	 * @access public
	 * @param string $strID - the id of the document
	 * @param string $strType - type of document
	 * @return void
	 */
	public function __construct($strID,$strType) {
		$this->dbObject 	= new openDB();
		$this->_id			= md5(openFilter::filterAction('clean','string',$strID));
		if (!$this->load()) {
			$this->docObject 	= new StdClass();
			$this->type			= openFilter::filterAction('clean','string',$strType);
		}	
	}
	
	/**
	 * unsets dbObject and docObject
	 * 
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		unset ($this->docObject,$this->dbObject);
	}
	
	/**
	 * maps the internal object into needed structure and returns it
	 * 
	 * @access public
	 * @return object $this->docObject
	 */
	public function get() {
		$this->map();
		return $this->docObject();
	}
	
	/**
	 * saves the current docObject to database
	 * 
	 * @access public
	 * @return boolean success?
	 */
	public function save() {
		$this->map();
		return $this->dbObject->dbStore($this->docObject);
	}
	
	/**
	 * loads document by given properties
	 * 
	 * @access public
	 * @return boolean success? 
	 */
	public function load() {
		return ($this->docObject = $this->dbObject->dbGetByID($this->data['_id'])) ? true : false;
	}
	
	/**
	 * maps overloaded properties to docObject
	 * 
	 * @access private
	 */
	private function map() {
		foreach ($this->data as $key=>$val) {
			$this->docObject->$key = $val;
		}
	}
}
?>