<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$menus = array (
    array('系统体检', '?file='.$file),
    array('PHP信息', '?file='.$file.'&action=phpinfo', ' target="_blank"'),
);
if($action == 'phpinfo') {
	phpinfo();
} else {
	include tpl('doctor');
}
?>