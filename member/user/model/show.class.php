<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
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
				$this->obj->member_log("ɾ����Ʒ����");
				$this->layer_msg('ɾ���ɹ���',9);
			}else{
				$this->layer_msg('ɾ��ʧ�ܣ�',8);
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
			$this->obj->member_log("ɾ����Ʒ����");
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
				$this->obj->member_log("�����Ʒ����");
				$this->obj->ACT_layer_msg("����ɹ���",9,"index.php?c=show&eid=".intval($_POST['eid']));
			}else{
				$this->obj->ACT_layer_msg("�Ƿ�������",3,"index.php");
			}
		}else{
			$this->obj->ACT_msg("index.php","�Ƿ�������");
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
				$this->obj->ACT_layer_msg("���³ɹ���",9,"index.php?c=show&eid=".$_POST['eid']);
			}else{
				$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,"index.php?c=show&eid=".$_POST['eid']);
			}
		}

   }
}
?>