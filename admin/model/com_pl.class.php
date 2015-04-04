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
class com_pl_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"end","name"=>'发送时间',"value"=>$ad_time);
		$a_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"r_time","name"=>'回复时间',"value"=>$a_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$where=1;
		$this->set_search();
		$keyword=trim($_GET['keyword']);
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
		if($_GET['r_time']){
			if($_GET['r_time']=='1'){
				$where.=" and `reply_time` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `reply_time` >= '".strtotime('-'.(int)$_GET['r_time'].'day')."'";
			}
			$urlarr['r_time']=$_GET['r_time'];
		}
		if($keyword)
		{
			if($_GET['type']=="1")
			{
				$user=$this->obj->DB_select_all("member","`username` LIKE '%".$_GET['keyword']."%'","uid");
				if(is_array($user) && !empty($user))
				{
					foreach($user as $v)
					{
						$userid[]=$v['uid'];
					}
					$where.=" and `uid` in (".@implode(",",$userid).")";
				}
			}elseif($_GET['type']=="2"){
				$com=$this->obj->DB_select_all("company","`name` LIKE '%".$_GET['keyword']."%'","uid");
				if(is_array($com) && !empty($com))
				{
					foreach($com as $v)
					{
						$comid[]=$v['uid'];
					}
					$where.=" and `cuid` in (".@implode(",",$comid).")";
				}
			}elseif ($_GET['type']=="3"){
				$where.=" and `content` LIKE '%".$_GET['keyword']."%'";
			}elseif ($_GET['type']=="4"){
			    $where.=" and `reply` LIKE '%".$_GET['keyword']."%'";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];

		}

		$comwhere=1;
		$comlist=$this->obj->DB_select_all("company",$comwhere,"`uid`,name");
		if(is_array($comlist))
		{
			foreach($comlist as $v)
			{
				$cuid[]=$v['uid'];
			}
		}
		$where.=" and `cuid` in (".@implode(",",$cuid).")";

		if($_GET['order'])
		{
			$order=$_GET['order'];
		}else{
			$order="desc";
		}
		$urlarr['page']="{{page}}";
		$urlarr=$this->url("index",$_GET['m'],$urlarr);
		$mes_list = $this->get_page("company_msg",$where." ORDER BY `id` $order",$urlarr,$this->config['sy_listnum']);
		if(is_array($mes_list))
		{
			foreach($mes_list as $v)
			{
				$uid[]=$v['uid'];
			}
			$userlist=$this->obj->DB_select_all("member","`uid` in (".@implode(",",$uid).")","`uid`,`username`");
			foreach($mes_list as $k=>$v)
			{
				$mes_list[$k]['content'] = str_replace('"',"",$v['content']);
				$mes_list[$k]['reply'] = str_replace('"',"",$v['reply']);

				foreach($userlist as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$mes_list[$k]['username']=$val['username'];
					}
				}
				foreach($comlist as $val)
				{
					if($v['cuid']==$val['uid'])
					{
						$mes_list[$k]['com_name']=$val['name'];
					}
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("mes_list",$mes_list);
		$this->yuntpl(array('admin/admin_compl'));
	}

	function del_action(){
	    if($_POST['del']){
	    	$del=$_POST['del'];
	    	if($del){
	    		if(@is_array($del)){
					$del=@implode(',',$del);
		    	}
		    	$this->obj->DB_delete_all("company_msg","`id` in (".$del.")","");
	    		$this->layer_msg( "评论(ID:".$del.")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$this->check_token();
			$result=$this->obj->DB_delete_all("company_msg","`id`='".$_GET['id']."'" );
			isset($result)?$this->layer_msg('评论(ID:'.$_GET['id'].')删除成功！',9):$this->layer_msg('删除失败！',8);
		}else{
			$this->layer_msg('非法操作！',3);
		}
	}
}