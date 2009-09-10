<?php
// ########################################################################
// # File: $Id: sqls.php 218 2009-08-14 14:16:41Z jens $
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
// # Revision: $Revision: 218 $
// ########################################################################


/**
 * "views" for couchDB
 * 
 */
$_SESSION['openWebX']['views'] = array(
	'_design/slots'		=> '
  			{
    			"all": {
      				"map": "function(doc) { if (doc.type == \'slot\')  emit(doc._id, doc) }"
    			},
    			"by_slotname": {
      				"map": "function(doc) { if (doc.slot && doc.type == \'slot\')  emit(doc._id, doc) }"
    			}
  			}
	'


);

?>
