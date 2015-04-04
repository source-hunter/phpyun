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
class admin_style_controller extends common
{
	function public_action()
	{
		include_once("model/model/style_class.php");
	}
	function index_action()
	{
		$this->public_action();
		$style = new style($this->obj);
		$list = $style->model_list_action();
		$this->yunset("sy_style",$this->config['style']);
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_style_list'));
	}

	function modify_action()
	{
		extract($_GET);
		$this->public_action();
		$style = new style($this->obj);
		$style_info = $style->model_modify_action($dir);
		$this->yunset("style_info",$style_info);
		$this->yuntpl(array('admin/admin_style_modfy'));
	}
	function save_action()
	{
		$this->public_action();
		$style = new style($this->obj);
		$style_info = $style->model_save_action($_POST);
	}

	function check_style_action(){
		extract($_GET);
		if($dir!="")
		{
			$style = $this->obj->DB_select_all("admin_config","`name`='style'");
			if(is_array($style))
			{
				$this->obj->DB_update_all("admin_config","`config`='$dir'","`name`='style'");
			}else{

				$this->obj->DB_insert_once("admin_config","`config`='$dir',`name`='style'");
			}
			$this->web_config();
			$this->layer_msg('ģ��������ɹ���',9);
		}else{
			$this->layer_msg('��Ŀ¼��Ч��',3);
		}
	}
}