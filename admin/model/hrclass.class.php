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
class hrclass_controller extends common
{
	function index_action()
	{
		$rows=$this->obj->DB_select_all("toolbox_class");
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_hrclass'));
	}
	function add_action()
	{
		if($_GET['id'])
		{
			$row=$this->obj->DB_select_once("toolbox_class","id='".$_GET['id']."'");
			$this->yunset("row",$row);
		}
		if($_POST['submit'])
		{
			$value.="`name`='".$_POST['name']."',";
			$value.="`content`='".$_POST['content']."'";
			$upload=$this->upload_pic("../upload/hrclass/","22");
			if($_FILES['pic']['tmp_name']!="")
			{
				$pictures=$upload->picture($_FILES['pic']);
				$value.=",`pic`='".str_replace("../","",$pictures)."'";
				if($_POST['id'])
				{
					$row=$this->obj->DB_select_once("toolbox_class","`id`='".$_POST['id']."' and `pic`<>''");
					if(is_array($row))
					{
						$this->obj->unlink_pic("../".$row['pic']);
					}
				}
			}
			if($_POST['id'])
			{
				$id=$this->obj->DB_update_all("toolbox_class",$value,"`id`='".$_POST['id']."'");
				$msg="����";
			}else{
				$id=$this->obj->DB_insert_once("toolbox_class",$value);
				$msg="���";
			}
			isset($id)?$this->obj->ACT_layer_msg("HR���(ID:".$id.")".$msg."�ɹ���",9,"index.php?m=hrclass",2,1):$this->obj->ACT_layer_msg($msg."ʧ�ܣ�",8,"index.php?m=hrclass");
		}
		$this->yuntpl(array('admin/admin_hrclass_add'));
	}
	function del_action()
	{
		if($_GET['del'])
		{
			$this->check_token();
			if(is_array($_GET['del']))
			{
				$del=@implode(",",$_GET['del']);
				$layer_type=1;
			}else{
				$del=$_GET['del'];
				$layer_type=0;
			}
		}
		$row=$this->obj->DB_select_all("toolbox_class","`id` in (".$del.") and `pic`<>''");
		if(is_array($row))
		{
			foreach($row as $v)
			{
				$this->obj->unlink_pic("../".$v['pic']);
			}
		}
		$delid=$this->obj->DB_delete_all("toolbox_class","`id` in ($del)","");
		$delid?$this->layer_msg('HR���(ID:'.$del.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
}

?>