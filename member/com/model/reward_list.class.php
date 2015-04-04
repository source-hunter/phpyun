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
class reward_list_controller extends company
{
	function index_action()
	{
		$this->public_action();
		$urlarr['c']='reward_list';
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("change","$where `uid`='".$this->uid."' order by id desc",$pageurl,"13");
		$this->yunset("js_def",4);
		$this->com_tpl('reward_list');
	}

 function del_action(){
		if($_COOKIE['usertype']!='2' || $this->uid==''){
			$this->layer_msg('非法操作！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$rows=$this->obj->DB_select_once("change","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' ");
			$this->obj->DB_update_all("reward","`stock`=`stock`+".$rows['num']."","`id`='".$rows['gid']."'");
			$this->obj->DB_update_all("company_statis","`integral`=`integral`+".$rows['integral']."","`uid`='".$this->uid."'");
			$this->obj->DB_delete_all("change","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' ");
			$this->obj->member_log("取消兑换");
			$this->layer_msg('取消成功！',9,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>