<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/module/'.$module.'/cart.class.php';
include load('misc.lang');
$do = new cart();
$cart = $do->get();
$max_cart = $MOD['max_cart'];
if($action == 'ajax') {
	$itemid or exit('-1');
	$s1 = isset($s1) ? intval($s1) : 0;
	$s2 = isset($s2) ? intval($s2) : 0;
	$s3 = isset($s3) ? intval($s3) : 0;
	$key = $itemid.'-'.$s1.'-'.$s2.'-'.$s3;
	if(strpos($cart.',', ','.$key.',') !== false) exit('-2');
	$item = $db->get_one("SELECT itemid,status,username FROM {$table} WHERE itemid=$itemid");
	if($item && $item['status'] == 3) {
		if($item['username'] != $_username) {
			$cart = ','.$key.$cart;
			$do->set($cart);
			exit('1');
		} else {
			exit('-4');
		}
	} else {
		exit('-3');
	}
} else if($action == 'clear') {
	$key = isset($key) ? trim($key) : '';
	if(preg_match("/^[0-9]{1,}-[0-9]{1,}-[0-9]{1,}-[0-9]{1,}$/", $key)) {
		$cart = str_replace(','.$key.',', ',', $cart.',');
		if(substr($cart, -1) == ',') $cart = substr($cart, 0, -1);
		$do->set($cart);
		if(isset($ajax)) exit('1');
		message('', 'cart.php?tm='.$DT_TIME);
	} else {		
		$do->del();
		message('', 'cart.php?tm='.$DT_TIME);
	}
} else {
	$reset = false;
	if($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $id) {
				$id = intval($id);
				$id = $id.'-0-0-0';
				if(strpos($cart.',', ','.$id.',') === false) {
					$reset = true;
					$cart = ','.$id.$cart;
				}
			}
		} else {
			$s1 = isset($s1) ? intval($s1) : 0;
			$s2 = isset($s2) ? intval($s2) : 0;
			$s3 = isset($s3) ? intval($s3) : 0;
			$itemid = $itemid.'-'.$s1.'-'.$s2.'-'.$s3;
			if(strpos($cart.',', ','.$itemid.',') === false) {
				$reset = true;
				$cart = ','.$itemid.$cart;
			}
		}
	}
	while(substr_count($cart, ',') > $max_cart) {
		$reset = true;
		$cart = substr($cart, 0, -strlen(strrchr($cart, ',')));
	}
	if($reset) $do->set($cart);
	$ids = $cart ? substr($cart, 1) : '';
	$_itemids = $itemids = '';
	$tags = $_tags = $datas = $_ids = array();
	$total = $price = 0;
	if($ids) {
		foreach(explode(',', $ids) as $v1) {
			$t1 = array_map('intval', explode('-', $v1));
			$_ids[$t1[0]] = $t1[0];
			$t2 = array();
			$t2['itemid'] = $t1[0];
			$t2['s1'] = $t1[1];
			$t2['s2'] = $t1[2];
			$t2['s3'] = $t1[3];
			$datas[$v1] = $t2;
		}
	}
	if($_ids) {
		$itemids = implode(',', $_ids);
		$result = $db->query("SELECT * FROM {$table} WHERE itemid IN ($itemids)");
		while($r = $db->fetch_array($result)) {
			if($r['username'] == $_username || $r['status'] != 3) continue;
			$r['alt'] = $r['title'];
			$r['title'] = dsubstr($r['title'], 40, '..');
			$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
			$r['p1'] = get_nv($r['n1'], $r['v1']);
			$r['p2'] = get_nv($r['n2'], $r['v2']);
			$r['p3'] = get_nv($r['n3'], $r['v3']);
			$price += $r['price'];
			$total++;			
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
				$tags[] = $t;
				$_itemids .= ','.implode('-', $v);
			}
		}
		if($_itemids != $cart) {
			$cart = $_itemids;
			$itemids = $cart ? substr($cart, 1) : '';
			$do->set($cart);
		}
	}
	$head_title = $L['cart_title'].$DT['seo_delimiter'].$MOD['name'];
	include template('cart', $module);
}
?>