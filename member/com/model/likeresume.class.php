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
class likeresume_controller extends company
{ 
	function index_action()
	{
		if($_GET['job_id'])
		{
			$id=(int)$_GET['job_id'];
			$job=$this->obj->DB_select_once("company_job","`id`='".$id."'");
			$this->yunset("job",$job);
			$select="a.id,a.uid,a.three_cityid,a.report,a.salary,b.edu,b.sex,b.marriage,b.exp,b.name";
			$resume=$this->obj->DB_select_alls("resume_expect","resume","a.`hy`='".$job['hy']."' and  FIND_IN_SET('".$job['job_post']."',a.job_classid) and a.`id`=b.`def_job`",$select);
			if(is_array($resume))
			{
				include APP_PATH."/plus/user.cache.php";
				include APP_PATH."/plus/com.cache.php";
				$this->yunset("userclass_name",$userclass_name);
				$this->yunset("comclass_name",$comclass_name);
				foreach($resume as $k=>$v)
				{
					$pre=60;
					if($v['three_cityid']==$job['three_cityid'])
					{
						$pre=$pre+10;
					}
					if($comclass_name[$job['edu']]==$userclass_name[$v['edu']] || $comclass_name[$job['edu']]=="����")
					{
						$pre=$pre+5;
					}
					if($comclass_name[$job['sex']]==$userclass_name[$v['sex']] || $comclass_name[$job['sex']]=="����")
					{
						$pre=$pre+5;
					}
					if($comclass_name[$job['marriage']]==$userclass_name[$v['marriage']] || $comclass_name[$job['marriage']]=="����")
					{
						$pre=$pre+5;
					}
					if($comclass_name[$job['report']]==$userclass_name[$v['report']] || $comclass_name[$job['report']]=="����")
					{
						$pre=$pre+5;
					}
					if($comclass_name[$job['exp']]==$userclass_name[$v['exp']] || $comclass_name[$job['exp']]=="����")
					{
						$pre=$pre+5;
					}
					if($comclass_name[$job['salary']]==$userclass_name[$v['salary']] || $comclass_name[$job['salary']]=="����")
					{
						$pre=$pre+5;
					}
					$resume[$k]['pre']=$pre;
				}
				$sort = array(
				        'direction' => 'SORT_DESC',
				        'field'     => 'pre',
				);
				$arrSort = array();
				foreach($resume AS $uniqid => $row){
				    foreach($row AS $key=>$value){
				        $arrSort[$key][$uniqid] = $value;
				    }
				}
				if($sort['direction']){
				    array_multisort($arrSort[$sort['field']], constant($sort['direction']), $resume);
				}
				$this->yunset("resume",$resume);
			}
		}
		$this->public_action();
		$this->yunset("js_def",3);
		$this->com_tpl('likeresume');
	}
}
?>