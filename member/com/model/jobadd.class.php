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
class jobadd_controller extends company
{
	function index_action()
	{
		$company=$this->get_user();
		$msg=array();
		$isallow_addjob="1";
		$url="index.php?c=binding";
		if($this->config['com_enforce_emailcert']=="1"){
			if($company['email_status']!="1"){
				$isallow_addjob="0";
				$msg[]="邮箱认证";
			}
		}
		if($this->config['com_enforce_mobilecert']=="1"){
			if($company['moblie_status']!="1"){
				$isallow_addjob="0";
				$msg[]="手机认证";
			}
		}
		if($this->config['com_enforce_licensecert']=="1"){
			if($company['yyzz_status']!="1"){
				$isallow_addjob="0";
				$msg[]="营业执照认证";
			}
		}
		if($this->config['com_enforce_setposition']=="1"){
			if(empty($company['x'])||empty($company['y'])){
				$isallow_addjob="0";
				$msg[]="设置企业地图";
				$url="index.php?c=map";
			}
		}
		if($isallow_addjob=="0"){
			$this->obj->ACT_msg($url,"请先完成".$this->pylode("、",$msg)."！");
		}

		$this->public_action();
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$row['hy']=$company['hy'];
		$row['sdate']=mktime();
		$row['edate']=strtotime("+1 month");
		$row['number']=$CacheArr['comdata']['job_number'][0];
		$row['salary']=$CacheArr['comdata']['job_salary'][0];
		$row['type']=$CacheArr['comdata']['job_type'][0];
		$row['exp']=$CacheArr['comdata']['job_exp'][0];
		$row['report']=$CacheArr['comdata']['job_report'][0];
		$row['age']=$CacheArr['comdata']['job_age'][0];
		$row['sex']=$CacheArr['comdata']['job_sex'][0];
		$row['edu']=$CacheArr['comdata']['job_edu'][0];
		$row['marriage']=$CacheArr['comdata']['job_marriage'][0];
		$this->yunset("company",$company);
		$this->yunset("row",$row);

		$this->yunset("today",date('Y-m-d',time()));
		$this->yunset("js_def",3);
		$this->com_tpl('jobadd');
	}
	function edit_action(){
		if($_GET['id']){
			$id=(int)$_GET['id'];
		}else{
			$id=(int)$_GET['jobcopy'];
		}
		$row=$this->obj->DB_select_once("company_job","`id`='".$id."' and `uid`='".$this->uid."'");
		$job_link=$this->obj->DB_select_once("company_job_link","`jobid`='".$id."' and `uid`='".$this->uid."'");
		if($row['lang']!="")
		{
			$row['lang']= @explode(",",$row['lang']);
		}
		if($row['welfare']!="")
		{
			$row['welfare']= @explode(",",$row['welfare']);
		}
		$company=$this->get_user();
		$row['days']= ceil(($row['edate']-$row['sdate'])/86400);
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$this->public_action();
		$this->yunset("company",$company);
		$this->yunset("job_link",$job_link);
		$this->yunset("row",$row);
		$this->yunset("js_def",3);
		$this->com_tpl('jobadd');
	}
	function save_action(){
		if($_POST['submitBtn']){
			$id=intval($_POST['id']);
			$state= intval($_POST['state']);
			unset($_POST['submitBtn']);
			unset($_POST['id']);
			unset($_POST['state']);
			$_POST['uid']=$this->uid;
			$_POST['lastupdate']=mktime();
			$_POST['state']=$this->config['com_job_status'];
			$_POST['description'] = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'background-color:','background-color:','white-space:'),html_entity_decode($_POST['description'],ENT_QUOTES,"GB2312"));
			if($this->config['com_job_status']=="0"){
				$msg="等待审核！";
			}
			if($_POST['job_post'])
			{
				$row1=$this->obj->DB_select_once("job_class","`id`='".intval($_POST['job_post'])."'","`keyid`");
				$row2=$this->obj->DB_select_once("job_class","`id`='".$row1['keyid']."'","`keyid`");
				$_POST['job1_son']=$row1['keyid'];
				$_POST['job1']=$row2['keyid'];
			}
			if(!empty($_POST['lang']))
			{
				$_POST['lang'] = $this->pylode(",",$_POST['lang']);
			}else{
				$_POST['lang'] = "";
			}
			if(!empty($_POST['welfare']))
			{
				$_POST['welfare'] = $this->pylode(",",$_POST['welfare']);
			}else{
				$_POST['welfare'] = "";
			}
			if(trim($_POST['days'])&&$_POST['days_type']==''){
				$sdate=time()+(int)trim($_POST['days'])*86400;
				$_POST['edate']=date('Y-m-d',$sdate);
				unset($_POST['days']);
			}else if($_POST['days_type']){unset($_POST['days_type']);unset($_POST['days']);}

