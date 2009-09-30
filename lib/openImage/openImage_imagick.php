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
	
}


?>
