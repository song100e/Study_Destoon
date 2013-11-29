<?php
defined('IN_DESTOON') or exit('Access Denied');
isset($job) or exit;
if($job == 'sell') {
	$moduleid = 5;
} else if($job == 'buy') {
	$moduleid = 6;
} else {
	exit;
}
tag("moduleid=$moduleid&condition=status=3&areaid=$city_id&pagesize=".$DT['page_trade']."&page=$page&datetype=2&order=addtime desc&time=addtime&template=list-trade");
?>