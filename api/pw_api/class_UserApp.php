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


class UserApp {

	var $base;
	var $db;

	function UserApp($base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function isInstall($uid) {
		$appid = array();
		$query = $this->db->query("SELECT appid FROM pw_userapp WHERE uid=" . pwEscape($uid));
		while ($rt = $this->db->fetch_array($query)) {
			$appid[] = $rt['appid'];
		}
		return new ApiResponse($appid);
	}

	function add($uid, $appid, $appname, $allowfeed ,$descrip) {
		global $timestamp;

		$this->db->update("REPLACE INTO pw_userapp SET " . pwSqlSingle(array(
			'uid'		=> $uid,
			'appid'		=> $appid,
			'appname'	=> $appname,
			'allowfeed'	=> $allowfeed
		)));

		if ($allowfeed) {
			$descrip = Char_cv($descrip);
			$this->db->update("INSERT INTO pw_feed SET " . pwSqlSingle(array(
				'uid'		=> $uid,
				'type'		=> 'app',
				'descrip'	=> $descrip,
				'timestamp'	=> $timestamp
			),false));
		}

		return new ApiResponse(true);
	}

	function appsUpdateCache($apps) {
		if ($apps && is_array($apps)) {

			require_once(R_P.'admin/cache.php');
			setConfig('db_apps_list',$apps);
			updatecache_c();
			return new ApiResponse(true);
		} else {
			return new ApiResponse(false);
		}
	}
}
?>