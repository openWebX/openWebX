<?php
// ########################################################################
// # File: $Id: openImage.php 217 2009-08-14 13:56:19Z jens $
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
class openImage extends openWebX implements openObject {

	public $data 	= array();
	private $image	= null;

    public function __construct() {
        $this->registerSlots();
    }

    public function __destruct() {

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

	private function imgProcess($arrParams) {
		$this->hash = md5(serialize($arrParams));
		if (!file_exists(Settings::get('path_webcache').$this->data['hash'].'.png')) {
			foreach ($arrParams as $key=>$val) {
				$myFS 		= new openFilesystem();
				$imgDirs 	= $myFS->fileRecurseDir(Settings::get('path_images'),-1,'DIRS');
				$imgFile	= $val;
				foreach ($imgDirs as $fkey=>$fval) {
					if ($myFS->fileExistsInFolder($fval,$imgFile)) {
						$myFile = $fval.'/'.$imgFile;
						$this->imgInit($myFile);
					}
				}
				unset($myFS);
                switch ($val) {
                    case 'blur':
                        $this->imgBlur();
                        break;
                    case 'pixelate':
                        $blocksize = $arrParams[$key+1];
                        $this->imgPixelate($blocksize);
                        break;
                    case 'greyscale':
                    case 'grayscale':
                        $this->imgGreyscale();
                        break;
                    case 'scatter':
                        $this->imgScatter();
                        break;
                    case 'noise':
                        $this->imgNoise();
                        break;
                    case 'duotone':
                        $rgb = $arrParams[$key+1];
                        $this->imgDuotone($rgb);
                        break;
				    case 'resize':
					   $size = $arrParams[$key+1];
					   $this->imgResize($size);
                       break;
				    case 'scale':
					   $size = $arrParams[$key+1];
					   $this->imgScale($size);
                       break;
				    case 'interlace':
                        $color = $arrParams[$key+1];
                        $this->imgInterlace($color);
                        break;
                    case 'palette':
                        $colors = $arrParams[$key+1];
                        $this->imgPalette($colors);
                        break;
                    case 'show':
                        $this->imgShow();
                        break;

				}
			}
		} else {
			$this->imgShow();
		}
	}



	private function imgInit($myFile) {
		$arrInfo = getimagesize($myFile);
		$this->image['file']		= $myFile;
		$this->image['width'] 		= $arrInfo[0];
		$this->image['height']		= $arrInfo[1];
		$this->image['extension']	= image_type_to_extension($arrInfo[2]);
		$this->image['mimetype']	= image_type_to_mime_type($arrInfo[2]);
		switch($arrInfo[2]) {
			case IMAGETYPE_GIF:
				$this->image['object'] = imagecreatefromgif($myFile);
				break;
			case IMAGETYPE_JPEG:
				$this->image['object'] = imagecreatefromjpeg($myFile);
				break;
			case IMAGETYPE_PNG:
				$this->image['object'] = imagecreatefrompng($myFile);
				break;
			case IMAGETYPE_WBMP:
				$this->image['object'] = imagecreatefromwbmp($myFile);
				break;
			case IMAGETYPE_XBM:
				$this->image['object'] = imagecreatefromxbm($myFile);
				break;
			default:
				$this->image['object'] = file_get_contents($myFile);
				break;
		}

	}

    private function imgBlur() {
        $imagex = imagesx($this->image['object']);
        $imagey = imagesy($this->image['object']);
        $dist = 1;
        for ($x = 0; $x < $imagex; ++$x) {
            for ($y = 0; $y < $imagey; ++$y) {
                $newr = 0;
                $newg = 0;
                $newb = 0;
                $colours = array();
                $thiscol = imagecolorat($this->image['object'], $x, $y);
                for ($k = $x - $dist; $k <= $x + $dist; ++$k) {
                    for ($l = $y - $dist; $l <= $y + $dist; ++$l) {
                        if ($k < 0) { $colours[] = $thiscol; continue; }
                        if ($k >= $imagex) { $colours[] = $thiscol; continue; }
                        if ($l < 0) { $colours[] = $thiscol; continue; }
                        if ($l >= $imagey) { $colours[] = $thiscol; continue; }
                        $colours[] = imagecolorat($this->image['object'], $k, $l);
                    }
                }

                foreach($colours as $colour) {
                    $newr += ($colour >> 16) & 0xFF;
                    $newg += ($colour >> 8) & 0xFF;
                    $newb += $colour & 0xFF;
                }

                $numelements = count($colours);
                $newr /= $numelements;
                $newg /= $numelements;
                $newb /= $numelements;

                $newcol = imagecolorallocate($this->image['object'], $newr, $newg, $newb);
                imagesetpixel($this->image['object'], $x, $y, $newcol);
            }
        }
    }

    private function imgPixelate($blocksize) {
    	$imagex = imagesx($this->image['object']);
        $imagey = imagesy($this->image['object']);
        for ($x = 0; $x < $imagex; $x += $blocksize) {
            for ($y = 0; $y < $imagey; $y += $blocksize) {
                $thiscol = imagecolorat($this->image['object'], $x, $y);
                $newr = 0;
                $newg = 0;
                $newb = 0;
                $colours = array();
                for ($k = $x; $k < $x + $blocksize; ++$k) {
                    for ($l = $y; $l < $y + $blocksize; ++$l) {
                        // if we are outside the valid bounds of the image, use a safe colour
                        if ($k < 0) { $colours[] = $thiscol; continue; }
                        if ($k >= $imagex) { $colours[] = $thiscol; continue; }
                        if ($l < 0) { $colours[] = $thiscol; continue; }
                        if ($l >= $imagey) { $colours[] = $thiscol; continue; }
                        // if not outside the image bounds, get the colour at this pixel
                        $colours[] = imagecolorat($this->image['object'], $k, $l);
                    }
                }
                // cycle through all the colours we can use for sampling
                foreach($colours as $colour) {
                    // add their red, green, and blue values to our master numbers
                    $newr += ($colour >> 16) & 0xFF;
                    $newg += ($colour >> 8) & 0xFF;
                    $newb += $colour & 0xFF;
                }
                // now divide the master numbers by the number of valid samples to get an average
                $numelements = count($colours);
                $newr /= $numelements;
                $newg /= $numelements;
                $newb /= $numelements;
                // and use the new numbers as our colour
                $newcol = imagecolorallocate($this->image['object'], $newr, $newg, $newb);
                imagefilledrectangle($this->image['object'], $x, $y, $x + $blocksize - 1, $y + $blocksize - 1, $newcol);
            }
        }
    }

    private function imgNoise() {
    	$imagex = imagesx($this->image['object']);
        $imagey = imagesy($this->image['object']);
        for ($x = 0; $x < $imagex; ++$x) {
            for ($y = 0; $y < $imagey; ++$y) {
                if (rand(0,1)) {
                    $rgb = imagecolorat($this->image['object'], $x, $y);
                    $red = ($rgb >> 16) & 0xFF;
                    $green = ($rgb >> 8) & 0xFF;
                    $blue = $rgb & 0xFF;
                    $modifier = rand(-20,20);
                    $red += $modifier;
                    $green += $modifier;
                    $blue += $modifier;
                    if ($red > 255) $red = 255;
                    if ($green > 255) $green = 255;
                    if ($blue > 255) $blue = 255;
                    if ($red < 0) $red = 0;
                    if ($green < 0) $green = 0;
                    if ($blue < 0) $blue = 0;
                    $newcol = imagecolorallocate($this->image['object'], $red, $green, $blue);
                    imagesetpixel($this->image['object'], $x, $y, $newcol);
                }
            }
        }
    }

    private function imgScatter() {
    	$imagex = imagesx($this->image['object']);
        $imagey = imagesy($this->image['object']);
        for ($x = 0; $x < $imagex; ++$x) {
            for ($y = 0; $y < $imagey; ++$y) {
                $distx = rand(-4, 4);
                $disty = rand(-4, 4);
                if ($x + $distx >= $imagex) continue;
                if ($x + $distx < 0) continue;
                if ($y + $disty >= $imagey) continue;
                if ($y + $disty < 0) continue;
                $oldcol = imagecolorat($this->image['object'], $x, $y);
                $newcol = imagecolorat($this->image['object'], $x + $distx, $y + $disty);
                imagesetpixel($this->image['object'], $x, $y, $newcol);
                imagesetpixel($this->image['object'], $x + $distx, $y + $disty, $oldcol);
            }
        }
    }

    private function imgDuotone($rgb) {
    	$arrRGB = explode(',',$rgb);
        $imagex = imagesx($this->image['object']);
        $imagey = imagesy($this->image['object']);
        for ($x = 0; $x <$imagex; ++$x) {
            for ($y = 0; $y <$imagey; ++$y) {
                $rgb = imagecolorat($this->image['object'], $x, $y);
                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;
                $red = (int)(($red+$green+$blue)/3);
                $green = $red + $arrRGB[1];
                $blue = $red + $arrRGB[2];
                $red += $arrRGB[0];
                if ($red > 255) $red = 255;
                if ($green > 255) $green = 255;
                if ($blue > 255) $blue = 255;
                if ($red < 0) $red = 0;
                if ($green < 0) $green = 0;
                if ($blue < 0) $blue = 0;
                $newcol = imagecolorallocate ($this->image['object'], $red,$green,$blue);
                imagesetpixel ($this->image['object'], $x, $y, $newcol);
            }
        }
    }

    private function imgGreyscale() {
    	$imagex = imagesx($this->image['object']);
        $imagey = imagesy($this->image['object']);
        for ($x = 0; $x <$imagex; ++$x) {
            for ($y = 0; $y <$imagey; ++$y) {
                $rgb = imagecolorat($this->image['object'], $x, $y);
                $red = ($rgb >> 16) & 255;
                $green = ($rgb >> 8) & 255;
                $blue = $rgb & 255;
                $grey = (int)(($red+$green+$blue)/3);
                $newcol = imagecolorallocate($this->image['object'], $grey,$grey,$grey);
                imagesetpixel($this->image['object'], $x, $y, $newcol);
            }
        }
    }

    private function imgScreen($rgb) {
    	$arrRGB = explode(',',$rgb);
        $imagex = imagesx($this->image['object']);
        $imagey = imagesy($this->image['object']);
        $color  = imagecolorallocate($this->image['object'], $arrRGB[0], $arrRGB[1], $arrRGB[2]);
        for($x = 1; $x <= $imagex; $x += 2) {
            imageline($this->image['object'], $x, 0, $x, $imagey, $color);
        }
        for($y = 1; $y <= $imagey; $y += 2) {
            imageline($this->image['object'], 0, $y, $imagex, $y, $color);
        }
    }

    private function imgInterlace($rgb) {
        $arrRGB = explode(',',$rgb);
        $imagex = imagesx($this->image['object']);
        $imagey = imagesy($this->image['object']);
        $color  = imagecolorallocate($this->image['object'], $arrRGB[0], $arrRGB[1], $arrRGB[2]);
        for ($y = 0; $y < $imagey; ++$y) {
            if ($y % 2) {
                for ($x = 0; $x < $imagex; ++$x) {
                    imagesetpixel($this->image['object'], $x, $y, $color);
                }
            }
        }
    }

	private function imgPalette($colors) {
		set_time_limit(0);
        $colors = openFilter::filterAction('clean','integer',$colors);
        $image = imagetruecolortopalette($this->image['object'], true, $colors);
        $this->image['object'] = $image;
	}

	private function imgResize($size) {
		$arrTmp = explode('x',$size);
		$width  = $arrTmp[0];
		$height	= $arrTmp[1];
		$image  = imagecreatetruecolor($width,$height);
		imagecopyresampled($image, $this->image['object'], 0, 0, 0, 0, $width, $height, $this->image['width'], $this->image['height']);
		$this->image['object'] = $image;
		$this->image['width']	= imagesx($this->image['object']);
		$this->image['height']	= imagesy($this->image['object']);
	}

	private function imgScale($size) {
		$arrTmp = explode('x',$size);
		$width  = $arrTmp[0];
		$height	= $arrTmp[1];
		$ratio 	= $this->image['width']/$this->image['height'];
		if ($width/$height > $ratio) {
   			$width = $height*$ratio;
		} else {
   			$height = $width/$ratio;
		}
		$image  = imagecreatetruecolor($width,$height);
		imagecopyresampled($image, $this->image['object'], 0, 0, 0, 0, $width, $height, $this->image['width'], $this->image['height']);
		$this->image['object'] 	= $image;
		$this->image['width']	= imagesx($this->image['object']);
		$this->image['height']	= imagesy($this->image['object']);
	}

	private function imgShow() {
		$target = Settings::get('path_webcache').$this->data['hash'].'.png';
		if (!file_exists($target)) {
			$this->imgMove();
		}
		echo '<img src="'.Settings::get('web_cache').$this->data['hash'].'.png'.'" />';
	}

	private function imgMove() {
		$image  = imagecreatetruecolor($this->image['width'],$this->image['height']);
		imagecopyresampled($image, $this->image['object'], 0, 0, 0, 0, $this->image['width'], $this->image['height'], $this->image['width'], $this->image['height']);
		imagepng($image,Settings::get('path_webcache').$this->data['hash'].'.png',9);
	}

}
?>