<?php
defined('IN_DESTOON') or exit('Access Denied');
$mid = isset($mid) ? intval($mid) : 0;
$mid or exit;
isset($MODULE[$mid]) or exit;
include template('catalog', 'chip');
?>