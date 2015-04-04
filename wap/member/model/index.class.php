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
class index_controller extends common
{
	function waptpl($tpname)
	{
		$this->yuntpl(array('wap/member/user/'.$tpname));
	}
	function index_action()
	{
		$looknum=$this->obj->DB_select_num("look_resume","`uid`='".$this->uid."' and `status`='0'");
		$look_jobnum=$this->obj->DB_select_num("look_job","`uid`='".$this->uid."' and `status`='0'");
		$this->yunset("looknum",$looknum);
		$this->yunset("look_jobnum",$look_jobnum);
		$yqnum=$this->obj->DB_select_num("userid_msg","`uid`='".$this->uid."'");
		$this->yunset("yqnum",$yqnum);
		$statis=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'");
		$resume_num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
		$this->yunset("resume_num",$resume_num);
		$sq_nums=$this->obj->DB_select_num("userid_job","`uid`='".$this->uid."' ");
		$statis['sq_jobnum']=$sq_nums;
		$this->yunset("statis",$statis);
		$this->waptpl('index');
	}
	function sq_action()
	{
		if($_GET['del']){
			$userid_job=$this->obj->DB_select_once("userid_job","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
			$id=$this->obj->DB_delete_all("userid_job","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
			if($id){
				$data['msg']='删除成功！';
				$this->obj->DB_update_all('company_statis',"`sq_job`=`sq_job`-1","`uid`='".$userid_job['com_id']."'");
				$this->obj->DB_update_all('member_statis',"`sq_jobnum`=`sq_jobnum`-1","`uid`='".$userid_job['uid']."'");
				$this->obj->member_log("删除申请的职位");
			}else{
				$data['msg']="删除失败!";
			}
			$data['url']='index.php?c=sq';
			$this->yunset("layer",$data);
		}
		$urlarr=array("c"=>"sq","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("userid_job","`uid`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$com_id[]=$v['com_id'];
			}
			$company=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$com_id).")","cityid,uid");
			include APP_PATH."/plus/city.cache.php";
			foreach($rows as $k=>$v)
			{
				foreach($company as $val)
				{
					if($v['com_id']==$val['uid'])
					{
						$rows[$k]['city']=$city_name[$val['cityid']];
					}
				}
			}
		}

