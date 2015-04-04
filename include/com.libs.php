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
function getComJob($params){
	global $views;
	global $phpyun,$config;
	$limit=(int)$params['limit'];
	$limit=$limit?$limit:10;
	$order=$order?$order:"id desc";
	$where=$where?$where:1;
	if($params['status']==""){
		$where.=" and `state`='1'";
	}else{
		$where.=" and `state`='".$params['status']."'";
	}
	$where.=" and `uid`='".$_GET['id']."'";
	$rows=$views->obj->DB_select_all("company_job",$where." and `r_status`<>'2' and `status`<>'2' order by $order limit $limit");
	$phpyun->_tpl_vars["job"]=$rows;

	return;
}

function getComJobPage($params){
	global $views;
	global $phpyun,$config;

	$limit=(int)$limit;
	$limit=$limit?$limit:10;
	$order=$order?$order:"id desc";
	$where=$where?$where:1;
	$where.=" and `r_status`<>'2' and `status`<>'1'";
	$time=time();
	$where.=" and `edate`>'$time'";
	if($params['status']==""){
		$where.=" and `state`='1'";
	}else{
		$where.=" and `state`='".$params['status']."'";
	}
	$where.=" and `uid`='".$_GET['id']."'";
	if($_GET['m']==""){
		$_GET['m']='index';
	}

	$pageurl=$views->curl(array("url"=>"id:".$_GET['id'].",tp:".$_GET['tp'].",page:{{page}}"));
	$rows = $views->get_page("company_job",$where." order by ".$order,$pageurl,$params['limit']);
	include(PLUS_PATH."city.cache.php");
	include(PLUS_PATH."com.cache.php");
	if(is_array($rows)){
		foreach($rows as $k=>$v){
			$rows[$k]['province']=$city_name[$v['provinceid']];
			$rows[$k]['city']=$city_name[$v['cityid']];
			$rows[$k]['nums']=$comclass_name[$v['number']];
		}
	}
	$phpyun->_tpl_vars["jobpage"]=$rows;
	return;
}


function getComShowPage($params){
	global $views;
	global $phpyun,$config;
	$limit=(int)$limit;
	$limit=$limit?$limit:10;
	$order=$order?$order:"id desc";
	$where=$where?$where:1;
	$where.=" and `uid`='".$_GET['id']."'";
	$pageurl=$views->curl(array("url"=>"id:".$_GET['id'].",tp:".$_GET['tp'].",page:{{page}}"));
	$rows = $views->get_page("company_show",$where." order by ".$order,$pageurl,$limit);
	$phpyun->_tpl_vars["showpage"]=$rows;
	$phpyun->_tpl_vars["jobpage"]=$rows;
	return;
}

function getComNewsPage($params){
	global $views;
	global $phpyun,$config;
	$limit=(int)$limit;
	$limit=$limit?$limit:10;
	$order=$order?$order:"id desc";
	$where=$where?$where:1;
	$status=$params['status'];
	if($status!=2){
		$where.=!empty($status)?" and `status`='".$status."'":" and `status`='1'";
	}
	$where.=" and `uid`='".$_GET['id']."'";
	$pageurl=$views->curl(array("url"=>"id:".$_GET['id'].",tp:".$_GET['tp'].",page:{{page}}"));
	$rows = $views->get_page("company_news",$where." order by ".$order,$pageurl,$limit);
	$phpyun->_tpl_vars["newspage"]=$rows;
	return;
}

function getComProductPage($params){
	global $views;
	global $phpyun,$config;
	$limit=(int)$limit;
	$limit=$limit?$limit:10;
	$order=$order?$order:"id desc";
	$where=$where?$where:1;
	$status=$params['status'];
	if($status!=2){
		$where.=!empty($status)?" and `status`='".$status."'":" and `status`='1'";
	}
	$where.=" and `uid`='".$_GET['id']."'";
	$pageurl=$views->curl(array("url"=>"id:".$_GET['id'].",tp:".$_GET['tp'].",page:{{page}}"));
	$rows = $views->get_page("company_product",$where." order by ".$order,$pageurl,$limit);
	$phpyun->_tpl_vars["productpage"]=$rows;
	return;
}

function getComShow($params){
	global $views;
	global $phpyun;
	$rows=$views->obj->DB_select_all("company_show","`uid`='".$_GET['id']."'");
	$phpyun->_tpl_vars["show"]=$rows;
	return;
}
function getComNews($params){
	global $views,$phpyun,$config;
	$rows=$views->obj->DB_select_all("company_news","`status`='1' and `uid`='".$_GET['id']."'");
	$phpyun->_tpl_vars["news"]=$rows;
	return;
}
function getComProduct($params){
	global $views,$phpyun,$config;
	$rows=$views->obj->DB_select_all("company_product","`status`='1' and `uid`='".$_GET['id']."'");
	$phpyun->_tpl_vars["product"]=$rows;
	return;
}
?>