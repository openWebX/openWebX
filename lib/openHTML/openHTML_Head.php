<?php
// ########################################################################
// # File: $Id: openHTML_Head.php 235 2009-09-10 06:03:02Z jens $
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
// # Revision: $Revision: 235 $
// ########################################################################


class openHTML_Head extends openWebX implements openObject {

    protected $data     = array();

    private $css        = array();
    private $meta       = array();
    private $js         = array();


	public function __construct($strTitle='') {
		$this->title	= openFilter::filterAction('clean','string',$strTitle);
		$this->path		= Settings::get('path_web');
        $myDoctype      = new openHTML_Head_Doctype('xhtml-strict');
        $this->doctype  = $myDoctype->build();
        unset($myDoctype);
    }

    public function addCSS($strFile,$strMedia='screen') {
    	$myID = md5($strFile);
		$this->_addCSS($myID,$strFile,$strMedia,'file');
	}

    public function addCSSExternal($strFile,$strMedia='screen') {
		$myID = md5($strFile);
		$this->_addCSS($myID,$strFile,$strMedia,'external');
	}

	public function addCSSCode($strCode,$strMedia='screen') {
		$myID = md5($strCode);
		$this->_addCSS($myID,$strCode,$strMedia,'code');
	}

	public function addJS($strFile) {
        $myID = md5($strFile);
		$this->_addJS($myID,$strFile,'file');
	}
    public function addJSExternal($strFile) {
        $myID = md5($strFile);
		$this->_addJS($myID,$strFile,'external');
	}
	public function addJSCode($strCode) {
		$myID = md5($strCode);
		$this->_addJS($myID,$strCode,'code');
	}

    public function addMeta($strTitle,$strContent) {
    	$myID = md5($strTitle);
        $this->_addMeta($myID,$strTitle,$strContent);
    }

	public function setTitle($strTitle) {
        $myTitle = trim(strip_tags($strTitle));
        if (Settings::get('autoseo')) {
            $myRequest = openSystem::sysGetValue('request');
            foreach ($myRequest as $key=>$value) {
                $myTitle .= ' | '.str_replace('_',' ',$value);
            }
        }
        $myTitle = Settings::get('site_name').' | '.$myTitle;
		$myTag = new openHTML_Tag('title');
        $myTag->content = $strTitle;
        $this->title = $myTag->build();
        unset($myTag);
	}

    public function build() {
        $myHTML             = new openHTML_Tag('html',false,true);
        $myHTML->xmlns      = 'http://www.w3.org/1999/xhtml';
        $myHTML->xmllang    = Settings::get('sys_language');
        $myHTML->lang       = Settings::get('sys_language');
        $myHead             = new openHTML_Tag('head');
        $myHead->content    = $this->data['title'];
        $myHead->content   .= $this->_buildMeta();
        if (!Settings::get('sys_csscache')) $myHead->content  	.= $this->_buildCSS();
        if (!Settings::get('sys_jscache')) 	$myHead->content   	.= $this->_buildJS();
        $myHTML->content    = $myHead->build();
        $retVal             = $this->data['doctype'];
        $retVal            .= $myHTML->build();
        return $retVal;
    }

    private function _addMeta($strID,$strTitle,$strValue) {
    	if (!isset($this->meta[$strID])) {
    		$myMeta = new openHTML_Head_Meta($strTitle,$strValue);
            $this->meta[$strID] = $myMeta->build();
            unset($myMeta);
    	}
    }

    private function _buildMeta() {
    	// First of all set the required metas...
        $this->addMeta('date'               ,date('Y-m-d\Th:i:sO'));
        $this->addMeta('expires'            ,'3600');
        $this->addMeta('content-language'   ,Settings::get('sys_language'));
        $this->addMeta('content-type'		,'text/html; charset='.Settings::get('sys_encoding'));
        $this->addMeta('cache-control'      ,'no-cache');
        $this->addMeta('pragma'             ,'no-cache');
        $this->addMeta('ICBM'               ,Settings::get('long').', '.Settings::get('lat'));
        $this->addMeta('geo.position'       ,Settings::get('long').', '.Settings::get('lat'));
        $this->addMeta('googlebot'          ,'noarchive');
        return implode('',$this->meta);
    }

    private function _addCSS($strID,$strContent,$strMedia,$strType) {
		if (!isset($this->css[$strID])) {
			$myCSS = new openHTML_Head_CSS($strID,$strContent,$strMedia,$strType);
			$this->css[$strID] = $myCSS->build();
			unset($myCSS);
		}
	}

    private function _addJS($strID,$strContent,$strType) {
		if (!isset($this->js[$strID])) {
			$myJS = new openHTML_Head_JS($strID,$strContent,$strType);
			$this->js[$strID] = $myJS->build();
			unset($myJS);
		}
	}

	private function _buildJS() {
		$requiredJS = array(
			'kernel'
		);
		$myFS = new openFilesystem();
		$myExts = $myFS->fileGetFilesInDir(Settings::get('path_js_modules'),true);
		/*foreach ($myExts as $key=>$val) {
		   	$requiredJS[] = 'modules/'.$val;
		}*/
		unset($myFS);
		$requiredJS[] = 'openWebX';
		foreach ($requiredJS as $key=>$val) {
			$this->_addJS(md5($val),$val,'file');
		}
		return implode('',$this->js);
	}

