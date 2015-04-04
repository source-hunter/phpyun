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
class admin_reason_controller extends common{
	function index_action(){
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$reason=$this->get_page("reason","1",$pageurl,$this->config['sy_listnum']);
		$this->yunset("reason",$reason);
		$this->yuntpl(array('admin/admin_reason_list'));
	}
	function add_action(){
		$reason_show=$this->obj->DB_select_once("reason","id='".$_GET['id']."'");
		$this->yunset("reason_show",$reason_show);
		$this->index_action();
	}

	function save_action(){
		$this->check_token();
		$url="index.php?m=admin_reason";
		if($_GET['id']){
			$nbid=$this->obj->DB_update_all("reason","`name`='".trim($_GET['name'])."'","`id`='".$_GET['id']."'");
			isset($nbid)?$this->obj->ACT_layer_msg("举报原因(ID:".$_GET['id'].")更新成功！",9,$url,2,1):$this->obj->ACT_layer_msg("更新失败！",8,$url);
		}else{
			$nbid=$this->obj->DB_insert_once("reason","`name`='".trim($_GET['name'])."'");
			isset($nbid)?$this->obj->ACT_layer_msg("举报原因(ID:".$nbid.")添加成功！",9,$url,2,1):$this->obj->ACT_layer_msg("添加失败！",8,$url);
		}
	}

	function del_action(){
		$this->check_token();
		if($_GET['del']){
	    	if($_GET['del']){
				$a_del=@implode(',',$_GET['del']);
				$this->obj->DB_delete_all("reason","`id` in(".$a_del.")","");
				$this->layer_msg('举报原因(ID:'.$a_del.')删除成功！',9,1,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('请选择您要删除的数据！',8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("reason", "`id`='".$_GET['id']."'");
 			isset($result)?$this->layer_msg("举报原因(ID:".$_GET['id']."')删除成功！",9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('非法操作！',3,1,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>