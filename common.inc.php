<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
define('DT_DEBUG', 0);		// 定义调试模式
if(DT_DEBUG) {		  		// 调试模式
	error_reporting(E_ALL); // 报告所有
	$mtime = explode(' ', microtime());       // microtime()：以 "msec sec" 的格式返回微秒数和时间戳
	$debug_starttime = $mtime[1] + $mtime[0]; // 开始计时时间
} else {
	error_reporting(0);		// 禁止报告错误
}
//当php.ini里面的register_globals=on时，各种变量都被注入代码，抵御网页攻击
if(isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) exit('Request Denied');
@set_magic_quotes_runtime(0);  // 外部引入的(包括数据库或者文件)数据中的溢出字符加上反斜线
$MQG = get_magic_quotes_gpc(); // 取得 PHP环境变量magic_quotes_gpc 的值
foreach(array('_POST', '_GET', '_COOKIE') as $__R) {
	if($$__R) { 
		foreach($$__R as $__k => $__v) { 
			// 删除数组里面键值和键名相同
			if(isset($$__k) && $$__k == $__v) unset($$__k); 
		} 
	}
}
define('IN_DESTOON', true);	// ????
define('IN_ADMIN', defined('DT_ADMIN') ? true : false);                // 是否是管理员
define('DT_ROOT', str_replace("\\", '/', dirname(__FILE__)));          // 站点物理路径
if(defined('DT_REWRITE')) include DT_ROOT.'/include/rewrite.inc.php';
$CFG = array();	// 初始化为空，避免污染，config.inc.php 中读取配置信息
require DT_ROOT.'/config.inc.php';		  // 网站配置文件
define('DT_PATH', $CFG['url']);			  // 站点首页网址
define('DT_STATIC', $CFG['static'] ? $CFG['static'] : $CFG['url']);					// 静态文件地址
define('DT_DOMAIN', $CFG['cookie_domain'] ? substr($CFG['cookie_domain'], 1) : ''); // Cookie作用域
define('DT_WIN', strpos(strtoupper(PHP_OS), 'WIN') !== false ? true: false);		// 是否 WIN系统
define('DT_CHMOD', ($CFG['file_mod'] && !DT_WIN) ? $CFG['file_mod'] : 0);			// 文件(夹)类型，默认0777
define('DT_LANG', $CFG['language']);      // 语言信息
define('DT_KEY', $CFG['authkey']);		  // 安全密钥
define('DT_CHARSET', $CFG['charset']);    // 字符编码
define('DT_CACHE', $CFG['cache_dir'] ? $CFG['cache_dir'] : DT_ROOT.'/file/cache');	// 定义 cache 目录
define('DT_SKIN', DT_STATIC.'skin/'.$CFG['skin'].'/');	// 风格(皮肤)目录
define('VIP', $CFG['com_vip']);			  // VIP名称
define('errmsg', 'Invalid Request');	  // 定义错误信息
$L = array();
include DT_ROOT.'/lang/'.DT_LANG.'/lang.inc.php';// 提示语言				 
require DT_ROOT.'/version.inc.php';				 // 版本信息
require DT_ROOT.'/include/global.func.php';		 // 全局函数
require DT_ROOT.'/include/tag.func.php';		 // 标签(tag)调用功能		
require DT_ROOT.'/api/im.func.php';				 // 在线通讯功能插件
require DT_ROOT.'/api/extend.func.php';			 // 自定义功能函数，建议写在这里
if(!$MQG) {  //是否需要特殊编码转换
	if($_POST) $_POST = daddslashes($_POST);
	if($_GET) $_GET = daddslashes($_GET);
	if($_COOKIE) $_COOKIE = daddslashes($_COOKIE);
}
if(function_exists('date_default_timezone_set')) date_default_timezone_set($CFG['timezone']); // 设置时区
$DT_PRE = $CFG['tb_pre'];						// 数据库表名前缀
// http://www.biuuu.com/index.php?p=222&q=biuuu；则$_SERVER["QUERY_STRING"] = "p=222&q=biuuu"
$DT_QST = addslashes($_SERVER['QUERY_STRING']);	// 对URL传参进行过滤
$DT_TIME = time() + $CFG['timediff'];			// 当前时间，因为后面默认0
$DT_IP = get_env('ip');                         // get_env():获取环境变量
$DT_URL = get_env('url');
$DT_REF = get_env('referer');
$DT_BOT = is_robot();							// 是否是机器人
header("Content-Type:text/html;charset=".DT_CHARSET);			// 输出文件头信息
require DT_ROOT.'/include/db_'.$CFG['database'].'.class.php';	// 加载数据库类文件	
require DT_ROOT.'/include/cache_'.$CFG['cache'].'.class.php';	// 加载缓存类文件
require DT_ROOT.'/include/session_'.$CFG['session'].'.class.php';//加载 session 类文件
require DT_ROOT.'/include/file.func.php';						// 加载文件操作功能文件
if(!empty($_SERVER['REQUEST_URI'])) strip_uri($_SERVER['REQUEST_URI']);// 对URL除根域名外的参数过滤
if($_POST) $_POST = strip_sql($_POST);							// strip_sql():对SQL敏感词过滤
if($_GET) $_GET = strip_sql($_GET);
if($_COOKIE) $_COOKIE = strip_sql($_COOKIE);
if(!IN_ADMIN) {	// 管理员？
	$BANIP = cache_read('banip.php');
	if($BANIP) banip($BANIP);
	$destoon_task = '';
}
if($_POST) extract($_POST, EXTR_SKIP);		// 展开变量 $_POST
if($_GET) extract($_GET, EXTR_SKIP);		// 展开变量 $_GET
$db_class = 'db_'.$CFG['database'];			// 数据库类型
$db = new $db_class;						// 数据库操作类
$db->halt = (DT_DEBUG || IN_ADMIN) ? 1 : 0;	// 是否显示数据库错误信息
$db->pre = $CFG['tb_pre'];					// 数据表前缀
$db->connect($CFG['db_host'], $CFG['db_user'], $CFG['db_pass'], $CFG['db_name'], $CFG['db_expires'], $CFG['db_charset'], $CFG['pconnect']);
$dc = new dcache();							// ????????
$dc->pre = $CFG['cache_pre'];				// 缓存前缀
$DT = $MOD = $EXT = $CSS = $JS = $DTMP = $CAT = $ARE = $AREA = array();// 初始化
$CACHE = cache_read('module.php');			// module 的缓存数据
if(!$CACHE) {	//没有module缓存信息则生成
	require_once DT_ROOT.'/admin/global.func.php';	//全局函数：注意是	require_once()
	require_once DT_ROOT.'/include/post.func.php';	//post function
	require_once DT_ROOT.'/include/cache.func.php';	//cache function
    cache_all();									//生成全部缓存信息
	$CACHE = cache_read('module.php');				//重新读取变量
}
$DT = $CACHE['dt'];					// 缓存中的 dt 数组：设置信息
$MODULE = $CACHE['module'];			// 缓存中的 module 数组：模块信息 
$EXT = cache_read('module-3.php');	// 无(module-3.php)
$lazy = $DT['lazy'] ? 1 : 0;
if(!IN_ADMIN && ($DT['close'] || $DT['defend_cc'] || $DT['defend_reload'] || $DT['defend_proxy'])){
	include DT_ROOT.'/include/defend.inc.php';			// 网站关闭，拒绝访问等设置信息
}
unset($CACHE, $CFG['timezone'], $CFG['db_host'], $CFG['db_user'], $CFG['db_pass'], $db_class, $db_file);
$moduleid = isset($moduleid) ? intval($moduleid) : 1;	// 模块id
if($moduleid > 1) {
	isset($MODULE[$moduleid]) or dheader(DT_PATH);		// 如果模块没开启则跳转首页
	$module = $MODULE[$moduleid]['module'];				// 获得对应 moduleid 的英文名
	$MOD = $moduleid == 3 ? $EXT : cache_read('module-'.$moduleid.'.php');// 读取模块的信息
	include DT_ROOT.'/lang/'.DT_LANG.'/'.$module.'.inc.php';// 加载模块处理类
} else {
	$moduleid = 1;
	$module = 'destoon';
}
$cityid = 0;
$city_name = $L['allcity'];
$city_domain = $city_template = $city_sitename = '';
if($DT['city']) include DT_ROOT.'/include/city.inc.php'; 	// 加载城市处理
($DT['gzip_enable'] && !$_POST && !defined('DT_WAP')) ? ob_start('ob_gzhandler') : ob_start();// 压缩输出缓存的内容
$forward = isset($forward) ? urldecode($forward) : $DT_REF;	// 来源页面
$action = (isset($action) && check_name($action)) ? trim($action) : '';// 检查 action
$submit = isset($_POST['submit']) ? 1 : 0;					// 是否是提交动作
if($submit) {
	isset($captcha) or $captcha = '';
	isset($answer) or $answer = '';
}
$sum = isset($sum) ? intval($sum) : 0;
$page = isset($page) ? max(intval($page), 1) : 1;
$catid = isset($catid) ? intval($catid) : 0;
$areaid = isset($areaid) ? intval($areaid) : 0;
$itemid = isset($itemid) ? (is_array($itemid) ? array_map('intval', $itemid) : intval($itemid)) : 0;
$pagesize = $DT['pagesize'] ? $DT['pagesize'] : 30;		// 每页容量							// 页码偏移量
$offset = ($page-1)*$pagesize;							// 页码偏移量
$kw = isset($_GET['kw']) ? htmlspecialchars(str_replace(array("\'"), array(''), trim(urldecode($_GET['kw'])))) : '';	
// 关键字信息
$keyword = $kw ? str_replace(array(' ', '*'), array('%', '%'), $kw) : '';
$today_endtime = strtotime(date('Y-m-d', $DT_TIME).' 23:59:59');	// 今天的截止时间
$seo_file = $seo_title = $head_title = $head_keywords = $head_description = $head_canonical = '';// seo初始化
if($catid) $CAT = get_cat($catid);		// 分类名称
if($areaid) $ARE = get_area($areaid);	// 地区名称
$_userid = $_admin = $_aid = $_message = $_chat = $_sound = $_online = $_money = $_credit = $_sms = 0;//用户信息初始化
$_username = $_company = $_passport = $_truename = ''; //会员名、公司名等初始化
$_groupid = 3;	
$destoon_auth = get_cookie('auth');
if($destoon_auth) {	//缓存的用户信息
	$_dauth = explode("\t", decrypt($destoon_auth, md5(DT_KEY.$_SERVER['HTTP_USER_AGENT'])));
	$_userid = isset($_dauth[0]) ? intval($_dauth[0]) : 0;
	$_username = isset($_dauth[1]) ? trim($_dauth[1]) : '';
	$_groupid = isset($_dauth[2]) ? intval($_dauth[2]) : 3;
	$_admin = isset($_dauth[4]) ? intval($_dauth[4]) : 0;
	if($_userid && !defined('DT_NONUSER')) {
		$_password = isset($_dauth[3]) ? trim($_dauth[3]) : ''; //缓存的密码
		$USER = $db->get_one("SELECT username,passport,company,truename,password,groupid,email,message,chat,sound,online,sms,credit,money,loginip,admin,aid,edittime,trade FROM {$DT_PRE}member WHERE userid=$_userid");
		if($USER && $USER['password'] == $_password) { 			//匹配缓存的登录信息是否正确
			if($USER['groupid'] == 2) dalert(lang('message->common_forbidden'));
			extract($USER, EXTR_PREFIX_ALL, '');
			if($USER['loginip'] != $DT_IP && ($DT['ip_login'] == 2 || ($DT['ip_login'] == 1 && IN_ADMIN))) {
				$_userid = 0; set_cookie('auth', '');
				dalert(lang('message->common_login', array($USER['loginip'])), DT_PATH);
			}
		} else {
			$_userid = 0;
			if($db->linked && !isset($swfupload) && strpos($_SERVER['HTTP_USER_AGENT'], 'Flash') === false) set_cookie('auth', '');
		}
		unset($destoon_auth, $USER, $_dauth, $_password); //销毁缓存
	}
}
if($_userid == 0) { $_groupid = 3; $_username = ''; }
if(!IN_ADMIN) {
	if($_groupid == 1) include DT_ROOT.'/module/member/admin.inc.php';
	if($_userid && !defined('DT_NONUSER')) {
		$db->query("REPLACE INTO {$DT_PRE}online (userid,username,ip,moduleid,online,lasttime) VALUES ('$_userid','$_username','$DT_IP','$moduleid','$_online','$DT_TIME')");
	} else {
		if(30 == intval(timetodate($DT_TIME, 's'))) {
			$lastime = $DT_TIME - $DT['online'];
			$db->query("DELETE FROM {$DT_PRE}online WHERE lasttime<$lastime");
		}
	}
	if($DT_BOT) {
		if($moduleid == 4) $MOD['order'] = 'userid DESC';
		if($moduleid > 4) $MOD['order'] = 'addtime DESC';
	}
}
$MG = cache_read('group-'.$_groupid.'.php');//加载用户组信息
?>