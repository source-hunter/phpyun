<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
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