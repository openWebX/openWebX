<?php
$timer = time();

error_reporting(E_ALL);
require_once('openWebX.php');
require_once('request.php');

$myGal = new openGallery();

$myGal->galleryGetAll();

openDebug::dbgVar($myGal->galleryArray);

unset($myGal);

echo '<br/>Laufzeit: '.(time()-$timer).' secs';
?>
