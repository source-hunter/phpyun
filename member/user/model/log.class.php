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
class log_controller extends user{
	function index_action()
	{
		$this->public_action();
		$urlarr=array("c"=>"log","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("member_log","`uid`='".$this->uid."' order by id desc",$pageurl,"20");
		$this->user_tpl('log');
	}

}
?>