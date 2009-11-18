<?php
error_reporting(E_ALL);
require_once('openWebX.php');

//openDebug::dbgVar($_SESSION);

$myHTML = new openHTML();
$myUI = new openUI();


$myTabs = array();
$myTabs[] = 'personal';
$myTabs[] = 'contact';
$myTabs[] = 'blahfasel';

//$Tabber = $myHTML->body->add('div','tabber');
//$Tabber->content .= $myUI->uiTabs($myTabs);
echo $myUI->uiTabs($myTabs);


unset($myUI,$myHTML);

?>
