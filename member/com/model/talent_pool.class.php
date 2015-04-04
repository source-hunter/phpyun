<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class talent_pool_controller extends company
{
	function index_action()
	{
		$where="`cuid`='".$this->uid."'";
		if($_GET['keyword'])
		{
			$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and a.`r_status`<>'2' and a.`name` like '%".$_GET['keyword']."%'","a.`name`,a.`uid`,a.`sex`,a.`edu`,b.`job_classid`");
			if(is_array($resume))
			{
				foreach($resume as $v)
				{
					$uid[]=$v['uid'];
				}
			}
			$where.=" and uid in (".@implode(',',$uid).")";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$this->public_action();
		$urlarr['c']='down';
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("talent_pool","$where  order by id desc",$pageurl,"10");
		if(is_array($rows))
		{
			if(!$_GET['keyword'])
			{
				if(empty($resume))
				{
					foreach($rows as $v)
					{
						$uid[]=$v['uid'];
					}
					$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and a.`r_status`<>'2' and a.uid in (".@implode(',',$uid).")","a.`name`,a.`uid`,a.`sex`,a.`edu`,b.`job_classid`");
				}
			}
			$userid_msg=$this->obj->DB_select_all("userid_msg","`fid`='".$this->uid."' and `uid` in (".@implode(",",$uid).")","uid");
			if(is_array($resume))
			{
				include(PLUS_PATH."user.cache.php");
				include(PLUS_PATH."job.cache.php");
				foreach($rows as $key=>$val)
				{
					foreach($resume as $va)
					{
						if($val['uid']==$va['uid'])
						{
							$rows[$key]['name']=$va['name'];
							$rows[$key]['sex']=$userclass_name[$va['sex']];
							$rows[$key]['edu']=$userclass_name[$va['edu']];
							if($va['job_classid']!="")
							{
								$job_classid=@explode(",",$va['job_classid']);
								$rows[$key]['jobname']=$job_name[$job_classid[0]];
							}
						}
					}
					foreach($userid_msg as $va)
					{
						if($val['uid']==$va['uid'])
						{
							$rows[$key]['userid_msg']=1;
						}
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$report=$this->config['com_report'];
		$this->yunset("report",$report);
		$this->company_satic();
		$this->yunset("js_def",5);
		$this->com_tpl('talent_pool');
	}
	function remark_action()
	{
		if($_POST['remark']=="")
		{
			$this->obj->ACT_layer_msg("备注内容不能为空！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$id=$this->obj->DB_update_all("talent_pool","`remark`='".$_POST['remark']."'","`id`='".(int)$_POST['id']."'");
			if($id)
			{
				$this->obj->member_log("备注人才".$_POST['r_name']);
				$this->obj->ACT_layer_msg("备注成功！",9,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("备注失败！",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function del_action()
	{
		if($_POST['delid'] || $_GET['id'])
		{
			if($_GET['id']){
				$id=(int)$_GET['id'];
				$layer_type='0';
			}else{
				$id=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}
			$nid=$this->obj->DB_delete_all("talent_pool","`id` in (".$id.") and `cuid`='".$this->uid."'"," ");
			if($nid){
				$this->obj->member_log("删除收藏简历人才",3,3);
				$this->layer_msg('删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
			}
		}
	}
}
?>