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
class usercert_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'���״̬',"value"=>array("1"=>"�����","2"=>"δ���"));
		$lo_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"time","name"=>'����ʱ��',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
		$where="`idcard_pic`<>''";
		if($_GET['status']){
			if($_GET['status']==1){
				$where.=" and `idcard_status`='1'";
				$urlarr['status']='1';
			}else if($_GET['status']==2){
				$where.=" and `idcard_status`='0'";
				$urlarr['status']='2';
			}
		}
		if(trim($_GET['keyword'])){
			$where.=" and `name` like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['time']){
			if($_GET['time']=='1'){
				$where.=" and `cert_time` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `cert_time` >= '".strtotime('-'.(int)$_GET['time'].'day')."'";
			}
			$urlarr['time']=$_GET['time'];
		}
		if($_GET['order']){
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.="order by `cert_time` desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("resume",$where,$pageurl,$this->config['sy_listnum']);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_user_cert'));
	}
	function status_action(){
		extract($_POST);
		$id=$this->obj->DB_update_all("resume","`idcard_status`='$status',`statusbody`='".$statusbody."'","`uid`=$uid");
		$this->obj->DB_update_all("friend_info","`iscert`='$status'","`uid`=$uid");
		if($this->config['sy_email_usercert']=='1'){
			$userinfo = $this->obj->DB_select_once("member","`uid`=".$uid,"`email`,`name`,`uid`,`usertype`");
			$data=$this->forsend($userinfo);
			$this->send_msg_email(array("email"=>$userinfo['email'],'uid'=>$data['uid'],'name'=>$data['name'],"certinfo"=>$statusbody,"username"=>$userinfo['username'],"type"=>"usercert"));
		}
		if($id)
		{
			$this->get_integral_action($this->uid,"integral_identity","�ϴ������֤");
			$this->obj->ACT_layer_msg( "������֤���(ID:".$uid.")���óɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
		}else{
			$this->obj->ACT_layer_msg( "����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function sbody_action(){
		$userinfo = $this->obj->DB_select_once("resume","`uid`=".$_POST['uid'],"`statusbody`");
		echo $userinfo['statusbody'];die;
	}


	function idcard_status_action(){
		$this->obj->DB_update_all("resume","`idcard_status`='".$_POST['idcard_status']."'","`uid` IN (".$_POST['allid'].")");
		$this->obj->admin_log("������֤(ID:".$_POST['allid'].")��˳ɹ�");
		echo $_POST['idcard_status'];die;
	}


	function del_action(){
		$this->check_token();
		if(is_array($_GET['del'])){
			$linkid=@implode(',',$_GET['del']);
			$layer_type=1;
		}else{
			$linkid=$_GET['id'];
			$layer_type=0;
		}
		if($linkid==""){
			$this->layer_msg('��ѡ����Ҫɾ�������ݣ�',8,1,$_SERVER['HTTP_REFERER']);
		}
	    $cert=$this->obj->DB_select_all("resume","`uid` in ($linkid)","`idcard_pic`");
	    if(is_array($cert)){
	     	foreach($cert as $v){
	     		$this->obj->unlink_pic($v['idcard_pic']);
	     	}
	    }
		$del=$this->obj->DB_update_all("resume","`idcard_pic`='',`idcard_status`='0',`cert_time`='',`statusbody`=''","`uid` in ($linkid)","");
		$del?$this->layer_msg('������֤���(ID:'.$linkid.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}

}

?>