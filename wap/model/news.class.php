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
class news_controller extends common
{
	function index_action()
	{
		$this->get_moblie();
		$this->yunset("title","ְ����Ѷ");
		$this->yuntpl(array('wap/news'));
	}
	function show_action()
	{
		if($_GET['id']){
    		$this->obj->DB_update_all("news_base","`hits`=`hits`+1","`id`='".(int)$_GET['id']."'");
    	}
		$this->get_moblie();
		$id=(int)$_GET[id];
		$row=$this->obj->DB_select_alls("news_base","news_content","a.id=b.nbid and a.id='".$id."'");
		$this->yunset("row",$row[0]);
		$this->yunset("title","ְ����Ѷ");
		$this->yuntpl(array('wap/news_show'));
	}
}
?>