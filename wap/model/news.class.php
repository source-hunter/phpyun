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
class news_controller extends common
{
	function index_action()
	{
		$this->get_moblie();
		$this->yunset("title","职场资讯");
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
		$this->yunset("title","职场资讯");
		$this->yuntpl(array('wap/news_show'));
	}
}
?>