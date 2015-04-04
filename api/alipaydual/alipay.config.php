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
 include_once ("alipay_data.php");

$aliapy_config['partner']      = $alipaydata[sy_alipayid];


$aliapy_config['key']          = $alipaydata[sy_alipaycode];


$aliapy_config['seller_email'] = $alipaydata[sy_alipayemail];


$aliapy_config['return_url']   = $alipaydata[sy_weburl]."/api/alipaydual/return_url.php";


$aliapy_config['notify_url']   = $alipaydata[sy_weburl]."/api/alipaydual/notify_url.php";





$aliapy_config['sign_type']    = 'MD5';


$aliapy_config['input_charset']= 'gbk';


$aliapy_config['transport']    = 'http';
$aliapy_config['receive_name']    =  $alipaydata[sy_alipaynames];
$aliapy_config['receive_address']    =  $alipaydata[receive_address];
$aliapy_config['receive_phone']    =  $alipaydata[receive_phone];

$aliapy_config['showurl']    =  $alipaydata[sy_weburl];

?>