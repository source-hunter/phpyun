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
class call_controller extends common{
	function index_action(){
		include LIB_PATH."datacall.class.php";
		$call= new datacall("plus/data/",$this->obj);
		$row=$call->get_data($_GET[id]);
		$row=str_replace("\n","",$row);
		$row=str_replace("\r","",$row);
		$row=str_replace("'","\'",$row);
		echo "document.write('$row');";
	}
}

?>