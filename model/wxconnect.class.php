<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class wxconnect_controller extends common
{
	function wxlogin_check_action()
	{
		if($_POST['username']!="" && $_POST['password']!="")
		{
			$user = $this->obj->DB_select_once("member","`username`='$username'","`uid`,`salt`,`password`");
			if(is_array($user))
			{
				$pass = md5(md5($_POST['password']).$user['salt']);
				if($pass==$user['password']){
					$this->obj->DB_update_all("member","`login_date`='".time()."'","`uid`='".$user[uid]."'");
					echo "�ɹ�";
				}
			}else{
				echo "<font color='red'>���û�������</font>";
			}
		}else{
			echo "<font color='red'>�û��������벻��Ϊ�գ�</font>";
		}
	}

	function index_action()
	{
		
		if($_COOKIE['uid']!=""&&$_COOKIE['username']!="")
		{
			$this->obj->ACT_msg($this->config['sy_weburl'], $msg = "���Ѿ���¼�ˣ�");
		}
		if($this->config['sy_wxlogin']!="1")
		{
			$this->obj->ACT_msg($this->config['sy_weburl'], $msg = "�Բ���΢�ŵ�¼�ѹرգ�");
		}
		$this->seo('wxlogin');
		$app_id = $this->config['sy_wxappid'];
		$app_secret = $this->config['sy_wxappkey'];
		$my_url = $this->config['sy_weburl']."/index.php?m=wxconnect";
		$code = $_GET['code'];
		if(empty($code))
		{
			$_SESSION['wx']['state'] = md5(uniqid(rand(), TRUE));

			$dialog_url = "https://open.weixin.qq.com/connect/qrconnect?response_type=code&scope=snsapi_login&appid="
				. $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
				. $_SESSION['wx']['state'].'#wechat_redirect';
			header("location:".$dialog_url);
		}
		if($_GET['state'] == $_SESSION['wx']['state'])
		{
			$token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code&"
			 . "appid=" . $app_id . "&secret=" . $app_secret . "&code=" . $code;
			if(!function_exists('curl_init')) {

				echo "�뿪��CURL�����������޷�������һ��������";
				die;
			}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$token_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$response=curl_exec ($ch);
			curl_close ($ch);
			
			$params = json_decode($response);
			if(!$params->openid || !$params->access_token)
			{
				$this->obj->ACT_msg($this->config['sy_weburl'],"΢�ŵ�¼��Ϣ��ȡʧ�ܣ�",8);
				exit();
			}
			$graph_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$params->access_token."&openid=".$params->openid;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$graph_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$str=curl_exec ($ch);
			curl_close ($ch);
			$user = json_decode($str,true);
			$user = $this->array_iconv("utf-8","GBK",$user);
			
			if(!$user['openid'])
			{
				$this->obj->ACT_msg($this->config['sy_weburl'],"΢�ŵ�¼��Ϣ��ȡʧ�ܣ�",8);
				exit();
			}else{
				$userinfo = $this->obj->DB_select_once("member","`wxid`='".$user['openid']."'");
				
				if(is_array($userinfo) && !empty($userinfo))
				{
					$this->obj->DB_update_all("member","`login_date`='".time()."'","`uid`='".$userinfo[uid]."'");
					if($this->config['sy_uc_type']=="uc_center"){
						$this->obj->uc_open();
						$uc_user = uc_get_user($userinfo['username']);
						$ucsynlogin=uc_user_synlogin($uc_user[0]);
						$msg =  '��¼�ɹ���';
						
						$this->obj->ACT_msg($this->config['sy_weburl'],"��¼�ɹ���",9);
					}else{
						$this->unset_cookie();
						$this->add_cookie($userinfo['uid'],$userinfo['username'],$userinfo['salt'],$userinfo['email'],$userinfo['password'],$userinfo['usertype']);
						$this->obj->ACT_msg($this->config['sy_weburl'],"��¼�ɹ���",9);
					}
				}else{
				
					$_SESSION['wx']["openid"] = $user['openid'];
					$_SESSION['wx']["tooken"] = $user['access_token'];
					$_SESSION['wx']["logininfo"] = "���ѵ�½΢�ţ���������ʻ���";
					if($user['nickname']){
						$_SESSION['wx']['nickname'] = $user['nickname'];
						$_SESSION['wx']['pic'] = $user['headimgurl'];
						header("location:".$this->config['sy_weburl']."/index.php?m=wxconnect&c=wxbind");
					}else{
						$this->obj->ACT_msg($this->config['sy_weburl'],"�û���Ϣ��ȡʧ�ܣ������µ�¼��",8);
					}
				}
			 }
		}else{
			  echo("The state does not match. You may be a victim of CSRF.");
		}
	}
	function wxbind_action()
	{
		if($_COOKIE['uid']!=""&&$_COOKIE['username']!="")
		{
			header("location:".$this->config['sy_weburl']."/member");
		}
		if(($_GET['usertype']=='1' || $_GET['usertype']=='2') && $_SESSION['wx']['openid'])
		{
				$usertype = $_GET['usertype'];
				$ip = $this->obj->fun_ip_get();
				$time = time();
				$salt = substr(uniqid(rand()), -6);
				$pass = md5(md5($salt).$salt);
  				$username = $this->checkuser($_SESSION['wx']['nickname'],$_SESSION['wx']['nickname']);

					$userid=$this->obj->DB_insert_once("member","`username`='$username',`password`='$pass',`usertype`='$usertype',`status`='1',`salt`='$salt',`reg_date`='$time',`reg_ip`='$ip',`wxid`='".$_SESSION['wx']['openid']."'");
 				if(!$userid)
				{
					$user = $this->obj->DB_select_once("member","`username`='".$username."'","`uid`,`email`");
					$userid = $user['uid'];
					$email = $user['email'];
				}
				$this->unset_cookie();
				if($usertype=="1")
				{
					$table = "member_statis";
					$table2 = "resume";
					$value="`uid`='$userid'";
					$value2 = "`uid`='$userid',`name`='$username'";

				}elseif($usertype=="2"){

					$table = "company_statis";
					$table2 = "company";
					$value="`uid`='$userid',".$this->rating_info();
					$value2 = "`uid`='$userid',`linktel`='$moblie'";
				}
				$this->obj->DB_insert_once($table,$value);
				$this->obj->DB_insert_once($table2,$value2);
				$this->obj->DB_insert_once("friend_info","`uid`='".$userid."',`nickname`='$username',`usertype`='$usertype'");
				$this->add_cookie($userid,$username,$salt,$email,$pass,$usertype);
 				unset($_SESSION['wx']);
				header("location:".$this->config['sy_weburl']."/member");
		}
		$this->seo("wxlogin");
		$this->yun_tpl(array('index'));
	} 

	function checkuser($username,$name)
	{

		$user = $this->obj->DB_select_once("member","`username`='".$username."'","`uid`");
		if($user['uid'])
		{
			$name.="_".rand(1000,9999);
			return $this->checkuser($name,$username);
		}else{

			return $username;
		}

	}
function rating_info()
	{
		$id =$this->config['com_rating'];
		$row = $this->obj->DB_select_once("company_rating","`id`='".$id."'");
		$value="`rating`='$id',";
		$value.="`integral`='".$this->config['integral_reg']."',";
		$value.="`rating_name`='".$row['name']."',";
		$value.="`job_num`='".$row['job_num']."',";
		$value.="`down_resume`='".$row['resume']."',";
		$value.="`invite_resume`='".$row['interview']."',";
		$value.="`editjob_num`='".$row['editjob_num']."',";
		$value.="`breakjob_num`='".$row['breakjob_num']."',";
		$value.="`rating_type`='".$row['type']."',";
		if($row['service_time']>0)
		{
			$time=time()+86400*$row['service_time'];
		}else{
			$time=0;
		}
		$value.="`vip_etime`='".$time."'";
		
		return $value;
	}
	function array_iconv($in_charset,$out_charset,$arr){
	        return eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));
	 }

}

