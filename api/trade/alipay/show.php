<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
require '../../../common.inc.php';
$itemid = intval(get_cookie('trade_id'));
if($itemid) {
	$r = $db->get_one("SELECT linkurl FROM {$DT_PRE}mall_order WHERE itemid=$itemid");
	if($r && $r['linkurl']) dheader($r['linkurl']);
} 
dheader(DT_PATH);
?>