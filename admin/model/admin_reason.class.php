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
class admin_reason_controller extends common{
	function index_action(){
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$reason=$this->get_page("reason","1",$pageurl,$this->config['sy_listnum']);
		$this->yunset("reason",$reason);
		$this->yuntpl(array('admin/admin_reason_list'));
	}
	function add_action(){
		$reason_show=$this->obj->DB_select_once("reason","id='".$_GET['id']."'");
		$this->yunset("reason_show",$reason_show);
		$this->index_action();
	}

	function save_action(){
		$this->check_token();
		$url="index.php?m=admin_reason";
		if($_GET['id']){
			$nbid=$this->obj->DB_update_all("reason","`name`='".trim($_GET['name'])."'","`id`='".$_GET['id']."'");
			isset($nbid)?$this->obj->ACT_layer_msg("�ٱ�ԭ��(ID:".$_GET['id'].")���³ɹ���",9,$url,2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$url);
		}else{
			$nbid=$this->obj->DB_insert_once("reason","`name`='".trim($_GET['name'])."'");
			isset($nbid)?$this->obj->ACT_layer_msg("�ٱ�ԭ��(ID:".$nbid.")��ӳɹ���",9,$url,2,1):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,$url);
		}
	}

	function del_action(){
		$this->check_token();
		if($_GET['del']){
	    	if($_GET['del']){
				$a_del=@implode(',',$_GET['del']);
				$this->obj->DB_delete_all("reason","`id` in(".$a_del.")","");
				$this->layer_msg('�ٱ�ԭ��(ID:'.$a_del.')ɾ���ɹ���',9,1,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('��ѡ����Ҫɾ�������ݣ�',8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("reason", "`id`='".$_GET['id']."'");
 			isset($result)?$this->layer_msg("�ٱ�ԭ��(ID:".$_GET['id']."')ɾ���ɹ���",9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('�Ƿ�������',3,1,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>