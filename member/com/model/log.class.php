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
class log_controller extends company
{
	function index_action()
	{
		$urlarr=array("c"=>"log","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("member_log","`uid`='".$this->uid."' order by id desc",$pageurl,"15");
 		$this->public_action();
		$this->yunset("js_def",6);
		$this->com_tpl('log');
	}
}
?>