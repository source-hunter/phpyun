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
class comnews_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'���״̬',"value"=>array("1"=>"�����","3"=>"δ���","2"=>"δͨ��"));
		$lo_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"time","name"=>'����ʱ��',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
      if($_GET['m']=='comnews'){
		if(trim($_GET['keyword'])){
			if($_GET['type']){
				if($_GET['type']=="1"){
					$where.=" and b.`name` like '%".$_GET['keyword']."%'";
				}else{
					$where.=" and a.`title` like '%".$_GET['keyword']."%'";
				}
				$urlarr['type']=$_GET['type'];
			}
			$urlarr['keyword']=$_GET['keyword'];
		}elseif($_GET['status']){
			if($_GET['status']=="3"){
				$where.=" and a.`status`='0'";
			}else{
				$where.=" and a.`status`='".$_GET['status']."'";
			}
			$urlarr['status']=$_GET['status'];
		}
      }
		if($_GET['time']){
			if($_GET['time']=='1'){
				$where.=" and a.`ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and a.`ctime` >= '".strtotime('-'.(int)$_GET['time'].'day')."'";
			}
			$urlarr['time']=$_GET['time'];
		}
	
		if($_GET['order'])
		{
			$where.=" order by a.`".$_GET['t']."` ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by a.`id` desc";
		}
		$urlarr['order']=$_GET['order'];
		include(LIB_PATH."page3.class.php");
		$limit=$this->config['sy_listnum'];
		$page=$_GET['page']<1?1:$_GET['page'];
		$ststrsql=($page-1)*$limit;
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$count=$this->obj->DB_select_alls("company_news","company","a.`uid`=b.`uid` $where","a.uid");
 		$num = count($count);
 		$page = new page($page,$limit,$num,$pageurl);
		$pagenav=$page->numPage();
		$rows=$this->obj->DB_select_alls("company_news","company","a.`uid`=b.`uid` $where  limit $ststrsql,$limit","b.name,a.*");
		$this->yunset("pagenav",$pagenav);
		$this->yunset("rows",$rows);
		$this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_comnews'));
	}
	function statusbody_action(){
		$userinfo = $this->obj->DB_select_once("company_news","`id`=".$_GET['id'],"`statusbody`");
		echo $userinfo['statusbody'];die;
	}
	function status_action(){
		extract($_POST);
		$id = @explode(",",$pid);
		
		if(is_array($id)){
			foreach($id as $value){
				$idlist[] = $value;
			}
			$aid = @implode(",",$idlist);
			$id=$this->obj->DB_update_all("company_news","`status`='$status',`statusbody`='".$statusbody."'","`id` IN ($aid)");
 			$id?$this->obj->ACT_layer_msg("���(ID:".$aid.")���óɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("�Ƿ�������",9,$_SERVER['HTTP_REFERER']);
		}
	}
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
				foreach($del as $v){
					$this->del_com($v);
				}
	    		$this->layer_msg( "����(ID:".@implode(',',$del).")ɾ���ɹ���",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
	    		$this->layer_msg( "��ѡ����Ҫɾ������Ϣ��",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }

	    if(isset($_GET['id'])){
			$result=$this->del_com($_GET['id']);
			$result?$this->layer_msg('����(ID:'.@implode(',',$_GET['id']).')ɾ���ɹ���',9):$this->layer_msg('ɾ��ʧ�ܣ�',8);
		}else{
			$this->layer_msg('�Ƿ�������',3);
		}
	}
	function del_com($id)
	{
		 $id_arr = @explode("-",$id);
		if($id_arr[0])
		{
			$result=$this->obj->DB_delete_all("company_news","`id`='".$id_arr[0]."'" );
		}
		return $result;
	}
}
?>