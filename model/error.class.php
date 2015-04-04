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
class error_controller extends common{
	function index_action()
	{
		$this->yunset("title",$this->config['sy_webname']." - 模块关闭 - Powered by PHPYun.");
		$this->yunset("keywords",$this->config['sy_webname']." - 模块关闭");
		$this->yunset("description",$this->config['sy_webname']." - 模块关闭");
		$this->yun_tpl(array('index'));
	}
}
?>