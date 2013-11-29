<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$AREA = cache_read('area.php');
$c = array();
$city = get_cookie('city');
if($city) {
	list($cityid, $city_domain) = explode('|', $city);
	if(strpos(DT_PATH, $_SERVER['HTTP_HOST']) === false && strpos($city_domain, $_SERVER['HTTP_HOST']) === false) {
		$c = $db->get_one("SELECT * FROM {$DT_PRE}city WHERE domain='http://".$_SERVER['HTTP_HOST']."/'");
		if($c) {
			set_cookie('city', $c['areaid'].'|'.$c['domain'], $DT_TIME + 365*86400);
			$cityid = $c['areaid'];
		}
	}
	#if($city_domain && !defined('DT_ADMIN') && strpos($DT_URL, DT_PATH) !== false) dheader(str_replace(DT_PATH, $city_domain, $DT_URL));
} else {
	if(strpos(DT_PATH, $_SERVER['HTTP_HOST']) === false) {
		$c = $db->get_one("SELECT * FROM {$DT_PRE}city WHERE domain='http://".$_SERVER['HTTP_HOST']."/'");
		if($c) {
			set_cookie('city', $c['areaid'].'|'.$c['domain'], $DT_TIME + 365*86400);
			$cityid = $c['areaid'];
		}
	} else {
		if($DT['city_ip'] && !defined('DT_ADMIN') && !$DT_BOT) {
			$iparea = ip2area($DT_IP);
			$result = $db->query("SELECT * FROM {$DT_PRE}city");
			while($r = $db->fetch_array($result)) {
				if(preg_match("/".$r['name'].($r['iparea'] ? '|'.$r['iparea'] : '')."/i", $iparea)) {
					set_cookie('city', $r['areaid'].'|'.$r['domain'], $DT_TIME + 365*86400);
					$cityid = $r['areaid'];
					if($r['domain']) dheader($r['domain']);
					$c = $r;
					break;
				}
			}
		}
	}
}
if($cityid) {
	$c or $c = $db->get_one("SELECT * FROM {$DT_PRE}city WHERE areaid=$cityid");
	if(!defined('DT_ADMIN')) {
		if($c['seo_title']) {		
			$DT['seo_title'] = $city_sitename = $c['seo_title'];
		} else {
			$citysite = lang($L['citysite'], array($c['name']));
			$DT['seo_title'] = $citysite.$DT['seo_delimiter'].$DT['seo_title'];
			$city_sitename = $citysite.$DT['seo_delimiter'].$DT['sitename'];
		}
		if($c['seo_keywords']) $DT['seo_keywords'] = $c['seo_keywords'];
		if($c['seo_description']) $DT['seo_description'] = $c['seo_description'];
	}
	$city_name = $c['name'];
	$city_domain = $c['domain'];
	$city_template = $c['template'];
}
if($city_domain) {
	foreach($MODULE as $k=>$v) {
		if($v['islink']) continue;
		$MODULE[$k]['linkurl'] = $k == 1 ? $city_domain : $city_domain.$v['moduledir'].'/';
	}
	$MOD['linkurl'] = $MODULE[$moduleid]['linkurl'];
	foreach($EXT as $k=>$v) {
		if(strpos($k, '_url') !== false) {
			$EXT[$k] = $city_domain.str_replace('_url', '', $k).'/';
		}
	}
}
?>