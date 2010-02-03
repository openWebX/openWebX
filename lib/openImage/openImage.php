<?php

class openImage extends openWebX implements openObject {

	private $imgObject 	= NULL;
	private $imgFile	= NULL;
	private $imgHash	= NULL;
	private $imgParams	= NULL;	

	public function __construct($strQueue = NULL,$absPath = NULL) {
		$this->registerSlots();
		if ($absPath) {
			$this->imgFile = $absPath;	
		}
		if ($strQueue) {
			$arrQueue = explode('/',$strQueue);
			$this->imgProcess($arrQueue);	
		}
	}
	
	public function __destruct() {
		unset ($this->docObject,$this->imgObject);	
	}
	
	public function handleSignal($strSignalName,$mixedParams) {
      	switch(strtolower($strSignalName)) {
        	case 'image':
				$this->imgProcess($mixedParams);
            	break;
      	}
    }
	
	private function registerSlots() {
        openWebX::registerSlot($this,'image',0);
    }
	
	private function initObject($strFile) {
		if (Extension::installed('gmagick')) {
			$this->imgObject = new openImage_gmagick();
		} elseif (Extension::installed('imagick')) {
			$this->imgObject = new openImage_imagick($strFile);
		} else {
			$this->imgObject = new openImage_gd();
		}
	}
	
	private function imgFindFile($strFile) {
		$myFS 		= new openFilesystem();
		$imgDirs 	= $myFS->fileRecurseDir(Settings::get('path_images'),-1,'DIRS');
		$imgFile	= $strFile;
		foreach ($imgDirs as $fkey=>$fval) {
			if ($myFS->fileExistsInFolder($fval,$imgFile)) {
				$this->imgFile = $fval.'/'.$imgFile;
				$this->docObject->addAttachment('original',file_get_contents($this->imgFile,FILE_BINARY),$myFS->fileGetMimeType($this->imgFile));
				unset($myFS);
				return true;
			}
		}
		unset($myFS);	
		return false;
	}
	
	private function imgProcess($arrParams) {
		if (!isset($this->imgFile)) {
			$fileName			= openFilter::filterAction('clean','string',$arrParams[0]);
			$this->imgHash 		= md5($fileName);
			$this->imgParams	= md5(serialize($arrParams));
			$this->initObject($this->imgFile);
			unset ($arrParams[0]);
		} else {
			$this->imgHash		= md5($this->imgFile);
			$this->initObject($this->imgFile);
		}
		foreach ($arrParams as $key=>$val) {
			switch ($val) {
     			case 'blur':
     				$this->imgObject->imgBlur(5,3);
     				break;
 				case 'greyscale':
 				case 'grayscale':
     				$this->imgObject->imgGreyscale();
     				break;
 				case 'resize':
					$size = explode('x',$arrParams[$key+1]);
   					$this->imgObject->imgResize($size[0],$size[1]);
   					break;
				case 'sepia':
					$this->imgObject->imgSepia(80);
					break;
				case 'scale':
   					$size = explode('x',$arrParams[$key+1]);
   					$this->imgObject->imgScale($size[0],$size[1]);
   					break;
				case 'show':
    				$this->imgShow();
    				break;
				case 'save':
            		$this->imgSave();
            		break;
			}
		}
	}
	
	private function imgShow() {
		$this->imgSave();
		$myImg 		= new openHTML_Tag('img',true);
		$myImg->id 	= 'img_'.$this->imgParams;
		$myImg->src = Settings::get('web_cache').$this->imgHash.'.png';
		echo $myImg->build();
		unset ($myImg);
	}
	
	private function imgSave() {
		file_put_contents(Settings::get('path_webcache').$this->imgHash.'.png',$this->imgObject->imgGet());
	}

}


interface openImage_Interface {
	
	public function imgBlur			($iRadius,$iDeviation);
	public function imgGet			();
	public function imgGreyscale	();
	public function imgMirror		($strDirection);
	public function imgResize		($iWidth, $iHeight);
	public function imgScale		($iWidth,$iHeight);	
	public function imgSepia		($iThreshold);


}
?>