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
class resume_list_controller extends company
{
	function index_action(){	//疑似无用方法

	if($_GET['jobid'])
	{
		$resume=$this->obj->DB_select_all("resume","`r_status`<>'2'");
		foreach($resume as $k=>$v)
		{
			$def_job[]=$v['def_job'];
		}
		$urlarr['c']="resume_list";
		$urlarr['jobid']=intval($_GET['jobid']);
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("resume_expect","`job_post`='".(int)$_GET['jobid']."' and `id` in (".$this->pylode(",",$def_job).")",$pageurl,"10");
		include APP_PATH."/plus/user.cache.php";
		include APP_PATH."/plus/city.cache.php";
		if(is_array($rows))
		{
			foreach($rows as $k=>$v)
			{
				$uid[]=$v['uid'];
				$rows[$k]['salary_info']=$userclass_name[$v['salary']];
				$rows[$k]['province']=$city_name[$v['provinceid']];
				$rows[$k]['city']=$city_name[$v['cityid']];
				foreach($resume as $key=>$val)
				{
					if($v['uid']==$val['uid'])
					{
						$rows[$k]['name_info']=$val['name'];
						$rows[$k]['edu_info']=$userclass_name[$val['edu']];
						$rows[$k]['exp_info']=$userclass_name[$val['exp']];
					}
				}
			}
		}
		$look=$this->obj->DB_select_all("look_resume","`uid` in (".$this->pylode(",",$uid).")");
		if(is_array($look))
		{
			foreach($look as $v)
			{
				$looks[$v['uid']]++;
			}
		}
		$this->yunset("looks",$looks);
		$this->yunset("rows",$rows);
		$this->public_action();
		$this->com_tpl('resume_list');
	}
 }
}
?>