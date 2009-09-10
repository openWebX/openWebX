<?php
// ########################################################################
// # File: $Id: openFilesystem.php 231 2009-08-24 09:21:45Z jens $
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
// # Revision: $Revision: 231 $
// ########################################################################
/**
* openDB
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openFilesystem.php 231 2009-08-24 09:21:45Z jens $
* @package openWebX
* @subpackage openDB
* @uses openWebX
*/
class openFilesystem extends openWebX implements openObject {

    public $data = array(); 

	public function __construct() {

	}
	public function __destruct() {
		unset($this->fileHandle,$this->fileName,$this->fileMode);
	}

	private function fileOpen($strFilename,$strMode='r') {
		try {
			$this->fileName		= $strFilename;
			$this->fileMode 	= $strMode;
			$this->fileHandle 	= fopen($this->fileName,$this->fileMode);
			$this->fileSize		= filesize($strFilename);
			if (!$this->fileHandle) throw new openException(EXCEPTION_FILE_OPENERROR,'Filename: '.$strFilename);
		} catch (Exception $e) {
			$e->errHandling();
		}
	}

	private function fileClose() {
		if (isset($this->fileHandle)) fclose($this->fileHandle);
	}

	private function fileWrite($strText) {
		try {
            if (isset($this->fileHandle)) fwrite($this->fileHandle,$strText);
        } catch (Exception $e) {
            $e->errHandling();
        }
    }

	
	public function fileGetFilesInDir($path,$omitExtension = false) {
		$retVal = $this->fileRecurseDir ($path,0,"FILES");
		foreach ($retVal as $key => $val) {
			$retVal[$key] = (($omitExtension) ? str_replace('.'.$this->fileGetFileExtension($val),'',basename($val)) : basename($val));
		}
		return $retVal;
	}

