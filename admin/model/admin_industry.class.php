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
class admin_industry_controller extends common
{
	function index_action(){
	
		$list=$this->obj->DB_select_all("industry","1 order by sort desc");
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_industry'));
	}
	
	function add_action(){
		if(!empty($_POST['add_name'])){
			if(!is_array($this->obj->DB_select_once("industry","`name`='".$_POST['add_name']."'"))){
				$add=$this->obj->DB_insert_once("industry","`name`='".$this->stringfilter(trim($_POST["add_name"]))."',`sort`='".$_POST['add_sort']."'");
				$this->cache_action();
			    $add?$msg=3:$msg=4;
			    $this->obj->admin_log("��ҵ���(ID:".$add.")��ӳɹ���");
			}else{
				$msg=2;
			}
		}else{
			$msg=1;
		}
		echo $msg;die;
	}
	
	function upp_action(){
		if($_POST['update']){
			if(!empty($_POST['name'])){
				$up=$this->obj->DB_update_all("industry","`name`='".$_POST['name']."',`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
				$this->cache_action();
 				 $up?$this->obj->ACT_layer_msg("��ҵ���(ID:".$_POST['id'].")���³ɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ����������ԣ�",8,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("����ȷ��д��Ҫ���µ���ҵ��",8,$_SERVER['HTTP_REFERER']);
			}
		}
		$this->yuntpl(array('admin/admin_industry'));
	}
	
	function del_action()
	{
		if((int)$_GET['delid'])
		{
			$this->check_token();
			$id=$this->obj->DB_delete_all("industry","`id`='".$_GET['delid']."'");
			$this->cache_action();
			$id?$this->layer_msg('��ҵ���(ID:'.$_GET['delid'].')ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['del'])
		{
			$del=@implode(",",$_POST['del']);
			$id=$this->obj->DB_delete_all("industry","`id` in (".$del.")","");
			$this->cache_action();
			isset($id)?$this->layer_msg('��ҵ���(ID:'.$del.')ɾ���ɹ���',9,1,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,1,$_SERVER['HTTP_REFERER']);
		}
		$this->yuntpl(array('admin/admin_industry'));
	}
	function cache_action()
	{
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->industry_cache("industry.cache.php");
	}
	function ajax_action(){
		if($_POST['sort']){
			$this->obj->DB_update_all("industry","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("��ҵ���(ID:".$_POST['id'].")�޸�����");
		}
		if($_POST['name']){
			$_POST['name']=$this->stringfilter($_POST['name']);
			$this->obj->DB_update_all("industry","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("��ҵ���(ID:".$_POST['id'].")�޸�������ƣ�");
		}
		$this->cache_action();
		echo '1';die;
	}
}
?>