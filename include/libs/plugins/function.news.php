<?php
/*
 * 职位调用
 * ----------------------------------------------------------------------------
 * 分别对 职位，对应招聘公司，公司所属企业级别，认证等级等作操作
 *
 * ============================================================================
*/
function smarty_function_news($paramer,&$smarty){
	global $db,$db_config,$config;
	if($paramer[id]!="")
	{
		$id = $paramer[id];
		$news=$db->select_alls("news_base","news_content","a.`id`='$id' and a.`id`=b.`nbid`");
	}else{
		$gonggao=$db->DB_select_once("admin_announcement","`id`='".$paramer[nid]."'");//公告
	}
	$smarty->assign("$paramer[assign_name]",array("news"=>$news[0],"gonggao"=>$gonggao));
}