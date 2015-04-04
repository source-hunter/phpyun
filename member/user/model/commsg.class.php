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
class commsg_controller extends user{
	function index_action()
	{
		$this->public_action();
		$urlarr=array("c"=>"commsg","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("msg","`uid`='".$this->uid."' order by id desc",$pageurl,"10");
		$this->obj->DB_update_all("msg","`user_remind_status`='1'","`uid`='".$this->uid."' and `user_remind_status`='0'");
		$this->unset_remind("usermsg",'1');
		$this->user_tpl('commsg');
	}
	function del_action(){
		$del=(int)$_GET['id'];
		$nid=$this->obj->DB_delete_all("msg","`id`='".$del."' and `uid`='".$this->uid."'");
		if($nid){
			$this->obj->member_log("删除求职咨询");
			$this->layer_msg('删除成功！',9,0,"index.php?c=commsg");
		}else{
			$this->layer_msg('删除失败！',8,0,"index.php?c=commsg");
		}
	}
}
?>