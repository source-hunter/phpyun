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
			$this->obj->ACT_layer_msg("��֤�����",8,"index.php?m=forgetpw");
		}

		$info = $this->obj->DB_select_once("member","`username`='".$_POST['username']."'","`uid`,`email_status`,`email`,`usertype`");
		if(is_array($info)&&$info){

			if($info[email_status]=="1"){ 
				$this->obj->ACT_layer_msg("�����˻��Ѿ������ֱ�ӵ�¼��",9,"index.php?m=login&usertype=1");
			}
			$fdata=$this->forsend($info);
			$randstr=rand(10000000,99999999);
			$base=base64_encode($info[uid]."|".$randstr."|".$this->config[coding]);
			$data["uid"]=$info[uid];
			$data["name"]=$fdata[name];
			$data["type"]="cert";
			$data["email"]=$info[email];
			$data["url"]="<a href='".$this->config[sy_weburl]."/index.php?m=qqconnect&c=mcert&id=".$base."'>�������</a>";
			$data["date"]=date("Y-m-d");
			$this->send_msg_email($data); 
			$this->obj->ACT_layer_msg("�����ʼ��Ѿ����͵��������䣡",9,"index.php?m=sendcert");
		}else{ 
			$this->obj->ACT_layer_msg("�Բ���û�и��û���",8,"index.php?m=login");
		}
	}

}