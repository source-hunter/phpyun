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
class look_resume_controller extends common
{
	function set_search(){
		$lo_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"time","name"=>'����ʱ��',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$where = "1";
		$this->set_search();
		if($_GET['keyword'])
		{
			if($_GET['type']=="3"){
				$company=$this->obj->DB_select_all("company","`name` like '%".$_GET['keyword']."%'","name,uid");
				if(is_array($company))
				{
					foreach($company as $v)
					{
						$com_id[]=$v['uid'];
					}
				}
				$where.=" and `com_id` in (".@implode(",",$com_id).")";
			}else{
				if($_GET['type']=="1")
				{
					$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and a.`name` like '%".$_GET['keyword']."%'","a.name,a.uid,b.name as resume_name,b.id");
				}elseif($_GET['type']=="2"){
					$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and b.`name` like '%".$_GET['keyword']."%'","a.name,a.uid,b.name as resume_name,b.id");
				}
				if(is_array($resume))
				{
					foreach($resume as $v)
					{
						$resume_id[]=$v['id'];
					}
				}
				$where.=" and `resume_id` in (".@implode(",",$resume_id).")";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['time']){
			if($_GET['time']=='1'){
				$where.=" and `datetime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `datetime` >= '".strtotime('-'.(int)$_GET['time'].'day')."'";
			}
			$urlarr['time']=$_GET['time'];
		}
		if($_GET['sdate'])
		{
			$sdate=strtotime($_GET['sdate']);
			$where.=" and `datetime`>'$sdate'";
			$urlarr['sdate']=$_GET['sdate'];
		}
		if($_GET['edate'])
		{
			$edate=strtotime($_GET['edate']);
			$where.=" and `datetime`<'$edate'";
			$urlarr['edate']=$_GET['edate'];
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$list=$this->get_page("look_resume",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($list))
		{
			foreach($list as $v)
			{
				$resume_ids[]=$v['resume_id'];
				$com_ids[]=$v['com_id'];
			}
			if(($_GET['type']!="1" && $_GET['type']!="2") || !$_GET['keyword']){
				$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and b.`id` in (".@implode(",",$resume_ids).")","a.name,a.uid,b.name as resume_name");
			}
			if($_GET['type']!="3" || !$_GET['keyword']){
				$company=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$com_ids).")","name,uid");
			}
			foreach($list as $k=>$v)
			{
				foreach($resume as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$list[$k]['name']=$val['name'];
						$list[$k]['resume_name']=$val['resume_name'];
					}
				}
				foreach($company as $val)
				{
					if($v['com_id']==$val['uid'])
					{
						$list[$k]['com_name']=$val['name'];
					}
				}
			}
		}
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/look_resume'));
	}
	function del_action()
	{
		$this->check_token();
	    if($_GET['del']){
	    	if(is_array($_GET['del']))
	    	{
	    		$del=@implode(",",$_GET['del']);
	    		$layer_status=1;
	    	}else{
	    		$del=$_GET['del'];
	    		$layer_status=0;
	    	}
			$this->obj->DB_delete_all("look_resume","`id` in (".$del.")","");
			$this->layer_msg( "���������¼(ID:".$del.")ɾ���ɹ���",9,$layer_status,$_SERVER['HTTP_REFERER']);
	   }else{
			$this->layer_msg( "��ѡ����Ҫɾ������Ϣ��",8,1,$_SERVER['HTTP_REFERER']);
    	}
	}
}
?>