<?php
// ########################################################################
// # File: $Id: openHTML_Body_Form.php 235 2009-09-10 06:03:02Z jens $
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

/**
 * openHTML_Body_Form
 * 
 * The base form class
 * 
 * @author jens
 *
 */
class openHTML_Body_Form extends openWebX implements openObject  {

	/**
	 * $data
	 * 
	 * overloades properties
	 * 
	 * @var unknown_type
	 */
	public $data = array();
	
	public function __construct($strID) {
		$this->id 		= openFilter::filterAction('clean','string',$strID);
		$this->hash		= md5($this->data['id']);
		$this->name		= $this->data['id'];
		$this->enctype	= 'multipart/form-data';
		$this->method	= 'post';
		$this->action	= '';
		$this->content	= '';
        return $this;
	}
	
	public function __destruct() {
		
	}
	
	
	public function addElement($strID,$strType,$strLabel='',$mixedValue='',$strMask='',$lstValidators='') {
		$strID = openFilter::filterAction('clean','string',$strID);
		$this->$strID = new openHTML_Body_Form_Element($strType.'_'.$strID.'_'.$this->data['hash'],$strType,$strLabel,$mixedValue,$strMask,$lstValidators);
		return $this->$strID;
	}
	
	public function build() {
		//if (!$retVal = openSystem::sysGetValue('open_Form_'.$myHash)) {
			$retVal = '';
			$myForm 			= new openHTML_Tag('form');
	    	$myForm->id			= 'form_'.$this->data['hash'];
	    	unset($this->data['id'],$this->data['content']);
	        foreach ($this->data as $key => $val) {
	        	if (is_a($val,'openHTML_Body_Form_Element')) {
	        		$myDiv = new openHTML_Tag('div');
	        		$myDiv->id 		= 'div_'.$key;
	        		$myDiv->class	= 'openHTML_Body_Form_Element';
	        		$myDiv->content.=$val->build();
	                $myForm->content .= $myDiv->build();
	                unset($myDiv);
	            } else {
	            	if($key!='action') { 
	        			$myForm->$key 	= $val;
	            	} else {
	            		// add signal to action...
	            		if (substr($val,0,5)!='form/') {
	            			$myForm->$key   = '?request=form'.((substr($val,0,1)!='/') ? '/' : '').$val;
	            		}
	            	}
	        	}
	        }
	        $retVal 			= $myForm->build();
	        unset 				($myForm);
	        openSystem::sysSetValue('open_Form_'.$this->data['hash'],$retVal);
		//}
		$this->isBuilt		= true;
        return $retVal;	
	}


}


class openHTML_Body_Form_Element extends openWebX implements openObject {
	
	public $data		= array();
	private $options 	= array();
	private $selected	= false;
	
	public function __construct($strID,$strType,$strLabel='',$mixedValue='',$strMask='',$lstValidators='') {
		$this->id 			= $strID;
		$this->type			= $strType;
		$this->label		= $strLabel;
		$this->value		= $mixedValue;
		if (isset($_GET[$this->data['id']]) && $_GET[$this->data['id']] != '') {
			$this->selected = $_GET[$this->data['id']];
		} elseif (isset($_POST[$this->data['id']]) && $_POST[$this->data['id']] != '') {
			$this->selected = $_POST[$this->data['id']];
		} 
		$this->mask			= $strMask;
		$this->alt			= '';
		$this->validators	= $lstValidators;
		return $this;
	}
	
	public function __destruct() {
	}
	
