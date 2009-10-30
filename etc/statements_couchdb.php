<?php
// ########################################################################
// # File: $Id: sqls.php 218 2009-08-14 14:16:41Z jens $
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
// # Revision: $Revision: 218 $
// ########################################################################


/**
 * "views" for couchDB
 * 
 */
$_SESSION['openWebX']['views'] = array(
	'_design/docs'		=> '
			{
				"all": {
					"map": "function(doc) { emit(doc._id, doc) }"
				},
				"by_type": {
					"map": "function(doc) { emit(doc.type, doc) }"
				}
			}
	',
	'_design/slot'		=> '
  			{
    			"all": {
      				"map": "function(doc) { if (doc.type == \'slot\')  emit(doc._id, doc) }"
    			},
    			"by_slot": {
      				"map": "function(doc) { if (doc.type == \'slot\')  emit(doc.slot, doc) }"
    			},
				"by_object": {
      				"map": "function(doc) { if (doc.type == \'slot\')  emit(doc.object, doc) }"
    			}
  			}
	',
	'_design/microblog'	=> '
			{
				"all": {
      				"map": "function(doc) { if (doc.type == \'microblog\')  emit(doc._id, doc) }"
    			},
    			"by_service": {
      				"map": "function(doc) { if (doc.type == \'microblog\')  emit(doc.service, doc) }"
    			},
				"by_date": {
      				"map": "function(doc) { if (doc.type == \'microblog\')  emit(doc.created, doc) }"
    			}
			}
	',
	'_design/lists'	=> '
			{
				"all": {
      				"map": "function(doc) { if (doc.type == \'list\')  emit(doc._id, doc) }"
    			},
    			"by_title": {
      				"map": "function(doc) { if (doc.type == \'list\')  emit(doc.title, doc) }"
    			}
			}
	',
);

?>
