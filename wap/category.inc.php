<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$pid = isset($pid) ? intval($pid) : 0;
$lists = get_maincat($pid, $moduleid);
$head_title = $MOD['name'].$DT['seo_delimiter'].$head_title;
include template('category', $TP);
?>