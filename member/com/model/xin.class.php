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
class xin_controller extends company{ 
	function index_action()
	{
		$where.= "`uid`='".$this->uid."' or (`fid`='".$this->uid."' and `status`<>'1') order by ctime desc";
		$urlarr['c'] = $_GET['c'];
		$urlarr["page"] = "{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("friend_message",$where,$pageurl,"12");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
				$uid[]=$v['fid'];
			}
			$statis =$this->obj->DB_select_all("friend_info","`uid` in (".@implode(",",$uid).")","`uid`,`nickname`");
			foreach($rows as $key=>$value)
			{
				foreach($statis as $k=>$v)
				{
					if($value['uid']==$v['uid'])
					{
						  $rows[$key]['nickname'] = $v['nickname'];
					}
					if($value['fid']==$v['uid'])
					{
						  $rows[$key]['name'] = $v['nickname'];
					}
				}
			}
		}
		$this->yunset("ownuid",$this->uid);
		$this->yunset("rows",$rows);
		$this->obj->DB_update_all("friend_message","`remind_status`='1'","`fid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("friend_message2",'2');
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('xin');
	}
	function save_action(){
		if($_POST['submit']){
			$data['content']=trim($_POST['content']);
			$data['ctime']=time();
			$data['pid']=$_POST['pid'];
			$data['fid']=$_POST['fid'];
			$data['nid']=$this->uid;
			$data['uid']=$this->uid;
			$nid=$this->obj->insert_into("friend_message",$data);
			if($nid){
				$this->obj->member_log("�ظ�վ����");
				$this->obj->ACT_layer_msg("�ظ��ɹ���",9,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function del_action(){
		if($_GET['id']){
			$nid = $this->obj->DB_delete_all("friend_message","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
		}else{
			$where['id']=(int)$_GET['did'];
			$where['fid']=$this->uid;
			$nid = $this->obj->update_once("friend_message",array("status"=>"1"),$where);
		}
		if($nid){
			$this->unset_remind("friend_message2",'2');
			$this->obj->member_log("ɾ��վ����");
			$this->layer_msg('ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>