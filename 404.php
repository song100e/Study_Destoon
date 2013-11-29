<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
require 'common.inc.php';
if(strpos($_SERVER['QUERY_STRING'], '404;') !== false) {
	$DT_URL = str_replace('404;', '', $_SERVER['QUERY_STRING']);
	$DT_URL = str_replace(':80', '', $DT_URL);
}
if($DT['log_404'] && strpos($DT_URL, '/404.php') === false) {
	require DT_ROOT.'/file/config/robot.inc.php';
	$url = addslashes($DT_URL);
	$time = $DT_TIME - 86400;
	$r = $db->get_one("SELECT itemid FROM {$DT_PRE}404 WHERE addtime>$time AND url='$url'");
	if(!$r) $db->query("INSERT INTO {$DT_PRE}404 (url,robot,username,ip,addtime) VALUES ('$url','".get_robot()."','$_username','$DT_IP','$DT_TIME')");
}
dhttp(404, $DT_BOT);
$head_title = '404 Not Found';
include template('404', 'message');
?>