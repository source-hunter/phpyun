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
class comproduct_controller extends common
{
	
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'���״̬',"value"=>array("1"=>"�����","3"=>"δ���","2"=>"δͨ��"));
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'����ʱ��',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
		if($_GET['m']=='comproduct'){
 		    if(trim($_GET['keyword'])){
	 			if($_GET['type']=="1"){
					$where.=" and b.`name` like '%".$_GET['keyword']."%'";
				}else{
					$where.=" and a.`title` like '%".$_GET['keyword']."%'";
				}
				$urlarr['type']=$_GET['type'];
				$urlarr['keyword']=$_GET['keyword'];
 		    }elseif($_GET['status']){
                 if($_GET['status']=='3'){
                 	$where.=" and a.`status`='0'";
                 }else{
                 	$where.=" and a.`status`='".$_GET['status']."'";
                 }
                 $urlarr['status']=$_GET['status'];
 		    }
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and a.`ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and a.`ctime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}


		if($_GET['order']){
			$order=$_GET['order'];
		}else{
			$order="desc";
		}
		$urlarr['order']=$_GET['order'];
		include(LIB_PATH."page3.class.php");
		$limit=$this->config['sy_listnum'];
		$page=$_GET['page']<1?1:$_GET['page'];
		$ststrsql=($page-1)*$limit;
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$count=$this->obj->DB_select_alls("company_product","company","a.`uid`=b.`uid` $where","a.uid");
 		$num = count($count);
 		$page = new page($page,$limit,$num,$pageurl);
		$pagenav=$page->numPage();
		$rows=$this->obj->DB_select_alls("company_product","company","a.`uid`=b.`uid` $where order by a.`id` $order limit $ststrsql,$limit","b.name,a.*");
		$this->yunset("pagenav",$pagenav);
		$this->yunset("rows",$rows);
		$this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_comproduct'));
	}
	function statusbody_action(){
		$userinfo = $this->obj->DB_select_once("company_product","`id`=".$_GET['id'],"`statusbody`");
		echo $userinfo['statusbody'];die;
	}

	function status_action(){
		extract($_POST);
		$id = @explode(",",$id);
		
		if(is_array($id)){
			foreach($id as $value){
				$idlist[] = $value;
			}
			$aid = @implode(",",$idlist);
			$id=$this->obj->DB_update_all("company_product","`status`='$status',`statusbody`='".$statusbody."'","`id` IN ($aid)");
 			$id?$this->obj->ACT_layer_msg("��Ʒ���(ID:".$aid.")���óɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("�Ƿ�������",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function statuss_action(){
		$this->obj->DB_update_all("company_product","`status`='".$_POST['status']."'","`id` IN (".$_POST['allid'].")");
		$this->obj->admin_log("������֤(ID:".$_POST['allid'].")��˳ɹ�");
		echo $_POST['status'];die;
	}
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
			    	foreach($del as $v){
			    	    $this->del_com($v);
			    	}
					$del_id=@implode(',',$del);
		    	}else{
		    		$this->del_com($del);
					$del_id=$del;
		    	}
				$this->layer_msg('��Ʒ(ID:'.$del_id.')ɾ���ɹ���',9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
	    		$this->layer_msg( "��ѡ����Ҫɾ������Ϣ��",8,1);
	    	}
	    }

	    if(isset($_GET['id'])){
	    	extract($_GET);
	    	$id_a=explode("-",$id);
			$result=$this->del_com($id);
			isset($result)?$this->layer_msg('��Ʒ(ID:'.$id_a[0].')ɾ���ɹ���',9,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('�Ƿ�������',3);
		}
	}

	function del_com($id)
	{
		$id_arr = explode("-",$id);

		if($id_arr[0])
		{
			$product=$this->obj->DB_select_once("company_product","`id`='".$id_arr[0]."'");
 			$this->obj->unlink_pic("../".$product['pic']);
			$result=$this->obj->DB_delete_all("company_product","`id`='".$id_arr[0]."'" );
		}
		return $result;
	}

}
?>