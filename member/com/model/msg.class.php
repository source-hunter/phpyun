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
class msg_controller extends company
{
	function index_action(){
		$urlarr=array("c"=>"msg","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("msg","`job_uid`='".$this->uid."' and `del_status`<>'1' order by datetime desc",$pageurl,"15");
		if(is_array($rows)&&$rows){
			foreach($rows as $key=>$val){
				$rows[$key]['content']=strip_tags(trim($val['content']));
			}
		}
		$this->obj->DB_update_all("msg","`com_remind_status`='1'","`job_uid`='".$this->uid."' and `com_remind_status`='0'");
		$this->unset_remind("commsg",'2');
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('msg');
	}
	function del_action(){
		if($_GET['id']){
			$where['id']=(int)$_GET['id'];
			$where['job_uid']=$this->uid;
			$nid=$this->obj->update_once("msg",array("del_status"=>"1"),$where);
 			if($nid)
 			{
 				$this->unset_remind("commsg",'2');
 				$this->obj->member_log("ɾ����ְ��ѯ");
 				$this->layer_msg('ɾ���ɹ���',9,0,"index.php?c=msg");
 			}else{
 				$this->layer_msg('ɾ��ʧ�ܣ�',8,0,"index.php?c=msg");
 			}
		}
	}
	function save_action(){
		if($_POST['submit']){
			$data['reply']=$_POST['reply'];
			$data['reply_time']=time();
			$data['user_remind_status']='0';
			$where['id']=(int)$_POST['id'];
			$where['job_uid']=$this->uid;
			$id=$this->obj->update_once("msg",$data,$where);
 			if($id){
 				$this->obj->member_log("�ظ���ְ��ѯ");
 				$this->obj->ACT_layer_msg("�ظ��ɹ���",9,"index.php?c=msg");
 			}else{
 				$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,"index.php?c=msg");
 			}
		}
	}
}
?>