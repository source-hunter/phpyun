<?php

/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����

 */
class register_controller extends common{
	function index_action()
	{
		if($_COOKIE['uid']!=""&&$_COOKIE['username']!=""){
			$this->obj->ACT_msg($this->config['sy_weburl'], "���Ѿ���¼�ˣ�");
		}
		if($_GET['uid'])
		{
			$_SESSION['regcode']=$_GET['uid'];
		}
		$this->seo("register");
		if($_GET['usertype']=="2"){
			$this->yun_tpl(array('company'));
		}else{
			$this->yun_tpl(array('user'));
		}
	}
	function ok_action()
	{
		if($_GET['type']==1)
		{
			$seo=$this->config['sy_webname']."- ע��ɹ�";
		}elseif($_GET['type']==2){
			$seo=$this->config['sy_webname']."- �ʺű�����";
		}elseif($_GET['type']==3){
			$seo=$this->config['sy_webname']."- ���δͨ��";
		}elseif($_GET['type']==4){
			$seo=$this->config['sy_webname']."- �ʼ���֤�ɹ�";
		}else{
			header("location:".$this->config['sy_weburl']);
		}
		$this->yunset("title",$seo." - Powered by PHPYun.");
		$this->yunset("keywords",$seo);
		$this->yunset("description",$seo);

		$this->yun_tpl(array('ok'));
	}
	function ajax_reg_action(){
		$post = array_keys($_POST);
		$key_name = $post[0];
		if(!in_array($key_name,array('username','email')))
		{
			exit();
		}

		if($key_name=="username"){
			$username=$this->stringfilter($_POST['username']);
			if(!$this->CheckRegUser($username) && !$this->CheckRegEmail($username))
			{
				echo 2;die;
			}
			if($this->config['sy_uc_type']=="uc_center"){
				$this->obj->uc_open();
				$user = uc_get_user($username);
			}else{
				$user = $this->obj->DB_select_num("member","`username`='".$username."'");
			}
			if($this->config['sy_regname']!=""){
				$regname=@explode(",",$this->config['sy_regname']);
				if(in_array($username,$regname)){
					echo 2;die;
				}
			}
		}elseif($key_name=="email"){
			if(!$this->CheckRegEmail($_POST['email'])){
				echo 2;die;
			}
			$user = $this->obj->DB_select_num("member","`email`='".$_POST['email']."' or `username`='".$_POST['email']."'");
		}
		if($user){echo 1;}else{echo 0;}
	}
	function regmoblie_action(){
		if($_POST['moblie']){
			$num=$this->obj->DB_select_num("member","`moblie`='".$_POST['moblie']."' or `username`='".$_POST['moblie']."'","uid");
			echo $num;die;
		}
	}
	function errjson($msg,$status='8'){
	
		$arr['status']=$status;
		$arr['msg']=$msg;
		$arr['msg']=iconv("gbk","utf-8",$arr['msg']);
		echo json_encode($arr);die;
	}
	function regsave_action()
	{
		$_POST=$this->post_trim($_POST);
		$usertype=intval($_POST['usertype']);

		$_POST['username']=$this->stringfilter($_POST['username']);
		$_POST['unit_name']=$this->stringfilter($_POST['unit_name']);
		$_POST['address']=$this->stringfilter($_POST['address']);
		$_POST['linkman']=$this->stringfilter($_POST['linkman']);
		$_POST['name']=$this->stringfilter($_POST['name']);
		
		if($_COOKIE['uid']!="" && $_COOKIE['username']!=""){
	
			$this->errjson('���Ѿ���¼�ˣ�');
		}
				
		if(strpos($this->config['code_web'],'ע���Ա')!==false && md5($_POST['authcode'])!=$_SESSION['authcode']){
			
			
			$this->errjson('��֤�����');
		}

		if(!$this->CheckRegUser($_POST['username']) && !$this->CheckRegEmail($_POST['username'])){
			
			$this->errjson('�û������������ַ���');
		}
		
		if($_POST['codeid']=='1')
		{
			if($this->config['username'] =='1')
			{	
				if(!$this->CheckRegUser($_POST['name']) || $_POST['name']==""){

					$this->errjson('��ʵ������ʽ���淶');
				}
			}
			
			if(($this->config['usertel']=='1' && $usertype=='1') || ($this->config['linkphone']=='1' && $usertype=='2'))
			{
				if(!preg_match("/1[3458]{1}\d{9}$/",$_POST['moblie'])){
				
					$this->errjson('�ֻ���ʽ����');
				
				}else{
					$moblieNum = $this->obj->DB_select_num("member","`moblie`='".$_POST['moblie']."'");
					if($moblieNum>0)
					{
						$this->errjson('�ֻ��Ѵ��ڣ�');
					}
				}
			}
			if(($this->config['useremail']=='1' && $usertype=='1') || ($this->config['comemail']=='1' && $usertype=='2'))
			{
				if(!$this->CheckRegEmail($_POST['email']) || $_POST['email']==""){
	
						$this->errjson('Email��ʽ���淶��');
					}
			}
		

			if($usertype=='2'){
			
				if($this->config['comname'] =='1')
				{	
					if(!$this->CheckRegUser($_POST['unit_name']) || $_POST['unit_name']==""){

						$arr['status']=8;
						$arr['msg']='����ȷ��д��ҵ����';
						$arr['msg']=iconv("gbk","utf-8",$arr['msg']);
						echo json_encode($arr);die;
					}
				}
				if($this->config['comaddress'] =='1')
				{	
					if(!$this->CheckRegUser($_POST['address']) || $_POST['address']==""){

						$arr['status']=8;
						$arr['msg']='����ȷ��д��ҵ��ַ';
						$arr['msg']=iconv("gbk","utf-8",$arr['msg']);
						echo json_encode($arr);die;
					}
				}
				if($this->config['linkman'] =='1')
				{	
					if(!$this->CheckRegUser($_POST['linkman']) || $_POST['linkman']==""){

						$this->errjson('����ȷ��д��ҵ��ϵ��');
					}
				}

			}

		}elseif($_POST['codeid']=='2'){
			

			if(!preg_match("/1[3458]{1}\d{9}$/",$_POST['moblie'])){
		
				$this->errjson('�ֻ���ʽ����');
				
			}

			if($this->config['sy_msg_regcode']=="1")
			{
				if($_POST['moblie_code'])
				{
					$regCertMobile = $this->obj->DB_select_once("company_cert","`check`='".$_POST['moblie']."'");
				}
				if($regCertMobile['check2']!=$_POST['moblie_code'] || $regCertMobile['check2']=='')
				{
					
					$this->errjson('������֤�����');
				}
			}

			$_POST['username'] =  $_POST['moblie'];

		}elseif($_POST['codeid']=='3'){
			
			if(!$this->CheckRegEmail($_POST['email']) || $_POST['email']==""){
				
				$this->errjson('Email��ʽ���淶��');
			}
			$_POST['username'] =  $_POST['email'];
		}

		if($_POST['username']!="" && $arr['status']==''){
			$nid = $this->obj->DB_select_num("member","`username`='".$_POST['username']."'","uid");
			if($nid){
				$arr['status']=8;
				$arr['msg']='�˻����Ѵ��ڣ�';
			}else{
				if($_POST['usertype']=='1'){
					$satus = 1;
				}elseif($_POST['usertype']=='2'){
					$satus = $this->config['com_status'];
				}
				if($this->config['sy_uc_type']=="uc_center"){
					$this->obj->uc_open();
					$uid=uc_user_register($_POST['username'],$_POST['password'],$_POST['email']);
					if($uid<=0){
						$arr['status']=8;
						$arr['msg']='�������Ѵ��ڣ�';
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
				if($arr['status']==''){ 
					$ip = $this->obj->fun_ip_get();
					$data['username']=$_POST['username'];
					$data['password']=$pass;
					$data['usertype']=$_POST['usertype'];
					$data['email']=$_POST['email'];
					$data['moblie']=$_POST['moblie'];
					$data['status']=$satus;
					$data['salt']=$salt;
					$data['reg_date']=time();
					$data['reg_ip']=$ip;
					$data['qqid']=$_SESSION['qq']['openid'];
					$data['sinaid']=$_SESSION['sina']['openid'];
					$data['wxid']=$_SESSION['wx']['openid'];
					$data['regcode']=$_SESSION['regcode'];
					$userid=$this->obj->insert_into("member",$data);
					if(!$userid){
						$user_id = $this->obj->DB_select_once("member","`username`='".$_POST['username']."'","`uid`");
						$userid = $user_id['uid'];
					}
					if($userid){
						if($_SESSION['regcode']!="")
						{
							if($this->config['integral_invite_reg_type']=="1")
							{
								$auto=true;
							}else{
								$auto=false;
							}
							$this->obj->company_invtal($_SESSION['regcode'],$this->config['integral_invite_reg'],$auto,"����ע��",true,2,'integral',23);
						}
						$this->unset_cookie();
						if($this->config[sy_pw_type]=="pw_center"){
							$this->obj->DB_update_all("member","`pwuid`='".$pwuid."'","`uid`='".$userid."'");
						}
						if($_POST['usertype']=="1"){
							$table = "member_statis";
							$table2 = "resume";
							$value="`uid`='".$userid."',`integral`='".$this->config['integral_score']."'";
							$value2 = "`uid`='".$userid."',`email`='".$_POST['email']."',`telphone`='".$_POST['moblie']."',`name`='".$_POST['name']."'";
						}elseif($_POST['usertype']=="2"){
							$table = "company_statis";
							$table2 = "company";
							$value="`uid`='".$userid."',".$this->rating_info();
							$value2 = "`uid`='".$userid."',`linkmail`='".$_POST['email']."',`name`='".$_POST['unit_name']."',`linktel`='".$_POST['moblie']."',`address`='".$_POST['address']."',`linkman`='".$_POST['linkman']."'";
						}
						if($_POST['codeid']=='2' && $this->config['sy_msg_regcode']=="1")
						{
							$this->obj->DB_update_all("member","`moblie`='',","`moblie`='".$_POST['moblie']."'");
							if($usertype == '1')
							{
								$this->obj->DB_update_all("resume","`telphone`='',`moblie_status`='0'","`telphone`='".$_POST['moblie']."'");
								$value2.=",`moblie_status`='1'";
							}elseif($usertype == '2'){
								$this->obj->DB_update_all("company_statis","`linktel`='',`moblie_status`='0'","`linktel`='".$_POST['moblie']."'");
								$value.=",`moblie_status`='1'";
							}
						}
						$this->obj->DB_insert_once($table,$value);
						$this->obj->DB_insert_once($table2,$value2);
						$this->obj->DB_insert_once("friend_info","`uid`='".$userid."',`nickname`='".$_POST['username']."',`usertype`='".$_POST['usertype']."'");
						if($_POST['usertype']=="1"){
							if($this->config['user_status']=="1"){
								$randstr=rand(10000000,99999999);
								$base=base64_encode($userid."|".$randstr."|".$this->config['coding']);
								$data_cert['uid']=$userid;
								$data_cert['type']="cert";
								$data_cert['email']=$_POST['email'];
								$data_cert['url']="<a href='".$this->config['sy_weburl']."/index.php?m=qqconnect&c=mcert&id=".$base."'>�����֤</a>";
								$data_cert['date']=date("Y-m-d");
								$this->send_msg_email($data_cert);
								$arr['status']=7;
								$arr['msg']='�ʺż����ʼ��ѷ��͵������䣬���ȼ��';
							}else{
								$this->obj->DB_update_all("member","`login_date`='".time()."'","`uid`='".$userid."'");
								$this->add_cookie($userid,$_POST['username'],$salt,$_POST['email'],$pass,$usertype);
								$_POST['uid']=$userid;
								$this->regemail($_POST);
								$arr['status']=1;
							}
						}elseif($usertype=="2"){
							$_POST['uid']=$userid;
							$this->regemail($_POST);
							if($this->config['com_status']!="1"){
								$arr['status']=7;
								$arr['msg']='ע��ɹ�����ȴ�����Ա��ˣ�';
							}else{
								$arr['status']=1;
								$this->obj->DB_update_all("member","`login_date`='".time()."'","`uid`='".$userid."'");
								$this->add_cookie($userid,$_POST['username'],$salt,$_POST['email'],$pass,$usertype);
							}
						} 
					}else{
						$arr['status']=8;
						$arr['msg']='ע��ʧ�ܣ�';
					}
				}
			}
		}else if($_POST['username']==''&&$arr['status']==''){
			$arr['status']=8;
			$arr['msg']='�û�������Ϊ�գ�';
		}
		$arr['msg']=iconv("gbk","utf-8",$arr['msg']);
		echo json_encode($arr);die;
	}
	function regemail($post){
		if($this->config['sy_smtpserver']!="" && $this->config['sy_smtpemail']!="" && $this->config['sy_smtpuser']!=""){
			$this->send_msg_email(array("username"=>$post['username'],"password"=>$post['password'],"email"=>$post['email'],"type"=>"reg",'uid'=>$post['uid']));
		}
		if($this->config["sy_msguser"]!="" && $this->config["sy_msgpw"]!="" && $this->config["sy_msgkey"]!=""){
			$this->send_msg_email(array("username"=>$post['username'],"password"=>$post['password'],"moblie"=>$post['moblie'],"type"=>"reg",'uid'=>$post['uid']));
		}
	}
	function rating_info()
	{
		$id =$this->config['com_rating'];
		$row = $this->obj->DB_select_once("company_rating","`id`='".$id."'");
		$value="`rating`='".$id."',";
		$value.="`integral`='".$this->config['integral_reg']."',";
		$value.="`rating_name`='".$row['name']."',";
		$value.="`rating_type`='".$row['type']."',";
		$value.="`job_num`='".$row['job_num']."',";
		$value.="`down_resume`='".$row['resume']."',";
		$value.="`invite_resume`='".$row['interview']."',";
		$value.="`editjob_num`='".$row['editjob_num']."',";
		$value.="`breakjob_num`='".$row['breakjob_num']."',";
		if($row['service_time']>0)
		{
			$time=time()+86400*$row['service_time'];
		}else{
			$time=0;
		}
		$value.="`vip_etime`='".$time."'";
		return $value;
	}
}
?>