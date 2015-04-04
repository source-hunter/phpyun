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
class sendcert_controller extends common
{
	function index_action()
	{
		$this->seo("sendcert");
		$this->yun_tpl(array('index'));
	}
	function sendcert_action(){
		if(md5($_POST["authcode"])!=$_SESSION[authcode]){ 
			unset($_SESSION['authcode']);
			$this->obj->ACT_layer_msg("验证码错误！",8,"index.php?m=forgetpw");
		}

		$info = $this->obj->DB_select_once("member","`username`='".$_POST['username']."'","`uid`,`email_status`,`email`,`usertype`");
		if(is_array($info)&&$info){

			if($info[email_status]=="1"){ 
				$this->obj->ACT_layer_msg("您的账户已经激活，请直接登录！",9,"index.php?m=login&usertype=1");
			}
			$fdata=$this->forsend($info);
			$randstr=rand(10000000,99999999);
			$base=base64_encode($info[uid]."|".$randstr."|".$this->config[coding]);
			$data["uid"]=$info[uid];
			$data["name"]=$fdata[name];
			$data["type"]="cert";
			$data["email"]=$info[email];
			$data["url"]="<a href='".$this->config[sy_weburl]."/index.php?m=qqconnect&c=mcert&id=".$base."'>点击激活</a>";
			$data["date"]=date("Y-m-d");
			$this->send_msg_email($data); 
			$this->obj->ACT_layer_msg("激活邮件已经发送到您的邮箱！",9,"index.php?m=sendcert");
		}else{ 
			$this->obj->ACT_layer_msg("对不起！没有该用户！",8,"index.php?m=login");
		}
	}

}