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
class login_controller extends common
{
	function index_action()
	{
		$this->get_moblie();
		if($this->uid || $this->username)
		{
			if($_GET['bind']=='1')
			{
				SetCookie("uid","",time() -286400, "/");
				SetCookie("username","",time() - 86400, "/");
				SetCookie("salt","",time() -86400, "/");
				$this->wapheader('index.php?m=login&wxid='.$_GET['wxid'].'&',"���°�������ְ�˻���");

			}else{
				$this->wapheader('member/index.php');
			}
			
		}
		if($_POST['submit'])
		{
			if($_POST['wxid'])
			{
				$wxparse = '&wxid='.$_POST['wxid'];
			}
			$usertype=$_POST['usertype']?intval($_POST['usertype']):1;
			$username = str_replace('\\','',$_POST['username']);

			if(!$this->CheckRegUser($username))
			{
				$this->wapheader('index.php?m=login&username='.$username.'&','��Ч���û�����');
			}
			if($usertype>0 && $username!='')
			{
				$userinfo = $this->obj->DB_select_once("member","`username`='".str_replace('\\','',$_POST['username'])."' and usertype='".$usertype."'","username,usertype,password,uid,salt");
				if(!is_array($userinfo))
				{
					$this->wapheader('index.php?m=login&username='.$username.'&usertype='.$usertype.$wxparse.'&','�û�������');
				}
				$pass = md5(md5($_POST['password']).$userinfo['salt']);
				if($pass!=$userinfo['password'])
				{
					$this->wapheader('index.php?m=login&username='.$username.'&usertype='.$usertype.$wxparse.'&','���벻��ȷ��');
				
				}else{
					
					if($_POST['wxid'])
					{
						$this->obj->update_once('member',array('wxid'=>''),array('wxid'=>$_POST['wxid']));
						$this->obj->update_once('member',array('wxid'=>$_POST['wxid']),array('uid'=>$userinfo['uid']));
					}

					setcookie("uid",$userinfo['uid'],time() + 86400, "/");
					setcookie("username",$userinfo['username'],time() + 86400, "/");
					setcookie("usertype",$userinfo['usertype'],time() + 86400, "/");
					setcookie("salt",$userinfo['salt'],time() + 86400, "/");
					setcookie("shell",md5($userinfo['username'].$userinfo['password'].$userinfo['salt']), time() + 86400,"/");
					if($_POST['wxid']){
						
						$this->wapheader('index.php?','�󶨳ɹ����밴���Ϸ����ؽ���΢�ſͻ���');
						
					}else{
						if(!($this->config['sy_wapdomain'])){
							$this->wapheader($this->config['sy_weburl'].'/'.$this->config['sy_wapdir'].'/member/index.php');
						}else{
							$this->wapheader($this->config['sy_wapdomain'].'/member/index.php');
						}
					}
				}
			}else{
				$this->wapheader('index.php?m=login&username='.$username.'&','���ݴ���');
			}
		}
		if($_GET['usertype']=="2")
		{
			$this->yunset("title","��ҵ��Ա��¼");
		}else{
			$this->yunset("title","���˻�Ա��¼");
		}
		$this->yuntpl(array('wap/login'));
	}
}
?>