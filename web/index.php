<?php
error_reporting(E_ALL);
require_once('openWebX.php');

//openDebug::dbgVar($_SESSION);

$myHTML = new openHTML();

$myImg = $myHTML->body->add('img','feed');
$myImg->src = '/share/images/icons/openFeed/rss.png';

$myFeeds = $myHTML->body->add('div','feeds');
$myFeeds->content = $myImg->build();

$myContent = $myHTML->body->add('div','divContent');
$myContent->class = 'rotate zoom-in';
$myContent->content = 'ich bin hier!';

unset($myHTML);

?>