	public function fileRecurseDir ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 ) {
		if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) {
			$path .= '/' ;
		}
		$dirlist = array () ;
		if ( $mode != "FILES" ) {
			$dirlist[] = $path ;
		}
		if ( $handle = opendir ( $path ) ) {
			while ( false !== ( $file = readdir ( $handle ) ) ) {
				if ( $file != '.' && $file != '..' ) {
					$file = $path . $file ;
					if ( ! is_dir ( $file ) ) {
						if ( $mode != "DIRS" ) {
							$dirlist[] = $file ;
						}
					} elseif ( $d >=0 && ($d < $maxdepth || $maxdepth < 0) ) {
						$result = $this->fileRecurseDir ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;
						$dirlist = array_merge ( $dirlist , $result ) ;
					}
				}
			}
			closedir ( $handle ) ;
    	}
		if ( $d == 0 ) {
			sort($dirlist);
		}
		return ($dirlist);
	}

	public function fileAppendText($strFilename,$strText) {
		$this->fileOpen($strFilename,'a+');
		$this->fileWrite($strText);
		$this->fileClose();
	}

	public function fileRead($strFilename) {
		$this->fileOpen($strFilename,'r+');
		$this->fileContent = fread($this->fileHanlde,$this->fileSize);
		$this->fileClose();
	}

	public function fileReadArray($strFilename) {
		$this->fileArray = file($strFilename);
	}

	public function fileCreate($strFilename,$mixedContent) {
		$this->fileOpen($strFilename,'w+');
		$this->fileWrite($mixedContent);
		$this->fileClose();
	}


	public function fileExistsInFolder($strFolder,$strFilename) {
		if (substr($strFolder,-1)=='/') {
			$myFile = $strFolder.$strFilename;
		} else {
			$myFile = $strFolder.'/'.$strFilename;
		}
		return ((file_exists($myFile)) ? true : false);

	}

	public function fileGetSubdirs ($strFolder,$strFileMustExist='',$omitDotFiles=true) {
		try {
			$this->fileDirectoryArray=array();
			$this->fileDirectory = opendir($strFolder);
			if (!$this->fileDirectory) throw new openException(EXCEPTION_FILE_OPENERROR,'Directory does not exist or cannot be opened: '.$strFolder);
			while (false !== ($file = readdir($this->fileDirectory))) {
        		if (is_dir($file) && substr($file,0,1)!='.') {
					if ($strFileMustExist=='') {
						$this->fileDirectoryArray[] = $file;
					} else {
						if ($this->fileExistsInFolder($strFolder.$file,$strFileMustExist)) {
							$this->fileDirectoryArray[] = $file;
						}
					}
				}
    		}
			closedir($this->fileDirectory);
		} catch (Exception $e) {
			$e->errHandling();
		}
	}

	public function fileExistsInPath($strFilename) {
		$strPath = $_ENV['PATH'];
		$arrPath = explode(':',$strPath);
		// gentoo-issue? double entries in path...
		// remove them...
		$arrPath = array_unique($arrPath);
		$retVal = false;
		foreach ($arrPath as $key=>$val) {
			if (file_exists($val.'/'.$strFilename)) $retVal = true;
		}
	}





	public function fileBuildDirectoryTree ($strPath) {
		$this->fileDirectoryTree = $this->fileRecurseDir($strPath);
	}

	public function fileFilesArray($strPath) {
		$this->fileFileArray = $this->fileRecurseDir($strPath,-1,'FILES');
	}

	public function fileShowTree($strPath) {
		$this->fileBuildDirectoryTree($strPath);
		for ($i=0;$i<count($this->fileDirectoryTree);$i++) {
			$arrItem = explode('/',$this->fileDirectoryTree[$i]);
			(is_dir($this->fileDirectoryTree[$i])) ? $iCount = 2 : $iCount = 1;
			$strItem = $arrItem[count($arrItem)-$iCount];
			for ($x=2;$x<substr_count($this->fileDirectoryTree[$i],'/');$x++) {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			$strExtension = '';
			if (!is_dir($this->fileDirectoryTree[$i])) {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;';
				$strExtension = substr($strItem,((strlen($strItem) - (strrpos($strItem,'.')+1)) * -1));
			}
			if (file_exists(Settings::get('path_mimetypes').$strExtension.'.png')) {
				echo '<img src="/share/images/mimetypes/'.$strExtension.'.png" border="0" align="absmiddle" />';
			} elseif (is_file($this->fileDirectoryTree[$i])) {
				echo '<img src="/share/images/mimetypes/'.$this->fileGetIcon($strExtension).'.png" border="0" align="absmiddle" />';
			} else {
				echo '<img src="/share/images/mimetypes/folder.png" border="0" align="absmiddle" />';
			}
			echo ' '.$strItem.'<br/>';
		}
	}


	public function fileGetTmpName($strContent,$strExtension='.html') {
		return (Settings::get('path_tmp').md5($strContent).$strExtension);
	}

	public function fileGetWebPath($strFilename) {
		return (str_replace(Settings::get('path_web'),'',$strFilename));
	}
	
	public function fileGetFileExtension($strFile) {
		$retVal = false;
        preg_match('/[^?]*/', $strFile, $matches);
        $string = $matches[0];
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
        # check if there is any extension
        if(count($pattern) > 1) {
            $filenamepart = $pattern[count($pattern)-1][0];
            preg_match('/[^?]*/', $filenamepart, $matches);
            $retVal = $matches[0];
        }
        return $retVal;
	}
	

	public function fileGZCompress($strSource,$iLevel=false){
    	$retVal = false;
    	$dest=$strSource.'.gz';
    	$mode='wb'.$iLevel;
    	$error=false;
    	if($fp_out=gzopen($dest,$mode)){
        	if($fp_in=fopen($strSource,'rb')){
            	while(!feof($fp_in))
                	gzwrite($fp_out,fread($fp_in,1024*512));
            	fclose($fp_in);
            	}
          	else $error=true;
        	gzclose($fp_out);
        } else {
        	$error=true;
    	}
    	if (!$error) $retVal = $dest;
    	return $retVal;
    }

}

?>
