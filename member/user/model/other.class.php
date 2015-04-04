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
class other_controller extends user{
	function index_action()
	{
		$this->resume("resume_other","other","resume","返回简历管理");
		$this->public_action();
		$this->user_tpl('other');
	}

}
?>