<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
define('DT_NONUSER', true);
require 'common.inc.php';
if($DT_BOT) dhttp(403);
require DT_ROOT.'/include/post.func.php';
if(preg_match("/^[a-z0-9]{1}[a-z0-9_\-]{0,}[a-z0-9]{1}$/", $action)) @include DT_ROOT.'/api/ajax/'.$action.'.inc.php';
?>