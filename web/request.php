<?php
error_reporting(E_ALL);
require_once('openWebX.php');

if (isset($_GET['request'])) {
	$myRequest 		= explode('/',$_GET['request']);
	$arrHandlers	= array();
	$actHandler		= '';
	foreach($myRequest as $key=>$val) {
		if ($slots = openWebX::getSlots($val)) {
			foreach ($slots as $slot_key=>$slot_val) {
				$actHandler = $slots[$slot_key]->object;
				$arrHandlers[$actHandler]['slot'] = $val;
			}
		} else {
			$arrHandlers[$actHandler]['params'][] = $val;
		}
	}
	foreach ($arrHandlers as $key=>$val) {
		if (!isset($val['params'])) $val['params'] = null;
		$myObj = new $key();
		$myObj->handleSignal($val['slot'],$val['params']);
	}
}
?>
