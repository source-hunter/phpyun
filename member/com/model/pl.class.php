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
class pl_controller extends company{ 
	function index_action(){
		$urlarr=array("c"=>"pl","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("company_msg","`cuid`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $k=>$v)
			{
				$uid[]=$v['uid'];
			}
			$uid=$this->pylode(",",$uid);
			$user=$this->obj->DB_select_all("resume","`uid` in ($uid)","`uid`,`name`");
			foreach($rows as $k=>$v)
			{
				foreach($user as $val){
					if($v['uid']==$val['uid']){
						$rows[$k]['name']=$val['name'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$com=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		if($com['pl_time'] && $com['pl_time']>time())
		{
			$this->yunset("pl_time",'1');
		}
		$this->obj->update_once("company_msg",array("status"=>(int)$_POST['status']),array("id"=>(int)$_POST['id']));
		$this->yunset("com",$com);
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('pl');
	}
	function save_action(){
		if($_POST['submit']){
			$data['reply']=$_POST['reply'];
			$data['reply_time']=time();
			$where['id']=(int)$_POST['id'];
			$where['cuid']=$this->uid;
			$nid=$this->obj->update_once("company_msg",$data,$where);
 			if($nid){
 				$this->obj->member_log("回复企业评论");
 				$this->obj->ACT_layer_msg("回复成功！",9,"index.php?c=".$_GET['c']);
 			}else{
 				$this->obj->ACT_layer_msg("添加失败！",8,"index.php?c=".$_GET['c']);
 			}
		}
	}
	function plset_action(){
		if($_POST['ajax']=="1" && $_POST['id']){
			$where['uid']=$this->uid;
			$id=$this->obj->update_once("company",array("pl_status"=>(int)$_POST['id']),"`uid`='".$this->uid."' and `pl_time`>'".time()."'");
			if($id){
				$this->obj->member_log("设置企业评论审核状态");
				echo 1;die;
			}else{
				echo 2;die;
			}
		}
		if($_POST['status'] && $_POST['id']){
			$info=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`pl_time`");
			if($info['pl_time']>time())
			{
				$where['id']=(int)$_POST['id'];
				$where['cuid']=$this->uid;
				$nid=$this->obj->update_once("company_msg",array("status"=>(int)$_POST['status']),$where);
				if($nid)
				{
					$this->obj->member_log("审核企业评论");
					echo 1;die;
				}else{
					echo 2;die;
				}
			}else{
				echo 0;die;
			}
		}
	}
	function del_action(){
		if($_GET['id']){
			$info=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`pl_time`");
			if($info['pl_time']<time())
			{
				$this->layer_msg('请先购买企业评论管理功能！',8,0,"index.php?c=pl");
			}
			$del=(int)$_GET['id'];
			$nid=$this->obj->DB_delete_all("company_msg","`id`='".$del."' and `cuid`='".$this->uid."'");
 			if($nid)
 			{
 				$this->obj->member_log("删除企业评论");
 				$this->layer_msg('删除成功！',9,0,"index.php?c=pl");
 			}else{
 				$this->layer_msg('删除失败！',8,0,"index.php?c=pl");
 			}
		}
	}
}
?>