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
class userclass_controller extends common
{
	
	function index_action(){
		$position=$this->obj->DB_select_all("userclass","`keyid`='0'");
		$this->yunset("position",$position);
		$this->yuntpl(array('admin/admin_userclass'));
	}
	
	function save_action(){
		if(!is_array($this->obj->DB_select_once("userclass","`name`='".$_POST['position']."'"))){
			$value="`name`='".$this->stringfilter(trim($_POST['position']))."',";
			if((int)$_POST['sort']==""){
				$_POST['sort']="0";
			}
			$value.="`sort`='".trim($_POST['sort'])."',";
			if($_POST['ctype']=='1'){
				$value.="`variable`='".trim($_POST['variable'])."'";
			}else{
				$value.="`keyid`='".$_POST['nid']."'";
			}
			$add=$this->obj->DB_insert_once("userclass",$value);
			$this->cache_action();
			$add?$msg=2:$msg=3;
			$this->obj->admin_log("���˻�Ա����(ID:".$add.")��ӳɹ�");
		}else{
			$msg=1;
		}
		echo $msg;die;
	}
	
	function up_action(){
		
		if($_GET['id']){
			$id=$_GET['id'];
			$class1=$this->obj->DB_select_once("userclass","`id`='".$_GET['id']."'");
			$class2=$this->obj->DB_select_all("userclass","`keyid`='".$_GET['id']."'");
			$this->yunset("id",$id);
			$this->yunset("class1",$class1);
			$this->yunset("class2",$class2);
		}
		$position=$this->obj->DB_select_all("userclass","`keyid`='0'");
		$this->yunset("position",$position);
		$this->yuntpl(array('admin/admin_userclass'));
	}
	
	function upp_action(){
		if($_POST['update']){
			if(!empty($_POST['position'])){
				if(preg_match("/[^\d-., ]/",$_POST['sort'])){
					$this->obj->ACT_layer_msg("����ȷ��д�����������֣�",8,$_SERVER['HTTP_REFERER']);
				}else{
					$value="`name`='".$this->stringfilter(trim($_POST['position']))."'";
					if($_POST['sort']){
						$value.=",`sort`='".$_POST['sort']."'";
					}
					$where="`id`='".$_POST['id']."'";
					$up=$this->obj->DB_update_all("userclass",$value,$where);
					$this->cache_action();
					$up?$this->obj->ACT_layer_msg("���˻�Ա����(ID:".$_POST['id'].")���³ɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ����������ԣ�",8,$_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->obj->ACT_layer_msg("����ȷ��д��Ҫ���µķ��࣡",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	function del_action()
	{
		if($_GET['delid'])
		{
			$this->check_token();
			$id=$this->obj->DB_delete_all("userclass","`id`='".$_GET['delid']."' or `keyid`='".$_GET['delid']."'","");
			$this->cache_action();
		    isset($id)?$this->layer_msg('���˻�Ա����ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['del'])
		{
			$del=@implode(",",$_POST['del']);
			$id=$this->obj->DB_delete_all("userclass","`id` in (".$del.") or `keyid` in (".$del.")","");
			$this->cache_action();
			isset($id)?$this->layer_msg('���˻�Ա����ɾ���ɹ���',9,1,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,1,$_SERVER['HTTP_REFERER']);
		}
	}
	function cache_action()
	{
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->user_cache("user.cache.php");
	}
	function ajax_action(){
		if($_POST['sort'])
		{
			$this->obj->DB_update_all("userclass","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("���˻�Ա����(ID:".$_POST['id'].")�����޸ĳɹ�");
		}
		if($_POST['name'])
		{
			$_POST['name']=$this->stringfilter($_POST['name']);
			$this->obj->DB_update_all("userclass","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("���˻�Ա����(ID:".$_POST['id'].")�����޸ĳɹ�");
		}
		$this->cache_action();echo '1';die;
	}
}
?>