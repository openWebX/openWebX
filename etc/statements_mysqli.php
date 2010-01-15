<?php
// ########################################################################
// # File: $Id: statements_mysqli.php 234 2009-09-10 06:02:52Z jens $
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
// # Revision: $Revision: 234 $
// ########################################################################

/**
 * SQLs for openDB
 */
define ('SQL_openDB_dbStructure','
    SELECT
      `COLUMN_NAME`     AS cName,
      `COLUMN_DEFAULT`  AS cDefault,
      `IS_NULLABLE`     AS cNull,
      `COLUMN_TYPE`     AS cType

    FROM
      `information_schema`.`COLUMNS`
    WHERE
      `TABLE_NAME` = :table
    '
);
/**
 * SQLs for openWebX
 */
 define ('SQL_openWebX_getByID','
	SELECT 
		`object_type_id`
	FROM
		`open_Object`
	WHERE
		`object_id` = :id
');
 
 /**
  * Register slot
  */
define('SQL_openWebX_registerSlot','
	INSERT INTO
		`open_Slots`
		(
			`hash`,
			`slot`,
			`object`,
			`prio`
		)
		VALUES
		(
			:hash,
			:slot,
			:object,
			:prio
		)
	ON DUPLICATE KEY UPDATE
  		`prio` = :prio_update ;
');
/**
 * Get slots for signal(s)
 */
define ('SQL_openWebX_getSlots','
  SELECT
    `object`
  FROM
    `open_Slots`
  WHERE
    `slot` = :slot
  ORDER BY
    `prio`
');

/**
 * openSystem
 */
define ('SQL_openSystem_storeValue','
  INSERT INTO
    `open_Storage`
    (
      id,
      value
    )
    VALUES
    (
      :id,
      :value
    )
');
/**
 * open_Microblog
 */
define ('SQL_openMicroblog_storeEntry','
	INSERT INTO
    	`open_Object_Microblog`
    SET
        `created`   = :created,
		`hash`		= :hash,
        `type`		= :type,
		`author`    = :author,
        `content`   = :content,
		`read`		= 0
	ON DUPLICATE KEY UPDATE `read` = 1;
');
define ('SQL_openMicroblog_getEntries','
	SELECT
		`created`,
		`author`,
		`content`,
		`type`
	FROM
    	`open_Object_Microblog`
	ORDER BY
		`created`	DESC
');
define ('SQL_openMicroblog_getEntriesByType','
	SELECT
		`created`,
		`author`,
		`content`,
		`type`
	FROM
    	`open_Object_Microblog`
	WHERE
		`type`	= :type
	ORDER BY
		`created`	DESC
');
?>
