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
include(dirname(dirname(__FILE__))."/global.php");
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
if(!is_file(MODEL_PATH.$model.'.class.php'))
{
	$controller='index';
	$action='index';
}
require(MODEL_PATH.'class/common.php');
require("model/".$model.'.class.php');
$conclass=$model.'_controller';
$actfunc=$action.'_action';
$views=new $conclass($phpyun,$db,$db_config[def],"index");
if(!method_exists($views,$actfunc)){
	$views->DoException();
}
if($config['sy_ask_web']=="2"){
	header("location:".$config['sy_weburl']."/index.php?m=error");
}
$views->$actfunc();
?>