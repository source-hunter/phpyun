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
class message_controller extends common{
	function index_action(){
		$this->yuntpl(array('admin/admin_user_message'));
	}

	function save_action(){
		extract($_POST);
		$uidarr=array();
		if($all==1){
			$userrows=$this->obj->DB_select_all("member","`usertype`='1'");
			if(is_array($userrows)){
				foreach($userrows as $v){
					$uidarr[]=$v["uid"];
				}
			}
		}
		if($all==2){
			$userrows=$this->obj->DB_select_all("member","`usertype`='2'");
			if(is_array($userrows)){
				foreach($userrows as $v){
					$uidarr[]=$v["uid"];
				}
			}
		}
		if($all==3){
			$useremail=@explode(',',$userarr);
			if(is_array($useremail)){
				foreach($useremail as $v){
					$userarr=$this->obj->DB_select_once("member","`username`='".$v."'");
					if(is_array($userarr)){
						$uidarr[]=$userarr["uid"];
					}
				}
			}
		}
		if(!count($uidarr)){
			$this->obj->ACT_layer_msg("û�з����������û������ȼ�飡",8,$_SERVER['HTTP_REFERER']); 
		}
		$emailid=$this->send_message($uidarr,$message_title,$content,true);
	}
}
?>