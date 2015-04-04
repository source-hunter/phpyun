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
class likejob_controller extends user{
	function index_action()
	{
		if($_GET['id'])
		{
			$id=(int)$_GET['id'];
			$resume=$this->obj->DB_select_alls("resume_expect","resume","a.`uid`=b.`uid` and a.id='".$id."'");
			$resume=$resume[0];
			$this->yunset("resume",$resume);
			if($resume['job_classid']!="")
			{
				$jobclass=@explode(",",$resume['job_classid']);
				foreach($jobclass as $v)
				{
					$where[]="`job_post`='".$v."'";
				}
				$where=" and (".@implode(" or ",$where).")";
			}
			$time = time();
			$select="id,name,three_cityid,edu,sex,marriage,report,exp,salary";
			$job=$this->obj->DB_select_all("company_job","`sdate`<'".$time."' and `edate`>'".$time."' and `state`='1' ".$where,$select);
			if(is_array($resume))
			{
				include APP_PATH."/plus/user.cache.php";
				include APP_PATH."/plus/com.cache.php";
				$this->yunset("comclass_name",$comclass_name);
				foreach($job as $k=>$v)
				{
					$pre=60;
					if($v['three_cityid']==$resume['three_cityid'])
					{
						$pre=$pre+10;
					}
					if($userclass_name[$resume['edu']]==$comclass_name[$v['edu']] || $comclass_name[$v['edu']]=="不限")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['sex']]==$comclass_name[$v['sex']] || $comclass_name[$v['sex']]=="不限")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['marriage']]==$comclass_name[$v['marriage']] || $comclass_name[$v['sex']]=="不限")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['report']]==$comclass_name[$v['report']] || $comclass_name[$v['report']]=="不限")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['exp']]==$comclass_name[$v['exp']] || $comclass_name[$v['exp']]=="不限")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['salary']]==$comclass_name[$v['salary']] || $comclass_name[$v['salary']]=="不限")
					{
						$pre=$pre+5;
					}
					$job[$k]['pre']=$pre;
				}
				$sort = array(
				        'direction' => 'SORT_DESC',
				        'field'     => 'pre',
				);
				$arrSort = array();
				foreach($job AS $uniqid => $row){
				    foreach($row AS $key=>$value){
				        $arrSort[$key][$uniqid] = $value;
				    }
				}
				if($sort['direction']){
				    array_multisort($arrSort[$sort['field']], constant($sort['direction']), $job);
				}
				$this->yunset("job",$job);
			}
		}
		$this->user_tpl('likejob');
	}
}
?>