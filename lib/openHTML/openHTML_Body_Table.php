<?php
// ########################################################################
// # File: $Id: openHTML_Body_Table.php 229 2009-08-19 04:40:11Z jens $
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
// # Revision: $Revision: 229 $
// ########################################################################
/**
* openHTML_Body_Table
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openHTML_Body_Table.php 229 2009-08-19 04:40:11Z jens $
* @package openWebX
* @subpackage openHTML
* @uses openWebX
*/
class openHTML_Body_Table extends openWebX implements openObject {
	
	public $data 				= array();
	public $tableZebra			= true;
	public $tablePager			= null;
	public $tablePagerDisplay	= null;	
	public $tableSortable		= null;
	
	private $fillUp 			= false;
	private $rowCount			= 0;
	private $cellCount			= 0;
	private $pagerCount			= 0;
	private $pagerOffset		= 1;
	
	
	public function __construct($strID,$bFillUp=true) {
        $this->id       	= openFilter::filterAction('sanitize','string',$strID);
        $this->content  	= '';
        $this->fillUp		= true;
        $this->getOffset();
        return $this;
    }

    public function __destruct() {

    }

    public function addRow($strID) {
        $strID = openFilter::filterAction('sanitize','string',$strID);
        $this->$strID = new openHTML_Body_Table_Row($strID);
        if ($this->tableZebra) {
	       	($this->rowCount % 2) ? $this->$strID->class = 'even' : $this->$strID->class = 'odd';
        }
        $this->rowCount++;
    }

    public function build() {
    	// first check consistency...
    	$this->checkConsistency();
    	
		$myTable 		= new openHTML_Tag('table');
    	$myTable->id	= $this->data['id'];
    	$retVal			= '';
    	$rows 			= 1;
    	if ($this->tablePager) $retVal.= $this->buildPager();
        foreach ($this->data as $key => $val) {
            if (is_a($val,'openHTML_Body_Table_Row')) {
            	if ($this->tablePager) {
            		if ($this->pagerOffset < 1) $this->pagerOffset = 1;
            		if ($this->pagerOffset > ($this->rowCount / $this->tablePager)) $this->pagerOffset = ($this->rowCount / $this->tablePager); 
            		$pagerStart = intval($this->pagerOffset - 1) * intval($this->tablePager);
            		$pagerEnd	= intval($this->pagerOffset) * intval($this->tablePager);
            		if ($rows > $pagerStart && $rows <= $pagerEnd) {
            			$retVal.=$val->build();
            		}	
            	} else {
                	$retVal.=$val->build();
            	}
            	$rows++;
                unset($this->data[$key]);
            } else {
            	$myTable->$key = $val;
            }
        }
        if ($this->tablePager) $retVal.= $this->buildPager();
        $myTable->content = $retVal;
        if ($this->tableZebra) {
        	$myTable->class = 'zebra';
        }
        $retVal = $myTable->build();
        unset ($myTable);
        $this->isBuilt = true;
        return $retVal;
    }

	private function getOffset() {
		$arrReq = openRequest::get('page');
		$this->pagerOffset = intval($arrReq[0]);
	}
    
