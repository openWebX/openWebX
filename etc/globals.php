<?php
// ########################################################################
// # File: 			$Id: globals.php 218 2009-08-14 14:16:41Z jens $
// ########################################################################
// # Autor:    		$Author: jens $
// ########################################################################
// # Revision: 		$Revision: 218 $
// ########################################################################
// # Last Commit:	$Date: 2009-08-14 16:16:41 +0200 (Fri, 14 Aug 2009) $
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
// ########################################################################


define('DEBUG',true);


//##############################################################################################################
define ('SYSTEM_SAPI'                       ,php_sapi_name());
//##############################################################################################################
define ('CRLF'                              ,"\n");
define ('TAB'                               ,"\t");
//##############################################################################################################
/**
* Define the Errorhandling-globals
*/
define ('EXCEPTION_OK'                      ,0);

define ('EXCEPTION_DB_CONNECTERROR'         ,1000);
define ('EXCEPTION_DB_QUERYERROR'           ,1001);
define ('EXCEPTION_DB_QUERYEMPTY'           ,1002);
define ('EXCEPTION_DB_QUERYTIMEOUT'         ,1003);
define ('EXCEPTION_DB_SELECTDBERROR'        ,1004);
define ('EXCEPTION_DB_GENERICERROR'         ,1005);

define ('EXCEPTION_PHP_SCRIPTTIMEOUT'       ,2000);

define ('EXCEPTION_QRCODE_NO_DATA'			,3000);

define ('EXCEPTION_OBJECT_CALLERROR'        ,4000);
define ('EXCEPTION_OBJECT_GETERROR'         ,4001);
define ('EXCEPTION_OBJECT_SETERROR'         ,4002);
define ('EXCEPTION_OBJECT_LOADERROR'        ,4003);

define ('EXCEPTION_FILE_OPENERROR'          ,5000);

define ('EXCEPTION_FTP_CONNECTERROR'        ,6000);
define ('EXCEPTION_FTP_LOGINERROR'          ,6001);
define ('EXCEPTION_FTP_GETERROR'            ,6002);
define ('EXCEPTION_FTP_PUTERROR'            ,6003);

define ('EXCEPTION_MAIL_CONNECTERROR'       ,7000);
define ('EXCEPTION_MAIL_OPENERROR'          ,7001);

define ('EXCEPTION_FILTER_UNKNOWN_TYPE'		,8000);
define ('EXCEPTION_FILTER_UNKNOWN_ACTION'	,8001);

define ('EXCEPTION_HTML_TABLE_CELLCOUNT'	,9000);

//##############################################################################################################
/**
* Where to log Exceptions?
*/
define('ERROR_2_HTML'						,0);
define('ERROR_2_XML'						,1);
define('ERROR_2_POPUP'						,2);
define('ERROR_2_LOG'				    	,3);
define('ERROR_2_SCREEN'						,4);
define('ERROR_2_NULL'						,666);
//##############################################################################################################
/**
 * Define the log-targets
 */
define('LOG_2_FILE'							,0);
define('LOG_2_SCREEN'						,1);
define('LOG_2_DB'							,2);
//##############################################################################################################
?>