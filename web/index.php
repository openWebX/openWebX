<?php
error_reporting(E_ALL);
require_once('openWebX.php');


$myDoc = new openDocument('test','blogentry');

$myDoc->author 	= 'Jens';
$myDoc->content	= 'blahfasel';

$myDoc->save();

?>
