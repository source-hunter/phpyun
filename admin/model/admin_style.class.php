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
			$this->layer_msg('模板风格更换成功！',9);
		}else{
			$this->layer_msg('该目录无效！',3);
		}
	}
}