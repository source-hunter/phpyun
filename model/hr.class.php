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
