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

!defined('IN_UC') && exit('Access Denied');

class appcontrol extends base {

	function __construct() {
		$this->appcontrol();
	}

	function appcontrol() {
		parent::__construct();
		$this->load('app');
	}

	function onls() {
		$this->init_input();
		$applist = $_ENV['app']->get_apps('appid, type, name, url, tagtemplates, viewprourl, synlogin');
		$applist2 = array();
		foreach($applist as $key => $app) {
			$app['tagtemplates'] = $this->unserialize($app['tagtemplates']);
			$applist2[$app['appid']] = $app;
		}
		return $applist2;
	}

	function onadd() {
	}

	function onucinfo() {
	}

	function _random($length, $numeric = 0) {
	}

	function _generate_key() {
	}

	function _format_notedata($notedata) {
	}
}

?>