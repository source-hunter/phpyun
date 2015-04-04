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
class company_pay_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'����ʱ��',"value"=>$ad_time);
		$search_list[]=array("param"=>"pay_state","name"=>'����״̬',"value"=>array("0"=>"֧��ʧ��","1"=>"�ȴ�����","2"=>"֧���ɹ�","3"=>"�ȴ�ȷ��"));
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
		$wheres=1;
		if($_GET['keyword']!="")
		{
			if ($_GET['type']=='1'){
				$where="`order_id` like '%".$_GET['keyword']."%'";
			}elseif ($_GET['type']=='3'){
				$where="`pay_remark` like '%".$_GET['keyword']."%'";
			}elseif ($_GET['type']=='2'){
				$member=$this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%'","uid");
				if(is_array($member))
				{
					foreach($member as $v)
					{
						$uids[]=$v['uid'];
					}
				}
				$where="`com_id` in (".@implode(",",$uids).")";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
		}else{
			$where=1;
		}
		if($_GET['pay_state']!=""){
            $where.=" and `pay_state`='".$_GET['pay_state']."'";
			$urlarr['pay_state']=$_GET['pay_state'];
	    }
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `pay_time` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `pay_time` >= '".strtotime('-'.$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
		$com=$this->obj->DB_select_all("company",$wheres,"`uid`,name");
		
		$user=$this->obj->DB_select_all("resume","1","`uid`,`name`");
		if(is_array($com))
		{
			foreach($com as $v)
			{
				$uid[]=$v['uid'];
			}
		}
		
		if(is_array($user))
		{
			foreach($user as $v)
			{
				$uid[]=$v['uid'];
			}
		}
		$where.=" and `com_id` in (".@implode(",",$uid).")";
		
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("company_pay",$where,$pageurl,$this->config['sy_listnum']);

		include (APP_PATH."/data/db.data.php");
		if(is_array($rows)){
			foreach($rows as $k=>$v){
				$rows[$k]['pay_state_n']=$arr_data['paystate'][$v['pay_state']];
				$classid[]=$v['com_id'];
			}
		}
		if(is_array($classid))
		{
			$group=$this->obj->DB_select_all("member","uid in (".@implode(",",$classid).")","`uid`,`username`");
			$group_com=array_merge($com,$lt,$user);
		}

		if(is_array($group)){
			foreach($group as $key=>$value){
				foreach($rows as $k=>$v){
					if($value['uid']==$v['com_id']){
						$rows[$k]['username']=$value['username'];
					}
				}
			}
		}
		if(is_array($group_com)){
			foreach($group_com as $key=>$value){
				foreach($rows as $k=>$v){
					if($value['uid']==$v['com_id']){
						$rows[$k]['comname']=$value['name'];
					}
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_company_pay'));
	}

	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
					$this->obj->DB_delete_all("company_pay","`id` in(".@implode(',',$_GET['del']).")","");
					$del=@implode(',',$_GET['del']);
		    	}else{
		    		$this->obj->DB_delete_all("company_pay","`id`='$del'");
		    	}
				$this->layer_msg('���Ѽ�¼(ID:'.$del.')ɾ���ɹ���',9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg('��ѡ����Ҫɾ������Ϣ��',9,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("company_pay","`id`='".$_GET['id']."'" );
			isset($result)?$this->layer_msg('���Ѽ�¼(ID:'.$_GET['id'].')ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('�Ƿ�������',9,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>