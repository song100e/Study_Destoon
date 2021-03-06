<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$menus = array (
array('信息统计', '?file='.$file),
);
switch($action) {
	case 'js':
		$db->halt = 0;
		$today = strtotime(timetodate($DT_TIME, 3).' 00:00:00');
		//
		//待受理客服中心
		$num = $db->count($DT_PRE.'ask', "status=0", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("ask").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'finance_charge', "status=0", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("charge").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'finance_cash', "status=0", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("cash").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'mall_order', "status=5", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("trade").innerHTML="'.$num.'";}catch(e){}';

		//待审核贸易提醒

		$num = $db->count($DT_PRE.'alert', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("alert").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'guestbook', "edittime=0", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("guestbook").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'comment', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("comment").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'link', "status=2 AND username=''", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("link").innerHTML="'.$num.'";}catch(e){}';

		//待审核公司新闻
		$num = $db->count($DT_PRE.'news', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("news").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'honor', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("honor").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'link', "status=2 AND username<>''", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("comlink").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'keyword', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("keyword").innerHTML="'.$num.'";}catch(e){}';


		//待审核实名认证
		$num = $db->count($DT_PRE.'validate', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("validate").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'ad', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("ad").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'spread', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("spread").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'know_answer', "status=2", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("answer").innerHTML="'.$num.'";}catch(e){}';

		//会员
		$num = $db->count($DT_PRE.'member');
		echo 'try{document.getElementById("member").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'upgrade', "status=2");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("member_vip").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'member', "groupid=4", 60);
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("member_check").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'member', "regtime>$today", 60);
		echo 'try{document.getElementById("member_new").innerHTML="'.$num.'";}catch(e){}';

		foreach($MODULE as $m) {
			if($m['moduleid'] < 5 || $m['islink']) continue;
			$table = get_table($m['moduleid']);
			//ALL
			$num = $db->count($table, '1', 60);
			echo 'try{Dd("m_'.$m['moduleid'].'").innerHTML="'.$num.'";}catch(e){}';
			//PUB
			$num = $db->count($table, "status=3", 60);
			echo 'try{Dd("m_'.$m['moduleid'].'_1").innerHTML="'.$num.'";}catch(e){}';
			//CHECK
			$num = $db->count($table, "status=2", 60);
			$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
			echo 'try{Dd("m_'.$m['moduleid'].'_2").innerHTML="'.$num.'";}catch(e){}';
			//NEW
			$num = $db->count($table, "addtime>$today", 30);
			echo 'try{Dd("m_'.$m['moduleid'].'_3").innerHTML="'.$num.'";}catch(e){}';

			if($m['moduleid'] == 9) {
				$table = $DT_PRE.'resume';
				//ALL
				$num = $db->count($table, '1', 60);
				echo 'try{Dd("m_resume").innerHTML="'.$num.'";}catch(e){}';
				//PUB
				$num = $db->count($table, "status=3", 60);
				echo 'try{Dd("m_resume_1").innerHTML="'.$num.'";}catch(e){}';
				//CHECK
				$num = $db->count($table, "status=2", 60);
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{Dd("m_resume_2").innerHTML="'.$num.'";}catch(e){}';
				//NEW
				$num = $db->count($table, "addtime>$today", 30);
				echo 'try{Dd("m_resume_3").innerHTML="'.$num.'";}catch(e){}';
			}
		}
	break;
	default:
		$year = isset($year) ? intval($year) : date('Y', $DT_TIME);
		$year or $year = date('Y', $DT_TIME);
		$month = isset($month) ? intval($month) : 0;
		$mid = isset($mid) ? intval($mid) : 0;
		if($mid == 1 || $mid == 3) $mid = 0;
		if($mid == 4) $mid = 2;
		include tpl('count');
	break;
}
?>