			if($_POST['edate']){
				$_POST['edate']=strtotime($_POST['edate']);
				if($_POST['edate']<time()){
					$this->obj->ACT_layer_msg("结束时间小于当前日期，提交失败！",8,$_SERVER['HTTP_REFERER']);
				}
			}
			if(!$_POST['xuanshang'])
			{
				$_POST['xuanshang']='0';
			}
			$satic=$this->company_satic();
			$company=$this->get_user();
			$_POST['com_name']=$company['name'];
			$_POST['com_logo']=$company['logo'];
			$_POST['com_provinceid']=$company['provinceid'];
			$_POST['pr']=$company['pr'];
			$_POST['mun']=$company['mun'];
			$_POST['rating']=$satic['rating'];
			$is_link=(int)$_POST['is_link'];
			$link_type=(int)$_POST['link_type'];
			$is_email=(int)$_POST['is_email'];
			$email_type=(int)$_POST['email_type'];
			$link_moblie=$_POST['link_moblie'];
			$email=$_POST['email'];
			$link_man=$_POST['link_man'];
			unset($_POST['is_email']);
			unset($_POST['link_moblie']);
			unset($_POST['email_type']);
			unset($_POST['link_man']);
			unset($_POST['email']);
			$where['id']=$id;
			$where['uid']=$this->uid;
			if(!$id||intval($_POST['jobcopy'])==$id){
				$_POST['sdate']=mktime();
				$this->get_com(1);
				$nid=$this->obj->insert_into("company_job",$_POST);
				$name="添加职位";
				$type='1';
				if($nid)
				{
					$this->obj->DB_update_all("company","`jobtime`='".$_POST['lastupdate']."'","`uid`='".$this->uid."'");
					$state_content = "发布了新职位 <a href=\"".$this->config['sy_weburl']."/index.php?m=com&c=comapply&id=$nid\" target=\"_blank\">".$_POST['name']."</a>。";
					$this->addstate($state_content);
					$this->warning("1");
				}
			}else{
				if($state=="1" || $state=="2")
				{
					$this->get_com(2);
				}
				$rows=$this->obj->DB_select_once("company_job","`id`='".$id."' and `uid`='".$this->uid."'");
				$nid=$this->obj->update_once("company_job",$_POST,$where);
				$name="更新职位";
				$type='2';
				if($nid)
				{
					$this->obj->DB_update_all("company","`jobtime`='".$_POST['lastupdate']."'","`uid`='".$this->uid."'");
				}
			}

			if($is_link=='1'){
				if($link_type=='2'){
					$linkman=trim($link_man);
					$linktel=trim($link_moblie);
				}else{
					$linkman=$company['linkman'];
					$linktel=$company['linktel'];
				}
			}
			if($is_email && $email_type==2){
				$linkmail=trim($email);
			}else{
				$linkmail=$company['linkmail'];

			}
			$job_link="";
			if($is_link=="1" && $link_type==2){
				$job_link.="`link_man`='".$linkman."',";
				$job_link.="`link_moblie`='".$linktel."',";
			}
			$job_link.="`email_type`='".$email_type."',";
			$job_link.="`is_email`='".$is_email."',";
			$job_link.="`email`='".$linkmail."'";
			if($id){
				$linkid=$this->obj->DB_select_once("company_job_link","`uid`='".$this->uid."' and `jobid`='".$id."'","id");
				if($linkid['id']){
					$this->obj->DB_update_all("company_job_link",$job_link,"`id`='".$linkid['id']."'");
				}else{
					$job_link.=",`uid`='".$this->uid."',`jobid`='".(int)$id."'";
					$this->obj->DB_insert_once("company_job_link",$job_link);
				}
			}else if($nid>0){
				$job_link.=",`uid`='".$this->uid."',`jobid`='".(int)$nid."'";
				$this->obj->DB_insert_once("company_job_link",$job_link);
			}
			if($nid && $_POST['xuanshang']){
				$nid=$this->obj->company_invtal($this->uid,$_POST['xuanshang'],false,"发布竟价职位",true,2,'integral',11);
			}
			if($nid)
			{
				$this->obj->member_log($name."《".$_POST['name']."》",1,$type);
				$this->obj->ACT_layer_msg($name."成功！",9,"index.php?c=job&w=1");
			}else{
				$this->obj->ACT_layer_msg($name."失败！",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
}
?>