<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */ 
include(dirname(__FILE__)."/global.php");
if($config['sy_wap_jump']=='1'){
	$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((preg_match($uachar, $ua))&& !strpos(strtolower($_SERVER['REQUEST_URI']),'wap')){		
		$Loaction = $config['sy_weburl'].'/wap/'; 
		if (!empty($Loaction)){
		   header("Location: $Loaction\n");exit;
		}
	}
}

if($config['webcache']=="1"){
	include_once(LIB_PATH."web.cache.php");
	$cache=new Phpyun_Cache('./cache',dirname(__FILE__)."/",$config['webcachetime']);
	$cache->read_cache();
}
//α��̬����statr
$var=@explode('-',str_replace('/','-',$_GET['yunurl']));
foreach($var as $p){
	$param=@explode('_',$p);
	$_GET[$param[0]]=$param[1];
}
unset($_GET['yunurl']);
//α��̬����end
//model����
//action����

if($_GET['m'] && !ereg("^[0-9a-zA-Z\_]*$",$_GET['m'])){

	$_GET['m'] = 'index';
}
$model = $_GET['m'];
$action = $_GET['c'];
if($model=="")	$model="index";
if($action=="")	$action = "index";
//Program
if(!is_file(MODEL_PATH.$model.'.class.php')){
	$controller='index';
	$action='index';
}
require(MODEL_PATH.'class/common.php');
require("model/".$model.'.class.php');
$conclass=$model.'_controller';
$actfunc=$action.'_action'; 

$views=new $conclass($phpyun,$db,$db_config["def"],"index",$model);

if(!method_exists($views,$actfunc)){
	$views->DoException();
}

$views->$actfunc();

if($config['webcache']=="1"){
	$cache->CacheCreate();
}
?>