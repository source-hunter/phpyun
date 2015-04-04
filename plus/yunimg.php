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
include_once("../global.php");
include_once("../plus/pimg_cache.php");
if($_GET['ad_id']&& $_GET['classid']){
	$ad_id = "ad_".$_GET['ad_id'];
	$ad_class_id = intval($_GET['classid']);
	if($ad_label[$ad_class_id][$ad_id]['did']=="0" || stripos($ad_label[$ad_class_id][$ad_id]['did'],$_SESSION['did'])!==false){
		$ad_info = $ad_label[$ad_class_id][$ad_id]['html'];
		$ad_info=str_replace("\n","",$ad_info);
		$ad_info=str_replace("\r","",$ad_info);
		$ad_info=str_replace("'","\'",$ad_info);
		echo "document.write('$ad_info');";
	}
}
?>