	private function _buildCSS() {
		$requiredCSS = array(
			'openWebX',
		);
		$myFS = new openFilesystem();
		$myExts = $myFS->fileGetFilesInDir(Settings::get('path_css_local'),true);
		foreach ($myExts as $key=>$val) {
		   	$requiredCSS[] = 'local/'.$val;
		}
		unset($myFS);
		foreach ($requiredCSS as $key=>$val) {
			$this->_addCSS(md5($val),$val,'screen','file');
		}
		return implode('',$this->css);
	}
}



/**
 * openHTML_Head_Doctype
 */
class openHTML_Head_Doctype extends openWebX implements openObject {

    protected $data = array();

    public function __construct($strDocType) {
    	switch ($strDocType) {
            case 'html-2.0':
                $this->doctype='<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">';
                break;
            case 'html-3.2':
                $this->doctype='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">';
                break;
            case 'html-strict':
                $this->doctype='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
                break;
            case 'html-frameset':
                $this->doctype='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
                break;
            case 'xhtml-transitional':
                $this->doctype='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
                break;
            case 'xhtml-strict':
                $this->doctype='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
                break;
            case 'xhtml-frameset':
                $this->doctype='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
                break;
            case 'xhtml-1.1':
                $this->doctype='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
                break;
            case 'html-transitional':
            default:
                $this->doctype='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
                break;
        }
    }

    public function build() {
    	return $this->data['doctype'];
    }
}

class openHTML_Head_Meta extends openWebX implements openObject {

    protected $data = array();

    public function __construct($strName,$strValue) {
    	$strName   	= strtolower(openFilter::filterAction('clean','string',$strName));
        $strValue  	= openFilter::filterAction('clean','string',$strValue);
        $myTag		= new openHTML_Tag('meta',true);
        switch ($strName) {
            case 'content-type':
            case 'content-language':
            case 'expires':
            case 'generator':
            case 'set-cookie':
            case 'cache-control':
            case 'pragma':
            	$myTag->http_equiv 	= $strName;
            	$myTag->content		= $strValue;
                $this->meta 		= $myTag->build();
                break;
            case 'author':
            case 'date':
            case 'description':
            case 'keywords':
            case 'robots':
            default:
            	$myTag->name		= $strName;
            	$myTag->content		= $strValue;
                $this->meta 		= $myTag->build();
                break;
        }
        unset($myTag);
    }

    public function build() {
    	return $this->data['meta'];
    }

}

class openHTML_Head_CSS extends openWebX implements openObject{

	protected $data = array();

    public function __construct($strID,$strContent,$strMedia='screen',$strType='file') {
        $myTag 			= new openHTML_Tag('link',true);
        $myTag->rel		= 'stylesheet';
        $myTag->type 	= 'text/css';
        $myTag->media	= $strMedia;
    	$this->css		= '';
    	switch (strtolower($strType)) {
            case 'file':
        		if (file_exists(Settings::get('path_css').$strContent.'.css')) {
                    //if (Settings::get('sys_csscache')) {
                    //
                    //} else {
                    	$myTag->href	= Settings::get('web_css').$strContent.'.css';
                        $this->css 	.= $myTag->build();
                    //}
        		}
                break;
            case 'external':
                if (file_exists($strContent)) {
                    //if (Settings::get('sys_csscache')) {
                    //
                    //} else {
                    	$myTag->href	= $strContent;
                        $this->css 	.= $myTag->build();
                    //}
                }
                break;
            case 'code':
            default:
            	$myTag->content = $strContent;
                $this->css 		.= $myTag->build();
                break;
    	}
		unset($myTag);
    }

    public function build() {
    	return $this->data['css'];
    }

}

class openHTML_Head_JS extends openWebX implements openObject {

    protected $data = array();

    public function __construct($strID,$strContent,$strType='file') {
        $myTag 			= new openHTML_Tag('script');
        $myTag->type 	= 'text/javascript';
    	$this->js 		= '';
    	switch (strtolower($strType)) {
            case 'file':
        		if (file_exists(Settings::get('path_js').$strContent.'.js')) {
                    if (Settings::get('sys_jscache')) {
                        $this->js 	.= openString::strMinifyJS(file_get_contents(Settings::get('path_js').'share/js/'.$strContent.'.js'));
                    } else {
                    	$myTag->src	= Settings::get('web_js').$strContent.'.js';
                        $this->js 	.= $myTag->build();
                    }
        		}
                break;
            case 'external':
                if (file_exists($strContent)) {
                    if (Settings::get('sys_jscache')) {
                        $this->js 	.= openString::strMinifyJS(file_get_contents($strContent));
                    } else {
                    	$myTag->src	= $strContent;
                        $this->js 	.= $myTag->build();
                    }
                }
                break;
            case 'code':
            default:
            	$myTag->content 	= $strContent;
                $this->js 			.= $myTag->build();
                break;
    	}
		unset($myTag);
    }

    public function build() {
    	return $this->data['js'];
    }
}

?>
