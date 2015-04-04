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