<?php
include(dirname(dirname(__FILE__))."/global.php");
$var=@explode('-',str_replace('/','-',$_GET['yunurl']));
foreach($var as $p){
	$param=@explode('_',$p);
	$_GET[$param[0]]=$param[1];
}
unset($_GET['yunurl']);
$model = $_GET['m'];
$action = $_GET['c'];
$usertype=$_COOKIE["usertype"];

if($_GET['m'] && !ereg("^[0-9a-zA-Z\_]*$",$_GET['m'])){

	$_GET['m'] = 'index';
}
if($model=="")	$model="index";
if($action=="")	$action = "index";
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

$views->$actfunc();
?>