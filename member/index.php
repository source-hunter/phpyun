<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
include(dirname(dirname(__FILE__))."/global.php");
if($_GET['c'] && !ereg("^[0-9a-zA-Z\_]*$",$_GET['c'])){
	$_GET['c'] = 'index';
}
$model = $_GET['c'];
$action = $_GET['act'];
if($model=="")	$model="index";
if($action=="")	$action = "index";

$usertype=$_COOKIE["usertype"];
if($usertype==1){
	$type="user";
}else if($usertype==2){
	$type="com";
}else{
	header('Location: '.$config['sy_weburl']);
}
if($_GET['c']=="subject_add" && $usertype!="4"){
	header('Location: '.$config['sy_weburl']);
}
require(MODEL_PATH.'class/common.php');
require($type."/".$type.'.class.php');
require($type."/model/".$model.'.class.php');

$conclass=$model.'_controller';
$actfunc=$action.'_action';

$views=new $conclass($phpyun,$db,$db_config["def"],"member");
if(!method_exists($views,$actfunc)){
	$views->DoException();
}

$views->$actfunc();
?>