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
class activate_controller extends common
{
	function index_action()
	{
		$this->seo("activate");
		$this->yun_tpl(array('index'));
	}
	function sendstr_action()
	{
		if($this->config['user_status']=="0"){
			$username=$this->stringfilter($_POST['username']);

			if(!$this->CheckRegUser($username))
			{
				die;
			}
			$info=$this->obj->DB_select_once("member","`username`='".$username."'","`uid`,`email`,`usertype`");
			if(!empty($info)){
				$fdata=$this->forsend($info);
				$randstr=rand(10000000,99999999);
				$base=base64_encode($info['uid']."|".$randstr."|".$this->config['coding']);
				$data['uid']=$info['uid'];
				$data['name']=$fdata['name'];
				$data['type']="cert";
				$data['email']=$info['email'];
				$data['url']="<a href='".$this->config['sy_weburl']."/index.php?m=qqconnect&c=mcert&id=".$base."'>�����֤</a>";
				$data['date']=date("Y-m-d");
				if($this->config['sy_smtpserver']!="" && $this->config['sy_smtpemail']!="" && $this->config['sy_smtpuser']!=""){
					$this->send_msg_email($data);
					echo 1;die;
				}else{
					echo 3;die;
				}
			}else{
				echo 2;die;
			}
		}else{
			echo 0;die;
		}
	}
	function editpw_action()
	{
		if($_POST['username'] && $_POST['code'] && $_POST['pass'])
		{
			if(!is_numeric($_POST['code']) || !$this->CheckRegUser($_POST['username']))
			{
				$this->obj->ACT_msg($this->url("index","forgetpw","1"), $msg = "��Ч����Ϣ��", $st = 2, $tm = 3);
				exit();
			}
			$password = $_POST['pass'];
			$cert = $this->obj->DB_select_once("company_cert","`type`='5' AND `check2`='".$_POST['username']."' AND `check`='".$_POST['code']."' order by id desc","`uid`,`check2`,`ctime`");
			if(!$cert['uid'])
			{
				$this->obj->ACT_msg($this->url("index","forgetpw","1"), $msg = "��֤����д����", $st = 2, $tm = 3);
				exit();
			}elseif((time()-$cert['ctime'])>1200){
				$this->obj->ACT_msg($this->url("index","forgetpw","1"), $msg = "��֤����ʧЧ�������»�ȡ��", $st = 2, $tm = 3);
				exit();
			}
			$info = $this->obj->DB_select_once("member","`uid`='".$cert['uid']."'","`email`");
			if(is_array($info))
			{
				$info['username'] = $cert['check2'];
				if($this->config[sy_uc_type]=="uc_center" && $info['name_repeat']!="1")
				{
					$this->obj->uc_open();
					uc_user_edit($info[username], "", $password, $info['email'],"0");
				}else{
					$salt = substr(uniqid(rand()), -6);
					$pass2 = md5(md5($password).$salt);
					$value="`password`='$pass2',`salt`='$salt'";
					$this->obj->DB_update_all("member",$value,"`uid`='".$cert['uid']."'");
				}
				$this->obj->ACT_msg($this->url("index","login","1"), $msg = "�����޸ĳɹ���", $st = 1, $tm = 3);
			}else{
				$this->obj->ACT_msg($this->url("index","forgetpw","1"), $msg = "�Բ���û�и��û���", $st = 2, $tm = 3);
			}
		}else{
			$this->obj->ACT_msg($this->url("index","forgetpw","1"), $msg = "��������д��Ϣ��", $st = 2, $tm = 3);
			exit();
		}
	}
}