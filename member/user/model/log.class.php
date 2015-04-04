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
class log_controller extends user{
	function index_action()
	{
		$this->public_action();
		$urlarr=array("c"=>"log","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("member_log","`uid`='".$this->uid."' order by id desc",$pageurl,"20");
		$this->user_tpl('log');
	}

}
?>