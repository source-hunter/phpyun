<?php
	class notice {
		function __construct($obj)
		{
			$this->obj = $obj;
		}
		function index()
		{
			global $config;
			$where="a.`def_job`=b.`id` and a.`status`<>'2' and a.`r_status`<>'2' and b.`job_classid`<>''";
			$resume=$this->obj->DB_select_alls("resume","resume_expect",$where);
			$select="id,name,hy,job_post,job1_son,three_cityid,edu,sex,marriage,report,exp,salary";
			$job=$this->obj->DB_select_all("company_job","state='1' and edate>'".time()."'",$select);

			foreach($resume as $k=>$v)
			{
				$this->select_job($v,$job);
			}
		}
		function select_job($resume,$job)
		{
			include PLUS_PATH."user.cache.php";
			include PLUS_PATH."com.cache.php";
			if(is_array($resume))
			{
				$job_classid=@explode(",",$resume['job_classid']);
				foreach($job as $k=>$v)
				{
					if($v['hy']==$resume['hy'] && (in_array($v['job1_son'],$job_classid) || in_array($v['job_post'],$job_classid)))
					{
						$pre=60;
						if($v['three_cityid']==$resume['three_cityid'])//地区
						{
							$pre=$pre+10;
						}
						if($userclass_name[$resume['edu']]==$comclass_name[$v['edu']] || $comclass_name[$v['edu']]=="不限")//学历
						{
							$pre=$pre+5;
						}
						if($userclass_name[$resume['sex']]==$comclass_name[$v['sex']] || $comclass_name[$v['sex']]=="不限")//性别
						{
							$pre=$pre+5;
						}
						if($userclass_name[$resume['marriage']]==$comclass_name[$v['marriage']] || $comclass_name[$v['marriage']]=="不限")//婚姻
						{
							$pre=$pre+5;
						}
						if($userclass_name[$resume['report']]==$comclass_name[$v['report']] || $comclass_name[$v['report']]=="不限")//到岗时间
						{
							$pre=$pre+5;
						}
						if($userclass_name[$resume['exp']]==$comclass_name[$v['exp']] || $comclass_name[$v['exp']]=="不限")//工作经验
						{
							$pre=$pre+5;
						}
						if($userclass_name[$resume['salary']]==$comclass_name[$v['salary']] || $comclass_name[$v['salary']]=="不限")//月薪
						{
							$pre=$pre+5;
						}
					}else{
						$pre=0;
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
				foreach($job as $k=>$v)
				{
					if($k<5 && $v['pre']>0)
					{
						$jobname[]=$v['name'];
					}
				}
				$data['jobname']=@implode(",",$jobname);
				$data['email']=$resume['email'];
				$data['telphone']=$resume['telphone'];
				$data['type']="notice";
				//$this->send_msg_email($data);
			}
		}
	}
?>