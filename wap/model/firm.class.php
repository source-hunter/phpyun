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
		$this->yunset("title","��˾����");
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
		$this->yunset("title","��˾����");
		$this->yuntpl(array('wap/firm_show'));
	}

}
?>