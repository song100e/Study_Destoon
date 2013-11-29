<?php
require '../../../common.inc.php';
include DT_ROOT.'/api/map/51ditu/config.inc.php';
$map = isset($map) ? $map : '';
preg_match("/^[0-9\.\,]{13,17}$/", $map) or $map = $map_mid;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo DT_CHARSET;?>" />
<title>51ditu - 点击标注位置</title>
<script type="text/javascript">window.onerror=function(){return true;}</script>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
</head>
<body>
<br/><br/><br/><br/><br/><br/><br/>
<center>
<script type="text/javascript" src="http://api.51ditu.com/js/maps.js"></script>
<script type="text/javascript" src="http://api.51ditu.com/js/ezmarker.js"></script>
<script type="text/javascript">
var ez=new LTEZMarker("ezmarker");
var point=new LTPoint(<?php echo $map;?>);
ez.setDefaultView(point, 2);
LTEvent.addListener(ez, 'mark', setMap);
function setMap(point,zoom){
	var xy = point.getLongitude().toString()+','+point.getLatitude().toString();
	window.parent.document.getElementById('map').value = xy;
	window.parent.cDialog();
}
</script>
</center>
</body>
</html>