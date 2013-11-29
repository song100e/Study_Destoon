<?php
defined('IN_DESTOON') or exit('Access Denied');
isset($name) or $name = '';
if($name) $name = convert($name, 'UTF-8', DT_CHARSET);
$limit = $DT['schcate_limit'] ? intval($DT['schcate_limit']) : 10;
$table = get_table($moduleid);
$key = in_array($moduleid, array(5, 6)) ? 'tag' : 'keyword';
$html = '';
$result = $db->query("SELECT DISTINCT catid FROM {$table} WHERE `{$key}` LIKE '%$name%' ORDER BY addtime DESC LIMIT $limit");
while($r = $db->fetch_array($result)) {
	$html .= '<input type="radio" name="dtcate" value="'.$r['catid'].'" onclick="load_category('.$r['catid'].', 1);" id="dtcate_'.$r['catid'].'"/> <label for="dtcate_'.$r['catid'].'">'.strip_tags(cat_pos(get_cat($r['catid']))).'</label><br/>';
}
echo $html;
?>