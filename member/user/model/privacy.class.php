<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2015 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class privacy_controller extends user{

	function index_action()
	{
		if(intval($_POST['status']))
		{
			if($_POST['type'] =='status' || $_POST['type']=='info_status')
			{

				$this->obj->DB_update_all("resume","`".$_POST[type]."`='".intval($_POST['status'])."'","`uid`='".$this->uid."'");
				$this->obj->member_log("���ü����Ƿ񹫿�");
			}
		}
		$resume = $this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`status`,`info_status`");
        $this->yunset("resume",$resume);
        $this->yunset("js_def",2);
		$this->public_action();
		$this->user_tpl('privacy');
	}
}
?>