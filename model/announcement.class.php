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
class announcement_controller extends common
{
	function index_action(){
		$_GET[id]=(int)$_GET[id];
		$this->seo("announcement");
		$this->yun_tpl(array('show'));
	}
}
?>