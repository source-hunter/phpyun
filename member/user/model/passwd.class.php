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
class passwd_controller extends user{
	function index_action()
	{
		$this->public_action();
		if($_POST['submit'])
		{
			$info = $this->obj->DB_select_once("member","`uid`='".$this->uid."'","`salt`,`password`,`name_repeat`,`username`");
			if(is_array($info))
			{
				$oldpass = md5(md5($_POST['oldpassword']).$info['salt']);
				if($info['password']!=$oldpass)
				{
					$this->obj->ACT_layer_msg("ԭʼ�������", 8,"index.php?c=passwd");
				}
				if($this->config['sy_uc_type']=="uc_center" &&$info['name_repeat']!="1")
				{
					$this->obj->uc_open();
					$ucresult= uc_user_edit($info['username'], $_POST['oldpassword'], $_POST['password'], "","1");
					if($ucresult == -1)
					{
						$this->obj->ACT_layer_msg("ԭʼ�������", 8,"index.php?c=passwd");
					}
				}else{
					$salt = substr(uniqid(rand()), -6);
					$pass2 = md5(md5($_POST['password']).$salt);
					$this->obj->update_once('member',array('password'=>$pass2,'salt'=>$salt),array('uid'=>$this->uid));

				}
				$this->unset_cookie();
				$this->obj->member_log("�޸�����",8);
				$this->obj->ACT_layer_msg("�޸ĳɹ��������µ�¼��", 9,$this->config['sy_weburl']."/index.php?m=login&usertype=".$_POST['usertype']);
			}
		}
		$this->user_tpl('passwd');
	}
}
?>