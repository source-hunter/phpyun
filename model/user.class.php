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
	function usersearch()
	{
		if($_SESSION['cityid'])
		{
			$_GET['cityid'] = $_SESSION['cityid'];
		}
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$Array = $this->CacheInclude($CacheArr);
		if($_GET['uptime']){
			if($_GET['uptime']=="1"){
				$uptimename="今天";
			}elseif($_GET['uptime']=="3"){
				$uptimename="最近3天";
			}elseif($_GET['uptime']=="7"){
				$uptimename="最近7天";
			}elseif($_GET['uptime']=="30"){
				$uptimename="最近一个月";
			}elseif($_GET['uptime']=="90"){
				$uptimename="最近三个月";
			}
			$finder['uptime']=$_GET['uptime'];
			$this->yunset("uptimename",$uptimename);
		}

		$list=array();
		if($_GET["job1_son"]){
			$list[]=$_GET["job1_son"];
			$finder['job1_son']=$_GET['job1_son'];
		}
		if($_GET["job_post"]){
			$list[]=$_GET["job_post"];
			$finder['job_post']=$_GET['job_post'];
		}
		if($_GET["jobids"]){
			$list[]=$_GET["jobids"];
			$finder['jobids']=$_GET['jobids'];
		}
		$_GET["jobids"]=implode(",",$list);
		
		$jobids=explode(",",$_GET['jobids']);
		foreach($jobids as $key=>$val){
			if($Array['job_name'][$val]){
				$jobnames.="、".$Array['job_name'][$val];
			}
		}

		$seatchcontition=array();
		if($_GET['keyword']){$finder['keyword']=$_GET['keyword'];}
		if($_GET['pic']){$finder['pic']=$_GET['pic'];}
		if($_GET['hy']){$finder['hy']=$_GET['hy'];}
		if(!empty($jobnames)){$searchcondition[]=substr($jobnames,2);}
		if($_GET['exp']){$finder['exp']=$_GET['exp'];}
		if($_GET['type']){$finder['type']=$_GET['type'];}
		if($_GET['edu']){$finder['edu']=$_GET['edu'];}
		if($_GET['report']){$finder['report']=$_GET['report'];}
		if($_GET['sex']){$finder['sex']=$_GET['sex'];}
		if($_GET['salary']){$finder['salary']=$_GET['salary'];}
		if($_GET['adtime']){$finder['adtime']=$_GET['adtime'];}
		if($_GET['word']){$finder['word']=$_GET['word'];}
		if($_GET['cityid']){
			$searchcondition[]=$Array['city_name'][$_GET['cityid']];
			$finder['cityid']=$_GET['cityid'];
		}
		if($uptimename){
			$searchcondition[]=$uptimename;
		}
		if($finder&&is_array($finder)){
			foreach($finder as $key=>$val){
				$para[]=$key."=".$val;
			}
			$paras=@implode('##',$para);
			$this->yunset("finder",$finder);
			$this->yunset("paras",$paras);
		}
		$uptime=array('1'=>'今天',"3"=>'最近三天','7'=>'最近七天','30'=>'最近一个月',"90"=>'最近三个月');
		$adtime=array('1'=>'一天内',"3"=>'三天内','7'=>'七天内',"15"=>'十五天内','30'=>'一个月内',"60"=>'两个月内');
		$this->yunset("jobnames",substr($jobnames,2));
		$this->yunset("adtime",$adtime);
		$this->yunset("uptime",$uptime);
		$this->yunset("gettype",$_SERVER["QUERY_STRING"]);
		if($_GET['order']=="")
		{
			$_GET['order']="topdate";
		}
		$this->yunset("getinfo",$_GET);
		$this->seo("user_search");
		$this->yun_tpl(array('search'));
	}
	function search_action(){
		$this->usersearch();
	}
	function index_action()
	{
		if($this->config['sy_default_userclass']=='1'){
			$CacheArr['job'] =array('job_index','job_type','job_name');
			$CacheArr['city'] =array('city_index','city_type','city_name');
			$CacheArr['industry'] =array('industry_index','industry_name');
			$Array = $this->CacheInclude($CacheArr);
			$this->yunset("gettype",$_SERVER["QUERY_STRING"]);
			$this->yunset("getinfo",$_GET);
			$this->seo("user");
			$this->yun_tpl(array('index'));
		}else{
			$this->usersearch();
		}
	}
	function saveshow_action()
	{
		if (!empty($_FILES))
		{
			$pic=$name='';
			$data=array();
			$tempFile = $_FILES['Filedata'];
			$upload=$this->upload_pic("./upload/show/");
			$pic=$upload->picture($tempFile);
			$name=@explode('.',$_FILES['Filedata']['name']);
			$picurl=str_replace("../upload/show","./upload/show",$pic);
			$data['picurl']= $picurl;
			$data['title']=$this->stringfilter($name[0]);
			$data['ctime']=time();
			$data['uid']=(int)$_POST['uid'];
			$data['eid']=(int)$_GET['eid'];
			$id=$this->obj->insert_into("resume_show",$data);
			if($id){
 				echo $name[0]."||".$picurl."||".$id;die;
			}else{
				echo "2";die;
			}
		}
	}
}
?>