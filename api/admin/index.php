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
include(dirname(dirname(dirname(__FILE__)))."/global.php");
if($_GET['m'] && !ereg("^[0-9a-zA-Z\_]*$",$_GET['m'])){
	
	$_GET['m'] = 'index';
}

$model = $_GET['m'];
$action = $_GET['c'];
if($model=="")	$model="index";
if($action=="")	$action = "index";
include(LIB_PATH."public.function.php");
require(MODEL_PATH.'class/common.php');
require("model/appadmin.class.php");
require("model/".$model.'.class.php');
if(!UserAgent()){
	echo "请用手机或PC客户端访问该页面！";die;
}
$conclass=$model.'_controller';
$actfunc=$action.'_action';
$views=new $conclass($phpyun,$db,$db_config[def],"appadmin");
if(!method_exists($views,$actfunc)){
	echo "您请求的页面不存在！";die;
}
$views->$actfunc();
?>