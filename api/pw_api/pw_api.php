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
error_reporting(0);
define('P_W','admincp');
define('S_DIR',dirname(__FILE__)."/");
define('R_P',S_DIR.'/');
define('D_P',R_P);
define('PHPYUN',dirname(dirname(dirname(__FILE__))));
require_once(S_DIR.'pw_config.php');
require_once(S_DIR.'security.php');
require_once(S_DIR.'pw_common.php');
require_once(S_DIR.'class_base.php');

$api = new api_client();
$response = $api->run($_POST + $_GET);
if($response) {
	echo $api->dataFormat($response);
}

?>