	public function build() {
		(isset($this->data['label']) && $this->data['label']!='') ? $retVal = $this->buildLabel($this->data['id'],$this->data['label']) : $retVal = '';
		switch ($this->data['type']) {
			case 'input':
				$retVal .= $this->buildInput();
				break;
			case 'textarea':
				$retVal .= $this->buildTextArea();
				break;
			case 'date':
			case 'calendar':
				$retVal .= $this->buildDate();
				break;
			case 'password':
				$retVal .= $this->buildPassword();
				break;
			case 'file':
				$retVal .= $this->buildFile();
				break;
			case 'radio':
				$retVal .= $this->buildRadios();
				break;
			case 'checkbox':
				$retVal .= $this->buildCheckboxes();
				break;
			case 'select':
			case 'dropdown':
				$retVal .= $this->buildSelect();
				break;
			case 'multiselect':
				$retVal .= $this->buildMultiSelect();
				break;
			case 'submit':
				$retVal .= $this->buildSubmit();
				break;
			case 'reset':
				$retVal .= $this->buildReset();
				break;
		}
		$this->isBuilt		= true;
		return $retVal;
	}
	
	private function buildSubmit() {
		$myInput 		= new openHTML_Tag('input',true);
		$myInput->id 	= $this->data['id'];
		$myInput->name  = $this->data['id'];
		$myInput->alt	= $this->data['alt'];
		$myInput->value	= $this->data['value'];
		$myInput->type	= 'submit';
		$retVal 		= $myInput->build();
		unset($myInput);
		return $retVal;
	}
	
	private function buildReset() {
		$myInput 		= new openHTML_Tag('input',true);
		$myInput->id 	= $this->data['id'];
		$myInput->name  = $this->data['id'];
		$myInput->alt	= $this->data['alt'];
		$myInput->value	= $this->data['value'];
		$myInput->type	= 'reset';
		$retVal 		= $myInput->build();
		unset($myInput);
		return $retVal;
	}
	
	private function buildLabel($target,$content) {
		$myLabel 			= new openHTML_Tag('label');
		$myLabel->id		= 'label_'.$target;
		$myLabel->for 		= $target;
		$myLabel->content 	= $content;
		$retVal 			= $myLabel->build();
		unset($myLabel);
		return $retVal;
	}
	
	private function buildMultiSelect() {
		$this->parseOptions();
		$retVal = '';
		$myInput          	= new openHTML_Tag('select');
		$myInput->id      	= $this->data['id'];
        $myInput->name    	= $this->data['id'].'[]';
        $myInput->alt		= $this->data['alt'];
        $myInput->multiple	= 'multiple';
        $myInput->size		= 5;
		foreach ($this->options as $val) {
		    $myOption = new openHTML_Tag('option');
		    $myOption->value     = $val['value'];
            $myOption->content   = $val['text'];
            $this->checkSelected(&$myOption);
			$myInput->content.=$myOption->build();
            unset($myOption); 
		}
        $retVal         .= $myInput->build();
        unset($myInput);
        return $retVal;
	}
	
	private function buildSelect() {
		$this->parseOptions();
		$retVal = '';
		$myInput          	= new openHTML_Tag('select');
		$myInput->id      	= $this->data['id'];
        $myInput->name    	= $this->data['id'].'[]';
        $myInput->alt		= $this->data['alt'];
		foreach ($this->options as $val) {
		    $myOption = new openHTML_Tag('option');
		    $myOption->value     = $val['value'];
            $myOption->content   = $val['text'];
            $this->checkSelected(&$myOption);
            $myInput->content.=$myOption->build();
            unset($myOption); 
		}
        $retVal         .= $myInput->build();
        unset($myInput);
        return $retVal;
	}
	
	private function buildRadios() {
		$this->parseOptions();
		$retVal = '';
		foreach ($this->options as $val) {
			$myInput 			= new openHTML_Tag('input');
			$myInput->id 		= $this->data['id'];
			$myInput->name  	= $this->data['id'].'[]';
			$myInput->alt		= $this->data['alt'];
			$myInput->value		= $val['value'];
			$myInput->content	= $val['text'];
			$myInput->type      = 'radio';
			$this->checkSelected(&$myInput);
			$retVal 		   .= $myInput->build();
			unset($myInput);
		}
		return $retVal;
	}
	
