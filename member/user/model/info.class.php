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
class info_controller extends user{
	function index_action()
	{
		$row=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		$this->yunset("row",$row);
		if($_POST['submitBtn'])
		{
			$_POST=$this->post_trim($_POST);
			$is_exist=$this->obj->DB_select_once("member","`uid`<>'".$this->uid."' and (`email`='".$_POST['email']."' or `moblie`='".$_POST['telphone']."')","`uid`");
			if($is_exist['uid']){
				$this->obj->ACT_layer_msg("手机或邮箱已存在！",2);
			}
			if($_POST['name']==""){
				$this->obj->ACT_layer_msg("姓名不能为空！",2);
			}
			if($_POST['sex']==""){
				$this->obj->ACT_layer_msg("性别不能为空！",2);
			}
			if($this->config['user_idcard']=="1"&&trim($_POST['idcard'])==""){
				$this->obj->ACT_layer_msg("身份证号码不能为空！",2);
			}
			if($_POST['living']==""){
				$this->obj->ACT_layer_msg("现居住地不能为空！",2);
			}
			unset($_POST['submitBtn']);
			$this->obj->delfiledir("../upload/tel/".$this->uid);
			$where['uid']=$this->uid;
			$nid=$this->obj->update_once("resume",$_POST,$where);
			$this->obj->update_once("member",array('email'=>$_POST['email'],'moblie'=>$_POST['telphone']),$where);
			$member_statis=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'","`resume_num`");
			if($member_statis['resume_num']<1){
				$url="index.php?c=expect&add=".$this->uid;
			}else{
				$url=$_SERVER['HTTP_REFERER'];
			}
			if($nid)
			{
				$this->obj->member_log("修改基本信息",7);
				if($row['name']=="")
				{
					$this->obj->company_invtal($this->uid,$this->config['integral_userinfo'],true,"首次填写基本资料",true,2,'integral',25);
				}
				$this->obj->ACT_layer_msg("信息更新成功！",9,$url);
			}else{
				$this->obj->ACT_layer_msg("信息更新失败！",8,$url);
			}
		}
		$this->public_action();
		$this->city_cache();
		$this->user_cache();
		$this->user_tpl('info');
	}

}
?>