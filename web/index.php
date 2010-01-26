<?php
error_reporting(E_ALL);
require_once('openWebX.php');
require_once('request.php');

$myFS = new openFilesystem();
$Folder = $myFS->fileProfileDir('/share/images');
openDebug::dbgVar($Folder,'Ordner');
?>
