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
class seemessage_controller extends user{
	function index_action()
	{
		$this->public_action();
		$urlarr=array("c"=>"seemessage","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("message","`uid`='".$this->uid."' order by id desc",$pageurl,"10");
		$this->user_tpl('seemessage');
	}
   function del_action()
   {
		if($_GET['id'])
		{
			$nid=$this->obj->DB_delete_all("message","`id`='".(int)$_GET['id']."' AND `uid`='".$this->uid."'"," ");
			$this->obj->member_log("ɾ�����Է�����Ϣ");
			if($nid){
				$this->layer_msg('ɾ���ɹ���',9);
			}else{
				$this->layer_msg('ɾ��ʧ�ܣ�',8);
			}
		}
   }
}
?>