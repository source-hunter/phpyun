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
class emailmsg_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"state","name"=>'����״̬',"value"=>array("1"=>"���ͳɹ�","2"=>"����ʧ��"));
		$lo_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"time","name"=>'����ʱ��',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
		$where="1";
		if($_GET['state']=="1"){
			$where.=" and `state`='1'";
			$urlarr['state']='1';
		}elseif($_GET['state']=="2"){
			$where.=" and `state`='0'";
			$urlarr['state']='2';
		}
		if($_GET['keyword']){
			if ($_GET['type']=='1'){
				$where.=" and `email` like '%".$_GET['keyword']."%'";
			}else if($_GET['type']=='3'){
				$where.=" and `name` like '%".$_GET['keyword']."%'";
			}else{
				 $where.=" and `cname` like '%".$_GET['keyword']."%'";
			}
			$urlarr['type']="".$_GET['type']."";
			$urlarr['keyword']="".$_GET['keyword']."";
		}
		if($_GET['time']){
			if($_GET['time']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.$_GET['time'].'day')."'";
			}
			$urlarr['time']=$_GET['time'];
		}
		if($_GET['order']){
			if($_GET['order']=="desc"){
				$order=" order by `".$_GET['t']."` desc";
			}else{
				$order=" order by `".$_GET['t']."` asc";
			}
		}else{
			$order=" order by `id` desc";
		}
		if($_GET['order']=="asc"){
			$this->yunset("order","desc");
		}else{
			$this->yunset("order","asc");
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("email_msg",$where.$order,$pageurl,$this->config['sy_listnum']);
		$this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_emailmsg'));
	}
	function del_action(){

		if(is_array($_POST['del'])){
			$delid=@implode(',',$_POST['del']);
			$layer_type=1;
		}else{
			$this->check_token();
			$delid=(int)$_GET['id'];
			$layer_type=0;
		}
		if(!$delid){
			$this->layer_msg('��ѡ��Ҫɾ�������ݣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}
		$del=$this->obj->DB_delete_all("email_msg","`id` in ($delid)"," ");
		$del?$this->layer_msg('�ʼ���¼(ID:'.$delid.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
}

?>