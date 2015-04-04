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
class subscribe_controller extends user{

	function index_action()
	{

		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		if($resume['hy_dy']!=""){
			$resume['hylist']=@explode(",",$resume['hy_dy']);
		}
		$this->yunset("resume",$resume);
		$this->public_action();
		$this->industry_cache();
		$this->yunset("js_def",2);
		$this->user_tpl('subscribe');
	}
	function set_action(){
		if($_POST['status']!='' && $_POST['type']){
            if($_POST['type']=='msg_dy' || $_POST['type']=='email_dy'){
				if($this->config['sy_email_userdy']=='2'){
					$this->layer_msg('邮件订阅功能已关闭，请等待管理员开通提示！',8,0,"index.php?c=subscribe");die;
				}else if($this->config['sy_msg_userdy']=='2'){
					$this->layer_msg('短信订阅功能已关闭，请等待管理员开通提示！',8,0,"index.php?c=subscribe");die;
				}else{
					$this->obj->member_log("设置订阅状态");
					$nid=$this->obj->DB_update_all("resume","`$_POST[type]`='".(int)$_POST['status']."'","`uid`='".$this->uid."'");
					$nid?$this->layer_msg('设置成功！',9,0,"index.php?c=subscribe"):$this->layer_msg('设置失败！',9,0,"index.php?c=subscribe");
				}
            }
		}
		if($_POST['hyid']){
			$this->obj->member_log("设置订阅行业类别");
			$nid=$this->obj->DB_update_all("resume","`hy_dy`='".intval($_POST['hyid'])."'","`uid`='".$this->uid."'");
			$nid?$this->layer_msg('设置成功！',9,0,"index.php?c=subscribe"):$this->layer_msg('设置失败！',9,0,"index.php?c=subscribe");
		}

		if($_POST['unsetid']){
			$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","hylist");
			foreach($resume['hylist'] as $v){
				if($v!=$_POST['unsetid'])
				{
					$hy[]=$v;
				}
				$hyid=@implode(",",$hy);
			}
			$this->obj->member_log("设置订阅行业类别");
			$nid=$this->obj->DB_update_all("resume","`hy_dy`='".intval($hyid)."'","`uid`='".$this->uid."'");
			$nid?$this->layer_msg('设置成功！',9,0,"index.php?c=subscribe"):$this->layer_msg('设置失败！',9,0,"index.php?c=subscribe");
		}
	}
}
?>