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
class subscribe_controller extends common
{
	function index_action()
	{
		if($_POST['submit'])
		{
			if(!$this->CheckRegEmail($_POST['email']))
			{
				$this->obj->ACT_layer_msg("接收邮箱格式不正确！",8,$_SERVER['HTTP_REFERER']);
			}
			$info=$this->obj->DB_select_once("subscribe","`email`='".$_POST['email']."' and `type`='".(int)$_POST['type']."'");
			if($info['status']=="1")
			{
				$this->obj->ACT_layer_msg("该邮箱已设置订阅，请不要重复设置！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$data['type']="cert";
				$code = substr(uniqid(rand()), -6);
				$data['code']=$code;
				$data['email']=$_POST['email'];
				$data['date']=date("Y-m-d");
				$base=base64_encode($_POST['email']."|".$code);
				$data['url']="<a href='".$this->config['sy_weburl']."/index.php?m=subscribe&c=cert&id=".$base."'>点击认证</a>";
				$status=$this->send_msg_email($data);
				$_POST['code']=$code;
				$_POST['ctime']=time();
				if($info['status']=="0")
				{
					$where['email']=$_POST['email'];
					$where['type']=$_POST['type'];
					$this->obj->update_once("subscribe",$_POST,$where);
				}else{
					$this->obj->insert_into("subscribe",$_POST);
				}
				$_SESSION['cert_email']=$_POST['email'];
				$_SESSION['email_type']=$_POST['type'];
				$this->obj->ACT_layer_msg("订阅设置成功，请认证邮箱！",9,"index.php?m=subscribe&c=cert");;
			}
		}
		$CacheArr['city'] =array('city_index','city_name');
		$CacheArr['job'] =array('job_index','job_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$this->CacheInclude($CacheArr);
		if($this->uid){
			$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","`email`");
			$this->yunset("cert_email",$member['email']);
		}
		$this->seo("subscribe");
		$this->yun_tpl(array('index'));
	}
	function cert_action()
	{
		if($_GET['id'])
		{
			$arr=@explode("|",base64_decode($_GET['id']));
			$email = $arr[0];
			$code = $arr[1];
			if(!$this->CheckRegEmail($email) || !ctype_alnum($code))
			{
				exit();
			}else{
				$nid=$this->obj->DB_update_all("subscribe","`status`='1'","`email`='".$email."' and `code`='".$code."'");
				header("location:".$this->config['sy_weburl']."/index.php?m=register&c=ok&type=4");
			}
		}
		$this->yunset("email",$_SESSION['cert_email']);
		$this->seo("subscribe");
		$this->yun_tpl(array('cert'));
	}
	function send_email_action()
	{
		if($_SESSION['cert_email'])
		{
			$data['type']="cert";
			$code = substr(uniqid(rand()), -6);
			$data['code']=$code;
			$data['date']=date("Y-m-d");
			$data['email']=$_SESSION['cert_email'];
			$base=base64_encode($_SESSION['cert_email']."|".$code);
			$data['url']="<a href='".$this->config['sy_weburl']."/index.php?m=subscribe&c=cert&id=".$base."'>点击认证</a>";
			$status=$this->send_msg_email($data);
			$this->obj->DB_update_all("subscribe","`code`='".$code."'","`email`='".$_SESSION['cert_email']."'");
			echo 1;die;
		}
	}
}
?>