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
class commsg_controller extends user{
	function index_action()
	{
		$this->public_action();
		$urlarr=array("c"=>"commsg","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("msg","`uid`='".$this->uid."' order by id desc",$pageurl,"10");
		$this->obj->DB_update_all("msg","`user_remind_status`='1'","`uid`='".$this->uid."' and `user_remind_status`='0'");
		$this->unset_remind("usermsg",'1');
		$this->user_tpl('commsg');
	}
	function del_action(){
		$del=(int)$_GET['id'];
		$nid=$this->obj->DB_delete_all("msg","`id`='".$del."' and `uid`='".$this->uid."'");
		if($nid){
			$this->obj->member_log("ɾ����ְ��ѯ");
			$this->layer_msg('ɾ���ɹ���',9,0,"index.php?c=commsg");
		}else{
			$this->layer_msg('ɾ��ʧ�ܣ�',8,0,"index.php?c=commsg");
		}
	}
}
?>