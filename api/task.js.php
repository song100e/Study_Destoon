<?php
@set_time_limit(0);
#@ignore_user_abort(true);
define('DT_TASK', true);
require '../common.inc.php';
check_referer() or exit;
if($DT_BOT) exit;
header("Content-type:text/javascript");	
include template('line', 'chip');
$db->linked or exit;
isset($html) or $html = '';
if($html) {
	$task_index = intval($DT['task_index']);
	$task_index > 60 or $task_index = 300;
	$task_list = intval($DT['task_list']);
	$task_index > 300 or $task_index = 1800;
	if($moduleid == 1) {
		if($DT['index_html'] && $DT_TIME - @filemtime(DT_ROOT.'/'.$DT['index'].'.'.$DT['file_ext']) > $task_index) tohtml('index');
	} else {
		include DT_ROOT.'/module/'.$module.'/common.inc.php';
		include DT_ROOT.'/module/'.$module.'/task.inc.php';
	}
}
if($DT['message_email'] && $DT['mail_type'] != 'close' && !$_userid) {
	$condition = 'isread=0 AND issend=0 AND status=3';
	if($DT['message_time']) {
		$time = $DT_TIME - $DT['message_time']*60;
		$condition .= " AND addtime<$time";
	}
	if($DT['message_type']) $condition .= " AND typeid IN ($DT[message_type])";
	$msg = $db->get_one("SELECT * FROM {$DT_PRE}message WHERE $condition ORDER BY itemid ASC");
	if($msg) {
		$db->query("UPDATE {$DT_PRE}message SET issend=1 WHERE itemid=$msg[itemid]");
		$user = $db->get_one("SELECT groupid,email,send FROM {$DT_PRE}member WHERE username='$msg[touser]'");
		if($user) {
			if($user['send']) {
				if(check_group($user['groupid'], $DT['message_group'])) {
					extract($msg);
					$NAME = $L['message_type'];
					$member_url = $MODULE[2]['linkurl'];
					$content = ob_template('message', 'mail');
					send_mail($user['email'], '['.$NAME[$typeid].']'.$title, $content);
				}
			}
		}
	}
}
$db->close();
?>