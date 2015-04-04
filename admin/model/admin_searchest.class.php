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
class admin_searchest_controller extends common
{
	function index_action()
	{
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		$this->yuntpl(array('admin/admin_searchest_job'));
	}
	

	function company_action()
	{
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		$this->yuntpl(array('admin/admin_searchest_company'));
	}
	function resume_action()
	{
		if($_POST['update']){

			if($_POST['hy']){
				$wheres .= " AND b.`hy` = '".$_POST['hy']."' ";
			}
			
			if($_POST['job1_son']||$_POST['job_post']){
				$wheres .= " AND (FIND_IN_SET('".$_POST['job1_son']."',b.`job_classid`) or FIND_IN_SET('".$_POST['job1_son']."',b.`job_classid`))  ";
			}
			if($_POST['provinceid']){
				$wheres .= " AND b.`provinceid` = '".$_POST['provinceid']."' ";
			}
			if($_POST['cityid']){
				$wheres .= " AND b.`cityid` = '".$_POST['cityid']."' ";
			}
			if($_POST['three_cityid']){
				$wheres .= " AND b.`three_cityid` = '".$_POST['three_cityid']."' ";
			}
			if($_POST['salary']){
				$wheres .= " AND b.`salary` = '".$_POST['salary']."' ";
			}
			if($_POST['type']){
				$wheres .= " AND b.`type` = '".$_POST['type']."' ";
			}
			if($_POST['exp']){
				$wheres .= " AND a.`exp` = '".$_POST['exp']."' ";
			}
			if($_POST['report']){
				$wheres .= " AND b.`report` = '".$_POST['report']."' ";
			}
			if($_POST['sex']){
				$wheres .= " AND a.`sex` = '".$_POST['sex']."' ";
			}
			if($_POST['edu']){
				$wheres .= " AND a.`edu` = '".$_POST['edu']."' ";
			}
			if($_POST['marriage']){
				$wheres .= " AND a.`marriage` = '".$_POST['marriage']."' ";
			}
			if($_POST['keywords']){
				$wheres .= " AND b.`name` like '%".$_POST['keywords']."%' ";
			}
			$userrows=$this->obj->DB_select_alls("resume","resume_expect","a.`uid`=b.`uid` $wheres","b.id");
			if(!empty($userrows)){
				foreach($userrows as $v){
				$userid[] = $v['id'];
				}
				$search_uid = implode(",",$userid);
			}else{
				$search_uid = "0";
			}
			echo "<script>location.href='index.php?m=admin_resume&searchid=".$search_uid."';</script>";
		}
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		$this->yuntpl(array('admin/admin_searchest_resume'));
	}
}