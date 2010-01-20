<?php
class openException extends Exception {

  private $errHandler;
  private $errText;
  private $errMessages;
  private $errMessage;


  public function __construct($errCode=0,$sourceMessage='',$errHandler='') {
    $errMessage     = '';
    $this->errText  = '';
    if ($errHandler != '') {
        $this->errHandler = $errHandler;
    } else {
        $this->errHandler = Settings::get('exception_handler');
    }
    if ($this->errMessages[$errCode] != '') {
        $this->errMessage = '<span style="color:#ff0000;">Error <b>'.$errCode.'</b>: '.$this->errMessages[$errCode].'</span>';
    }
    $tmpDbg = debug_backtrace();
    $arrDbg = $tmpDbg[sizeof($tmpDbg)-1];
    (isset($arrDbg['class']) && $arrDbg['class']!='') ? $arrDbg['class'].='::' : $arrDbg['class']='';
    $this->errMessage = 'Exception caught in file <b>"'.$arrDbg['file'].'"</b> in line <b>'.$arrDbg['line']."</b>\n".$this->errMessage.' <b>'.$arrDbg['class'].$sourceMessage."</b>\n";
    if (is_array($sourceMessage)) $sourceMessage = '<pre>'.print_r($sourceMessage,true).'</pre>';
        parent::__construct($this->errMessage,intval($errCode));
    }

    public function errHandling() {
        //openWebX::sendSignal('log','<br/>---<br/>&nbsp;&nbsp;&nbsp;&nbsp;'.strip_tags($this->errMessage));
        echo '<br/>---<br/>&nbsp;&nbsp;&nbsp;&nbsp;'.strip_tags($this->errMessage);
    }

}?>
