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
class banner_controller extends company
{
	function index_action()
	{
		if($_POST['submit'])
		{
			$upload=$this->upload_pic("../upload/company/",false,$this->config['com_uppic']);
			$pic=$upload->picture($_FILES['pic']);
			$this->picmsg($pic,$_SERVER['HTTP_REFERER']);
			$data['uid']=$this->uid;
			$data['pic']=$pic;
			$this->obj->insert_into("banner",$data);
			$this->obj->member_log("上传企业横幅");
			$this->get_integral_action($this->uid,"integral_banner","上传企业横幅");
 			$this->obj->ACT_layer_msg("设置成功！",9,"index.php?c=banner");
		}
		if($_POST['update'])
		{
			$upload=$this->upload_pic("../upload/company/",false,$this->config['com_uppic']);
			$pic=$upload->picture($_FILES['pic']);
			$this->picmsg($pic,$_SERVER['HTTP_REFERER']);
			$row=$this->obj->DB_select_once("banner","`uid`='".$this->uid."'");
			if(is_array($row))
			{
				$this->obj->unlink_pic($row['pic']);
			}
			$this->obj->update_once("banner",array("pic"=>$pic),array("uid"=>$this->uid));
			$this->obj->member_log("编辑企业横幅");
 			$this->obj->ACT_layer_msg("设置成功！",9,"index.php?c=banner");
		}
		$banner=$this->obj->DB_select_once("banner","`uid`='".$this->uid."'");
		$this->yunset("banner",$banner);
		$this->public_action();
		$this->yunset("js_def",2);
		$this->com_tpl("banner");
	}
}
?>