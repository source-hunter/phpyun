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
class information_controller extends common
{
	function index_action(){

		$this->yuntpl(array('admin/information'));
	}
	function save_action(){
		extract($_POST); 
		if(trim($content)==''){
			$this->obj->ACT_layer_msg("������������ݣ�",8,$_SERVER['HTTP_REFERER']);
		}
		$uidarr=array();
		if($all==4){
			$userrows=$this->obj->DB_select_all("member","`moblie` in(".$userarr.")","`moblie`,`uid`,`usertype`");
		}else{
			$userrows=$this->obj->DB_select_all("member","`usertype`='".$all."'","`moblie`,`uid`,`usertype`");
		}
		if(is_array($userrows)&&$userrows){ 
			$user=$com=$lt=$userinfo=array();
			foreach($userrows as $v){
				if($v['usertype']=='1'){$user[]=$v['uid'];}
				if($v['usertype']=='2'){$com[]=$v['uid'];}
				if($v['usertype']=='3'){$lt[]=$v['uid'];}
				$uidarr[$v['uid']]=$v["moblie"]; 
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
		if(is_array($uidarr)&&$uidarr){
			if($this->config["sy_msguser"]=="" || $this->config["sy_msgpw"]=="" || $this->config["sy_msgkey"]==""){ 
				$this->obj->ACT_layer_msg("��û�����ö��ţ�",8,$_SERVER['HTTP_REFERER']);
			}
			foreach($uidarr as $key=>$v){
				$msguser=$this->config["sy_msguser"];
				$msgpw=$this->config["sy_msgpw"];
				$msgkey=$this->config["sy_msgkey"];
				$result=$this->obj->sendSMS($msguser,$msgpw,$msgkey,$v,$content,'','',array('uid'=>$key,'name'=>$userinfo[$key]));
			}
		} 
		$this->obj->ACT_layer_msg($result,14,$_SERVER['HTTP_REFERER'],2,1);
	}
}
?>