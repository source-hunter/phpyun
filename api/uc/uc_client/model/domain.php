<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/

!defined('IN_UC') && exit('Access Denied');

class domainmodel {

	var $db;
	var $base;

	function __construct(&$base) {
		$this->domainmodel($base);
	}

	function domainmodel(&$base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function add_domain($domain, $ip) {
		if($domain) {
			$this->db->query("INSERT INTO ".UC_DBTABLEPRE."domains SET domain='$domain', ip='$ip'");
		}
		return $this->db->insert_id();
	}

	function get_total_num() {
		$data = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."domains");
		return $data;
	}

	function get_list($page, $ppp, $totalnum) {
		$start = $this->base->page_get_start($page, $ppp, $totalnum);
		$data = $this->db->fetch_all("SELECT * FROM ".UC_DBTABLEPRE."domains LIMIT $start, $ppp");
		return $data;
	}

	function delete_domain($arr) {
		$domainids = $this->base->implode($arr);
		$this->db->query("DELETE FROM ".UC_DBTABLEPRE."domains WHERE id IN ($domainids)");
		return $this->db->affected_rows();
	}

	function update_domain($domain, $ip, $id) {
		$this->db->query("UPDATE ".UC_DBTABLEPRE."domains SET domain='$domain', ip='$ip' WHERE id='$id'");
		return $this->db->affected_rows();
	}
}

?>