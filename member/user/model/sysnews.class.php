<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class sysnews_controller extends user
{
	function index_action()
	{
		$where.= "`fa_uid`='".$this->uid."' order by id desc";
		$urlarr["c"] = $_GET["c"];
		$urlarr["page"] = "{{page}}";
		$pageurl = $this->url("index","index",$urlarr);
		$rows=$this->get_page("sysmsg",$where,$pageurl,"10");
		$this->obj->DB_update_all("sysmsg","`remind_status`='1'","`fa_uid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("sysmsg1",'1'); 
		$this->public_action();
		$this->user_tpl('sysnews');
	}
	function del_action()
	{
		$nid = $this->obj->DB_delete_all("sysmsg","`id`='".(int)$_GET['id']."' and `fa_uid`='".$this->uid."'");
		if($nid){
			$this->unset_remind("sysmsg1",'1'); 
			$this->obj->member_log("删除系统消息"); 
			$this->layer_msg('删除成功！',9);
		}else{
			$this->layer_msg('删除失败！',8);
		}
	}
}
?>