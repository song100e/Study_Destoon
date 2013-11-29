<?php
defined('IN_DESTOON') or exit('Access Denied');
if(!$_userid) exit;
$mid = isset($mid) ? intval($mid) : 0;
$mid or exit;
isset($MODULE[$mid]) or exit;
if(!$content) exit;
$content = stripslashes($content);
$content = convert($content, 'UTF-8', DT_CHARSET);
$content = '<?php exit;?>'.timetodate($DT_TIME).$content;
file_put(DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/editor.data.'.$mid.'.php', $content);
?>