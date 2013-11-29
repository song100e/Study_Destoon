<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class dcache {
	var $pre;
	var $obj;

    function dcache() {
		$this->obj = new Memcache;
		include DT_ROOT.'/file/config/memcache.inc.php';
		$num = count($MemServer);
		if($num == 1) {
			$key = 0;
		} else {
			$key = get_cookie('memcache');
			if($key == -1) {
				$key = 0;
			} else if(!isset($MemServer[$key])) {
				$key = array_rand($MemServer);
				set_cookie('memcache', $key ? $key : -1);
			}
		}
		$this->obj->connect($MemServer[$key]['host'], $MemServer[$key]['port'], 2);
    }

	function get($key) {
        return $this->obj->get($this->pre.$key);
    }

    function set($key, $val, $ttl = 600) {
         return $this->obj->set($this->pre.$key, $val, 0, $ttl);
    }

    function rm($key) {
        return $this->obj->delete($this->pre.$key);
    }

    function clear() {
        return $this->obj->flush();
    }

	function expire() {
		return true;
	}
}
?>