    private function buildCheckboxes() {
        $this->parseOptions();
        $retVal = '';
        foreach ($this->options as $val) {
            $myInput            = new openHTML_Tag('input');
            $myInput->id        = $this->data['id'];
            $myInput->name      = $this->data['id'].'[]';
            $myInput->alt       = $this->data['alt'];
            $myInput->value     = $val['value'];
            $myInput->content   = $val['text'];
            $myInput->type      = 'checkbox';
            $this->checkSelected(&$myInput);
            $retVal            .= $myInput->build();
            unset($myInput);
        }
        return $retVal;
    }
	
	private function buildFile() {
		$myInput 		= new openHTML_Tag('input',true);
		$myInput->id 	= $this->data['id'];
		$myInput->name  = $this->data['id'];
		$myInput->alt	= $this->data['alt'];
		$myInput->value	= $this->data['value'];
		$myInput->type	= 'file';
		$this->checkSelected(&$myInput);
		$retVal 		= $myInput->build();
		unset($myInput);
		return $retVal;
	}
	
	private function buildPassword() {
		$myInput 		= new openHTML_Tag('input',true);
		$myInput->id 	= $this->data['id'];
		$myInput->name  = $this->data['id'];
		$myInput->alt	= $this->data['alt'];
		$myInput->value	= $this->data['value'];
		$myInput->type	= 'password';
		$this->checkSelected(&$myInput);
		$retVal 		= $myInput->build();
		unset($myInput);
		return $retVal;
	}
	
	private function buildDate() {
		$myInput 		= new openHTML_Tag('input',true);
		$myInput->id 	= 'datepicker_'.$this->data['id'];
		$myInput->name  = $this->data['id'];
		$myInput->alt	= $this->data['alt'];
		$myInput->format= ($this->mask!='')?$this->mask:'%x';
		$myInput->value	= $this->data['value'];
		$myInput->type	= 'text';
		$this->checkSelected(&$myInput);
		$retVal 		= $myInput->build();
		unset($myInput);
		return $retVal;
	}
	
	private function buildInput() {
		$myInput 		= new openHTML_Tag('input',true);
		$myInput->id 	= $this->data['id'];
		$myInput->name  = $this->data['id'];
		$myInput->alt	= $this->data['alt'];
		$myInput->value	= $this->data['value'];
		$myInput->type	= 'text';
		$this->checkSelected(&$myInput);
		$retVal 		= $myInput->build();
		unset($myInput);
		return $retVal;
	}

	private function buildTextArea() {
		$myInput = new openHTML_Tag('textarea');
		$myInput->id 		= $this->data['id'];
		$myInput->name  	= $this->data['id'];
		$myInput->alt		= $this->data['alt'];
		$myInput->content	= $this->data['value'];
		$this->checkSelected(&$myInput);
		$retVal 			= $myInput->build();
		unset($myInput);
		return $retVal;
	}
	
	
/*******************************************************************************************************************
 * Helper functions
 *******************************************************************************************************************/
	
	private function checkSelected(&$obj) {
		$retVal = false;
		if ($this->selected) {
			if (!is_array($this->selected)) $this->selected = array($this->selected);
			foreach ($this->selected as $val) {
				if ($obj->data['value'] == $val) {
					switch ($this->data['type']) {
						case 'radio':
						case 'checkbox':
							$obj->checked='checked';
							break;
						case 'select':
						case 'dropdown':
						case 'multiselect':
							$obj->selected='selected';
							break;
					}
				} else {
					
				}
			}
			
		}
		return $retVal;
	}
	
	private function parseOptions() {
		$this->options = array();
		if (is_array($this->value)) {
			foreach ($this->value as $key=>$val) {
				$this->options[] = array('value' => $key,'text' => $val);
			}
		} elseif (strpos($this->value,',')!==false) {
			$arrTmp = explode(',',$this->value);
			for ($i=0;$i<count($arrTmp);$i++) {
				$this->options[] = array('value' => $i,'text' => $arrTmp[$i]);
			}
		} else {
			$this->options[] = array('value'=>0,'text' => $this->value);
		}
	}
}
?>