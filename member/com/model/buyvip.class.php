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
class buyvip_controller extends company
{
	function index_action()
	{
		$this->public_action();
		$this->company_satic();
		$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_GET['vipid']."' and display='1'");
		$this->yunset("row",$row);
		$this->yunset("js_def",4);
		if(intval($_GET['vipid'])==0)
		{
			$this->com_tpl('buypl');
		}else{
			$this->com_tpl('buyvip');
		}
	}
}
?>