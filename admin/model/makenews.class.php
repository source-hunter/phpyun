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
class makenews_controller extends common
{
	function index_action(){
		$this->yunset("type","news");
		$this->yuntpl(array('admin/admin_makenews'));
	}
	
	function makecache_action(){
		extract($_POST);
	}

}
?>