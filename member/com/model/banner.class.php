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
class banner_controller extends company
{
	function index_action()
	{
		if($_POST['submit'])
		{
			$upload=$this->upload_pic("../upload/company/",false,$this->config['com_uppic']);
			$pic=$upload->picture($_FILES['pic']);
			$this->picmsg($pic,$_SERVER['HTTP_REFERER']);
			$data['uid']=$this->uid;
			$data['pic']=$pic;
			$this->obj->insert_into("banner",$data);
			$this->obj->member_log("�ϴ���ҵ���");
			$this->get_integral_action($this->uid,"integral_banner","�ϴ���ҵ���");
 			$this->obj->ACT_layer_msg("���óɹ���",9,"index.php?c=banner");
		}
		if($_POST['update'])
		{
			$upload=$this->upload_pic("../upload/company/",false,$this->config['com_uppic']);
			$pic=$upload->picture($_FILES['pic']);
			$this->picmsg($pic,$_SERVER['HTTP_REFERER']);
			$row=$this->obj->DB_select_once("banner","`uid`='".$this->uid."'");
			if(is_array($row))
			{
				$this->obj->unlink_pic($row['pic']);
			}
			$this->obj->update_once("banner",array("pic"=>$pic),array("uid"=>$this->uid));
			$this->obj->member_log("�༭��ҵ���");
 			$this->obj->ACT_layer_msg("���óɹ���",9,"index.php?c=banner");
		}
		$banner=$this->obj->DB_select_once("banner","`uid`='".$this->uid."'");
		$this->yunset("banner",$banner);
		$this->public_action();
		$this->yunset("js_def",2);
		$this->com_tpl("banner");
	}
}
?>