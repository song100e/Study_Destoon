<?php
defined('IN_DESTOON') or exit('Access Denied');
if(!$_userid) exit;
$mid = isset($mid) ? intval($mid) : 0;
$mid or exit;
isset($MODULE[$mid]) or exit;
$file = DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/editor.data.'.$mid.'.php';
$content = file_get($file);
if($content) {
	echo substr($content, 13);
} else {
	echo '';
}
?>