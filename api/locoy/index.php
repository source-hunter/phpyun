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
include(dirname(dirname(dirname(__FILE__)))."/global.php");
if($_GET['m'] && !ereg("^[0-9a-zA-Z\_]*$",$_GET['m'])){
	
	$_GET['m'] = 'index';
}
$model = $_GET['m'];
$action = $_GET['c'];
if($model=="")	$model="index";
if($action=="")	$action = "index";

require(MODEL_PATH.'class/common.php');
require("model/".$model.'.class.php');


$conclass=$model.'_controller';
$actfunc=$action.'_action';

$views=new $conclass($phpyun,$db,$db_config[def],"index");
if(!method_exists($views,$actfunc)){
	echo "�������ҳ�治���ڣ�";die;
}

$views->$actfunc();


?>