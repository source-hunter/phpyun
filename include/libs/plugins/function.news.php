<?php
/*
 * ְλ����
 * ----------------------------------------------------------------------------
 * �ֱ�� ְλ����Ӧ��Ƹ��˾����˾������ҵ������֤�ȼ���������
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
		$gonggao=$db->DB_select_once("admin_announcement","`id`='".$paramer[nid]."'");//����
	}
	$smarty->assign("$paramer[assign_name]",array("news"=>$news[0],"gonggao"=>$gonggao));
}