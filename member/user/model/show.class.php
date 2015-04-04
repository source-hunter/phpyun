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
class show_controller extends user
{
	function index_action()
	{
		if($_GET['eid'])
		{
			$urlarr['c']="show";
			$urlarr["page"]="{{page}}";
			$pageurl=$this->url("index","index",$urlarr);
			$this->get_page("resume_show","`uid`='".$this->uid."' and `eid`='".(int)$_GET['eid']."' order by sort desc",$pageurl,"12","`title`,`id`,`picurl`");
			$this->public_action();
			$this->user_tpl('show');
		}else{
			header("location:"."index.php?c=resume");
		}
	}
	function del_action(){
		if($_GET['id']){
			$row=$this->obj->DB_select_once("resume_show","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'","`picurl`");
			if(is_array($row))
			{
				$this->obj->unlink_pic(".".$row['picurl']);
				$oid=$this->obj->DB_delete_all("resume_show","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			}
			if($oid)
			{
				$this->obj->member_log("删除作品案例");
				$this->layer_msg('删除成功！',9);
			}else{
				$this->layer_msg('删除失败！',8);
			}
		}
	}
	function showpic_action(){
		if($_GET['id']){
			$this->public_action();
			$picurl=$this->obj->DB_select_once("resume_show","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'","`picurl`,`title`,`sort`");
			$this->yunset("picurl",$picurl);
			$this->yunset("uid",$this->uid);
			$this->yunset("id",$_GET['id']);
		    $this->yunset("js_def",2);
			$this->user_tpl('editshow');
		}
	}
	function delshow_action(){
		$ids=@explode(',',$_POST['ids']);
		$resume_show=$this->obj->DB_select_all("resume_show","`id` in (".$this->pylode(',',$ids).") and `uid`='".$this->uid."'","`picurl`");
		if(is_array($resume_show) && $resume_show){
			foreach($resume_show as $val){
				$this->obj->unlink_pic(".".$val['picurl']);
			}
			$this->obj->DB_delete_all("resume_show","`id` in (".$this->pylode(',',$ids).") and `uid`='".$this->uid."'","");
			$this->obj->member_log("删除作品案例");
		}
		return true;
	}
	function saveshow_action()
	{
		if($_POST['submitbtn'])
		{
			$pid=$this->pylode(',',$_POST['id']);
			$resume_show=$this->obj->DB_select_all("resume_show","`id` in (".$pid.") and `uid`='".$this->uid."'","`id`");
			if($resume_show&&is_array($resume_show)){
				foreach($resume_show as $val){
					$title=$_POST['title_'.$val['id']];
					$this->obj->update_once("resume_show",array("title"=>trim($title)),array("id"=>(int)$val['id']));
				}
				$this->obj->member_log("添加作品案例");
				$this->obj->ACT_layer_msg("保存成功！",9,"index.php?c=show&eid=".intval($_POST['eid']));
			}else{
				$this->obj->ACT_layer_msg("非法操作！",3,"index.php");
			}
		}else{
			$this->obj->ACT_msg("index.php","非法操作！");
		}
	}
	function addshow_action(){
		$this->public_action();
		$this->user_tpl('addshow');
	}
	function upshow_action(){
       if($_POST['submitbtn']){
       	$time=time();
            unset($_POST['submitbtn']);
	        if(!empty($_FILES['uplocadpic']['tmp_name']))
			{
					$upload=$this->upload_pic("../upload/show/",false);
					$uplocadpic=$upload->picture($_FILES['uplocadpic']);
					$this->picmsg($uplocadpic,$_SERVER['HTTP_REFERER']);
					$uplocadpic = str_replace("../upload/show","./upload/show",$uplocadpic);
	            	$row=$this->obj->DB_select_once("resume_show","`uid`='".(int)$_POST['uid']."' and `id`='".$_POST['id']."'","`picurl`");
					if(is_array($row))
					{
						$this->obj->unlink_pic(".".$row['picurl']);
					}
					$picfield = "`picurl`='".$uplocadpic."',";
			}
			$nid=$this->obj->DB_update_all("resume_show",$picfield."`title`='".$_POST['title']."',`sort`='".$_POST['showsort']."',`ctime`='".$time."'","`uid`='".$this->uid."'and `id`='".$_POST['id']."'");
			if($nid)
			{
				$this->obj->ACT_layer_msg("更新成功！",9,"index.php?c=show&eid=".$_POST['eid']);
			}else{
				$this->obj->ACT_layer_msg("更新失败！",8,"index.php?c=show&eid=".$_POST['eid']);
			}
		}

   }
}
?>