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
					if($userclass_name[$resume['edu']]==$comclass_name[$v['edu']] || $comclass_name[$v['edu']]=="����")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['sex']]==$comclass_name[$v['sex']] || $comclass_name[$v['sex']]=="����")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['marriage']]==$comclass_name[$v['marriage']] || $comclass_name[$v['sex']]=="����")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['report']]==$comclass_name[$v['report']] || $comclass_name[$v['report']]=="����")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['exp']]==$comclass_name[$v['exp']] || $comclass_name[$v['exp']]=="����")
					{
						$pre=$pre+5;
					}
					if($userclass_name[$resume['salary']]==$comclass_name[$v['salary']] || $comclass_name[$v['salary']]=="����")
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