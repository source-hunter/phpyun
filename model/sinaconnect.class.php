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
class sinaconnect_controller extends common
{
	function index_action()
	{

		if($this->config['sy_sinalogin']!="1")
		{
			if($_GET['login']=="1")
			{
				$this->obj->ACT_msg("index.php","对不起，新浪绑定已关闭！");
			}else{
				$this->obj->ACT_msg("index.php","对不起，新浪登录已关闭！");
			}
		}
		include_once( APP_PATH.'api/weibo/saetv2.ex.class.php' );
		define("WB_AKEY" ,$this->config['sy_sinaappid']);
		define("WB_SKEY" , $this->config['sy_sinaappkey']);
		define("WB_CALLBACK_URL" , $this->config['sy_weburl'].'/index.php?m=sinaconnect' );
		$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
		if(isset($_GET['code']))
		{
			$keys = array();
			$keys['code'] = $_GET['code'];
			$keys['redirect_uri'] = WB_CALLBACK_URL;
			$token = $o->getAccessToken('code',$keys);
			if($token['access_token'])
			{
				$tokens = 	$token['access_token'];
				$tokenuid =  $token['uid'];
				if($tokenuid>0)
				{
					$userinfo = $this->obj->DB_select_once("member","`sinaid`='".$tokenuid."'");
					if(is_array($userinfo) && !$this->uid)
					{
						if($this->config['sy_uc_type']=="uc_center")
						{
							$this->obj->uc_open();
							$user = uc_get_user($userinfo['username']);
							$ucsynlogin=uc_user_synlogin($user[0]);
							$this->obj->ACT_msg("index.php","登录成功！",9);
						}else{
							$this->unset_cookie();
							$this->add_cookie($userinfo['uid'],$userinfo['username'],$userinfo['salt'],$userinfo['email'],$userinfo['password'],$userinfo['usertype']);
							$this->obj->ACT_msg("index.php","登录成功！",9);
						}
					}else{

						
						$_SESSION['sina']["openid"] = $tokenuid;
						$_SESSION['sina']["tooken"] = $token['access_token'];
						$_SESSION['sina']["logininfo"] = "您已登陆新浪微博，请绑定您的帐户！";
						
						if($this->uid){
							
							$this->obj->DB_update_all("member","`sinaid`=''","`sinaid`='".$_SESSION['sina']["openid"]."'");
							$this->obj->DB_update_all("member","`sinaid`='".$_SESSION['sina']["openid"]."'","`uid`='".$this->uid."'");
							$this->obj->ACT_msg($this->config['sy_weburl'].'/member/index.php?c=binding',"新浪微博绑定成功！",9);

						}else{
							$GetUrl = "https://api.weibo.com/2/users/show.json?uid=".$tokenuid."&access_token=".$tokens;
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL,$GetUrl);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
							$str=curl_exec ($ch);
							curl_close ($ch);
							$user = json_decode($str,true);
							$user = $this->array_iconv("utf-8","GBK",$user);
							if($user['name']){
								$_SESSION['sina']['nickname'] = $user['name'];
								$_SESSION['sina']['pic'] = $user['avatar_hd'];
							}else{
								$this->obj->ACT_msg($this->config['sy_weburl'],"用户信息获取失败，请重新登陆新浪微博！",8);
							}
							echo "<script>window.location.href='".$this->config['sy_weburl']."/index.php?m=sinaconnect&c=sinabind';</script>";
						}
					}
				}else{
					$this->obj->ACT_msg($this->config['sy_weburl'],"新浪微博授权失败，请重新授权！");
				}
			}
		}else{
			$code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );
			echo "<script>window.location.href='".$code_url."';</script>";
		}
	  }
	  function sinabind_action()
	{

		if(($_GET['usertype']=='1' || $_GET['usertype']=='2') && $_SESSION['sina']['openid'])
		{
				$usertype = $_GET['usertype'];
				$ip = $this->obj->fun_ip_get();
				$time = time();
				$salt = substr(uniqid(rand()), -6);
				$pass = md5(md5($salt).$salt);
  				$username = $this->checkuser($_SESSION['sina']['nickname'],$_SESSION['sina']['nickname']);

					$userid=$this->obj->DB_insert_once("member","`username`='$username',`password`='$pass',`usertype`='$usertype',`status`='1',`salt`='$salt',`reg_date`='$time',`reg_ip`='$ip',`sinaid`='".$_SESSION['sina']['openid']."'");
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
 				unset($_SESSION['sina']);
				$this->obj->ACT_msg($this->config['sy_weburl'].'/member',"登录成功！",9);
		}
		$this->seo("sinalogin");
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

