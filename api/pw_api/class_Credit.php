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


class Credit {

	var $base;
	var $db;

	function Credit($base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function get() {
		 return new ApiResponse(pwCreditNames());
	}

	function syncredit($arr) {
		global $config,$mysqli;
		if (is_array($arr)) {
			foreach ($arr as $uid => $setv) {
				$updateMemberData = array();
				foreach ($setv as $cid => $value) {
					 $value = intval($value);
					 $mysqli->query("UPDATE ".wk('works')." SET userpointsnum='$value' WHERE uid='$uid'");
				}

			}
		}
		return new ApiResponse(1);
	}

	function getvalue($uid) {
		require_once(R_P.'require/credit.php');
		$getv = $credit->get($uid);
		$retv = array();
		foreach ($credit->cType as $key => $value) {
			$retv[$key] = array('title' => $value, 'value' => isset($getv[$key]) ? $getv[$key] : 0);
		}
		return new ApiResponse($retv);
	}
}
?>