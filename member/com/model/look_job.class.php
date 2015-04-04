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
class look_job_controller extends company
{
	function index_action()
	{

		$this->public_action();
		$urlarr['c']='look_job';
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("look_job","`com_id`='".$this->uid."' and `com_status`='0' order by datetime desc",$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
				$jobid[]=$v['jobid'];
			}
			$resume=$this->obj->DB_select_all("resume","`uid` in (".@implode(",",$uid).")","`uid`,`name`,`edu`,`exp`");
			$job=$this->obj->DB_select_all("company_job","`id` in (".@implode(",",$jobid).")","`id`,`name`");
			$userid_msg=$this->obj->DB_select_all("userid_msg","`fid`='".$this->uid."' and `uid` in (".@implode(",",$uid).")","uid");
			include(PLUS_PATH."user.cache.php");
			foreach($rows as $key=>$val)
			{
				foreach($resume as $va)
				{
					if($val['uid']==$va['uid'])
					{
						$rows[$key]['exp']=$userclass_name[$va['exp']];
						$rows[$key]['edu']=$userclass_name[$va['edu']];
						$rows[$key]['name']=$va['name'];
					}
				}
				foreach($job as $va)
				{
					if($val['jobid']==$va['id'])
					{
						$rows[$key]['jobname']=$va['name'];
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
		$this->yunset("rows",$rows);
		$this->yunset("js_def",5);
		$this->com_tpl('look_job');
	}
	function del_action(){
		if($_POST['delid']||$_GET['id']){
			if(is_array($_POST['delid'])){
				$delid=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}else{
				$delid=(int)$_GET['id'];
				$layer_type='0';
			}
			$nid=$this->obj->DB_update_all("look_job","`com_status`='1'","`id` in (".$delid.") and `com_id`='".$this->uid."'"," ");
			if($nid)
			{
				$this->obj->member_log("删除已浏览简历记录(ID:".$delid.")");
				$this->layer_msg('删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
			}
		}
	}
}
?>