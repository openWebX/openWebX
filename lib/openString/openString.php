<?php
// ########################################################################
// # File: $Id: openString.php 217 2009-08-14 13:56:19Z jens $
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
* openString
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openString.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openString
* @uses openWebX
*/
class openString extends openWebX  {

    static public function strGenerateTeaser($strOrig,$iLength=100,$dots = true) {
        return (strlen($strOrig)<$iLength) ? $strOrig : substr($strOrig,0,$iLength-3) . (($dots) ? '...' : '');
    }

    static public function strHighlite ($strOrig,$strHighlite) {
        if ($strHighlite!='') {
            $pattern = $strHighlite;
            $retVal = eregi_replace ($pattern,'<span style="background:#FF9001;font-weight:bold;color:#000000;padding:1px;border:1px solid #000000;">'.($strHighlite).'</span>',$strOrig);
        } else {
            $retVal = $strOrig;
        }
        return $retVal;
    }

    static public function strCountWords($strOrig) {
      $str = strval($strOrig);
        if(preg_match_all("#\w+#s", $strOrig, $arrMatches)) {
            return count($arrMatches[0]);
        }
        return 0;
    }

    static public function strCountWordsOccurrences ($str) {
        $str    = strip_tags((string)$str);
        $dels   = array('"',',','.','!',';',':','-','(',')','[',']','{','}');
        $str  = str_replace($dels,' ',$str);
        if(strlen($str) == 0) {
            return -1;
        }
        $str = preg_replace("#(\t|\n)#", ' ', strtolower($str));
        $values = array_count_values(array_filter(explode(' ', $str)));
        arsort($values);
        return $values;
     }

  static public function strGetKeywords($strOrig) {
      $retVal     = '';
      $fillers  = array('ich','du','er','sie','es','wir','ihr','sie','und','oder','der','die','das','zum','in','im','zu','zur','ob','und','oder','den','ein','eine','mit','ohne','um','dies','ist','sein','werden');
      $myArray  = self::strCountWordsOccurrences($strOrig);
      $myKeywords = array();
      $myCount  = 0;
      if (is_array($myArray)) {
            foreach ($myArray as $key=>$val) {
          if (!in_array($key,$fillers) && $val>1 && $myCount<=10) {
            $myKeywords[] = trim($key);
            $myCount++;
          }
        }
        $retVal = implode(', ',$myKeywords);
      }
      return($retVal);
  }

    static public function strConvertLinks($strOrig) {
        $text = $strOrig;
        $text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','<a href="\\1" rel="external">\\1 <img src="/share/images/icons/openString/extlink.png" /></a>', $text);
        return(trim($text));
    }

    static public function strPlainText($strOrig) {
        return (strip_tags($strOrig));
    }

    static public function strSqueeze($strOrig) {
        return trim(ereg_replace( ' +', ' ', $strOrig));
    }

    static public function strCleanURL($strOrig) {
        $dels       = array('"',',','.','!',';',':','-','(',')','[',']','{','}','/','\\','`');
        $retVal     = str_replace(' ','_',self::strSqueeze(trim(str_replace($dels,'',self::strPlainText(openI18N::i18nTransliterate($strOrig))))));
        return($retVal);
    }

    static public function strMinifyJS($strOrig) {
      return JSMin::minify($strOrig);
    }
}

class JSMin {
  const ORD_LF    = 10;
  const ORD_SPACE = 32;

  protected $a           = '';
  protected $b           = '';
  protected $input       = '';
  protected $inputIndex  = 0;
  protected $inputLength = 0;
  protected $lookAhead   = null;
  protected $output      = '';

  // -- Public Static Methods --------------------------------------------------

  public static function minify($js) {
    $jsmin = new JSMin($js);
    return $jsmin->min();
  }

  // -- Public Instance Methods ------------------------------------------------

  public function __construct($input) {
    $this->input       = str_replace("\r\n", "\n", $input);
    $this->inputLength = strlen($this->input);
  }

  // -- Protected Instance Methods ---------------------------------------------

  protected function action($d) {
    switch($d) {
      case 1:
        $this->output .= $this->a;

      case 2:
        $this->a = $this->b;

        if ($this->a === "'" || $this->a === '"') {
          for (;;) {
            $this->output .= $this->a;
            $this->a       = $this->get();

            if ($this->a === $this->b) {
              break;
            }

            if (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated string literal.');
            }

            if ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            }
          }
        }

      case 3:
        $this->b = $this->next();

        if ($this->b === '/' && (
            $this->a === '(' || $this->a === ',' || $this->a === '=' ||
            $this->a === ':' || $this->a === '[' || $this->a === '!' ||
            $this->a === '&' || $this->a === '|' || $this->a === '?')) {

          $this->output .= $this->a . $this->b;

          for (;;) {
            $this->a = $this->get();

            if ($this->a === '/') {
              break;
            } elseif ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            } elseif (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated regular expression '.
                  'literal.');
            }

            $this->output .= $this->a;
          }

          $this->b = $this->next();
        }
    }
  }

  protected function get() {
    $c = $this->lookAhead;
    $this->lookAhead = null;

    if ($c === null) {
      if ($this->inputIndex < $this->inputLength) {
        $c = $this->input[$this->inputIndex];
        $this->inputIndex += 1;
      } else {
        $c = null;
      }
    }

    if ($c === "\r") {
      return "\n";
    }

    if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
      return $c;
    }

    return ' ';
  }

  protected function isAlphaNum($c) {
    return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
  }

  protected function min() {
    $this->a = "\n";
    $this->action(3);

    while ($this->a !== null) {
      switch ($this->a) {
        case ' ':
          if ($this->isAlphaNum($this->b)) {
            $this->action(1);
          } else {
            $this->action(2);
          }
          break;

        case "\n":
          switch ($this->b) {
            case '{':
            case '[':
            case '(':
            case '+':
            case '-':
              $this->action(1);
              break;

            case ' ':
              $this->action(3);
              break;

            default:
              if ($this->isAlphaNum($this->b)) {
                $this->action(1);
              }
              else {
                $this->action(2);
              }
          }
          break;

        default:
          switch ($this->b) {
            case ' ':
              if ($this->isAlphaNum($this->a)) {
                $this->action(1);
                break;
              }

              $this->action(3);
              break;

            case "\n":
              switch ($this->a) {
                case '}':
                case ']':
                case ')':
                case '+':
                case '-':
                case '"':
                case "'":
                  $this->action(1);
                  break;

                default:
                  if ($this->isAlphaNum($this->a)) {
                    $this->action(1);
                  }
                  else {
                    $this->action(3);
                  }
              }
              break;

            default:
              $this->action(1);
              break;
          }
      }
    }

    return $this->output;
  }

  protected function next() {
    $c = $this->get();

    if ($c === '/') {
      switch($this->peek()) {
        case '/':
          for (;;) {
            $c = $this->get();

            if (ord($c) <= self::ORD_LF) {
              return $c;
            }
          }

        case '*':
          $this->get();

          for (;;) {
            switch($this->get()) {
              case '*':
                if ($this->peek() === '/') {
                  $this->get();
                  return ' ';
                }
                break;

              case null:
                throw new JSMinException('Unterminated comment.');
            }
          }

        default:
          return $c;
      }
    }

    return $c;
  }

  protected function peek() {
    $this->lookAhead = $this->get();
    return $this->lookAhead;
  }
}

// -- Exceptions ---------------------------------------------------------------
class JSMinException extends Exception {}
?>
