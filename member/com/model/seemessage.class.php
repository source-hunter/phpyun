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
class seemessage_controller extends company
{
	function index_action()
	{
		$urlarr=array("c"=>"seemessage","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("message","`uid`='".$this->uid."' order by id desc",$pageurl,"10");
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('seemessage');
	}
	function del_action()
	{
		if($_GET['id'])
		{
			$nid=$this->obj->DB_delete_all("message","`id`='".(int)$_GET['id']."' AND `uid`='".$this->uid."'"," ");
 			if($nid){
 				$this->obj->member_log("ɾ�����Է���");
 				$this->layer_msg('ɾ���ɹ���',9,0,"index.php?c=seemessage");
 			}else{
 				$this->layer_msg('ɾ��ʧ�ܣ�',8,0,"index.php?c=seemessage");
 			}
		}
	}
}
?>