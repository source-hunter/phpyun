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
class sysnews_controller extends user
{
	function index_action()
	{
		$where.= "`fa_uid`='".$this->uid."' order by id desc";
		$urlarr["c"] = $_GET["c"];
		$urlarr["page"] = "{{page}}";
		$pageurl = $this->url("index","index",$urlarr);
		$rows=$this->get_page("sysmsg",$where,$pageurl,"10");
		$this->obj->DB_update_all("sysmsg","`remind_status`='1'","`fa_uid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("sysmsg1",'1'); 
		$this->public_action();
		$this->user_tpl('sysnews');
	}
	function del_action()
	{
		$nid = $this->obj->DB_delete_all("sysmsg","`id`='".(int)$_GET['id']."' and `fa_uid`='".$this->uid."'");
		if($nid){
			$this->unset_remind("sysmsg1",'1'); 
			$this->obj->member_log("ɾ��ϵͳ��Ϣ"); 
			$this->layer_msg('ɾ���ɹ���',9);
		}else{
			$this->layer_msg('ɾ��ʧ�ܣ�',8);
		}
	}
}
?>