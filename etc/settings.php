<?php
// ########################################################################
// # File: $Id: settings.php 234 2009-09-10 06:02:52Z jens $
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
* strInstallPath
*/
$strInstallPath = '/var/www/openWebX/'; //str_replace('etc','',dirname(__FILE__)).'';

/**
* _SETTINGS
*
* Array containing all settings
*/
$_SETTINGS = array	(
        'site_name'								=> 'AREA openWebX',
        //#########################################################
        // Database-Settings -> MySQL(i)
        /* 
        'database_type'                      	=> 'mysqli',
        'database_port'							=> '3306',
        'database_server'		                => 'localhost',
		'database_username'		                => 'openWebX',
		'database_password'		                => 'openWebX',
		'database_name'			                => 'openWebX',
        */
        //#########################################################
        // Database-Settings -> CouchDB
        'database_type'                			=> 'couchDB',
        'database_server'		           		=> 'localhost',
		'database_port'							=> '5984',
		'database_username'		               	=> 'openwebx',
		'database_password'		               	=> 'openwebx',
		'database_name'			               	=> 'openwebx',
		//#########################################################
		// Exception-Settings
		'exception_handler'   	              	=> ERROR_2_LOG,
		//#########################################################
        // Log-Settings
		'log_type'        		                => LOG_2_SCREEN,
		'log_prefix'			                => '__',
		'log_counter'			                => 0,
		//#########################################################
        // System-Settings
		'sys_cache'				              	=> false,
        'sys_memcache'                        	=> false,
        'sys_filecache'                       	=> false,
        'sys_jscache'						  	=> false,
        'sys_csscache'							=> false,
        'sys_gzip'								=> false,
        'sys_cachelifetime'                   	=> 0,
		'sys_storage'         	              	=> 'session',
		'sys_language'        	              	=> 'de',
        'sys_locale'                            => 'de_DE',
        'sys_timezone'							=> 'Europe/Berlin',
		'sys_encoding'						  	=> 'utf-8',
		'sys_version'			                => '0.git.'.date('Y-m-d'),
		'sys_name'				                => 'big schaeuble is watching you!',
        //#########################################################
		// Absolute paths
		'path_install'							=> $strInstallPath,
		'path_share'							=> $strInstallPath.'share/',
        'path_status'           				=> $strInstallPath.'upload/status/',
		'path_web'            					=> $strInstallPath.'web/',
		'path_css'            					=> $strInstallPath.'web/share/css/',
        'path_css_local'                		=> $strInstallPath.'web/share/css/local/',
        'path_images'							=> $strInstallPath.'web/share/images/',
		'path_js'            					=> $strInstallPath.'web/share/js/',
		'path_mootools'        					=> $strInstallPath.'web/share/js/mootools/',
		'path_mootools_ext'    					=> $strInstallPath.'web/share/js/mootools_ext/',
		'path_webcache'       					=> $strInstallPath.'web/cache/',
		'path_tmp'       						=> $strInstallPath.'web/tmp/',
		//'path_cache'          				=> $strInstallPath.'cache/',
		'path_lib'								=> $strInstallPath.'lib/',
		'path_i18n'								=> $strInstallPath.'/share/i18n/',
		'path_log'								=> $strInstallPath.'log/',
        //#########################################################
		// web paths
		'web_cache'								=> '/cache/',
		'web_share'								=> '/share/',
		'web_css'								=> '/share/css/',
		'web_js'								=> '/share/js/',
		'web_mootools'							=> '/share/js/mootools/',
		'web_mootools_ext'						=> '/share/js/mootools_ext/',
		'web_images'							=> '/share/images/',
		'web_icons'								=> '/share/images/icons/',
		'web_smilies'							=> '/share/images/smilies/',
        //#########################################################
		// Additional Settings
		// amarok
		'amarok_type'							=> 'mysqli',
		'amarok_server'							=> '127.0.0.1',
		'amarok_username'						=> 'amarok',
		'amarok_password'						=> 'amarok',
		'amarok_name'							=> 'amarok',
		'amazon_id'								=> '0525E2PQ81DD7ZTWTK82',
		// google_maps
		'google_maps_key'						=> 'ABQIAAAAU8Aa7C9THFGBNWBB9VjH_BQu7O69AldwsUFPLOm6uGSPR2CVlBQLpEiiyeSdDnak6fG9-ycA0Ls3NQ',
		// memcache
		'memcache_server'						=> '127.0.0.1',
		'memcache_port'							=> '11211',
		// SEO
		'autoseo'			    				=> true,
		'dublincore'							=> true,
		'long'				    				=> '50.981384',
		'lat'				    				=> '7.123969',
        // IM
        'twitter_user'          				=> 'openWeb',
        'twitter_pass'          				=> 'isjMu6Ua',
        'laconica_url'							=> 'http://mublogging.de/api/',
        'laconica_user'							=> 'openWeb',
        'laconica_pass'							=> 'isjMu6Ua',
        'aim_user'              				=> 'jr300774',
        'aim_pass'              				=> 'isjMu6Ua',
        'flickr_key'                            => '7a77a72e829d3b8dbc07c8f342dabd7e',
        'flickr_secret'                         => 'c596ca6c8b55106e',
        // SCM
        'scm_module'							=> 'git'
);

/**
* Put the settings in an Session-Variable
*/
$_SESSION['openWebX']['settings'] 		= $_SETTINGS;
/**
* Get all installed extensions
*/
$_SESSION['openWebX']['extensions'] 	= get_loaded_extensions();
?>
