<?php
class webshell_controller extends common {
	
	function index_action() {
		
		require_once APP_PATH.'/webscan360/webscan360.class.php';
		$webscan_model = new webscan360();
		$url = $webscan_model->getWebshellUrl();
		$this->yunset("url", $url);
		$this->yuntpl(array('admin/admin_webshell'));
	}
	
}

?>