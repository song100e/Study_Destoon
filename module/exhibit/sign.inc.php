<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
$itemid or dheader($MOD['linkurl']);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
include load('misc.lang');
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
$item or message($L['not_exists']);
if($item['fromtime'] && $DT_TIME > $item['fromtime']) message($L['has_started']);
if($item['totime'] && $DT_TIME > $item['totime']) message($L['has_expired']);
$item['status'] == 3 or message($L['not_exists']);
$item['username'] or message($L['com_not_member']);
$_username != $item['username'] or message($L['sign_self']);

$today = $today_endtime - 86400;
$sql = $_userid ? "username='$_username'" : "addtime>$today AND ip='$DT_IP'";
$t = $db->get_one("SELECT id FROM {$table_order} WHERE id=$itemid AND $sql");
if($t) message($L['sign_again']);

$linkurl = $MOD['linkurl'].$item['linkurl'];
$need_captcha = $MOD['captcha_sign'] == 2 ? $MG['captcha'] : $MOD['captcha_sign'];
$need_question = $MOD['question_sign'] == 2 ? $MG['question'] : $MOD['question_sign'];
require DT_ROOT.'/include/post.func.php';

if($submit) {
	$amount = intval($amount);
	if($amount < 1) message($L['msg_sign_amount']);
	$company = htmlspecialchars($company);
	$truename = htmlspecialchars($truename);
	if(strlen($truename) < 4) message($L['msg_type_truename']);
	if(!is_mobile($mobile)) message($L['msg_type_mobile']);
	$areaid = intval($areaid);
	$address = htmlspecialchars($address);
	$postcode = htmlspecialchars($postcode);
	is_email($email) or $email = '';
	is_qq($qq) or $qq = '';
	$content = htmlspecialchars($content);
	$user = $item['username'];
	$title = addslashes($item['title']);
	$db->query("INSERT INTO {$table_order} (id,user,title,amount,company,truename,mobile,areaid,address,postcode,email,qq,content,addtime,username,ip) VALUES ('$itemid','$user','$title','$amount','$company','$truename','$mobile','$areaid','$address','$postcode','$email','$qq','$content','$DT_TIME','$_username','$DT_IP')");
	$db->query("UPDATE {$table} SET orders=orders+1 WHERE itemid=$itemid");
	message($L['msg_sign_success'], $linkurl, 3);
} else {
	if($_userid) {
		$user = userinfo($_username);
		$company = $user['company'];
		$truename = $user['truename'];
		$mobile = $user['mobile'];
		$areaid = $user['areaid'];
		$address = $user['address'];
		$postcode = $user['postcode'];
		$email = $user['mail'] ? $user['mail'] : $user['email'];
		$qq = $user['qq'];
	} else {	
		$company = $truename = $mobile = $areaid = $address = $postcode = $email = $qq =  '';
	}
	$head_title = $L['sign_title'];
	include template('sign', $module);
}
?>