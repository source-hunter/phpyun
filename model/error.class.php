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
class error_controller extends common{
	function index_action()
	{
		$this->yunset("title",$this->config['sy_webname']." - ģ��ر� - Powered by PHPYun.");
		$this->yunset("keywords",$this->config['sy_webname']." - ģ��ر�");
		$this->yunset("description",$this->config['sy_webname']." - ģ��ر�");
		$this->yun_tpl(array('index'));
	}
}
?>