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
!defined('P_W') && exit('Forbidden');


class Site {

	var $base;
	var $db;

	function Site($base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function connect() {
		return new ApiResponse(1);
	}
}
?>