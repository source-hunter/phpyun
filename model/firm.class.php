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
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		$this->seo("firm");
		if(is_array($_SESSION["history"]))
		{
			$this->yunset("history",$_SESSION["history"]);
		}
		$this->yunset("gettype",$_SERVER["QUERY_STRING"]);
		$this->yunset("getinfo",$_GET);
		$this->yun_tpl(array('index'));
	}
}
?>