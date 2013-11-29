<?php
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$data = "[InternetShortcut]\r\n";
$data .= "URL=".DT_PATH."?from=desktop\r\n";
$data .= "IconFile=".DT_PATH."favicon.ico\r\n";
$data .= "IconIndex=1";
$file = file_vname($DT['sitename'].'.url');
$file = convert($file, DT_CHARSET, 'GBK');
file_down('', $file, $data);
?>