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
class invitereg_controller extends common
{
	function index_action()
	{
		if($this->uid=="")
		{
			$this->obj->ACT_msg($this->config['sy_weburl'], "����û�е�¼�����ȵ�¼��");
		}
		if($_POST['submit'])
		{
			if($this->config["sy_smtpserver"]=="" || $this->config["sy_smtpemail"]=="" ||	$this->config["sy_smtpuser"]==""){
			
				$this->obj->ACT_layer_msg("��վ�ʼ��������ݲ����ã�",8,$_SERVER['HTTP_REFERER']);
			}
			if(!$this->CheckRegEmail($_POST['email']))
			{
				$this->obj->ACT_layer_msg("�ʼ���ʽ����ȷ��",8,$_SERVER['HTTP_REFERER']);
			}
			if($_POST['content']=="")
			{
				$this->obj->ACT_layer_msg("���ݲ���Ϊ�գ�",8,$_SERVER['HTTP_REFERER']);
			}
			if($this->config['sy_smtpserver']=="" || $this->config['sy_smtpemail']=="" || $this->config['sy_smtpuser']=="")
			{
				$this->obj->ACT_layer_msg("��û���������䣬����ϵ����Ա��",8,$_SERVER['HTTP_REFERER']);
			}
			$smtp=$this->email_set();
			$smtpusermail =$this->config['sy_smtpemail'];
	 		$sendid = $smtp->sendmail($_POST['email'],$smtpusermail,"����ע��",$_POST['content']);
	 		if($sendid)
	 		{
	 			$this->obj->ACT_layer_msg("����ע���ʼ��ѷ��ͣ�",9,$_SERVER['HTTP_REFERER']);
	 		}else{
	 			$this->obj->ACT_layer_msg("����ע���ʼ�����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
	 		}
		}
		$this->seo("invitereg");
		$this->yun_tpl(array('index'));
	}
}
?>