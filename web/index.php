<?php
error_reporting(E_ALL);
require_once('openWebX.php');
require_once('request.php');

$myGallery = new openGallery();

$myGallery->galleryBuildFromDirectory('/share/images/');

unset($myGallery);
?>
