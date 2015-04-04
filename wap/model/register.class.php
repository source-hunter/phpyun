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
class register_controller extends common
{
	function index_action()
	{
		$this->get_moblie();
		if($this->uid || $this->username)
		{
			echo "<script>location.href='member/index.php';</script>";
		}
		if($_POST['submit'])
		{
			if($_POST['wxid'])
			{
				$wxparse = '&wxid='.$_POST['wxid'].'&wxname='.$_POST['wxname'];
			}
			if($_POST['wxname'])
			{
				$wxparse = '&wxname='.$_POST['wxname'];
			}
			$usertype=$_POST['usertype']?$_POST['usertype']:1;

			if(!$this->CheckRegUser($_POST['username']))
			{
				$this->wapheader('index.php?m=login&','无效的用户名！');
			}
			if(!$this->CheckRegEmail($_POST['email']))
			{
				$this->wapheader('index.php?m=login&','邮箱格式不正确！');
			}
			$member=$this->obj->DB_select_once("member","`username`='".$_POST['username']."' OR `email`='".$_POST['email']."'");
			if(is_array($member))
			{
				if($member['username']==$_POST['username'])
				{
					$this->wapheader('index.php?m=register&usertype='.$usertype.$wxparse.'&','用户名已存在，请重新输入！');
				}elseif($member['email']==$_POST['email']){
					$this->wapheader('index.php?m=register&usertype='.$usertype.$wxparse.'&','邮箱已存在，请重新输入！');
				}
			}else{
				$regname=@explode(",",$this->config['sy_regname']);
				if(in_array($_POST['username'],$regname))
				{
					$this->wapheader('index.php?m=register&usertype='.$usertype.$wxparse.'&','用户名已存在，请重新输入！');
				}
			}
			if($usertype=='1')
			{
				$status = 1;
			}elseif($usertype=='2'){
				$status = $this->config['com_status'];
			}
			if($this->config['sy_uc_type']=="uc_center")
			{
				$this->obj->uc_open();
				$uid=uc_user_register($_POST['username'],$_POST['password'],$_POST['email']);
				if($uid<=0)
				{
					$this->wapheader('index.php?m=register&usertype='.$usertype.$wxparse.'&','该用户或邮箱已存在！');
				}else{
					list($uid,$username,$password,$email,$salt)=uc_user_login($_POST['username'],$_POST['password']);
					$pass = md5(md5($_POST['password']).$salt);
					$ucsynlogin=uc_user_synlogin($uid);
				}
			}elseif($this->config['sy_pw_type']=="pw_center"){
				include(APP_PATH."/api/pw_api/pw_client_class_phpapp.php");
				$username=$username;
				$password=$_POST['password'];
				$email=$_POST['email'];
				$pw=new PwClientAPI($username,$password,$email);
				$pwuid=$pw->register();
				$salt = substr(uniqid(rand()), -6);
				$pass = md5(md5($password).$salt);
			}else{
				$salt = substr(uniqid(rand()), -6);
				$pass = md5(md5($_POST['password']).$salt);
			}
			
			$idata['username'] = $_POST['username'];
			$idata['password'] = $pass;
			$idata['email']    = $_POST['email'];
			$idata['usertype'] = $usertype;
			$idata['status']   = $status;
			$idata['salt']     = $salt;
			$idata['source']     = '2';
			$idata['reg_date']=$idata['login_date'] = time();
			if($_POST['wxid']){
				
				$this->obj->update_once('member',array('wxid'=>'','wxname'=>''),array('wxid'=>$this->stringfilter($_POST['wxid'])));
				$idata['wxid'] = $_POST['wxid'];
				$idata['wxname'] = $this->stringfilter($_POST['wxname']);
				$idata['wxbindtime'] =time();
			}

			$userid=$this->obj->insert_into('member',$idata);
			if($userid)
			{
				if($this->config[sy_pw_type]=="pw_center")
				{
					$this->obj->update_once('member',array('pwuid'=>$pwuid),array('uid'=>$userid));
				}
				if($usertype=="1")
				{
					$table = "member_statis";
					$table2 = "resume";
					$value="`uid`='".$userid."'";
					$udata['uid'] = $userid;
					$udata2['uid'] = $userid;
					$udata2['email'] = $_POST['email'];
				}elseif($usertype=="2"){
					$table = "company_statis";
					$table2 = "company";
					
					$udata['uid'] = $userid;
					$udata2['uid'] = $userid;
					$udata2['linkmail'] = $_POST['email'];
					$udata2['linkman'] = trim($_POST['linkman']);
					$udata2['linktel'] = trim($_POST['moblie']);

					$udata=$this->rating_info($udata);
				}
				$this->obj->insert_into($table,$udata);
				$this->obj->insert_into($table2,$udata2);
				$this->obj->insert_into('friend_info',array('uid'=>$userid,'nickname'=>$_POST['username'],'usertype'=>$usertype));

				setcookie("uid",$userid,time() + 86400, "/");
				setcookie("username",$_POST['username'],time() + 86400, "/");
				setcookie("usertype",$usertype,time() + 86400, "/");
				setcookie("salt",$salt,time() + 86400, "/");
				setcookie("shell",md5($idata['username'].$idata['password'].$idata['salt']), time() + 86400,"/");

				$this->wapheader('member/index.php');
			}
		}
		if($_GET['usertype']=="2")
		{
			$this->yunset("title","企业会员注册");
		}else{
			$this->yunset("title","个人会员注册");
		}
		$this->yuntpl(array('wap/register'));
	}
	function rating_info($data=array())
	{
		$id =$this->config['com_rating'];
		$row = $this->obj->DB_select_once("company_rating","`id`='".$id."'");

		$data['rating']=$id;
		$data['integral']=$this->config['integral_reg'];
		$data['rating_name']=$row['name'];
		if($row['type']==1)
		{
			$data['job_num']=$row['job_num'];
			$data['down_resume']=$row['resume'];
			$data['interview']=$row['interview'];
			$data['editjob_num']=$row['editjob_num'];
			$data['breakjob_num']=$row['breakjob_num'];
		}else{
			$time=time()+86400*$row['service_time'];
			$data['vip_etime']=$time;
		}
		return $data;
	}
}
?>