<?php
// ########################################################################
// # File: $Id: openUI.php 217 2009-08-14 13:56:19Z jens $
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
* openUI
*
* User Interface Class using the Mootools 1.2 or later

* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openUI.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openUI
* @uses openWebX
*/
class openUI extends openWebX {

    public function __construct() {
    }

    public function __destruct() {
    }

    /**
    * uiLinkSlider
    *
    * Generates a slider Div with the given width
    */
    public function uiLinkSlider($strTitle,$iWidth,$arrContent) {
        $retVal     = '';
        $strTitle   = openFilter::filterAction('clean','string',$strTitle);
        $iWidth     = intval($iWidth);
        if (is_array($arrContent)) {
            $retVal = '
            <div id="browser'.$strTitle.'" class="slider">
            <img id="prev'.$strTitle.'" src="/share/images/icons/openUI/left.png" style="float:left" class="sliderButton" />
            <div id="mask'.$strTitle.'" class="mask" style="width:'.$iWidth.'px;">
            <div id="box'.$strTitle.'" class="box">
            ';
            for ($i=0;$i<count($arrContent);$i++) {
                $retVal.='<div style="width:'.$iWidth.'px;">'.$arrContent[$i].'</div>';
            }
            $retVal .= '
            </div>
            </div><img id="next'.$strTitle.'" src="/share/images/icons/openUI/right.png" style="float:left" class="sliderButton" />
            <br clear="all"/>
            </div>
            ';
        }
        return($retVal);
    }

    public function uiFlow($strName,$arrContent) {
        $retVal = '';
        if (is_array($arrContent)) {
        	$myDiv = new openHTML_Tag('div');
        	$myDiv->id = 'uiFlow_'.openFilter::filterAction('clean','string',$strName);
            foreach ($arrContent as $image) {
            	$myContainer = new openHTML_Tag('div');
                $myImage = new openHTML_Tag('img');
                $myImage->src = $image;
                $myContainer->content = $myImage->build();
                unset($myImage);
                $myDiv->content.=$myContainer->build();
                unset($myContainer);
            }
            $retVal = $myDiv->build();
            unset($myDiv);
        }
        return($retVal);
    }

    public function uiBox ($strTitle,$strContent,$iHeight=-1) {
        $strTitle   = oopenFilter::filterAction('clean','string',$strTitle);
        $retVal     = '
        <div id="uiBox_'.$strTitle.'" class="uiBox">
            <div id="uiBoxTop_'.$strTitle.'" class="uiBox_top" '.(($iHeight!=-1) ? 'style="height:'.$iHeight.'px;"' : '').'>
                <strong>'.$strTitle.(($iHeight!=-1) ? '&nbsp;<span id="box_toggle_'.$strTitle.'" class="uiBox_toggle">[+]</span>' : '').'</strong>
                <br/>
                '.$strContent.'
            </div>
            <div class="uiBox_bottom"></div>
        </div>
        ';
        return($retVal);
    }

    public function uiPulsar($strTitle,$strImage,$iInitial=32,$iFinal=128) {
        $strTitle   = openFilter::filterString($strTitle);
        $retVal = '
        <div class="uiPulsar">
            <img id="uiPulsar_'.$strTitle.'" class="uiPulsar" src="'.$strImage.'" style="height:'.intval($iInitial).'px;width:'.intval($iInitial).'px;" />
        </div>
        ';
        return ($retVal);
    }

    public function uiAccordion($strTitle,$arrElements) {
      $strTitle   = openFilter::filterAction('clean','string',$strTitle);
        $retVal = '
        <div class="uiAccordion_'.$strTitle.'">
        ';
        $countPanel = 1;
        foreach($arrElements as $key => $val) {
          $retVal.='
          <h3 class="toggler atStart" id="section'.$countPanel.'">
            '.$key.'
          </h3>
          <div id="element atStart">
            '.$val.'
          </div>
          ';
          $countPanel++;
        }
        $retVal.='
        </div>
        ';
        return ($retVal);
    }

    public function uiKwick($strTitle,$arrElements,$iWidth,$iHeight) {
      $strTitle   = openFilter::filterAction('clean','string',$strTitle);
      $iWidth     = openFilter::filterAction('clean','int',$iWidth);
      $iHeight    = openFilter::filterAction('clean','int',$iHeight);
        $retVal = '
        <div id="uiKwick_'.$strTitle.'" class="uiKwick" style="width:'.$iWidth.'px;height:'.$iHeight.'px">
          <ul class="kwicks">
        ';
        for ($i=0;$i<count($arrElements);$i++) {
          $retVal.='
            <li class="kwick opt'.$i.'" id="menu'.$i.'" style="height:'.$iHeight.'px;width:'.(100).'px;"><span id="link'.$i.'"><img src="/share/images/'.$arrElements[$i].'.png" />"</span></li>
          ';
        }
        $retVal .='
          </ul>
        </div>
        ';
        return ($retVal);
    }

    public function uiChart($strTitle,$arrElements) {
      $strTitle   = openFilter::filterAction('clean','string',$strTitle);
      $retVal     = '
      <div id="uiChart_'.$strTitle.'">
	  </div>
      ';
      return $retVal;
    }

	public function uiPopUp($strTitle,$strImage,$strLink) {
		$strTitle = openFilter::filterAction('clean','string',$strTitle);
		$retVal = '';
		$retVal	.= '<div id="'.$strTitle.'" class="popup">';
		$retVal .= '<a href="'.$strLink.'"><img src="'.$strImage.'" border="0" /></a>';
		$retVal	.= '</div>';
		return ($retVal);
	}


    public function uiSIFX($strTitle,$arrElements,$strEffect='fade') {
      	$strTitle   = openFilter::filterAction('clean','string',$strTitle);
      	$effect = $strEffect;
      	$target = 'uiSIFX_'.$effect;
      	$retVal = '';
		    $retVal.='<div id=".uiSIFX_'.$strTitle.'" rel="'.$effect.'">';
      	for ($i=0;$i<count($arrElements);$i++) {
        	$retVal.='<img src="/share/images/pictures/'.$arrElements[$i].'" class="'.$target.'" />';
      	}
      	$retVal.= '<div id="clear" clear="both" />';
      	$retVal.='</div>';
      	return $retVal;
    }

    public function uiWheel($strTitle,$arrElements) {
    	$retVal = '';
    	$strTitle = openFilter::filterAction('clean','string',$strTitle);
    	$retVal.='<div id="uiWheel_'.$strTitle.'" class="loading magicwheel">';
    	for ($i=0;$i<count($arrElements);$i++) {
    		$retVal.= '<a href="/share/images/pictures/'.$arrElements[$i]['image'].'" title="'.$arrElements[$i]['title'].'"><img class="icon" src="/share/images/pictures/'.$arrElements[$i]['image'].'" /></a>';
    	}
    	$retVal.='</div>';
    	return $retVal;
    }
    
    public function uiMessagingButton($strTitle,$strText,$strLink,$strType) {
    	$retVal = '';
    	$strTitle 	= openFilter::filterAction('clean','string',$strTitle);
    	$strText	= openFilter::filterAction('clean','string',$strText);
    	$strLink 	= openFilter::filterAction('clean','string',$strLink);
    	$strType	= strtolower(openFilter::filterAction('clean','string',$strType));
    	$retVal.='<a id="uiMessagingButton_'.$strTitle.'" class="messaging-button" href="'.$strLink.'"><img src="/share/images/icons/openMessage/'.$strType.'.png" alt="Button" />'.$strText.'</a>';
    	return $retVal;
    }
    
}

?>