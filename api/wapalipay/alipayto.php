<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	<title>֧������ʱ���˽��׽ӿڽӿ�</title>
</head>
<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
error_reporting(0);

require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.safety.php");
require_once(dirname(dirname(dirname(__FILE__)))."/plus/config.php");

require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);
if(!is_numeric($_GET['dingdan'])){die;}
 
$_COOKIE['uid']=(int)$_COOKIE['uid'];
$_GET['is_invoice']=(int)$_GET['is_invoice'];
$_GET['balance']=(int)$_GET['balance']; 
$member_sql=$db->query("SELECT * FROM `".$db_config["def"]."member` WHERE `uid`='".$_COOKIE['uid']."' limit 1");
$member=mysql_fetch_array($member_sql);  
if($member['username'] != $_COOKIE['username'] || $member['usertype'] != $_COOKIE['usertype']||md5($member['username'].$member['password'].$member['salt'])!=$_COOKIE['shell']){  
	echo '��¼��Ϣ��֤���������µ�¼��';die;
}
$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$_GET['dingdan']."' AND `order_price`>=0");
$row=mysql_fetch_array($sql);
if((!$row['uid']) || ($row['uid']!=$_COOKIE['uid']))
{
	die;
}
require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");



$format = "xml";

$v = "2.0";

$req_id = date('Ymdhis');

$notify_url = $config['sy_weburl']."/api/wapalipay/notify_url.php";

$call_back_url = $config['sy_weburl']."/api/wapalipay/call_back_url.php";

$merchant_url = $config['sy_weburl']."/api/wapalipay/interrupt_back_url.php";

$seller_email = $alipaydata['sy_alipayemail'];

$out_trade_no = $_GET['dingdan'];

$subject = $_GET['dingdan'];

$total_fee = $_GET['alimoney'];

$req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';

$para_token = array(
		"service" => "alipay.wap.trade.create.direct",
		"partner" => trim($alipay_config['partner']),
		"sec_id" => trim($alipay_config['sign_type']),
		"format"	=> $format,
		"v"	=> $v,
		"req_id"	=> $req_id,
		"req_data"	=> $req_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);



$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestHttp($para_token);


$html_text = urldecode($html_text);


$para_html_text = $alipaySubmit->parseResponse($html_text);


$request_token = $para_html_text['request_token'];



$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';

$parameter = array(
		"service" => "alipay.wap.auth.authAndExecute",
		"partner" => trim($alipay_config['partner']),
		"sec_id" => trim($alipay_config['sign_type']),
		"format"	=> $format,
		"v"	=> $v,
		"req_id"	=> $req_id,
		"req_data"	=> $req_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);


$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', 'ȷ��');
echo $html_text;
?>
</body>
</html>