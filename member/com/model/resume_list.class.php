<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class resume_list_controller extends company
{
	function index_action(){	//�������÷���

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