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
require_once ("classes/PayRequestHandler.class.php");
require_once ("tenpay_data.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.safety.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
require_once(dirname(dirname(dirname(__FILE__)))."/plus/config.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);
if(!is_numeric($_POST['dingdan']))
{
	die;
}
$_COOKIE['uid']=(int)$_COOKIE['uid'];
$_POST['is_invoice']=(int)$_POST['is_invoice'];
$_POST['balance']=(int)$_POST['balance']; 
$member_sql=$db->query("SELECT * FROM `".$db_config["def"]."member` WHERE `uid`='".$_COOKIE['uid']."' limit 1");
$member=mysql_fetch_array($member_sql);  
if($member['username'] != $_COOKIE['username'] || $member['usertype'] != $_COOKIE['usertype']||md5($member['username'].$member['password'].$member['salt'])!=$_COOKIE['shell']){  
	echo '登录信息验证错误，请重新登录！';die;
}  
$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='$_POST[dingdan]' AND `order_price`>=0");
$row=mysql_fetch_array($sql);
if(!$row['uid'] || $row['uid']!=$_COOKIE['uid'])
{
	die;
}
if((int)$_POST['is_invoice']=='1'&&$config["sy_com_invoice"]){
	$invoice_title=",`is_invoice`='".$_POST['is_invoice']."'";
	if($_POST['linkway']=='1'){
		$com_sql=$db->query("select `linkman`,`linktel`,`address` from `".$db_config["def"]."company` where `uid`='".$_COOKIE['uid']."'");
		$company=mysql_fetch_array($com_sql);   
		$link=",'".$company['linkman']."','".$company['linktel']."','".$company['address']."'";
		$up_record=",`link_man`='".$company['linkman']."',`link_moblie`='".$company['linktel']."',`address`='".$company['address']."'";
	}else{  
		$link=",'".$_POST['link_man']."','".$_POST['link_moblie']."','".$_POST['address']."'"; 
		$up_record=",`link_man`='".$_POST['link_man']."',`link_moblie`='".$_POST['link_moblie']."',`address`='".$_POST['address']."'";
	}
	$record_sql=$db->query("select `id` from `".$db_config["def"]."invoice_record` where `order_id`='".$_POST['dingdan']."' and `uid`='".$_COOKIE['uid']."'");
	$record=mysql_fetch_array($record_sql);  
	if($record['id']){
		$upr_sql=$db->query("update `".$db_config["def"]."invoice_record` set `title`='".trim($_POST['invoice_title'])."',`status`='0',`addtime`='".time()."'".$up_record." where `id`='".$record['id']."'");
		mysql_fetch_array($upr_sql);
	}else{
		$db->query("insert into `".$db_config["def"]."invoice_record`(order_id,uid,title,status,addtime,`link_man`,`link_moblie`,`address`) values('".$row['order_id']."','".$_COOKIE['uid']."','".trim($_POST['invoice_title'])."','0','".time()."'".$link.")");
	} 
}
if((int)$_POST['balance']&&$_COOKIE['uid']){
	$c_sql=$db->query("select `pay` from `".$db_config["def"]."company_statis` where `uid`='".$_COOKIE['uid']."'");
	$company_statis=mysql_fetch_array($c_sql); 
	if($company_statis['pay']>=$row['order_price']){
		$up_sql=$db->query("update `".$db_config["def"]."company_statis` set `pay`=`pay`-'".$row['order_price']."' where `uid`='".$_COOKIE['uid']."'");
		mysql_fetch_array($up_sql);
		$up_order=$db->query("update `".$db_config["def"]."company_order` set `order_price`='0'".$invoice_title." where `order_id`='".$row['order_id']."'");
		mysql_fetch_array($up_order);
		$price=$row['order_price'];
	}else{ 
		$price=$company_statis['pay'];
		$up_sql=$db->query("update `".$db_config["def"]."company_statis` set `pay`='0' where `uid`='".$_COOKIE['uid']."'");
		$up_sql_status=mysql_fetch_array($up_sql);
		$up_order=$db->query("update `".$db_config["def"]."company_order` set `order_price`=`order_price`-'".$price."'".$invoice_title." where `order_id`='".$row['order_id']."'");
		mysql_fetch_array($up_order);
	} 
	$insert_company_pay=$db->query("insert into `".$db_config["def"]."company_pay`(order_id,order_price,pay_time,pay_state,com_id,pay_remark,type) values('".$row['order_id']."','-".$price."','".time()."','2','".$_COOKIE['uid']."','".$row['order_remark']."','2')");
	mysql_fetch_array($insert_company_pay);
	$new_sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$row['order_id']."'");
	$row=mysql_fetch_array($new_sql); 
}else if($invoice_title){
	$up_order=$db->query("update `".$db_config["def"]."company_order` set `is_invoice`='".$_POST['is_invoice']."' where `order_id`='".$row['order_id']."'");
	mysql_fetch_array($up_order);
}

$bargainor_id = $tenpaydata[sy_tenpayid];


$key = $tenpaydata[sy_tenpaycode];


$return_url = $tenpaydata[sy_weburl]."/api/tenpay/return_url.php";


$strDate = date("Ymd");
$strTime = date("His");


$randNum = rand(1000, 9999);

$attach=$_POST[pay_type];


$strReq = $strTime . $randNum;


$sp_billno = $_POST[dingdan];


$transaction_id =trim($bargainor_id.$strDate.$strReq);


$total_fee = $row[order_price]*100;



$desc = "订单号：" . $transaction_id;


$reqHandler = new PayRequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);

$reqHandler->setParameter("bargainor_id", $bargainor_id);			
$reqHandler->setParameter("transaction_id", $transaction_id);		
$reqHandler->setParameter("sp_billno", $sp_billno);					
$reqHandler->setParameter("total_fee", $total_fee);					
$reqHandler->setParameter("return_url", $return_url);				
$reqHandler->setParameter("desc", "订单号：" . $transaction_id);	   
$reqHandler->setParameter("attach", $attach);			        	






$reqUrl = $reqHandler->getRequestURL();



Header("Location:$reqUrl");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gbk">
	<title>财付通即时到帐程序</title>
</head>
<body>
<script>//location.href='<?php echo $reqUrl;?>';</script>
</body>
</html>
