<?php
defined('IN_DESTOON') or exit('Access Denied');
include DT_ROOT.'/api/map/51ditu/config.inc.php';
preg_match("/^[0-9\.\,]{13,17}$/", $map) or $map = $map_mid;
?>
<tr>
<td class="tl">公司地图标注</td>
<td class="tr">
<input type="text" name="setting[map]" id="map" value="<?php echo $map;?>" readonly size="50" onclick="MapMark();"/>&nbsp;&nbsp;
<a href="javascript:MapMark();" class="t">标注</a>&nbsp;|&nbsp;<a href="javascript:DelMark();" class="t">清空</a>
<script type="text/javascript">
function MapMark() {
	Dwidget(DTPath+'api/map/51ditu/mark.php?map='+Dd('map').value, '地图标注');
}
function DelMark() {
	Dd('map').value='';
}
</script>
</td>
</tr>