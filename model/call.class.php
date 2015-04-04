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
class call_controller extends common{
	function index_action(){
		include LIB_PATH."datacall.class.php";
		$call= new datacall("plus/data/",$this->obj);
		$row=$call->get_data($_GET[id]);
		$row=str_replace("\n","",$row);
		$row=str_replace("\r","",$row);
		$row=str_replace("'","\'",$row);
		echo "document.write('$row');";
	}
}

?>