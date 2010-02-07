<?php
$timer = time();

error_reporting(E_ALL);
require_once('openWebX.php');
//require_once('request.php');

$myHTML = new openHTML();


$myGal = new openGallery();



$myGals = $myHTML->body->add('div','galleries');

$myGals->load='qr/data:fgfgdfgdfg/size:m';


unset($myGal,$myHTML);
?>
