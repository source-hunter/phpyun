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
class hr_controller extends common
{
	function index_action()
	{
		if($this->config['sy_gjx_web']=="2")
		{
			header("location:".$this->url("index","error"));
		}
		$this->seo("hrindex");
		$this->yun_tpl(array('index'));
	}
	function list_action()
	{
		if($this->config['sy_gjx_web']=="2")
		{
			header("location:".$this->url("index","error"));
		}
		$this->yunset("keyword",$_GET['key']);
		$this->yunset("id",$_GET['id']);
		$class=$this->obj->DB_select_once("toolbox_class","`id`='".(int)$_GET['id']."'");
		$this->yunset("class",$class);
		$data['hr_class']=$class['name'];
		$this->data=$data;
		$this->seo("hrlist");
		$this->yun_tpl(array('list'));
	}
	function ajax_action()
	{
		if($_POST['id'])
		{
			$this->obj->DB_update_all("toolbox_doc","`downnum`=`downnum`+1","`id`='".(int)$_POST['id']."'");
			$row=$this->obj->DB_select_once("toolbox_doc","`id`='".(int)$_POST['id']."'");
			echo $this->config['sy_weburl'].$row['url'];die;
		}
	}
}
