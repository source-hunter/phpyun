<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
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
		$this->yunset("title","ְλ����");
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
		$this->yunset("title","ְλ����");
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
				$data['msg']='����û�е�¼�����ȵ�¼��';
			}else {
				if($_GET['type']=='sq'){
					$row=$this->obj->DB_select_num("userid_job","`uid`='".$this->uid."' and `job_id`='".$_GET['id']."'");
					$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
					if(empty($resume)){
						$data['msg']='����û�м�����������Ӽ�����';
						$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
						echo json_encode($data);
						die;
					}else if(intval($row)>0){
						$data['msg']='���Ѿ��������ְλ���벻Ҫ�ظ����룡';
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
							$data['msg']='����ɹ���';
							$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
							echo json_encode($data);
							die;
						}else{
							$data['msg']='����ʧ�ܣ�';
							$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
							echo json_encode($data);
							die;
						}
					}
				}else if($_GET[type]=='fav'){
					$rows=$this->obj->DB_select_all("fav_job","`uid`='".$this->uid."' and `job_id`='".$_GET['id']."'");
					if(is_array($rows)&&!empty($rows)){
						$data['msg']='���Ѿ��ղع���ְλ���벻Ҫ�ظ��ղأ�';
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
						$data['msg']='�ղسɹ���';
							$data['url']='index.php?m=com';
							$data['msg']=iconv("GBK","UTF-8",$data['msg']);
							echo json_encode($data);
							die;
					}else{
						$data['msg']='�ղ�ʧ�ܣ�';
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
		$this->yunset("title","ְλ����");
		$this->yuntpl(array('wap/com_show'));
	}
}
?>