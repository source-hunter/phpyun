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