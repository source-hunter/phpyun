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
function totime($params){
	extract($params);
	if($type==""){
		$type="Y-m-d";
	}
	return date($type,$time);
}
function sublen($params){
	extract($params);
	if($html==""){
	 	$str=strip_tags(html_entity_decode(str_replace(array("&amp;","&nbsp;"," "),array("&","",""),$str),ENT_QUOTES,"GB2312"));
	}
	$length=$length?$length:20;
	$charset=$charset?$charset:"gbk";
	return iconv_substr($str,0,$length,$charset);
}
function htmlentitydecode($params){
	extract($params);
	$str=str_replace(array("&amp;nbsp;"," "),array("",""),$str);
	return html_entity_decode($str,ENT_QUOTES,"GB2312");
}
function seacrh_url($params){
	extract($params);
	$url=$_GET;
	
	$return_url="index.php?";
	
	if($m){
      $return_url_new[]="m=".$m;
	}
	foreach($params as $key=>$va){
		if($key!="m" && $key!="untype" && $key!="thisdir" && $key!="adt"  && $key!="adv"){
			$return_url_new[]=$key."=".$va;
		}
	}
	
	unset($url['m']);
	$untype=@explode(",",$untype);
	foreach($url as $key=>$va){
		if($va!="" && !in_array($key,$untype)){
			$return_url_new[]=$key."=".$va;
		}
	}
	if($params['adt']){
		$return_url_new[]=$params['adt']."=".$params['adv'];
	}
	$return_url=$return_url.@implode('&',$return_url_new);
	return $return_url;
}
?>