    private function buildPager() { 
    	$pagerRow 		= 'tr_'.$this->data['id'].'_pager'.$this->pagerCount;
    	$pagerCell 		= 'td_'.$this->data['id'].'_pager'.$this->pagerCount;
    	$pagerFirst		= $this->data['id'].'_first'.$this->pagerCount;
    	$pagerPrevious	= $this->data['id'].'_previous'.$this->pagerCount;
    	$pagerNext		= $this->data['id'].'_next'.$this->pagerCount;
    	$pagerLast		= $this->data['id'].'_last'.$this->pagerCount;
    	$pager = $this->addRow($pagerRow);
    	$this->$pagerRow->addCell($pagerCell);
    	$this->$pagerRow->$pagerCell->colspan = $this->cellCount;
    	$pages = intval($this->rowCount / $this->tablePager);
    	if ($this->pagerOffset==1) {
    		$myImg = new openHTML_Body_Image($pagerFirst);
    		$myImg->src = Settings::get('web_icons').'openHTML/openHTML_Body_Table/gray_first.png';
    		$this->$pagerRow->$pagerCell->content.=$myImg->build();
    		unset($myImg);
    	} else {
    		$myLink = new openHTML_Body_Link($pagerFirst,'icon::openHTML/openHTML_Body_Table/first.png');
    		$myLink->href='/page/:1';
    		$this->$pagerRow->$pagerCell->content.=$myLink->build();
    		unset($myLink);
    	}
    	if ($this->pagerOffset==1) {
    		$myImg = new openHTML_Body_Image($pagerFirst);
    		$myImg->src = Settings::get('web_icons').'openHTML/openHTML_Body_Table/gray_previous.png';
    		$this->$pagerRow->$pagerCell->content.=$myImg->build();
    		unset($myImg);
    	} else {
    		$myLink = new openHTML_Body_Link($pagerFirst,'icon::openHTML/openHTML_Body_Table/previous.png');
    		$myLink->href='/page/:'.($this->pagerOffset-1);
    		$this->$pagerRow->$pagerCell->content.=$myLink->build();
    		unset($myLink);
    	}
    	
    	$pagerDisplayOffset = false;
    	
    	if ($this->tablePagerDisplay) {
    		$pagerDisplay = intval($this->tablePagerDisplay);
    		if (!$pagerDisplay % 2) $pagerDisplay = ($pagerDisplay - 1);
    		if ($pagerDisplay>0) $pagerDisplayOffset = ($pagerDisplay / 2);
    	}
    	
    	for ($i=1;$i<=$pages;$i++) {
    		if (!$pagerDisplayOffset || $i==1 || $i==$pages || (($i>=intval($this->pagerOffset) - $pagerDisplayOffset ) && ($i<=intval($this->pagerOffset) + $pagerDisplayOffset ))) {
		    	$myLink = new openHTML_Body_Link('link_'.$i.'_'.$this->data['id'].'_pager'.$this->pagerCount);
		       	$myLink->content=$i;
		    	if ($i==intval($this->pagerOffset)) $myLink->style='font-weight:bold;';
		    	$myLink->href='/page/:'.$i;
		    	if ($pagerDisplayOffset && $i==$pages && intval($this->pagerOffset) < ($i-($pagerDisplayOffset+1))) $this->$pagerRow->$pagerCell->content.= '&nbsp;...&nbsp;';
		    	$this->$pagerRow->class='pager';
		    	$this->$pagerRow->$pagerCell->content.=$myLink->build();
		    	if ($pagerDisplayOffset && $i==1 && intval($this->pagerOffset) > ($i+$pagerDisplayOffset+1)) $this->$pagerRow->$pagerCell->content.= '&nbsp;...&nbsp;';
		    	unset($myLink);
    		}
    	}
    	
    	
    	
    	if ($this->pagerOffset == intval($this->rowCount / $this->tablePager)) {
    		$myImg = new openHTML_Body_Image($pagerFirst);
    		$myImg->src = Settings::get('web_icons').'openHTML/openHTML_Body_Table/gray_next.png';
    		$this->$pagerRow->$pagerCell->content.=$myImg->build();
    		unset($myImg);
    	} else {
    		$myLink = new openHTML_Body_Link($pagerFirst,'icon::openHTML/openHTML_Body_Table/next.png');
    		$myLink->href='/page/:'.($this->pagerOffset+1);
    		$this->$pagerRow->$pagerCell->content.=$myLink->build();
    		unset($myLink);
    	}
    	if ($this->pagerOffset == intval($this->rowCount / $this->tablePager)) {
    		$myImg = new openHTML_Body_Image($pagerFirst);
    		$myImg->src = Settings::get('web_icons').'openHTML/openHTML_Body_Table/gray_last.png';
    		$this->$pagerRow->$pagerCell->content.=$myImg->build();
    		unset($myImg);
    	} else {
    		$myLink = new openHTML_Body_Link($pagerFirst,'icon::openHTML/openHTML_Body_Table/last.png');
    		$myLink->href='/page/:'.intval($this->rowCount / $this->tablePager);
    		$this->$pagerRow->$pagerCell->content.=$myLink->build();
    		unset($myLink);
    	}
    	$this->pagerCount++;
    	return $this->$pagerRow->build();
    }
    
    private function checkConsistency() {
    	$maxCells 	= 0;
    	$arrRows	= array();	
    	foreach ($this->data as $val) {
    		$cellCount = 0;
    		if (is_a($val,'openHTML_Body_Table_Row')) {
    			foreach ($val->data as $rowVal) {
    				if (is_a($rowVal,'openHTML_Body_Table_Cell')) {
    					$cellCount++;
    				}
    			}
    			$arrRows[$val->data['id']] = $cellCount;
    			if ($cellCount>$maxCells) $maxCells = $cellCount;
    		}
    	}
    	$this->cellCount = $maxCells;
    	foreach ($arrRows as $key=>$val) {
    		if (!$this->fillUp) {
    			if ($val<$maxCells) throw new openException(EXCEPTION_HTML_TABLE_CELLCOUNT,'Number of cells differ!');
    		} else {
    			if ($val<$maxCells) {
    				$cellCount = $val;
    				while($cellCount<$maxCells) {
    					$cellCount++;
    					$this->$key->addCell(md5(microtime().$cellCount));
    				}
    			}
    		}
    	}
    }
}

class openHTML_Body_Table_Row extends openWebX implements openObject {
	
	public $data = array();
	
	 public function __construct($strID) {
        $this->id       = openFilter::filterAction('sanitize','string',$strID);
        $this->content  = '';
        return $this;
    }

    public function __destruct() {

    }

    public function addCell($strID,$strHeader='') {
        $strID = openFilter::filterAction('sanitize','string',$strID);
        $this->$strID = new openHTML_Body_Table_Cell($strID);
    }

    public function build() {
		$myTR 		= new openHTML_Tag('tr');
    	$myTR->id	= $this->data['id'];
    	$retVal		= '';
        foreach ($this->data as $key => $val) {
            if (is_a($val,'openHTML_Body_Table_Cell')) {
                $retVal.=$val->build();
                unset($this->data[$key]);
            } else {
            	$myTR->$key = $val;
            }
        }
        $myTR->content = $retVal;
        $retVal = $myTR->build();
        unset ($myTR);
        $this->isBuilt = true;
        return $retVal;
    }
}
class openHTML_Body_Table_Cell extends openWebX implements openObject {
	
	public $data = array();
	
	 public function __construct($strID) {
        $this->id       = openFilter::filterAction('sanitize','string',$strID);
        $this->content  = '';
        return $this;
    }

    public function __destruct() {

    }

    public function build() {
    	$myID 		= $this->data['id'];
    	$myContent	= $this->data['content'];
    	unset ($this->data['id'],$this->data['content']);
		$myTD = new openHTML_Tag('td');
		$myTD->id	= $myID;
        foreach ($this->data as $key=>$val) {
        	$key = openFilter::filterAction('clean','string',$key);
        	$val = openFilter::filterAction('clean','string',$val);
        	$myTD->$key = $val;
		}
		$myTD->content = $myContent;
		$retVal = $myTD->build();
		unset ($myTD);
		return ($retVal);
    }
}


?>