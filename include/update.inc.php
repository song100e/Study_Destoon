<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) return;
if($update) $db->query("UPDATE LOW_PRIORITY {$table} SET ".(substr($update, 1))." WHERE itemid=$itemid", 'UNBUFFERED');
if($page == 1) {
	if($DT['cache_hits']) {
		 cache_hits($moduleid, $itemid);
	} else {
		$db->query("UPDATE LOW_PRIORITY {$table} SET hits=hits+1 WHERE itemid=$itemid", 'UNBUFFERED');
	}
}	
?>