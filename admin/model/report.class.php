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
class report_controller extends common
{
	function set_search(){
		if($_GET['type']=='1'){
			$search_list[]=array("param"=>"status","name"=>'审核状态',"value"=>array("1"=>"已处理","2"=>"未处理"));
		}
		$ad_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"end","name"=>'举报时间',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		extract($_GET);
		$this->set_search();
		if($type=='0' || $type==''){
			if($_GET['ut']=="2"){
				$this->yunset("ut",$_GET['ut']);
				$where="`usertype`='".$_GET['ut']."' and `type`='0' ";
				$urlarr['ut']=$_GET['ut'];
				$urlarr['type']=$_GET['type'];
				
				$wheres=1;
				
				$user=$this->obj->DB_select_all("resume_expect",$wheres,"`id`");
				if(is_array($user)){
					foreach($user as $v){
						$eid[]=$v['id'];
					}
				}
				$where.=" and `eid` in (".@implode(",",$eid).")";
			
			}else{
				$this->yunset("ut",$_GET['ut']);
				$where="`usertype`='1' and `type`='0' ";
			
				$wheres=1;
				$com=$this->obj->DB_select_all("company",$wheres,"`uid`");
				if(is_array($com)){
					foreach($com as $v){
						$uid[]=$v['uid'];
					}
				}
				$where.=" and `c_uid` in (".@implode(",",$uid).")";
				
			}
			if($_GET['end']){
				if($_GET['end']=='1'){
					$where.=" and `inputtime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
				}else{
					$where.=" and `inputtime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
				}
				$urlarr['end']=$_GET['end'];
			}
			if($_GET['s']!=""){
				$where.=" and `status`= ".$_GET['s'];
				$urlarr['s']=$_GET['s'];
			}
			if ($_GET['qysearch']){
				if ($_GET['f_type']=='1'){
					$where.=" and `r_name` like '%".$_GET['qyname']."%' ";
				}elseif ($_GET['f_type']=='2'){
					$where.=" and `username` like '%".$_GET['qyname']."%' ";
				}elseif ($_GET['f_type']=='3'){
					$where.=" and `r_reason` like '%".$_GET['qyname']."%' ";
				}
				$urlarr['f_type']=$_GET['f_type'];
				$urlarr['qyname']=$_GET['qyname'];
				$urlarr['qysearch']=$_GET['qysearch'];
			}
			if($_GET['order']){
				$where.=" order by ".$_GET['t']." ".$_GET['order'];
				$urlarr['order']=$_GET['order'];
				$urlarr['t']=$_GET['t'];
			}else{
				$where.=" order by id desc";
			}
			$urlarr['page']="{{page}}";
			$pageurl=$this->url("index","report",$urlarr);
			$userrows=$this->get_page("report",$where,$pageurl,$this->config['sy_listnum']);
			if($userrows &&is_array($userrows)){
				$uids=array();
				foreach($userrows as $val){
					if(in_array($val['c_uid'],$uids)==false){
						$uids[]=$val['c_uid'];
					}
				}
				$member=$this->obj->DB_select_all("member","`uid` in(".@implode(',',$uids).")","`uid`,`email`");
				if($member&&is_array($member)){
					foreach($member as $val){
						foreach($userrows as $key=>$value){
							if($val['uid']==$value['c_uid']){
								$userrows[$key]['email']=$val['email'];
							}
						}
					}
				}
			}
			$this->yunset("userrows",$userrows);
			$type='0';
		}else if($type=='1'){
			$where="`type`='1'";
			if($_GET['status']=='2'){
				$where .=" and `status`='0'";
			}else{
				$where .=" and `status`='1'";
			}
			$urlarr['status']=$_GET['status'];
			if($_GET['end']){
				if($_GET['end']=='1'){
					$where.=" and `inputtime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
				}else{
					$where.=" and `inputtime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
				}
				$urlarr['end']=$_GET['end'];
			}
			if ($_GET['comquestion']){
				if ($_GET['p_type']=='1'){
					$where .=" and `r_name` like '%".$_GET['question']."%'";
				}else{
					$where .=" and `username` like '%".$_GET['question']."%'";
				}
				$urlarr['p_type']=$_GET['p_type'];
				$urlarr['question']=$_GET['question'];
				$urlarr['comquestion']=$_GET['comquestion'];

				if ($_GET['r_type']!=''){
					$where .=" and `r_type`='".$_GET['r_type']."'";
					$urlarr['r_type']=$_GET['r_type'];
				}
			}
			if($_GET['order'])
			{
				$where.=" order by ".$_GET['t']." ".$_GET['order'];
				$urlarr['order']=$_GET['order'];
				$urlarr['t']=$_GET['t'];
			}else{
				$where.=" order by id desc";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['page']="{{page}}";
			$pageurl=$this->url("index",$_GET['m'],$urlarr);
			$q_report=$this->get_page("report",$where,$pageurl,$this->config['sy_listnum']);
			$reason=$this->obj->DB_select_all("reason","1","`id`,`name`");
			foreach($q_report as $key=>$val){
				if($val['r_type']=='1'){
					$q_report[$key]['c']="add";
					$question=$this->obj->DB_select_once("question","`id`='".$val['eid']."'","`title`");
					if($question['title']){
						$q_report[$key]['title']=$question['title'];
						$q_report[$key]['url']="index.php?m=admin_question&id=".$val['eid'];
					}else{
						$q_report[$key]['is_del']='问题已被删除';
					}
				}else if($val['r_type']=='2'){
					$q_report[$key]['c']="add_answer";
					$answer=$this->obj->DB_select_once("answer","`id`='".$val['eid']."'","content");
					if($answer['content']){
						$q_report[$key]['title']=$answer['content'];
						$q_report[$key]['url']="index.php?m=admin_question&c=get_answer&aid=".$val['eid'];
					}else{
						$q_report[$key]['is_del']='回答已被删除';
					}
				}else{
					$q_report[$key]['c']="add_review";
					$answer=$this->obj->DB_select_once("answer_review","`id`='".$val['eid']."'","content");
					if($answer['content']){
						$q_report[$key]['title']=$answer['content'];
						$q_report[$key]['url']="index.php?m=admin_question&c=get_comment&aid=".$val['eid'];
					}else{
						$q_report[$key]['is_del']='评论已被删除';
					}
				}
				foreach($reason as $r_v){
					if($val['r_reason']==$r_v['id']){
						$q_report[$key]['reason']=$r_v['name'];
					}else if($val['r_reason']=='0'){
						$q_report[$key]['reason']='原因已被删除';
					}
				}
			}
			$this->yunset("q_report",$q_report);
		}
		$nav_user=$this->obj->DB_select_alls("admin_user","admin_user_group","a.`m_id`=b.`id` and a.`uid`='".$_SESSION["auid"]."'");
		$power=unserialize($nav_user[0]["group_power"]);
		if(in_array('141',$power)){
			$this->yunset("email_promiss", '1');
		}
		$back_url=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$this->yunset("get_type", $_GET);
		$this->yunset("type",$type);
		$this->yunset("back_url",$back_url);
		$this->yuntpl(array('admin/admin_report_userlist'));
	}
	function recommend_action(){
		$nid=$this->obj->DB_update_all("report","`".$_GET['type']."`='".$_GET['rec']."'","`id`='".$_GET['id']."' and `type`='1'");
		$this->admin_log("举报问答(ID:".$_GET['id'].")设置是否处理");
		echo $nid?1:0;die;
	}
	function delresume_action()
	{
		$id=$_GET['eid'];
		$result=$this->obj->DB_delete_all("resume_expect","`id`='".$id."'" );
		$del_array=array("resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume","resume_show","down_resume","userid_job");
		$show=$this->obj->DB_select_all("resume_show","`eid`='".$id."' and `picurl`<>''","`picurl`");
		if(is_array($show))
		{
			foreach($show as $v)
			{
				@unlink(".".$show['picurl']);
			}
		}
		foreach($del_array as $v){
			$this->obj->DB_delete_all($v,"`eid`='".$id."'");
		}
		$this->obj->DB_update_all("member_statis","`resume_num`=`resume_num`-1","`uid`='".$_GET['uid']."'");
		$this->obj->DB_delete_all("report","`id`='".$_GET['id']."'");
		$this->layer_msg('简历(ID:'.$id.')删除成功！',9,0,$_SERVER['HTTP_REFERER']);
	}
	function deljob_action()
	{
		$this->obj->DB_delete_all("company_job","`id`='".$_GET['eid']."'");
		$this->obj->DB_delete_all("report","`id`='".$_GET['id']."'");
		$this->layer_msg('职位(ID:'.$_GET['eid'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']);
	}
	function del_action()
	{
		$this->check_token();
		
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
					$layer_type=1;
					$this->obj->DB_delete_all("report","`id` in(".@implode(',',$del).")","");
					$del=@implode(',',$del);
		    	}else{
					$this->obj->DB_delete_all("report","`id`='$del'");
					$layer_type=0;
		    	}
				$this->layer_msg('举报(ID:'.$del.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg('请选择您要删除的信息！',8,0,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	}
}

?>