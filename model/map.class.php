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
class map_controller extends common
{
	function index_action()
	{
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		if($_GET[r]<500){
			$zoom=15;
		}elseif($_GET[r]>=500 && $_GET[r]<5000){
			$zoom=13;
		}else{
			$zoom=11;
		}
		$this->yunset("zoom",$zoom);
		$this->yunset("getinfo",$_GET);
		$this->seo("map");
		$this->yun_tpl(array('index'));
	}
}
?>