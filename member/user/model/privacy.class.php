<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2015 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class privacy_controller extends user{

	function index_action()
	{
		if(intval($_POST['status']))
		{
			if($_POST['type'] =='status' || $_POST['type']=='info_status')
			{

				$this->obj->DB_update_all("resume","`".$_POST[type]."`='".intval($_POST['status'])."'","`uid`='".$this->uid."'");
				$this->obj->member_log("设置简历是否公开");
			}
		}
		$resume = $this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`status`,`info_status`");
        $this->yunset("resume",$resume);
        $this->yunset("js_def",2);
		$this->public_action();
		$this->user_tpl('privacy');
	}
}
?>