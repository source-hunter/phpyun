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
class email_controller extends common{
	function index_action(){
		$this->yuntpl(array('admin/admin_send_email'));
	}
	function send_action(){ 
		extract($_POST);
		if($email_title==''||$content==''){
			$this->obj->ACT_layer_msg("�ʼ����������Ϊ�գ�",8,$_SERVER['HTTP_REFERER']);
		} 
		$emailarr=$user=$com=$lt=$userinfo=array();
		if(@in_array(1,$all)){
			$userrows=$this->obj->DB_select_all("member","`usertype`='1'","email,`uid`,`usertype`");
		}
		if(@in_array(2,$all)){
			$userrows=$this->obj->DB_select_all("member","`usertype`='2'","email,`uid`,`usertype`");
		}
		if(@in_array(4,$all)){
			$userrows=$this->obj->DB_select_all("member","`usertype`='3'","email,`uid`,`usertype`");			
		}
		if(@in_array(3,$all)){
			$email_user=@explode(',',$_POST['email_user']); 
			$userrows=$this->obj->DB_select_all("member","`email` in('".@implode("','",$email_user)."')","email,`uid`,`usertype`");
		}
		if(is_array($userrows)&&$userrows){
			foreach($userrows as $v){
				if($v['usertype']=='1'){$user[]=$v['uid'];}
				if($v['usertype']=='2'){$com[]=$v['uid'];}
				if($v['usertype']=='3'){$lt[]=$v['uid'];}
				$emailarr[$v['uid']]=$v["email"];
			}
			if($user&&is_array($user)){
				$resume=$this->obj->DB_select_all("resume","`uid` in(".@implode(',',$user).")","`name`,`uid`");
				foreach($resume as $val){
					$userinfo[$val['uid']]=$val['name'];
				}
			}
			if($com&&is_array($com)){
				$company=$this->obj->DB_select_all("company","`uid` in(".@implode(',',$com).")","`name`,`uid`");
				foreach($company as $val){
					$userinfo[$val['uid']]=$val['name'];
				}
			}
			
		} 
		if(!count($emailarr)){ 
			$this->obj->ACT_layer_msg("û�з������������䣬���ȼ�飡",8,$_SERVER['HTTP_REFERER']);
		}
		set_time_limit(10000);
		$emailid=$this->send_email($emailarr,$email_title,$content,true,$userinfo);
	
	}
}

?>