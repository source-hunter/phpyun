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
class msg_controller extends user{
	function index_action(){
		$this->public_action();
		$urlarr=array("c"=>"msg","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("userid_msg","`uid`='".$this->uid."' and `type`<>'1' order by id desc",$pageurl,"20");
 		$this->yunset("js_def",4);
		$this->user_tpl('msg');
	}
	function shield_action(){
		if($_GET['id']){
			$info=$this->obj->DB_select_once("userid_msg","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			$data['p_uid']=$info['fid'];
			$data['inputtime']=mktime();
			$data['c_uid']=$this->uid;
			$data['usertype']=1;
			$data['com_name']=$info['fname'];
			$haves=$this->obj->DB_select_once("blacklist","`c_uid`='".$this->uid."' and `p_uid`='".$info['fid']."'  and `usertype`='1'");
			if(is_array($haves)){
				$this->obj->ACT_layer_msg("���û��������������У�",8,$_SERVER['HTTP_REFERER']);
			}else{
				$nid=$this->obj->insert_into("blacklist",$data);
				$this->obj->DB_delete_all("userid_msg","`uid`='".$this->uid."' and `fid`='".$info['fid']."'"," ");
				if($nid)
				{
					$this->obj->member_log("���ι�˾ <".$info['fname']."> ����ɾ��������Ϣ");
					$this->layer_msg('�����ɹ���',9,0,"index.php?c=msg");
				}else{
					$this->layer_msg('����ʧ�ܣ�',8,0,"index.php?c=msg");
				}
			}
		}
	}
	function del_action(){
		if($_GET['id']){
			$del=(int)$_GET['id'];
			$nid=$this->obj->DB_delete_all("userid_msg","`id`='".$del."' and `uid`='".$this->uid."'");
			if($nid){
				$this->unset_remind("userid_msg",'1');
				$this->obj->member_log("ɾ��������Ϣ");
				$this->layer_msg('ɾ���ɹ���',9,0,"index.php?c=msg");
			}else{
				$this->layer_msg('ɾ��ʧ�ܣ�',8,0,"index.php?c=msg");
			}
		}
	}
	function ajax_action()
	{
		if($_POST['id'])
		{
			$this->obj->DB_update_all("userid_msg","`is_browse`='2'","`uid`='".$this->uid."' and `id`='".(int)$_POST['id']."'");
			$this->unset_remind("userid_msg",'1');
			$row=$this->obj->DB_select_once("userid_msg","`uid`='".$this->uid."' and `id`='".(int)$_POST['id']."'");
			$arr['jobname']=iconv("gbk","utf-8",$row['jobname']);
			$arr['linkman']=iconv("gbk","utf-8",$row['linkman']);
			$arr['linktel']=iconv("gbk","utf-8",$row['linktel']);
			$arr['intertime']=iconv("gbk","utf-8",$row['intertime']);
			$arr['address']=iconv("gbk","utf-8",$row['address']);
			$arr['content']=iconv("gbk","utf-8",$row['content']);
			echo json_encode($arr);die;
		}
	}
}
?>