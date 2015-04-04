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
class search_controller extends company
{
	function index_action(){
		$this->public_action();
		$urlarr["c"]="search";
		$where="`uid`='".$this->uid."'";
		if($_GET['keyword']){
			$where.=" and name like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("company_job",$where,$pageurl,"10","`id`,`name`,`state`,`sdate`,`edate`,`lastupdate`,`jobhits`");
		if(is_array($rows) && !empty($rows))
		{
			foreach($rows as $v)
			{
				$jobid[]=$v['id'];
			}
			$jobrows=$this->obj->DB_select_all("userid_job","`job_id` in (".$this->pylode(',',$jobid).")");
			if(is_array($jobrows))
			{
				foreach($jobrows as $v)
				{
					$jobnum[$v['job_id']]++;
				}
			}
		}
		$this->company_satic();
		$this->yunset("jobnum",$jobnum);
		$this->yunset("js_def",3);
		$this->com_tpl('search');
	}
}
?>