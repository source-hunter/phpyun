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
class firm_controller extends common
{
	function index_action()
	{
		$this->get_moblie(); 
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		if($_GET['three_cityid']){ 
			$this->yunset("cityname",$CacheArr['city_name'][$_GET['three_cityid']]);
		}
		$this->yunset("title","公司搜索");
		$this->yuntpl(array('wap/firm'));
	}
	function show_action()
	{
		$this->get_moblie(); 
		$CacheArr['job'] =array('job_index','job_type','job_name'); 
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$row=$this->obj->DB_select_once("company","uid='".$_GET['id']."'");
		$row['lastupdate']=date("Y-m-d",$row['lastupdate']);
		$this->yunset("row",$row);
		$this->yunset("title","公司详情");
		$this->yuntpl(array('wap/firm_show'));
	}

}
?>