<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
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