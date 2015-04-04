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


class Invite {

	var $base;
	var $db;

	function Invite($base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function get($appid, $uid, $num, $start = 0) {
		if ($num == 'all') {
			$num = 500;
		} elseif (!is_numeric($num) || $num < 1) {
			$num = 20;
		} elseif ($num > 500) {
			$num = 500;
		}
		(!is_numeric($start) || $start < 0) && $start = 0;

		$users = array();
		$query = $this->db->query("SELECT friendid FROM pw_friends WHERE status='0' AND uid=" . pwEscape($uid) . pwLimit($start, $num));
		while ($rt = $this->db->fetch_array($query)) {
			$app = $this->db->get_one("SELECT * FROM pw_userapp WHERE uid=".pwEscape($rt['friendid'])." AND appid=".pwEscape($appid));
			if (empty($app)) {
				$users[] = $rt['friendid'];
			}
		}
		return new ApiResponse($users);
	}
}
?>