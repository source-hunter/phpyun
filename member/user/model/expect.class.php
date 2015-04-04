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
class expect_controller extends user{
	function index_action()
	{
		if($this->config['user_enforce_identitycert']=="1"){
			$row=$this->obj->DB_select_once("resume","`idcard_pic`<>'' and `uid`='".$this->uid."'");
			if($row['idcard_status']!="1"){
				$this->obj->ACT_msg("index.php?c=binding","请先完成身份认证！");
			}
		}
		if($_POST['shell']==1)
		{
			$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
			if($resume['name']=="")
			{
				echo 1;
			}die;
		}
		if($_GET['add'] == $this->uid)
		{
			$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
			if($num>=$this->config['user_number'])
			{
				$this->obj->ACT_msg("index.php?c=resume","你的简历数已经超过系统设置的简历数了");
			}
			echo "<script>location.href='index.php?c=expect'</script>";exit;
		}
		include APP_PATH."/plus/job.cache.php";
		include APP_PATH."/plus/city.cache.php";
		$job_area = $this->city_info($job_index,$job_name);
		$this->yunset("job_area",$job_area);
		$this->industry_cache();
		if($_GET['e']){
			$eid=(int)$_GET['e'];
			$row=$this->obj->DB_select_once("resume_expect","id='".$eid."' AND `uid`='".$this->uid."'");
			if(!is_array($row) || empty($row))
			{
				$this->obj->ACT_msg("index.php?c=resume","无效的简历！");
			}
			$job_classid=@explode(",",$row['job_classid']);
			if(is_array($job_classid)){
				foreach($job_classid as $key){
					$job_classname[]=$job_name[$key];
				}
				$this->yunset("job_classname",$this->pylode(' ',$job_classname));
				$this->yunset("job_classname2",$this->pylode(',',$job_classname));
			}
			$this->yunset("job_classid",$job_classid);
			$this->yunset("row",$row);
			$skill = $this->obj->DB_select_all("resume_skill","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("skill",$skill);
			$work = $this->obj->DB_select_all("resume_work","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("work",$work);
			$project = $this->obj->DB_select_all("resume_project","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("project",$project);
			$edu = $this->obj->DB_select_all("resume_edu","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("edu",$edu);
			$training = $this->obj->DB_select_all("resume_training","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("training",$training);
			$cert = $this->obj->DB_select_all("resume_cert","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("cert",$cert);
			$other = $this->obj->DB_select_all("resume_other","`eid`='".$eid."' AND `uid` = '".$this->uid."'");
			$this->yunset("other",$other);
		}
		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		$this->yunset("resume",$resume);
		$this->public_action();
		$this->user_left();
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$this->CacheInclude($CacheArr);
		$this->yunset("js_def",2);
		$this->user_tpl('expect');
	}

	function saveexpect_action(){
		if($_POST['submit']){
			$eid=(int)$_POST['eid'];
			unset($_POST['submit']);
			unset($_POST['eid']);
			unset($_POST['urlid']);
			$_POST['name'] = $this->stringfilter($_POST['name']);
			$where['id']=$eid;
			$where['uid']=$this->uid;
			$_POST['lastupdate']=time();
			if($eid=="")
			{
				$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
				if($num>=$this->config['user_number'])
				{
					echo 1;die;
				}
				$_POST['uid']=$this->uid;
				$_POST['ctime']=time();
				$nid=$this->obj->insert_into("resume_expect",$_POST);
				if ($nid)
				{
					if($num==0)
					{
						$this->obj->update_once('resume',array('def_job'=>$nid),array('uid'=>$this->uid));
					}
					$data['uid'] = $this->uid;
					$data['eid'] = $nid;
					$this->obj->insert_into("user_resume",$data);
					$this->obj->DB_update_all('member_statis',"`resume_num`=`resume_num`+1","`uid`='".$this->uid."'");
					$state_content = "发布了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$nid\" target=\"_blank\">新简历</a>。";
					$fdata['uid']	  = $this->uid;
					$fdata['content'] = $state_content;
					$fdata['ctime']   = time();
					$this->obj->insert_into("friend_state",$fdata);
					$this->obj->member_log("创建一份简历",2,1);
					$this->get_integral_action($this->uid,"integral_add_resume","发布简历");
					$this->warning("3");
				}
				$eid=$nid;
			}else{
				$nid=$this->obj->update_once("resume_expect",$_POST,$where);
				$this->obj->member_log("修改简历",2,2);
			}
			$row=$this->obj->DB_select_once("user_resume","`expect`='1',`uid`='".$this->uid."'","`eid`");
			if(!is_array($row)){$this->send_dingyue($eid,1);}
			$this->obj->update_once('user_resume',array('expect'=>1),array('eid'=>$eid,'uid'=>$this->uid));
			if($nid){
				$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
				$numresume=$this->obj->complete($resume_row);
				$resume=$this->obj->DB_select_once("resume_expect","`id`='".$eid."'");
				$resume['numresume']=$numresume;
				include APP_PATH."/plus/user.cache.php";
				include APP_PATH."/plus/job.cache.php";
				include APP_PATH."/plus/city.cache.php";
				include APP_PATH."/plus/industry.cache.php";
				$resume['report']=$userclass_name[$resume['report']];
				$resume['hy']=$industry_name[$resume['hy']];
				$resume['city']=$city_name[$resume['provinceid']]." ".$city_name[$resume['cityid']]." ".$city_name[$resume['three_cityid']];
				$resume['salary']=$userclass_name[$resume['salary']];
				$resume['type']=$userclass_name[$resume['type']];
				if($resume['job_classid']!="")
				{
					$job_classid=@explode(",",$resume['job_classid']);
					foreach($job_classid as $v)
					{
						$job_classname[]=$job_name[$v];
					}
					$resume['job_classname']=$this->pylode(" ",$job_classname);
				}
				$resume['three_cityid']=$city_name[$resume['three_cityid']];
				if(is_array($resume))
				{
					foreach($resume as $k=>$v)
					{
						$arr[$k]=iconv("gbk","utf-8",$v);
					}
				}
				echo json_encode($arr);die;
			}else{
				echo 0;die;
			}
		}
	}

	function work_action(){
		$this->resume("resume_work","work","expect","填写工作经验");
		$this->public_action();
	}
	function edu_action(){
		$this->resume("resume_edu","edu","training","填写教育经历");
		$this->public_action();
		$this->user_tpl('edu');
	}
	function training_action(){
		$this->resume("resume_training","training","cert","填写培训经历");
		$this->public_action();
		$this->user_tpl('training');
	}
	function project_action(){
		$this->resume("resume_project","project","edu","填写项目经历");
		$this->public_action();
		$this->user_tpl('project');
	}
	function skill_action(){
		$this->resume("resume_skill","skill","expect","填写专业技能");
		$this->public_action();
	}
	function cert_action(){
		$this->resume("resume_cert","cert","other","填写证书信息");
		$this->public_action();
		$this->user_tpl('cert');
	}
	function other_action(){
		$this->resume("resume_other","other","resume","返回简历管理");
		$this->public_action();
		$this->user_tpl('other');
	}
	function setreviewresume_action(){
		if(empty($_POST['eid'])){
			$user=$this->obj->DB_select_once("resume","`r_status`<>'2' and `uid`='".$this->uid."'");
			$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","username");

			if(is_array($user)){
				include APP_PATH."/plus/city.cache.php";
				include APP_PATH."/plus/job.cache.php";
				include APP_PATH."/plus/user.cache.php";
				include APP_PATH."/plus/industry.cache.php";
				if($this->config['user_name']==3)
				{
					$user["username_n"] = "NO.".$user_jy['id'];
				}elseif($this->config['user_name']==2){
					if($user['sex']=='6')
					{
						$user['username_n'] = mb_substr(empty($_POST['name'])?$user['name']:$_POST['name'],0,2)."先生";
					}else{
						$user['username_n'] = mb_substr(empty($_POST['name'])?$user['name']:$_POST['name'],0,2)."女士";
					}
				}else{
					$user['username_n'] = empty($_POST['name'])?$user['name']:$_POST['name'];
				}
				if($this->config['sy_usertype_1']=='1'){
					if($user['resume_photo']==""||file_exists($user['resume_photo'])==false){
						$user['resume_photo']=$this->config['sy_weburl'].'/'.$this->config['sy_member_icon'];
					}
				}else if($user['resume_photo']==""||file_exists($user['resume_photo'])==false){
					$user['resume_photo']=$this->config['sy_weburl'].'/'.$this->config['sy_member_icon'];
				}
				$user['username']=$member['username'];
				$user['user_sex']=$userclass_name[empty($_POST['sex'])?$user['sex']:$_POST['sex']];
				$user['user_exp']=$userclass_name[empty($_POST['exp'])?$user['exp']:$_POST['exp']];
				$user['user_marriage']=$userclass_name[empty($_POST['marriage'])?$user['marriage']:$_POST['marriage']];
				$user['useredu']=$userclass_name[empty($_POST['edu'])?$user['edu']:$_POST['edu']];
				$a=date('Y',strtotime(empty($_POST['birthday'])?$user['birthday']:$_POST['birthday']));
				$user['age']=date("Y")-$a;
				$user['city_one']=$city_name[empty($_POST['provinceid'])?$user_jy['provinceid']:$_POST['provinceid']];
				$user['city_two']=$city_name[empty($_POST['citysid'])?$user_jy['cityid']:$_POST['citysid']];
				$user['city_three']=$city_name[empty($_POST['three_cityid'])?$user_jy['three_cityid']:$_POST['three_cityid']];
				$user['salary']=$userclass_name[empty($_POST['salaryid'])?$user_jy['salary']:$_POST['salaryid']];
				$user['report']=$userclass_name[empty($_POST['reportid'])?$user_jy['report']:$_POST['reportid']];
				$user['type']=$userclass_name[empty($_POST['typeid'])?$user_jy['type']:$_POST['typeid']];
				$user['hy']=$industry_name[empty($_POST['hyid'])?$user_jy['hy']:$_POST['hyid']];
				$user['lastupdate']=date("Y-m-d",empty($_POST['lastupdate'])?$user_jy['lastupdate']:$_POST['lastupdate']);
				$user['r_name'] = empty($_POST['name'])?$user_jy['name']:$_POST['name'];
				$user['doc'] = $user_jy['doc'];
				$user['hits']=$user_jy['hits'];
				$resume_diy=split('[|]',$user_jy['resume_diy']);
				$user['resume_diy']=$resume_diy[0];
				$user['dom_sort']=$user_jy['dom_sort'];
				$user['tmpid']=$user_jy['tmpid'];
				$jy=@explode(",",empty($_POST['job_class'])?$user['job_classid']:$_POST['job_class']);
				if(@is_array($jy))
				{
					foreach($jy as $v)
					{
						$jobname[]=$job_name[$v];
					}
					$user['jobname']=@implode(",",$jobname);
				}
			}

			$user['m_status']=1;
			$user['user_jy'][0]=array("hy"=>$industry_name[$_POST['hyid']],"job_class"=>$user['jobname'],"provinceid"=>$city_name[$_POST['provinceid']],"cityid"=>$city_name[$_POST['citysid']],"three_cityid"=>$city_name[$_POST['three_cityid']],"salary"=>$userclass_name[$_POST['salaryid']],"type"=>$userclass_name[$_POST['typeid']],"report"=>$userclass_name[$_POST['reportid']]);
 			$user['user_edu'][0]=array("name"=>$_POST['edu_name'],"sdate"=>$_POST['edu_sdate'],"edate"=>$_POST['edu_edate'],"specialty"=>$_POST['edu_specialty'],"title"=>$_POST['edu_title'],"edu_content"=>$_POST['edu_content']);
 			$user['user_tra'][0]=array("name"=>$_POST['training_name'],"sdate"=>$_POST['training_sdate'],"edate"=>$_POST['training_edate'],"title"=>$_POST['training_title'],"content"=>$_POST['training__content']);
 			$user['user_work'][0]=array("name"=>$_POST['work_name'],"sdate"=>$_POST['work_sdate'],"edate"=>$_POST['work_edate'],"department"=>$_POST['work_department'],"title"=>$_POST['work_title'],"content"=>$_POST['work_content']);
 			$user['user_project'][0]=array("name"=>$_POST['project_name'],"sdate"=>$_POST['project_sdate'],"edate"=>$_POST['project_edate'],"sys"=>$_POST['project_sys'],"title"=>$_POST['project_title'],"content"=>$_POST['project_content']);
 			$user['user_skill'][0]=array("name"=>$_POST['skill_name'],"skill_n"=>$userclass_name[$_POST['skillcid']],"longtime"=>$_POST['longtime'],"ing_n"=>$userclass_name[$_POST['levelid']]);
 			$user['user_cert'][0]=array("name"=>$_POST['cert_name'],"sdate"=>$_POST['cert_sdate'],"title"=>$_POST['cert_title'],"content"=>$_POST['cert_content']);
 			$user['user_other'][0]=array("title"=>$_POST['other_title'],"edu_content"=>$_POST['other_content']);
		}else{
 			$id=$_POST['eid'];
			$user_jy=$this->obj->DB_select_once("resume_expect","`id`='".$id."'");
			$user=$this->obj->DB_select_once("resume","`r_status`<>'2' and `uid`='".$user_jy['uid']."'");
			$member=$this->obj->DB_select_once("member","`uid`='".$user_jy['uid']."'","username");

			if(is_array($user_jy)||is_array($user))
			{
				include APP_PATH."/plus/city.cache.php";
				include APP_PATH."/plus/job.cache.php";
				include APP_PATH."/plus/user.cache.php";
				include APP_PATH."/plus/industry.cache.php";
				if($this->config['user_name']==3)
				{
					$user["username_n"] = "NO.".$user_jy['id'];
				}elseif($this->config['user_name']==2){
					if($user['sex']=='6')
					{
						$user['username_n'] = mb_substr(empty($_POST['name'])?$user['name']:$_POST['name'],0,2)."先生";
					}else{
						$user['username_n'] = mb_substr(empty($_POST['name'])?$user['name']:$_POST['name'],0,2)."女士";
					}
				}else{
					$user['username_n'] = empty($_POST['name'])?$user['name']:$_POST['name'];
				}
				$user['diy_status']=1;
				if($this->config['sy_usertype_1']=='1')
				{
					if($user['resume_photo']==""||file_exists($user['resume_photo'])==false)
					{
						$user['resume_photo']=$this->config['sy_weburl'].'/'.$this->config['sy_member_icon'];
					}
				}else if($user['resume_photo']==""||file_exists($user['resume_photo'])==false){
					$user['resume_photo']=$this->config['sy_weburl'].'/'.$this->config['sy_member_icon'];
				}
				$user['username']=$member['username'];
				$user['user_sex']=$userclass_name[empty($_POST['sex'])?$user['sex']:$_POST['sex']];
				$user['user_exp']=$userclass_name[empty($_POST['exp'])?$user['exp']:$_POST['exp']];
				$user['user_marriage']=$userclass_name[empty($_POST['marriage'])?$user['marriage']:$_POST['marriage']];
				$user['useredu']=$userclass_name[empty($_POST['edu'])?$user['edu']:$_POST['edu']];
				$a=date('Y',strtotime(empty($_POST['birthday'])?$user['birthday']:$_POST['birthday']));
				$user['age']=date("Y")-$a;
				$user['city_one']=$city_name[empty($_POST['provinceid'])?$user_jy['provinceid']:$_POST['provinceid']];
				$user['city_two']=$city_name[empty($_POST['citysid'])?$user_jy['cityid']:$_POST['citysid']];
				$user['city_three']=$city_name[empty($_POST['three_cityid'])?$user_jy['three_cityid']:$_POST['three_cityid']];
				$user['salary']=$userclass_name[empty($_POST['salaryid'])?$user_jy['salary']:$_POST['salaryid']];
				$user['report']=$userclass_name[empty($_POST['reportid'])?$user_jy['report']:$_POST['reportid']];
				$user['type']=$userclass_name[empty($_POST['typeid'])?$user_jy['type']:$_POST['typeid']];
				$user['hy']=$industry_name[empty($_POST['hyid'])?$user_jy['hy']:$_POST['hyid']];
				$user['lastupdate']=date("Y-m-d",empty($_POST['lastupdate'])?$user_jy['lastupdate']:$_POST['lastupdate']);
				$user['r_name'] = empty($_POST['name'])?$user_jy['name']:$_POST['name'];
				$user['doc'] = $user_jy['doc'];
				$user['hits']=$user_jy['hits'];
				$resume_diy=split('[|]',$user_jy['resume_diy']);
				$user['resume_diy']=$resume_diy[0];
				$user['background_image']=$resume_diy[1];
				$user['id']=$id;
				$jy=@explode(",",empty($_POST['job_class'])?$user['job_classid']:$_POST['job_class']);
				if(@is_array($jy))
				{
					foreach($jy as $v)
					{
						$jobname[]=$job_name[$v];
					}
					$user['jobname']=@implode(",",$jobname);
				}
				$user_edu=$this->obj->DB_select_all("resume_edu","`eid`='$user_jy[id]'");
				$user_training=$this->obj->DB_select_all("resume_training","`eid`='$user_jy[id]'");
				$user_work=$this->obj->DB_select_all("resume_work","`eid`='$user_jy[id]'");
				$user_other=$this->obj->DB_select_all("resume_other","`eid`='$user_jy[id]'");
				$user_project=$this->obj->DB_select_all("resume_project","`eid`='$user_jy[id]'");
				$user_skill=$this->obj->DB_select_all("resume_skill","`eid`='$user_jy[id]'");
				$user_xm=$this->obj->DB_select_all("resume_project","`eid`='".$user_jy['id']."'");
				$user_cert=$this->obj->DB_select_all("resume_cert","`eid`='".$user_jy['id']."'");

			}
			if(count($user_edu)>0)
			{
				foreach($user_edu as $k=>$v)
				{
					if($v['id']==$_POST['eduid']&&$_POST['eduid']!=''){
						$user_edu[$k]['sdate']=$_POST['edu_sdate'];
						$user_edu[$k]['edate']=$_POST['edu_edate'];
						$user_edu[$k]['name']=$_POST['edu_name'];
						$user_edu[$k]['specialty']=$_POST['edu_specialty'];
						$user_edu[$k]['title']=$_POST['edu_title'];
						$user_edu[$k]['content']=$_POST['edu_content'];
					}
				}
				if($_POST['eduid']==''&&($_POST['edu_sdate']||$_POST['edu_edate']||$_POST['edu_name']||$_POST['edu_specialty']||$_POST['edu_title']||$_POST['edu_content'])){
					$user_edu[$k+1]['sdate']=$_POST['edu_sdate'];
					$user_edu[$k+1]['edate']=$_POST['edu_edate'];
					$user_edu[$k+1]['name']=$_POST['edu_name'];
					$user_edu[$k+1]['specialty']=$_POST['edu_specialty'];
					$user_edu[$k+1]['title']=$_POST['edu_title'];
					$user_edu[$k+1]['content']=$_POST['edu_content'];
				}
			}else{
				if(($_POST['edu_sdate']||$_POST['edu_edate']||$_POST['edu_name']||$_POST['edu_specialty']||$_POST['edu_title']||$_POST['edu_content'])){
					$user_edu[0]['sdate']=$_POST['edu_sdate'];
					$user_edu[0]['edate']=$_POST['edu_edate'];
					$user_edu[0]['name']=$_POST['edu_name'];
					$user_edu[0]['specialty']=$_POST['edu_specialty'];
					$user_edu[0]['title']=$_POST['edu_title'];
					$user_edu[0]['content']=$_POST['edu_content'];
				}
			}
			if(count($user_training)>0)
			{
				foreach($user_training as $k=>$v)
				{
					if($v['id']==$_POST['trainingid']&&$_POST['trainingid']!=''){
						$user_training[$k]['sdate']=$_POST['training_sdate'];
						$user_training[$k]['edate']=$_POST['training_edate'];
						$user_training[$k]['name']=$_POST['training_name'];
						$user_training[$k]['title']=$_POST['training_title'];
						$user_training[$k]['content']=$_POST['training_content'];
					}
				}
				if(($_POST['trainingid']=='')&&($_POST['training_sdate']||$_POST['training_edate']||$_POST['training_name']||$_POST['training_title']||$_POST['training_content'])){
					$user_training[$k+1]['sdate']=$_POST['training_sdate'];
					$user_training[$k+1]['edate']=$_POST['training_edate'];
					$user_training[$k+1]['name']=$_POST['training_name'];
					$user_training[$k+1]['title']=$_POST['training_title'];
					$user_training[$k+1]['content']=$_POST['training_content'];
				}
			}else{
				if(($_POST['training_sdate']||$_POST['training_edate']||$_POST['training_name']||$_POST['training_title']||$_POST['training_content'])){
					$user_training[0]['sdate']=$_POST['training_sdate'];
					$user_training[0]['edate']=$_POST['training_edate'];
					$user_training[0]['name']=$_POST['training_name'];
					$user_training[0]['title']=$_POST['training_title'];
					$user_training[0]['content']=$_POST['training_content'];
				}
			}
			if(count($user_work)>0)
			{
				foreach($user_work as $k=>$v)
				{
					if($v['id']==$_POST['workid']&&$_POST['workid']!=''){
						$user_work[$k]['sdate']=$_POST['work_sdate'];
						$user_work[$k]['edate']=$_POST['work_edate'];
						$user_work[$k]['department']=$_POST['work_department'];
						$user_work[$k]['name']=$_POST['work_name'];
						$user_work[$k]['title']=$_POST['work_title'];
						$user_work[$k]['content']=$_POST['work_content'];
					}
				}
				if($_POST['workid']==''&&($_POST['work_sdate']||$_POST['work_edate']||$_POST['work_department']||$_POST['work_name']||$_POST['work_title']||$_POST['work_content'])){
					$user_work[$k+1]['sdate']=$_POST['work_sdate'];
					$user_work[$k+1]['edate']=$_POST['work_edate'];
					$user_work[$k+1]['department']=$_POST['work_department'];
					$user_work[$k+1]['name']=$_POST['work_name'];
					$user_work[$k+1]['title']=$_POST['work_title'];
					$user_work[$k+1]['content']=$_POST['work_content'];
				}
			}else{
				if(($_POST['work_sdate']||$_POST['work_edate']||$_POST['work_department']||$_POST['work_name']||$_POST['work_title']||$_POST['work_content'])){
					$user_work[0]['sdate']=$_POST['work_sdate'];
					$user_work[0]['edate']=$_POST['work_edate'];
					$user_work[0]['department']=$_POST['work_department'];
					$user_work[0]['name']=$_POST['work_name'];
					$user_work[0]['title']=$_POST['work_title'];
					$user_work[0]['content']=$_POST['work_content'];
				}
			}
			if(count($user_project)>0)
			{
				foreach($user_project as $k=>$v)
				{
					if($v['id']==$_POST['projectid']&&$_POST['projectid']!=''){
						$user_project[$k]['sdate']=$_POST['project_sdate'];
						$user_project[$k]['edate']=$_POST['project_edate'];
						$user_project[$k]['sys']=$_POST['project_sys'];
						$user_project[$k]['name']=$_POST['project_name'];
						$user_project[$k]['title']=$_POST['project_title'];
						$user_project[$k]['content']=$_POST['project_content'];
					}
				}
				if($_POST['projectid']==''&&($_POST['project_sdate']||$_POST['project_edate']||$_POST['project_sys']||$_POST['project_name']||$_POST['project_title']||$_POST['project_content'])){
					$user_project[$k+1]['sdate']=$_POST['project_sdate'];
					$user_project[$k+1]['edate']=$_POST['project_edate'];
					$user_project[$k+1]['sys']=$_POST['project_sys'];
					$user_project[$k+1]['name']=$_POST['project_name'];
					$user_project[$k+1]['title']=$_POST['project_title'];
					$user_project[$k+1]['content']=$_POST['project_content'];
				}
			}else{
				if(($_POST['project_sdate']||$_POST['project_edate']||$_POST['project_sys']||$_POST['project_name']||$_POST['project_title']||$_POST['project_content'])){
					$user_project[0]['sdate']=$_POST['project_sdate'];
					$user_project[0]['edate']=$_POST['project_edate'];
					$user_project[0]['sys']=$_POST['project_sys'];
					$user_project[0]['name']=$_POST['project_name'];
					$user_project[0]['title']=$_POST['project_title'];
					$user_project[0]['content']=$_POST['project_content'];
				}
			}
			if(count($user_skill)>0)
			{
				foreach($user_skill as $k=>$v)
				{
					if($v['id']==$_POST['skillid']&&$_POST['skillid']!=''){
						$user_skill[$k]['skill_n']=$userclass_name[$_POST['skillcid']];
						$user_skill[$k]['ing_n']=$userclass_name[$_POST['levelid']];
					}else{
						$user_skill[$k]['skill_n']=$userclass_name[$v['skill']];
						$user_skill[$k]['ing_n']=$userclass_name[$v['ing']];
					}
				}
				if($_POST['skillid']==''&&($_POST['skillcid']||$_POST['levelid']||$_POST['skill_longtime']||$_POST['skill_name'])){
					$user_skill[$k+1]['skill_n']=$userclass_name[$_POST['skillcid']];
					$user_skill[$k+1]['ing_n']=$userclass_name[$_POST['levelid']];
					$user_skill[$k+1]['longtime']=$_POST['skill_longtime'];
					$user_skill[$k+1]['name']=$_POST['skill_name'];
				}
			}else{
				if(($_POST['skillcid']||$_POST['levelid']||$_POST['skill_longtime']||$_POST['skill_name'])){
					$user_skill[0]['skill_n']=$userclass_name[$_POST['skillcid']];
					$user_skill[0]['ing_n']=$userclass_name[$_POST['levelid']];
					$user_skill[0]['longtime']=$_POST['skill_longtime'];
					$user_skill[0]['name']=$_POST['skill_name'];
				}
			}
			if(count($user_cert)>0)
			{
				foreach($user_cert as $k=>$v)
				{
					if($v['id']==$_POST['certid']&&$_POST['certid']!=''){
						$user_cert[$k]['sdate']=$_POST['cert_sdate'];
						$user_cert[$k]['name']=$_POST['cert_name'];
						$user_cert[$k]['title']=$_POST['cert_title'];
						$user_cert[$k]['content']=$_POST['cert_content'];
					}
				}
				if($_POST['skillid']==''&&($_POST['cert_sdate']||$_POST['cert_name']||$_POST['cert_title']||$_POST['cert_content'])){
					$user_cert[$k+1]['sdate']=$_POST['cert_sdate'];
					$user_cert[$k+1]['name']=$_POST['cert_name'];
					$user_cert[$k+1]['title']=$_POST['cert_title'];
					$user_cert[$k+1]['content']=$_POST['cert_content'];
				}
			}else{
				if(($_POST['cert_sdate']||$_POST['cert_name']||$_POST['cert_title']||$_POST['cert_content'])){
					$user_cert[0]['sdate']=$_POST['cert_sdate'];
					$user_cert[0]['name']=$_POST['cert_name'];
					$user_cert[0]['title']=$_POST['cert_title'];
					$user_cert[0]['content']=$_POST['cert_content'];
				}
			}
			if(count($user_cert)>0)
			{
				foreach($user_other as $k=>$v)
				{
					if($v['id']==$_POST['otherid']){
						$user_other[$k]['title']=$_POST['other_title'];
						$user_other[$k]['content']=$_POST['other_content'];
					}
				}
				if($_POST['otherid']==''&&($_POST['other_title']||$_POST['other_content'])){
					$user_other[$k+1]['title']=$_POST['other_title'];
					$user_other[$k+1]['content']=$_POST['other_content'];
				}
			}else{
				if(($_POST['other_title']||$_POST['other_content'])){
					$user_other[0]['title']=$_POST['other_title'];
					$user_other[0]['content']=$_POST['other_content'];
				}
			}
			if($this->uid==$user['uid'] && $this->username && $_COOKIE['usertype']==1)
			{
				$user['m_status']=1;
			}
			$user['user_jy']=$user_jy;
			$user['user_edu']=$user_edu;
			$user['user_tra']=$user_training;
			$user['user_work']=$user_work;
			$user['user_other']=$user_other;
			$user['user_xm']=$user_xm;
			$user['user_skill']=$user_skill;
			$user['user_cert']=$user_cert;
			$data['resume_username']=$user['username_n'];
			$data['resume_city']=$user['city_one'].",".$user['city_two'];
			$data['resume_job']=$user['hy'];
			$this->yunset("Info",$user);
		}
		$_SESSION["ResumeInfo"]=$user;
	}
	function reviewresume_action(){
		$this->yunset("Info",$_SESSION["ResumeInfo"]);
		$this->yunset("title","简历预览");
		$this->public_action();
		$user_jy=$this->obj->DB_select_once("resume_expect","`id`='".$_SESSION["ResumeInfo"]['id']."'");
		if(empty($_GET['tmp'])){
			$_GET['tmp']=$user_jy['tmpid'];
			if($user_jy['tmpid']=='1'){
				$this->yuntpl(array('default/resume/jianli1/index'));
			}else if($user_jy['tmpid']=='2'){
				$this->yuntpl(array('default/resume/jianli2/index'));
			}else if($user_jy['tmpid']=='3'){
				$this->yuntpl(array('default/resume/jianli3/index'));
			}else{
				$this->yuntpl(array('default/resume/index'));
			}
		}else{
			if($_GET['tmp']=='1'){
				$this->yuntpl(array('default/resume/jianli1/index'));
			}else if($_GET['tmp']=='2'){
				$this->yuntpl(array('default/resume/jianli2/index'));
			}else if($_GET['tmp']=='3'){
				$this->yuntpl(array('default/resume/jianli3/index'));
			}else{
				$this->yuntpl(array('default/resume/index'));
			}
		}

	}
}
?>