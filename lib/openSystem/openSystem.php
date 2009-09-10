<?php
// ########################################################################
// # File: $Id: openSystem.php 217 2009-08-14 13:56:19Z jens $
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
* openSystem
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openSystem.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openSystem
* @uses openWebX
*/
class openSystem extends openWebX implements openObject {

    public function __construct() {

    }

    public function __destruct() {

    }


  public static function sysObjectCache() {
    $dirLib = Settings::get('path_lib');
    $myFS   = new openFilesystem();
    $arrObj = $myFS->fileRecurseDir($dirLib,2,'FILES');
    foreach ($arrObj as $val) {
      $val=str_replace($dirLib,'',$val);
      $arrTmp=explode('/',$val);
      if (isset($arrTmp[1]) && $arrTmp[0].'.php'==$arrTmp[1]) {
        $ret = openWebX::init($arrTmp[0]);
        unset ($ret);
      }
    }
    self::sysSetValue('object_cache',1);
    unset ($myFS);
  }

	public static function sysSetValue($strKey,$mixedValue) {
		$strStoreID     = md5($strKey);
		$strStoreValue  = serialize($mixedValue);
        $intStoreTTL    = intval(Settings::get('sys_cachelifetime'));
		switch (Settings::get('sys_storage')) {
			case 'database':
				$myDB = openWebX::init('openDB');
				$myDB->dbSetStatement(SQL_openSystem_storeValue,array(':id'=>$strStoreID,':value'=>$strStoreValue));
				$myDB->dbQuery();
				break;
			case 'session':
				$_SESSION['open_Storage'][$strStoreID] = $strStoreValue;
				break;
            case 'memcache':
                $myMC = new Memcache();
                $myMC->connect(Settings::get('memcache_server'),Settings::get('memcache_port'));
                $myMC->set($strStoreID,$strStoreValue,MEMCACHE_COMPRESSED,$intStoreTTL);
                unset($myMC);
                break;
            case 'eaccelerator':
                eaccelerator_put($strStoreID,$strStoreValue,$intStoreTTL);
                break;
            case 'apc':
            	apc_store($strStoreID,$strStoreValue,$intStoreTTL);
            	break;
		}
	}

	public static function sysGetValue($strKey) {
		$strStoreID     = md5($strKey);
    	$strStoreValue  = null;
		switch (Settings::get('sys_storage')) {
			case 'database':
				$myDB = openWebX::init('openDB');
				$myDB->setSQLQuery('SELECT value FROM open_Storage WHERE id="'.$strStoreID.'"');
				$myDB->dbFetchArray();
				$strStoreValue = $myDB->dbResultArray[0]['value'];
				unset($myDB);
				break;
			case 'session':
				$strStoreValue = (isset($_SESSION['open_Storage'][$strStoreID]) ? $_SESSION['open_Storage'][$strStoreID] : '');
				break;
            case 'memcache':
                $myMC = new Memcache();
                $myMC->connect(Settings::get('memcache_server'),Settings::get('memcache_port'));
                $strStoreValue = $myMC->get($strStoreID);
                unset($myMC);
                break;
             case 'eaccelerator':
                $strStoreValue = eaccelerator_get($strStoreID);
                break;
             case 'apc':
             	$strStoreValue = apc_fetch($strStoreID);
             	break;
        }
        return (($strStoreValue) ? unserialize($strStoreValue) : null);
	}

	public static function sysExecute($strCommand,$iMode=0) {
		$retVal = '';
		switch ($iMode) {
			case 0:
				$retVal = exec($strCommand);
				break;
			case 1:
				$retVal = passthru($strCommand);
				break;
			case 2:
				$retVal = shell_exec($strCommand);
				break;
			case 3:
				system($strCommand,$retVal);
				break;
		}
		return ($retVal);
	}
}
?>