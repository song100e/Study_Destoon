<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
define('DT_NONUSER', true);
if($_SERVER['QUERY_STRING']) {
	$exprise = isset($_GET['tag_expires']) ? intval($_GET['tag_expires']) : 0;
	$tag = $_SERVER['QUERY_STRING'];
	$_SERVER['QUERY_STRING'] = $_SERVER['REQUEST_URI'] = '';
	foreach($_GET as $k=>$v) { unset($$k); }
	$_GET = array();
	require '../common.inc.php';
	header("Content-type:text/javascript");	
	($DT['safe_domain'] && check_referer()) or exit('document.write("<h2>Invalid Referer</h2>");');
	$tag = strip_sql(stripslashes(urldecode($tag)));
	foreach(array('#', '$', 'table', 'fields', 'debug') as $v) {
		strpos($tag, $v) === false or exit('document.write("<h2>Bad Parameter</h2>");');
	}
	ob_start();
	tag($tag, $exprise);
	$data = ob_get_contents();
	ob_clean();
	echo 'document.write(\''.dwrite($data).'\');';
} else {
	header("Content-type:text/javascript");	
	echo 'document.write("<h2>Bad Parameter</h2>");';
}
?>