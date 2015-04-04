<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
//======================== 系统常量 ========================\\

error_reporting(0);
define('APP_PATH',dirname(__FILE__)."/");
define('CONFIG_PATH',APP_PATH.'/data/');
define('LIB_PATH',APP_PATH.'/include/');
define('MODEL_PATH',APP_PATH.'/model/');
define('PLUS_PATH',APP_PATH.'/plus/');
define('ALL_PS','conn');

ini_set('session.gc_maxlifetime',900);
ini_set('session.gc_probability',10);
ini_set('session.gc_divisor',100);

session_start();
include(CONFIG_PATH."db.config.php");
include_once(PLUS_PATH."config.php");
include(CONFIG_PATH."db.safety.php");

$starttime=time();
define('DEF_DATA', $db_config['def']);

unset ($_ENV, $HTTP_ENV_VARS, $_REQUEST, $HTTP_POST_VARS, $HTTP_GET_VARS);
$_COOKIE = (is_array($_COOKIE)) ? $_COOKIE : $HTTP_COOKIE_VARS;

header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
header('Content-Type: text/html; charset=' . $db_config['charset']);
header("Cache-control: private");
@ ob_start("ob_gzhandler");
date_default_timezone_set($db_config['timezone']);
include_once(LIB_PATH."mysql.class.php");

$city_ABC = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");


include_once(LIB_PATH.'libs/Smarty.class.php');

$phpyun = new smarty();
$phpyun->template_dir = APP_PATH.'/template/';
$phpyun->compile_dir  = APP_PATH.'/templates_c/';
$phpyun->cache_dir    = APP_PATH.'/cache/';
$phpyun->left_delimiter = "{yun:}";
$phpyun->right_delimiter = "{/yun}";
$phpyun->get_install();

if(is_file(LIB_PATH.'webscan360/360safe/360webscan.php')){
	require_once(LIB_PATH.'webscan360/360safe/360webscan.php');
}

$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset'],$db_config["def"]);
include_once(MODEL_PATH."domain.class.php");

include(LIB_PATH."public.url.php");
include(PLUS_PATH."seo.cache.php");
?>