<?php
// ########################################################################
// # File: $Id: openCache.php 217 2009-08-14 13:56:19Z jens $
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
* openCache
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openCache.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openCache
* @uses openWebX
*/
class openCache extends openWebX {

  public static function cacheCheck() {
    $strCacheFile = Settings::get('path_webcache').'/'.openSystem::sysGetValue('pageID').'.html';
    if (is_file($strCacheFile)) {
      echo file_get_contents($strCacheFile);
      exit;
    }
  }

  public static function cacheStart() {
    ob_start();
  }

  public static function cacheStop() {
     $strCacheFile = Settings::get('path_webcache').'/'.openSystem::sysGetValue('pageID').'.html';
     file_put_contents($strCacheFile,ob_get_contents());
     ob_end_flush();
  }
}
?>