<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class other_controller extends user{
	function index_action()
	{
		$this->resume("resume_other","other","resume","���ؼ�������");
		$this->public_action();
		$this->user_tpl('other');
	}

}
?>