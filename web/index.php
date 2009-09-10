<?php
error_reporting(E_ALL);
require_once('openWebX.php');


$myDB = new openDB();


$myHTML = new openHTML();

$myImage = new openImage();
//$myForm = $myHTML->body->add('form','frmTest');
//
//$myForm->action='/mein_ziel';
//
//$myRadios = $myForm->addElement('r1','radio','Auswahl1','eins,zwei,drei');
//$myCheck =  $myForm->addElement('c2','checkbox','Auswahl1','eins,zwei,drei');
//$mySelect =  $myForm->addElement('s3','dropdown','Auswahl1','eins,zwei,drei');
//$mySelect2 =  $myForm->addElement('s4','multiselect','Auswahl1','eins,zwei,drei');
//$myDate = $myForm->addElement('d1','date','Geburtstag','','%d.%m.%Y');
//$myInput = $myForm->addElement('i1','input','Irgendwas');
//$mySubmit = $myForm->addElement('los','submit','','Abschicken');
//$mySubmit = $myForm->addElement('nene','reset','','Löschen');
//
echo $myHTML->build();

unset($myHTML);

?>
