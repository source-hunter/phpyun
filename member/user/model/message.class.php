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
class message_controller extends user{
	function index_action()
	{
		if($_POST['add_message'])
		{
			$data['content']=$_POST['content'];
			$data['uid']=$this->uid;
			$data['username']=$this->username;
			$data['ctime']=mktime();
			$nid=$this->obj->insert_into("message",$data);
			if($nid)
			{
				$this->obj->member_log("�������Է���");
				$this->obj->ACT_layer_msg("���Է��ͳɹ���",9,"index.php?c=seemessage");
			}else{
				$this->obj->ACT_layer_msg("���Է���ʧ�ܣ�",8,"index.php?c=seemessage");
			}
		}
		$this->public_action();
		$this->user_tpl('message');
	}

}
?>