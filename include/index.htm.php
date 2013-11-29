<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$data = DT_DOMAIN ? 'if(window.location.href.indexOf("'.DT_DOMAIN.'") != -1){document.domain = "'.DT_DOMAIN.'";}' : '';
$data .= 'var DTPath = "'.DT_PATH.'";';
$data .= 'var SKPath = "'.DT_SKIN.'";';
$data .= 'var MEPath = "'.$MODULE[2]['linkurl'].'";';
$data .= 'var EXPath = "'.$MODULE[3]['linkurl'].'";';
$data .= 'var CKDomain = "'.$CFG['cookie_domain'].'";';
$data .= 'var CKPath = "'.$CFG['cookie_path'].'";';
$data .= 'var CKPrex = "'.$CFG['cookie_pre'].'";';
file_put(DT_ROOT.'/file/script/config.js', $data);
$filename = $CFG['com_dir'] ? DT_ROOT.'/'.$DT['index'].'.'.$DT['file_ext'] : DT_CACHE.'/index.inc.html';
if(!$DT['index_html']) {
	if(is_file($filename)) unlink($filename);
	return false;
}
$destoon_task = "moduleid=1&html=index";
$AREA = cache_read('area.php');
$seo_title = $DT['seo_title'];
$head_keywords = $DT['seo_keywords'];
$head_description = $DT['seo_description'];
ob_start();
include template('index');
$data = ob_get_contents();
ob_clean();
file_put($filename, $data);
return true;
?>