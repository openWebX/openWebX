<?php


class openImage_imagick implements openImage_Interface {

	private $imgObject 	= null;

	public function __construct($imgFile= '') {
		$this->imgObject = new Imagick($imgFile);
	}
	
	public function __destruct() {
		unset($this->imgObject);
	}
	
	public function imgBlur($iRadius,$iDeviation) {
		$iRadius = intval($iRadius);
		$iDeviation = intval($iDeviation);
		$this->imgObject->blurImage($iRadius,$iDeviation);	
	}
	
	public function imgGreyscale() {
		$this->imgObject->setImageColorspace(Imagick::COLORSPACE_GRAY);
	}
	
	public function imgGet() {
		$this->imgObject->setImageFormat('png');
		$this->imgObject->setCompression(9);
		$retVal = $this->imgObject->getImageBlob();
		$this->imgObject->clear();
		$this->imgObject->destroy();
		return $retVal;
	}
	
	public function imgMirror($strDirection) {
		$strDirection = strtolower(openFilter::filterAction('clean','string',$strDirection));
		switch ($strDirection) {
			case 'vertical':
				$this->imgObject->flipImage();
				break;
			case 'horizontal':
				$this->imgObject->flopImage();
				break;	
		}
	}
	
	public function imgResize($iWidth,$iHeight) {
		$iWidth = intval($iWidth);
		$iHeight = intval($iHeight);
		$this->imgObject->resizeImage($iWidth,$iHeight,Imagick::FILTER_LANCZOS,1);
	}
	
	public function imgScale($iWidth,$iHeight) {
		$iWidth = intval($iWidth);
		$iHeight = intval($iHeight);
		if (!$iWidth) $iWidth = $this->imgObject->getImageWidth();
		if (!$iHeight) $iHeight = $this->imgObject->getImageHeight();
		$this->imgObject->scaleImage($iWidth,$iHeight,true);
	}
	
	public function imgSepia($iThreshold) {
		$iThreshold = openFilter::filterAction('clean','int',$iThreshold);
		$this->imgObject->sepiaToneImage($iThreshold);
	}
	
	public function imgText2Image($text,$font,$size) {
		Header("Content-type: image/png"); 

class textPNG { 
    var $font = 'fonts/TIMES.TTF'; //default font. directory relative to script directory. 
    var $msg = "undefined"; // default text to display. 
    var $size = 24; 
    var $rot = 0; // rotation in degrees. 
    var $pad = 0; // padding. 
    var $transparent = 1; // transparency set to on. 
    var $red = 0; // white text... 
    var $grn = 0; 
    var $blu = 0; 
    var $bg_red = 255; // on black background. 
    var $bg_grn = 255; 
    var $bg_blu = 255; 

function draw() { 
    $width = 0; 
    $height = 0; 
    $offset_x = 0; 
    $offset_y = 0; 
    $bounds = array(); 
    $image = ""; 
     
    // determine font height. 
    $bounds = ImageTTFBBox($this->size, $this->rot, $this->font, "W"); 
    if ($this->rot < 0) { 
        $font_height = abs($bounds[7]-$bounds[1]);         
    } else if ($this->rot > 0) { 
        $font_height = abs($bounds[1]-$bounds[7]); 
    } else { 
        $font_height = abs($bounds[7]-$bounds[1]); 
    } 

    // determine bounding box. 
    $bounds = ImageTTFBBox($this->size, $this->rot, $this->font, $this->msg); 
    if ($this->rot < 0) { 
        $width = abs($bounds[4]-$bounds[0]); 
        $height = abs($bounds[3]-$bounds[7]); 
        $offset_y = $font_height; 
        $offset_x = 0; 
         
    } else if ($this->rot > 0) { 
        $width = abs($bounds[2]-$bounds[6]); 
        $height = abs($bounds[1]-$bounds[5]); 
        $offset_y = abs($bounds[7]-$bounds[5])+$font_height; 
        $offset_x = abs($bounds[0]-$bounds[6]); 
         
    } else { 
        $width = abs($bounds[4]-$bounds[6]); 
        $height = abs($bounds[7]-$bounds[1]); 
        $offset_y = $font_height;; 
        $offset_x = 0; 
    } 
     
    $image = imagecreate($width+($this->pad*2)+1,$height+($this->pad*2)+1); 
     
    $background = ImageColorAllocate($image, $this->bg_red, $this->bg_grn, $this->bg_blu); 
    $foreground = ImageColorAllocate($image, $this->red, $this->grn, $this->blu); 
     
    if ($this->transparent) ImageColorTransparent($image, $background); 
    ImageInterlace($image, false); 
     
    // render it. 
    ImageTTFText($image, $this->size, $this->rot, $offset_x+$this->pad, $offset_y+$this->pad, $foreground, $this->font, $this->msg); 
     
    // output PNG object. 
    imagePNG($image); 
} 
} 

$text = new textPNG; 

if (isset($msg)) $text->msg = $msg; // text to display 
if (isset($font)) $text->font = $font; // font to use (include directory if needed). 
if (isset($size)) $text->size = $size; // size in points 
if (isset($rot)) $text->rot = $rot; // rotation 
if (isset($pad)) $text->pad = $pad; // padding in pixels around text. 
if (isset($red)) $text->red = $red; // text color 
if (isset($grn)) $text->grn = $grn; // .. 
if (isset($blu)) $text->blu = $blu; // .. 
if (isset($bg_red)) $text->bg_red = $bg_red; // background color. 
if (isset($bg_grn)) $text->bg_grn = $bg_grn; // .. 
if (isset($bg_blu)) $text->bg_blu = $bg_blu; // .. 
if (isset($tr)) $text->transparent = $tr; // transparency flag (boolean). 

$text->draw(); 
?>	
	}
	
}


?>
