<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('misc.lang');
include load('member.lang');
if($submit) {
	require DT_ROOT.'/module/'.$module.'/cart.class.php';
	$do = new cart();
	$cart = $do->get();
	if($post) {
		$add = array_map('trim', $add);
		$add['address'] = area_pos($add['areaid'], '').$add['address'];
		$add = array_map('htmlspecialchars', $add);
		$buyer_address = $add['address'];
		if(strlen($buyer_address) < 10) message($L['msg_type_address']);
		$buyer_postcode = $add['postcode'];
		if(strlen($buyer_postcode) < 6) message($L['msg_type_postcode']);
		$buyer_name = $add['truename'];
		if(strlen($buyer_name) < 2) message($L['msg_type_truename']);
		$buyer_mobile = $add['mobile'];
		if(strlen($buyer_mobile) < 11) message($L['msg_type_mobile']);
		$buyer_phone = $add['telephone'];
		$buyer_receive = $add['receive'];
		if(strlen($buyer_receive) < 2) message($L['msg_type_express']);
		$i = 0;
		foreach($post as $k=>$v) {
			$t1 = array_map('intval', explode('-', $k));
			$itemid = $t1[0];
			$s1 = $t1[1];
			$s2 = $t1[2];
			$s3 = $t1[3];
			$t = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
			if($t && $t['status'] == 3 && $t['username'] != $_username && $t['amount'] > 1) {

				$number = intval($v['number']);				
				if($number < 1) $number = 1;
				if($t['amount'] && $number > $t['amount']) $number = $t['amount'];
				$amount = $number*$t['price'];

				$note = '';
				$t['p1'] = get_nv($t['n1'], $t['v1']);
				$t['p2'] = get_nv($t['n2'], $t['v2']);
				$t['p3'] = get_nv($t['n3'], $t['v3']);
				$t['s1'] = $s1;
				$t['s2'] = $s2;
				$t['s3'] = $s3;
				$t['m1'] = isset($t['p1'][$t['s1']]) ? $t['p1'][$t['s1']] : '';
				$t['m2'] = isset($t['p2'][$t['s2']]) ? $t['p2'][$t['s2']] : '';
				$t['m3'] = isset($t['p3'][$t['s3']]) ? $t['p3'][$t['s3']] : '';
				if($t['m1']) $note .= $t['n1'].':'.$t['m1'].' ';
				if($t['m2']) $note .= $t['n2'].':'.$t['m2'].' ';
				if($t['m3']) $note .= $t['n3'].':'.$t['m3'].' ';

				$note = htmlspecialchars($note.$v['note']);
				$title = addslashes($t['title']);
				$linkurl = $MOD['linkurl'].$t['linkurl'];
				$status = $MOD['checkorder'] ? 0 : 1;

				if($v['express'] && in_array($v['express'], array(1,2,3))) {
					$i = $v['express'];
					$buyer_receive = $t['express_name_'.$i];
					$fee_name = $t['express_name_'.$i];
					$fee = dround($t['fee_start_'.$i] + $t['fee_step_'.$i]*($number-1));
					$express_id = $t['express_'.$i];
					$area_id = $add['areaid'];
					if($express_id && $area_id) {
						$E = $db->get_one("SELECT * FROM {$DT_PRE}mall_express WHERE itemid=$express_id");
						if($E && $E['items'] > 0) {
							$AREA = cache_read('area.php');
							$aid = $area_id;
							$ii = 0;
							do {
								$E = $db->get_one("SELECT * FROM {$DT_PRE}mall_express WHERE parentid=$express_id AND areaid=$aid");
								if($E) {
									$fee = dround($E['fee_start'] + $E['fee_step']*($number-1));
									break;
								} else {
									$aid = $AREA[$aid]['parentid'];
								}
								if($ii++ > 5) break;//safe
							} while($aid > 0);
						}
					}
				} else {
					$fee_name = '';
					$fee = 0;
				}
				$db->query("INSERT INTO {$DT_PRE}mall_order (mallid,buyer,seller,title,thumb,price,number,amount,addtime,updatetime,note, buyer_postcode,buyer_address,buyer_name,buyer_phone,buyer_mobile,buyer_receive,status,fee_name,fee) VALUES ('$itemid','$_username','$t[username]','$title','$t[thumb]','$t[price]','$number','$amount','$DT_TIME','$DT_TIME','$note','$buyer_postcode','$buyer_address','$buyer_name','$buyer_phone','$buyer_mobile','$buyer_receive','$status','$fee_name','$fee')");
				$itemid = $db->insert_id();
				$db->query("REPLACE INTO {$DT_PRE}mall_comment (itemid,mallid,buyer,seller) VALUES ($itemid,$k,'$_username','$t[username]')");
				$tmp = $db->get_one("SELECT mallid FROM {$DT_PRE}mall_stat WHERE mallid=$k");
				if(!$tmp) $db->query("REPLACE INTO {$DT_PRE}mall_stat (mallid,buyer,seller) VALUES ($k,'$_username','$t[username]')");
				//send message
				$touser = $t['username'];
				$_title = $title;
				$title = lang($L['trade_message_t6'], array($itemid));
				$url = $MODULE[2]['linkurl'].'trade.php?itemid='.$itemid;
				$goods = '<a href="'.$linkurl.'" target="_blank" class="t"><strong>'.$_title.'</strong></a>';
				$content = lang($L['trade_message_c6'], array(userurl($_username), $_username, timetodate($DT_TIME, 3), $goods, $itemid, $amount, $url));
				$content = ob_template('messager', 'mail');
				send_message($touser, $title, $content);
	
				$cart = str_replace(','.$k.',', ',', $cart.',');
				if(substr($cart, -1) == ',') $cart = substr($cart, 0, -1);
				$i++;
			}
		}
	}
	$do->set($cart);
	message($L['msg_buy_success'], $MODULE[2]['linkurl'].(($status == 1 && $i == 1 && $itemid) ? 'trade.php?action=update&step=pay&itemid='.$itemid : 'trade.php?action=order'));
} else {
	$tags = $_tags = array();
	$receive = '';
	if(is_array($itemid)) {
		$_itemids = $itemids = '';
		$_ids = $datas = array();
		foreach($keyid as $v1) {
			$t1 = array_map('intval', explode('-', $v1));
			$_ids[$t1[0]] = $t1[0];
			$t2 = array();
			$t2['itemid'] = $t1[0];
			$t2['s1'] = $t1[1];
			$t2['s2'] = $t1[2];
			$t2['s3'] = $t1[3];
			$datas[$v1] = $t2;
		}
		if($_ids) {
			$itemids = implode(',', $_ids);
			$total = $total_amount = 0;
			$amounts = (isset($amounts) && is_array($amounts)) ? $amounts : array();
			$result = $db->query("SELECT * FROM {$table} WHERE itemid IN ($itemids)");
			while($r = $db->fetch_array($result)) {
				if($r['username'] == $_username || $r['status'] != 3 || $r['amount'] < 1) continue;
				$r['alt'] = $r['title'];
				$r['title'] = dsubstr($r['title'], 40, '..');
				$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
				$r['p1'] = get_nv($r['n1'], $r['v1']);
				$r['p2'] = get_nv($r['n2'], $r['v2']);
				$r['p3'] = get_nv($r['n3'], $r['v3']);
				$_tags[$r['itemid']] = $r;
			}
			foreach($datas as $k=>$v) {
				if(isset($_tags[$v['itemid']])) {
					$t = $_tags[$v['itemid']];
					$t['key'] = $k;
					$t['s1'] = $v['s1'];
					$t['s2'] = $v['s2'];
					$t['s3'] = $v['s3'];
					$t['m1'] = isset($t['p1'][$t['s1']]) ? $t['p1'][$t['s1']] : '';
					$t['m2'] = isset($t['p2'][$t['s2']]) ? $t['p2'][$t['s2']] : '';
					$t['m3'] = isset($t['p3'][$t['s3']]) ? $t['p3'][$t['s3']] : '';
					$t['number'] = (isset($amounts[$k]) && $amounts[$k] > 1) ?  intval($amounts[$k]) : 1;
					$tags[] = $t;
					$_itemids .= ','.implode('-', $v);
					if($t['express_name_1']) $receive = $t['express_name_1'];
					$total_amount += $r['price'];
					$total++;
				}
			}
		}
	} else if($itemid) {
		$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		if($r['username'] == $_username) dalert($L['buy_self'], 'goback');
		if($r['status'] == 3 || $r['amount'] > 1) {
			$r['alt'] = $r['title'];
			$r['title'] = dsubstr($r['title'], 40, '..');
			$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
			$r['number'] = 1;
			$s1 = isset($s1) ? intval($s1) : 0;
			$s2 = isset($s2) ? intval($s2) : 0;
			$s3 = isset($s3) ? intval($s3) : 0;
			$r['p1'] = get_nv($r['n1'], $r['v1']);
			$r['p2'] = get_nv($r['n2'], $r['v2']);
			$r['p3'] = get_nv($r['n3'], $r['v3']);
			$r['key'] = $itemid.'-'.$s1.'-'.$s2.'-'.$s3;
			$r['s1'] = $s1;
			$r['s2'] = $s2;
			$r['s3'] = $s3;
			$r['m1'] = isset($r['p1'][$r['s1']]) ? $r['p1'][$r['s1']] : '';
			$r['m2'] = isset($r['p2'][$r['s2']]) ? $r['p2'][$r['s2']] : '';
			$r['m3'] = isset($r['p3'][$r['s3']]) ? $r['p3'][$r['s3']] : '';
			$tags[] = $r;
			$receive = $r['express_name_1'];
			$total = 1;
			$total_amount = $r['price'];
		}
	}
	if($tags) {
		$address = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}address WHERE username='$_username' ORDER BY  listorder ASC,itemid ASC LIMIT 30");
		while($r = $db->fetch_array($result)) {	
			$address[] = $r;
		}
		$user = userinfo($_username);
		$_MOD = cache_read('module-2.php');
		$send_types = explode('|', trim($_MOD['send_types']));
	}
	$head_title = $L['buy_title'];
	include template('buy', $module);
}
?>