		$this->yunset("rows",$rows);
		$this->waptpl('sq');
	}
	function collect_action()
	{
		if($_GET['del'])
		{
			$id=$this->obj->DB_delete_all("fav_job","`id`='".$_GET['del']."' and `uid`='".$this->uid."'");
			if($id){
				$data['msg']="删除成功!";
				$this->obj->DB_update_all("member_statis","`fav_jobnum`=`fav_jobnum`-1","uid='".$this->uid."'");
				$this->obj->member_log("删除收藏的职位");
			}else{
				$data['msg']="删除失败！";
			}
			$data['url']='index.php?c=collect';
			$this->yunset("layer",$data);
		}
		$urlarr=array("c"=>"collect","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("fav_job","`uid`='".$this->uid."'",$pageurl,"10");
		$this->waptpl('collect');
	}
	function password_action(){
		if($_POST['submit']){
			$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'");
			$pw=md5(md5($_POST['oldpassword']).$member['salt']);
			if($pw!=$member['password']){
				$data['msg']="旧密码不正确，请重新输入！";
				$data['url']='index.php?c=password';
			}else if(strlen($_POST['password1'])<6 || strlen($_POST['password1'])>20){
				$data['msg']="密码长度应在6-20位！";
				$data['url']='index.php?c=password';
			}else if($_POST['password1']!=$_POST['password2']){
				$data['msg']="新密码和确认密码不一致！";
				$data['url']='index.php?c=password';
			}else if($this->config['sy_uc_type']=="uc_center" && $member['name_repeat']!="1"){
				$this->obj->uc_open();
				$ucresult= uc_user_edit($member['username'], $_POST['oldpassword'], $_POST['password1'], "","1");
				if($ucresult == -1){
					$data['msg']="旧密码不正确，请重新输入！";
					$data['url']='index.php?c=password';
				}
			}else{
				$salt = substr(uniqid(rand()), -6);
				$pass2 = md5(md5($_POST['password1']).$salt);
				$this->obj->DB_update_all("member","`password`='".$pass2."',`salt`='".$salt."'","`uid`='".$this->uid."'");
				SetCookie("uid","",time() -286400, "/");
				SetCookie("username","",time() - 86400, "/");
				SetCookie("salt","",time() -86400, "/");
				SetCookie("shell","",time() -86400, "/");
				$this->obj->member_log("修改密码");
				$data['msg']="修改成功，请重新登录！";
				$data['url']=$this->config['sy_weburl'].'/wap/index.php?m=login';
			}
			$this->yunset("layer",$data);
		}

		$this->waptpl('password');
	}
	function invite_action()
	{
		if($_GET['del'])
		{
			$id=$this->obj->DB_delete_all("userid_msg","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
			if($id)
			{
				$this->obj->member_log("删除邀请信息");
				$data['msg']="删除成功!";
			}else{
				$data['msg']="删除失败!";
			}
			$data['url']='index.php?c=invite';
			$this->yunset("layer",$data);
		}
		$urlarr=array("c"=>"invite","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("userid_msg","`uid`='".$this->uid."'",$pageurl,"10");
		$this->waptpl('invite');
	}
	function look_action()
	{
		if($_GET['del'])
		{
			$id=$this->obj->DB_delete_all("look_resume","`id`='".(int)$_GET['del']."' and `uid`='".$this->uid."'");
			if($id)
			{
				$data['msg']="删除成功!";
				$this->obj->member_log("删除简历浏览记录");
			}else{
				$data['msg']="删除失败!";
			}
			$data['url']='index.php?c=look';
			$this->yunset("layer",$data);
		}
		$urlarr=array("c"=>"look","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("look_resume","`uid`='".$this->uid."'",$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['com_id'];
				$eid[]=$v['resume_id'];
			}
			$company=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$uid).")","`uid`,`name`");
			$resume=$this->obj->DB_select_all("resume_expect","`id` in (".@implode(",",$eid).")","`id`,`name`");
			foreach($rows as $k=>$v)
			{
				foreach($company as $val)
				{
					if($v['com_id']==$val['uid'])
					{
						$rows[$k]['com_name']=$val['name'];
					}
				}
				foreach($resume as $val)
				{
					if($v['resume_id']==$val['id'])
					{
						$rows[$k]['resume_name']=$val['name'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->waptpl('look');
	}
	function addresume_action()
	{
		if($this->config['user_enforce_identitycert']=="1")
		{
			$row=$this->obj->DB_select_once("resume","`idcard_pic`<>'' and `uid`='".$this->uid."'");
			if($row['idcard_status']!="1")
			{
				$data['msg']='请先登录电脑客户端完成身份认证！';
				$data['url']='index.php';
			}
		}
		if($_GET['type']&&intval($_GET['id'])){
			$nid=$this->obj->DB_delete_all("resume_".$_GET['type'],"`eid`='".(int)$_GET['eid']."' and `id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			if($nid)
			{
				$url=$_GET['type'];
				$this->obj->DB_update_all("user_resume","`$url`=`$url`-1","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
				$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".(int)$_GET['eid']."'");
				$this->obj->complete($resume_row);
				$data['msg']='删除成功！';
			}else{
				$data['msg']='删除失败！';
			}
			$data['url']='index.php?c=addresume&eid='.(int)$_GET['eid'];

		}
		if($_POST['submit']){
			$_POST=$this->post_trim_iconv($_POST);
			if($_POST['eid']>0){
				$table="resume_".$_POST['table'];
				$id=(int)$_POST['id'];
				$url=$_POST['table'];
				unset($_POST['submit']);
				unset($_POST['table']);
				unset($_POST['id']);
				if($_POST['syear'])
				{
					$_POST['sdate']=strtotime($_POST['syear']."-".$_POST['smouth']."-".$_POST['sday']);
					$_POST['edate']=strtotime($_POST['eyear']."-".$_POST['emouth']."-".$_POST['eday']);
					unset($_POST['syear']);
					unset($_POST['smouth']);
					unset($_POST['sday']);
					unset($_POST['eyear']);
					unset($_POST['emouth']);
					unset($_POST['eday']);
				}
				if($id)
				{
					$where['id']=$id;
					$where['uid']=$this->uid;
					$nid=$this->obj->update_once($table,$_POST,$where);
				}else{
					$_POST['uid']=$this->uid;
					$nid=$this->obj->insert_into($table,$_POST);
					$this->obj->DB_update_all("user_resume","`$url`=`$url`+1","`eid`='".(int)$_POST['eid']."' and `uid`='".$this->uid."'");
					$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".(int)$_POST['eid']."'");
					$this->obj->complete($resume_row);
				}
				$nid?$data['msg']='保存成功！':$data['msg']='保存失败！';
				$data['url']=$nid?('index.php?c=addresume&eid='.(int)$_POST['eid']):'';
				$data['msg']=iconv('gbk','utf-8',$data['msg']);
				echo json_encode($data);die;
			} else{
				if($_POST['name']==""){
					$data['msg']='姓名不能为空！';
				}else if($_POST['sex']==""){
					$data['msg']='性别不能为空！';
				}else if($this->config['user_idcard']=="1"&&trim($_POST['idcard'])==""){
					$data['msg']='身份证号码不能为空！';
				}else if($_POST['living']==""){
					$data['msg']='现居住地不能为空！';
				}else{
					unset($_POST['submit']);
					$this->obj->delfiledir("../upload/tel/".$this->uid);
					$where['uid']=$this->uid;
					$nid=$this->obj->update_once("resume",$_POST,$where);
					if($nid){
						$this->obj->update_once("member",array('email'=>$_POST['email'],'moblie'=>$_POST['telphone']),$where);
						$this->obj->member_log("保存基本信息");
						$data['msg']='保存成功！';
						$data['url']='index.php?c=addresume';
					}else{
						$data['msg']='保存失败！';
						$data['url']='index.php?c=addresume';
					}
				}
			}
		}
		if(!$_GET['eid']&&$_POST['submit']==''){
			$num=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'");
			$maxnum=$this->config['user_number']-$num['resume_num'];
			$confignum=$this->config['user_number'];
			if($maxnum<=0 &&$confignum!=""){
				$data['msg']='你的简历数已经超过系统设置的简历数了！';
				$data['url']='index.php?c=resume';
			}
		}else if($_GET['eid']){
			$row=$this->obj->DB_select_once("resume_expect","`id`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			include(PLUS_PATH."job.cache.php");
			$job_classid=@explode(",",$row['job_classid']);
			foreach($job_classid as $v){
				$jobname[]=$job_name[$v];
			}
			$jobname=@implode(",",$jobname);
			$this->yunset("row",$row);
			$this->yunset("jobname",$jobname);
			$skill=$this->obj->DB_select_all("resume_skill","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$work=$this->obj->DB_select_all("resume_work","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$project=$this->obj->DB_select_all("resume_project","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$edu=$this->obj->DB_select_all("resume_edu","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$training=$this->obj->DB_select_all("resume_training","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$cert=$this->obj->DB_select_all("resume_cert","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$other=$this->obj->DB_select_all("resume_other","`eid`='".(int)$_GET['eid']."' and `uid`='".$this->uid."'");
			$this->yunset("skill",$skill);
			$this->yunset("work",$work);
			$this->yunset("project",$project);
			$this->yunset("edu",$edu);
			$this->yunset("training",$training);
			$this->yunset("cert",$cert);
			$this->yunset("other",$other);
		}
		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		$this->yunset("resume",$resume);
		$this->yunset("layer",$data);
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$this->waptpl('addresume');
	}
	function addresumeson_action()
	{
		if($_GET['id']){
			$row=$this->obj->DB_select_once("resume_".$_GET['type'],"`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			$this->yunset("row",$row);
		}
		$this->user_cache();
		$this->waptpl('addresumeson');
	}
	function post_trim_iconv($data){
		foreach($data as $d_k=>$d_v){
			if(is_array($d_v)){
				$data[$d_k]=$this->post_trim_iconv($d_v);
			}else{
				$data[$d_k]=$this->stringfilter(trim($data[$d_k]));
			}
		}
		return $data;
	}
	function info_action()
	{
		if($_POST['submit']){
			$_POST=$this->post_trim_iconv($_POST);
			if($_POST['name']==""){
				$data['msg']='姓名不能为空！';
				$data['url']='index.php?c=addresume';
			}else if($_POST['sex']==""){
				$data['msg']='性别不能为空！';
				$data['url']='index.php?c=addresume';
			}else if($this->config['user_idcard']=="1"&&trim($_POST['idcard'])==""){
				$data['msg']='身份证号码不能为空！';
				$data['url']='index.php?c=addresume';
			}else if($_POST['living']==""){
				$data['msg']='现居住地不能为空！';
				$data['url']='index.php?c=addresume';
			}else{
				unset($_POST['submit']);
				$this->obj->delfiledir("../upload/tel/".$this->uid);
				$where['uid']=$this->uid;
				$nid=$this->obj->update_once("resume",$_POST,$where);
				if($nid){
					$this->obj->update_once("member",array('email'=>$_POST['email'],'moblie'=>$_POST['telphone']),$where);
					$this->obj->member_log("保存基本信息");
					$data['msg']=iconv('gbk','utf-8','保存成功！');
					$data['url']='index.php?c=addresume';
				}else{
					$data['msg']=iconv('gbk','utf-8','保存失败！');
					$data['url']='';
				}
			}
			$this->yunset("layer",$data);
		}
		echo json_encode($data);die;
		$this->waptpl('addresume');
	}
	function expect_action()
	{
		$eid=(int)$_POST['eid'];
		unset($_POST['submit']);
		unset($_POST['eid']);
		$_POST['name'] = $this->stringfilter($_POST['name']);
		$where['id']=$eid;
		$where['uid']=$this->uid;
		$_POST['lastupdate']=time();
	
		if($eid=="")
		{
			$_POST['uid']=$this->uid;
			$_POST['source']=2;
			$nid=$this->obj->insert_into("resume_expect",$_POST);
			if ($nid)
			{
				$num=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'");
				if($num['resume_num']==0)
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
				$this->obj->member_log("发布了新简历");
			}
			$eid=$nid;
		}else{
			$nid=$this->obj->update_once("resume_expect",$_POST,$where);
			$this->obj->member_log("更新了简历");
		}
		echo $nid;die;
	}

	function resume_action()
	{
		if($_GET['del']){
			$del=(int)$_GET['del'];
			$del_array=array("resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume");
			if($this->obj->DB_delete_all("resume_expect","`id`='".$del."' and `uid`='".$this->uid."'"))
			{
				foreach($del_array as $v)
				{
					$this->obj->DB_delete_all($v,"`eid`='".$del."'' and `uid`='".$this->uid."'","");
				}
				$def_id=$this->obj->DB_select_once("resume","`uid`='".$this->uid."' and `def_job`='".$del."'");
			    if(is_array($def_id))
			    {
					$row=$this->obj->DB_select_once("resume_expect","`uid`='".$this->uid."'");
					$this->obj->update_once('resume',array('def_job'=>$row['id']),array('uid'=>$this->uid));
			    }
				$this->obj->DB_update_all('member_statis',"`resume_num`=`resume_num`-1","`uid`='".$this->uid."'");
				$this->obj->member_log("删除简历");
				$data['msg']='删除成功！';
				$data['url']='index.php?c=resume';
			}else{
				$data['msg']='删除失败！';
				$data['url']='index.php?c=resume';
			}
		}
		if($_GET['update'])
		{
			$id=(int)$_GET['update'];
			$nid=$this->obj->update_once('resume_expect',array('lastupdate'=>time()),array('id'=>$id,'uid'=>$this->uid));
			if($nid)
			{
				$this->obj->member_log("刷新简历");
				$data['msg']='刷新成功！';
				$data['url']='index.php?c=resume';
			}else{
				$data['msg']='刷新失败！';
				$data['url']='index.php?c=resume';
			}
		}
		if($_POST['type']=="def_job"){
			$this->obj->DB_update_all("resume","`def_job`='".(int)$_POST['eid']."'","`uid`='".$this->uid."'");die;
		}
		$rows=$this->obj->DB_select_all("resume_expect","`uid`='".$this->uid."'","id,name,lastupdate,doc");
		$this->yunset("layer",$data);
		$this->yunset("rows",$rows);
		$def_job=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","def_job");
		$this->yunset("def_job",$def_job);
		$this->waptpl('resume');
	}
	function loginout_action()
	{
		SetCookie("uid","",time() -86400, "/");
		SetCookie("username","",time() - 86400, "/");
		SetCookie("usertype","",time() -86400, "/");
		SetCookie("salt","",time() -86400, "/");
		SetCookie("shell","",time() -86400, "/");
		$this->wapheader('../index.php');
	}
	function look_job_del_action(){
		if($_GET['del']||$_GET['id']){
			if(is_array($_GET['del'])){
				$del=$this->pylode(",",$_GET['del']);
				$layer_type=1;
			}else{
				$del=(int)$_GET['id'];
				$layer_type=0;
			}
			$nid=$this->obj->DB_update_all("look_job","`status`='1'","`id` in (".$del.") and `uid`='".$this->uid."'");
			if($nid)
			{
				$this->obj->member_log("删除职位浏览记录(ID:".$del.")");
				$data['msg']="删除成功！";
				$data['url']=$this->config['sy_weburl'].'/wap/member/index.php?c=look_job';
			}else{
				$data['msg']="删除失败！";
				$data['url']=$this->config['sy_weburl'].'/wap/member/index.php?c=look_job';
			}
		}
		$this->yunset("layer",$data);
		$this->waptpl('look_job');
	}
	function look_job_action(){

		$urlarr=array("c"=>"look","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$look=$this->get_page("look_job","`uid`='".$this->uid."' and `status`='0' order by `datetime` desc",$pageurl,"10");
		if(is_array($look))
		{
			include APP_PATH."/plus/city.cache.php";
			include APP_PATH."/plus/com.cache.php";
			foreach($look as $v)
			{
				$jobid[]=$v['jobid'];
			}
			$job=$this->obj->DB_select_all("company_job","`id` in (".@implode(",",$jobid).")","`id`,`name`,`com_name`,`salary`,`provinceid`,`cityid`,`uid`,`id`");

			foreach($look as $k=>$v)
			{
				foreach($job as $val)
				{
					if($v['jobid']==$val['id'])
					{
						$look[$k]['jobname']=$val['name'];
						$look[$k]['com_id']=$val['uid'];
						$look[$k]['job_id']=$val['id'];
						$look[$k]['comname']=$val['com_name'];
						$look[$k]['salary']=$comclass_name[$val['salary']];
						$look[$k]['provinceid']=$city_name[$val['provinceid']];
						$look[$k]['cityid']=$city_name[$val['cityid']];
					}
				}
			}
		}
		$this->yunset("js_def",2);
		$this->yunset("look",$look);
		$this->waptpl('look_job');
	}
}
?>