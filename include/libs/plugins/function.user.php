<?php
/*
 * 单个人才调用
 * ----------------------------------------------------------------------------
 * 分别对 职位，对应招聘公司，公司所属企业级别，认证等级等作操作
 *
 * ============================================================================
*/
function smarty_function_user($paramer,&$smarty){
	global $db,$db_config,$config;
	if($paramer[id]){
    		$user_jy=$db->DB_select_once("resume_expect","`id`='".$paramer[id]."'");
    		$user=$db->DB_select_once("resume","`r_status`!='2' and `uid`='".$user_jy[uid]."'");
    		if(is_array($user_jy)||is_array($user)){
				//处理类别字段

			include APP_PATH."/plus/city.cache.php";
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/user.cache.php";
			include APP_PATH."/plus/industry.cache.php";
							//处理类别字段

				$user[sex_n]=$userclass_name[$user[sex]];
				$user[exp_n]=$userclass_name[$user[exp]];
				$user[marriage_n]=$userclass_name[$user[marriage]];
				$user[edu_n]=$userclass_name[$user[edu]];
				$user[province_n]=$city_name[$user[province]];
				$user[city_n]=$city_name[$user[city]];
				$user[three_city_n]=$city_name[$user[three_city]];
				$user[provinceid_n]=$city_name[$user[provinceid]];
				$user[cityid_n]=$city_name[$user[cityid]];
				$user[three_cityid_n]=$city_name[$user[three_cityid]];
				$a=date('Y',strtotime($user[birthday]));
				$user[age]=date("Y")-$a;

				$user_jy[provinceid_n]=$city_name[$user_jy[provinceid]];
				$user_jy[cityid_n]=$city_name[$user_jy[cityid]];
				$user_jy[three_cityid_n]=$city_name[$user_jy[three_cityid]];
				$user_jy[salary_n]=$userclass_name[$user_jy[salary]];
				$user_jy[report_n]=$userclass_name[$user_jy[report]];
				$user_jy[type_n]=$userclass_name[$user_jy[type]];
				$user_jy[hy_n]=$job_name[$user_jy[hy]];

				$jy=@explode(",",$user_jy[job_classid]);
				if(is_array($jy)){
					foreach($jy as $v){
						$jobname[]=$job_name[$v];
					}
				$user_jy[jobname]=implode(",",$jobname);
				}

				if($user_jy[doc]){
					$user_doc=$db->DB_select_once("resume_doc","`eid`='".$user[id]."'");
				}else{
					$user_edu=$db->select_all("resume_edu","`eid`='$user_jy[id]'");
					$user_training=$db->select_all("resume_training","`eid`='$user_jy[id]'");
					$user_work=$db->select_all("resume_work","`eid`='$user_jy[id]'");
					$user_other=$db->select_all("resume_other","`eid`='$user_jy[id]'");
					$user_project=$db->select_all("resume_project","`eid`='$user_jy[id]'");
					$user_skill=$db->select_all("resume_skill","`eid`='$user_jy[id]'");
				}
			}
			if(is_array($user_skill)){
				foreach($user_skill as $k=>$v){
					$user_skill[$k][skill_n]=$userclass_name[$v[skill]];
					$user_skill[$k][ing_n]=$userclass_name[$v[ing]];
				}
				$user_cert=$db->select_all("resume_cert","`eid`='$user_jy[id]'");
			}
    	}
	$smarty->assign("$paramer[assign_name]",array("user"=>$user,"user_jy"=>$user_jy,"user_doc"=>$user_doc,"user_edu"=>$user_edu,"user_tra"=>$user_training,"user_work"=>$user_work,"user_other"=>$user_other,"user_xm"=>$user_project,"user_skill"=>$user_skill,"user_cert"=>$user_cert));
}