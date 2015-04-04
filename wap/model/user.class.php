<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class user_controller extends common
{
	function index_action()
	{
		$this->get_moblie(); 
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		if($_GET['three_cityid']){ 
			$this->yunset("cityname",$CacheArr['city_name'][$_GET['three_cityid']]);
		}
		$this->yunset("title","找人才");
		$this->yuntpl(array('wap/user'));
	}
	function search_action(){
		$this->get_moblie();  
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr['city'] =array('city_index','city_type','city_name'); 
		$CacheArr['job'] =array('job_index','job_type','job_name'); 
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$this->yunset("title","找人才");
		$this->yuntpl(array('wap/usersearch'));
	}
	function show_action()
	{
		$this->get_moblie();
		
		if($_GET['uid'])
		{
			if($_GET['type']=="2")
			{
				$user=$this->obj->DB_select_once("resume_expect","`uid`='".(int)$_GET['uid']."' and `height_status`='2'");
				$this->resume_select($user['id']);
			}else{
				$def_job=$this->obj->DB_select_once("resume","`r_status`<>'2' and `uid`='".(int)$_GET['uid']."'","def_job");
				if(!is_array($def_job))
				{
	    			//$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"没有找到该人才！");
	    		}else{
	    			if($def_job['def_job']=="0")
	    			{
						//$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"还没有创建简历！");
					}else{
						$_GET['id']=$def_job['def_job'];
					}
	    		}
	    		//$this->resume_select($def_job['def_job']);
				$id=$def_job['def_job'];
			}
		}else{			
			$id=$_GET['id'];
		}
		
		$user_jy=$this->obj->DB_select_once("resume_expect","`id`='".$id."'");
		$user=$this->obj->DB_select_once("resume","`r_status`<>'2' and `uid`='".$user_jy['uid']."'");
		if(is_array($user_jy)||is_array($user))//处理类别字段
		{
			//简历浏览记录
			if($_COOKIE['usertype']=="2")
			{
				$look_resume=$this->obj->DB_select_once("look_resume","`com_id`='".$this->uid."' and `resume_id`='".$id."'");
				if(!empty($look_resume))
				{
					$this->obj->DB_update_all("look_resume","`datetime`='".time()."'","`com_id`='".$this->uid."' and `resume_id`='".$id."'");
				}else{
					$value.="`uid`='".$user_jy['uid']."',";
					$value.="`resume_id`='".$id."',";
					$value.="`com_id`='".$this->uid."',";
					$value.="`datetime`='".time()."'";
					$this->obj->DB_insert_once("look_resume",$value);
				}
			}
			include APP_PATH."/plus/city.cache.php";
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/user.cache.php";
			include APP_PATH."/plus/industry.cache.php";
			//名称显示处理
			if($this->config['user_name']==3){
				$user['username_n'] = "NO.".$user_jy['id'];
			}elseif($this->config['user_name']==2){
				if($user['sex']=='6'){
					$user['username_n'] = mb_substr($user['name'],0,2)."先生";
				}else{
					$user['username_n'] = mb_substr($user['name'],0,2)."女士";
				}
			}else{
				$user['username_n'] = $user['name'];
			}
			//$my_down=array();
			if($_COOKIE['usertype']=='2'){
				$my_down=$this->obj->DB_select_all("down_resume","`comid`='".$_COOKIE['uid']."'","uid");
			}
			if(!empty($my_down)){
				foreach($my_down as $m_k=>$m_v){
					$my_down_uid[]=$m_v['uid'];
				}
			}
			if($this->config['sy_usertype_1']=='1'){
				if(in_array($user['uid'],$my_down_uid)==false ||$user['resume_photo']==""){
					$user['resume_photo']='/upload/user/ltphoto.jpg';
				}
			}else if($user['resume_photo']==""){
				$user['resume_photo']='/upload/user/ltphoto.jpg';
			}
			//处理类别字段
			$user['user_sex']=$userclass_name[$user['sex']];
			$user['user_exp']=$userclass_name[$user['exp']];
			$user['useredu']=$userclass_name[$user['edu']];
			$user['user_city_one']=$city_name[$user['province']];
			$user['user_city_two']=$city_name[$user['city']];
			$user['user_city_three']=$city_name[$user['three_city']];
			$user['user_cityid_one']=$city_name[$user['provinceid']];
			$user['user_cityid_two']=$city_name[$user['cityid']];
			$user['user_cityid_three']=$city_name[$user['three_cityid']];
			$user['age']=date("Y")-$a;
			$user['salary']=$userclass_name[$user_jy['salary']];
			$user['hy']=$industry_name[$user_jy['hy']];
			$user['lastupdate']=date("Y-m-d",$user_jy['lastupdate']);
			$user['r_name'] = $user_jy['name'];
			$user['doc'] = $user_jy['doc'];
			$user['hits']=$user_jy['hits'];
			$user['id']=$id;
			$jy=@explode(",",$user_jy['job_classid']);
			if(@is_array($jy)){
				foreach($jy as $v){
					$jobname[]=$job_name[$v];
				}
				$user['jobname']=@implode(",",$jobname);
			}
			if($user_jy['doc']){
				$user_doc=$this->obj->DB_select_once("resume_doc","`eid`='".$user['id']."'");
			}else{
				$user_work=$this->obj->DB_select_all("resume_work","`eid`='$user_jy[id]'");
			}
			
			$user_edu=$this->obj->DB_select_all("resume_edu","`eid`='$user_jy[id]'");
			$user_training=$this->obj->DB_select_all("resume_training","`eid`='$user_jy[id]'");
			$user_work=$this->obj->DB_select_all("resume_work","`eid`='$user_jy[id]'");
			$user_other=$this->obj->DB_select_all("resume_other","`eid`='$user_jy[id]'");
			$user_project=$this->obj->DB_select_all("resume_project","`eid`='$user_jy[id]'");
			$user_skill=$this->obj->DB_select_all("resume_skill","`eid`='$user_jy[id]'");
			$user_xm=$this->obj->DB_select_all("resume_project","`eid`='".$user_jy['id']."'");
			$user_show=$this->obj->DB_select_all("resume_show","`eid`='".$user_jy['id']."'");
			if(is_array($user_skill))
			{
				foreach($user_skill as $k=>$v)
				{
					$user_skill[$k]['skill_n']=$userclass_name[$v['skill']];
					$user_skill[$k]['ing_n']=$userclass_name[$v['ing']];
				}
				$user_cert=$this->obj->DB_select_all("resume_cert","`eid`='".$user_jy['id']."'");
			}
		}
		$userid_job=$this->obj->DB_select_once("userid_job","`com_id`='".$this->uid."' and `eid`='".$user_jy['id']."'");
		if(!empty($userid_job))
		{
			$user['m_status']=1;
		}
		if($this->uid==$user['uid'] && $this->username && $_COOKIE['usertype']==1){
			$user['m_status']=1;
		}
		if($this->uid && $this->username && ($_COOKIE['usertype']==2 || $_COOKIE['usertype']==3)){
			$row=$this->obj->DB_select_once("down_resume","`eid`='$id' and comid='".$this->uid."'");
			if(is_array($row)){
				$user['m_status']=1;
				$user['username_n'] = $user['name'];
			}else{
				$user['link_msg']="<a href='javascript:void(0)' onclick=\"for_link('$id')\">查看联系方式</a>";
			}
		}
		if($this->uid && $this->username && $_COOKIE['usertype']==1)
		{
			$user['link_msg']="您不是企业用户！";
		}
		if(!$this->uid && !$this->username)
		{
			$user['link_msg']="您还没有登录，请点击<a href='".$this->config['sy_weburl']."/wap/index.php?m=login&usertype=2'>登录</a>！";
		}
		if($_GET['look']){
			$user['m_status']=1;
		}
		$user['user_edu']=$user_edu;
		$user['user_training']=$user_training;
		$user['user_work']=$user_work;
		$user['user_other']=$user_other;
		$user['user_skill']=$user_skill;
		$user['user_cert']=$user_cert;
		$user['user_doc']=$user_doc;
		$user['user_jy']=$user_jy;
		$user['user_work']=$user_work;
		$data['resume_username']=$user['username_n'];//简历人姓名
		$data['resume_city']=$user['user_city_one'].",".$user['user_city_two'];//城市
		$data['resume_job']=$user['hy'];//行业
		$this->yunset("Info",$user);
		$this->yunset("title","找人才");
		$this->yuntpl(array('wap/user_show'));
	}
	function invite_action(){	
		$this->get_moblie();
		$rows=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."'");
		$this->yunset("joblist",$rows);
		$this->yunset("cuid",$this->uid);		
		$this->yuntpl(array('wap/invite'));
	}
}
?>