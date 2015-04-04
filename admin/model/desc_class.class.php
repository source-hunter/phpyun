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
class desc_class_controller extends common
{
	function index_action()
	{
		$list=$this->obj->DB_select_all("desc_class","1 order by sort desc");
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_descclass'));
	}

	function add_action()
	{
		if(!empty($_POST['add_name']))
		{
			$row=$this->obj->DB_select_once("desc_class","`name`='".$_POST['add_name']."'");
			if(!is_array($row))
			{
				$add=$this->obj->DB_insert_once("desc_class","`name`='".$this->stringfilter(trim($_POST['add_name']))."',`sort`='".$_POST['add_sort']."'");
			    $add?$msg=3:$msg=4;
			    $this->obj->admin_log("��ҳ�����(ID:".$add.")��ӳɹ���");
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
				$up=$this->obj->DB_update_all("desc_class","`name`='".$_POST['name']."',`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
 				$up?$this->obj->ACT_layer_msg("��ҳ�����(ID:".$_POST['id'].")���³ɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ����������ԣ�",8,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("����ȷ��д��Ҫ���µĵ�ҳ�����",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}

	function del_action()
	{
		if((int)$_GET['delid'])
		{
			$this->check_token();
			$id=$this->obj->DB_delete_all("desc_class","`id`='".$_GET['delid']."'");
			$id?$this->layer_msg('��ҳ�����(ID:'.$_GET['delid'].')ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['del'])
		{
			$del=@implode(",",$_POST['del']);
			$id=$this->obj->DB_delete_all("desc_class","`id` in (".$del.")","");
			isset($id)?$this->layer_msg('��ҳ�����(ID:'.$del.')ɾ���ɹ���',9,1,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,1,$_SERVER['HTTP_REFERER']);
		}
	}
	function ajax_action(){
		if($_POST['sort']){
			$this->obj->DB_update_all("desc_class","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("��ҳ�����(ID:".$_POST['id'].")�޸�����");
		}
		if($_POST['name']){
			$_POST['name']=$this->stringfilter($_POST['name']);
			$this->obj->DB_update_all("desc_class","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("��ҳ�����(ID:".$_POST['id'].")�޸�������ƣ�");
		}
		echo '1';die;
	}
}
?>