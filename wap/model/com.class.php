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
class com_controller extends common{
	function index_action(){
		$this->get_moblie();
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		if($_GET['job_classid']){
			$job_classid=@explode(',',$_GET['job_classid']);
			foreach($job_classid as $val){
				$jobname[]=$CacheArr['job_name'][$val];
			}
			$jobname=@implode(',',$jobname);
			$this->yunset("jobname",$jobname);
		}
		if($_GET['three_cityid']){
			$this->yunset("cityname",$CacheArr['city_name'][$_GET['three_cityid']]);
		}
		$this->yunset("title","职位搜索");
		$this->yuntpl(array('wap/com'));
	}
	function search_action()
	{
		$this->get_moblie();
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$this->yunset("title","职位搜索");
		$this->yuntpl(array('wap/comsearch'));
	}
	function view_action()
	{
		$this->get_moblie();
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$job=$this->obj->DB_select_once("company_job","`id`='".(int)$_GET['id']."'");
		$company=$this->obj->DB_select_once("company","`uid`='".$job['uid']."'");
		$this->yunset("job",$job);
		$this->yunset("company",$company);
		$this->obj->DB_update_all("company_job","`jobhits`=`jobhits`+1","`id`='".(int)$_GET['id']."'");

		if($_COOKIE['usertype']=="1"){
			$look_job=$this->obj->DB_select_once("look_job","`uid`='".$this->uid."' and `jobid`='".(int)$_GET['id']."'");
			if(!empty($look_job)){
				$this->obj->DB_update_all("look_job","`datetime`='".time()."'","`uid`='".$this->uid."' and `jobid`='".(int)$_GET['id']."'");
			}else{
				$value.="`uid`='".$this->uid."',";
				$value.="`jobid`='".(int)$_GET['id']."',";
				$value.="`com_id`='".$job['uid']."',";
				$value.="`datetime`='".time()."'";
				$this->obj->DB_insert_once("look_job",$value);
			}
		}
		if($_GET['type']){
			if(!$this->uid || !$this->username || $_COOKIE['usertype']!=1){
				$data['msg']='您还没有登录，请先登录！';
			}else {
				if($_GET['type']=='sq'){
					$row=$this->obj->DB_select_num("userid_job","`uid`='".$this->uid."' and `job_id`='".$_GET['id']."'");
					$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
					if(empty($resume)){
						$data['msg']='您还没有简历，请先添加简历！';
						$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
						echo json_encode($data);
						die;
					}else if(intval($row)>0){
						$data['msg']='您已经申请过该职位，请不要重复申请！';
						$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
						echo json_encode($data);
						die;
					}else{
						$info=$this->obj->DB_select_once("company_job","`id`='".$_GET['id']."'");
						$value['job_id']=$_GET['id'];
						$value['com_name']=$info['com_name'];
						$value['job_name']=$info['name'];
						$value['com_id']=$info['uid'];
						$value['uid']=$this->uid;
						$value['eid']=$resume['def_job'];
						$value['datetime']=mktime();
						$nid=$this->obj->insert_into("userid_job",$value);
						if($nid){
							$this->obj->DB_update_all('company_statis',"`sq_job`=`sq_job`+1","`uid`='".$value['com_id']."'");
							$this->obj->DB_update_all('member_statis',"`sq_jobnum`=`sq_jobnum`+1","`uid`='".$value['uid']."'");
							$data['msg']='申请成功！';
							$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
							echo json_encode($data);
							die;
						}else{
							$data['msg']='申请失败！';
							$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
							echo json_encode($data);
							die;
						}
					}
				}else if($_GET[type]=='fav'){
					$rows=$this->obj->DB_select_all("fav_job","`uid`='".$this->uid."' and `job_id`='".$_GET['id']."'");
					if(is_array($rows)&&!empty($rows)){
						$data['msg']='您已经收藏过该职位，请不要重复收藏！';
							$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
							echo json_encode($data);
							die;
					}
					$job=$this->obj->DB_select_once("company_job","`id`='".$_GET['id']."'");
					$data['job_id'] = $job['id'];
					$data['com_name'] = $job['com_name'];
					$data['job_name'] = $job['name'];
					$data['com_id'] = $job['uid'];
					$data['uid'] = $this->uid;
					$data['datetime'] = time();
					$nid=$this->obj->insert_into('fav_job',$data);
					if($nid){
						$this->obj->DB_update_all('member_statis',"`fav_jobnum`=`fav_jobnum`+1","`uid`='".$this->uid."'");
						$data['msg']='收藏成功！';
							$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
							echo json_encode($data);
							die;
					}else{
						$data['msg']='收藏失败！';
							$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
							echo json_encode($data);
							die;
					}
				}
			}
			$data['url']='index.php?m=com';
			$this->yunset("layer",$data);
		}
		$this->yunset("title","职位详情");
		$this->yuntpl(array('wap/com_show'));
	}
}
?>