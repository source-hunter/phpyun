<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class look_job_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"end","name"=>'浏览时间',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
		$where = "1";
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `datetime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `datetime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
		if($_GET['keyword'])
		{
			if($_GET['type']=="1"){
				$member=$this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%' and `usertype`='1'","username,uid");
				if(is_array($member))
				{
					foreach($member as $v)
					{
						$uid[]=$v['uid'];
					}
				}
				$where.=" and `uid` in (".@implode(",",$uid).")";
			}else{
				if($_GET['type']=="2")
				{
					$job=$this->obj->DB_select_all("company_job","`name` like '%".$_GET['keyword']."%'","name,uid,com_name,id");
				}elseif($_GET['type']=="3"){
					$job=$this->obj->DB_select_all("company_job","`com_name` like '%".$_GET['keyword']."%'","name,uid,com_name,id");
				}
				if(is_array($job))
				{
					foreach($job as $v)
					{
						$com_id[]=$v['uid'];
					}
				}
				$where.=" and `com_id` in (".@implode(",",$com_id).")";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
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
		$list=$this->get_page("look_job",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($list))
		{
			foreach($list as $v)
			{
				$uids[]=$v['uid'];
				$jobid[]=$v['jobid'];
			}
			if($_GET['type']!="1" || !$_GET['keyword']){
				$member=$this->obj->DB_select_all("member","`uid` in (".@implode(",",$uids).")","username,uid");
			}
			if(($_GET['type']!="2" && $_GET['type']!="3") || !$_GET['keyword']){
				$job=$this->obj->DB_select_all("company_job","`id` in (".@implode(",",$jobid).")","name,com_name,id");
			}
			foreach($list as $k=>$v)
			{
				foreach($member as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$list[$k]['username']=$val['username'];
					}
				}
				foreach($job as $val)
				{
					if($v['jobid']==$val['id'])
					{
						$list[$k]['job_name']=$val['name'];
						$list[$k]['com_name']=$val['com_name'];
					}
				}
			}
		}
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/look_job'));
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
			$this->obj->DB_delete_all("look_job","`id` in (".$del.")","");
			$this->layer_msg( "职位浏览记录(ID:".$del.")删除成功！",9,$layer_status,$_SERVER['HTTP_REFERER']);
	   }else{
			$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
    	}
	}
}
?>