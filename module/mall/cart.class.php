<?php 
defined('IN_DESTOON') or exit('Access Denied');
class cart {
	var $db;
	var $table;
	var $userid;
	var $time;

    function cart() {
		global $db, $table_cart, $DT_TIME, $_userid;
		$this->userid = $_userid;
		$this->time = $DT_TIME;
		$this->table = $table_cart;
		$this->db = &$db;
    }

	function set($var) {
		set_cookie('cart', $var, $this->time + 30*86400);
		if($this->userid) $this->db->query("REPLACE INTO {$this->table} (userid,data,edittime) VALUES ('$this->userid', '".addslashes($var)."', '$this->time')");
	}

	function get() {
		$cart1 = get_cookie('cart');
		if($this->userid) {
			$t = $this->db->get_one("SELECT data FROM {$this->table} WHERE userid=$this->userid");
			$cart2 = $t ? $t['data'] : '';
			if($cart1 != $cart2) {
				$cart1 = $cart2;
				$this->set($cart1);
			}
		}
		return $cart1;
	}

	function del() {
		set_cookie('cart', '', $this->time + 30*86400);
		if($this->userid) $this->db->query("DELETE FROM {$this->table} WHERE userid=$this->userid");
	}
}
?>