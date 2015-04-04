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
error_reporting(0);

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
require_once(dirname(dirname(dirname(__FILE__)))."/plus/config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);


$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {
	
	$out_trade_no = $_GET['out_trade_no'];

	
	$trade_no = $_GET['trade_no'];

	
	$result = $_GET['result'];


	
	$out_trade_no = $_GET['out_trade_no'];

	
	$trade_no = $_GET['trade_no'];

	
	$result = $_GET['result'];	
	
    
    $dingdan           = $out_trade_no;    
    $total_fee         = $_GET['total_fee'];	    
    $sOld_trade_status = "0";		    

    
	if(!is_numeric($dingdan)){

		echo "订单号格式不正确";die;
	}
	
    if(($result == 'TRADE_FINISHED') || ($result == 'TRADE_SUCCESS') || ($result == 'success') ) {
	
		$select=$db->query("select  * from `".$db_config[def]."company_order` where `order_id`='$dingdan'");

		$order=mysql_fetch_array($select);

		if($order['order_state']!='2'){ 
			$mselect=$db->query("select  `usertype` from `".$db_config[def]."member` where `uid`='".$order['uid']."'");
			$member=mysql_fetch_array($mselect);
			if($member['usertype']=='1'){
				$table='member_statis';
			}else if($member['usertype']=='2'){
				$table='company_statis'; 
				$tvalue=",`all_pay`=`all_pay`+".$order["order_price"];
			}else if($member['usertype']=='3'){
				$table='lt_statis'; 
				$tvalue=",`all_pay`=`all_pay`+".$order["order_price"];
			}
			if($order['type']=='1'&&$order['rating']&&$member['usertype']!='1'){
				$select_rating=$db->query("select * from `".$db_config[def]."company_rating` where `id`='".$order['rating']."'");
				$row=mysql_fetch_array($select_rating);
				$value="`rating`='".$row['id']."',";
				$value.="`rating_name`='".$row['name']."',";
				$value.="`rating_type`='".$row['type']."',";
				if($row['service_time']>0){
					$viptime=time()+$row['service_time']*86400;
				}else{
					$viptime=0;
				}
				$value.="`vip_etime`='".$viptime."',";
				$value.="`job_num`='".$row['job_num']."',";
				$value.="`down_resume`='".$row['resume']."',";
				$value.="`invite_resume`='".$row['interview']."',";
				$value.="`editjob_num`='".$row['editjob_num']."',";
				$value.="`breakjob_num`='".$row['breakjob_num']."'";
				mysql_query("update `".$db_config[def]."company_statis` set `all_pay`=`all_pay`+".$order["order_price"].",".$value." where `uid`='".$order['uid']."'"); 
			}else if($order['type']=='2'&&$order['integral']){  
				mysql_query("update `".$db_config[def].$table."` set `integral`=`integral`+'".$order['integral']."'".$tvalue." where `uid`='".$order["uid"]."'");
			}else if($order['type']=='3'||$order['type']=='4'){
				mysql_query("update `".$db_config[def].$table."` set `pay`=`pay`+'".$order["order_price"]."'".$tvalue." where `uid`='".$order["uid"]."'");
			}else if($order['type']=='5'&&$order['integral']&&$usertype['usertype']=='2'){ 
				mysql_query("update `".$db_config[def]."company_statis` set `all_pay`=`all_pay`+'".$order["order_price"]."',`msg_num`=`msg_num`+'".$order['integral']."' where `uid`='".$order["uid"]."'");
			}
			mysql_query("update `".$db_config[def]."company_order` set `order_state`='2' where `id`='".$order['id']."'");
		}
		
        

        if ($sOld_trade_status < 1) {
            

        }
    }
    else {
		echo '<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
        <title>支付宝即时到账交易接口</title>
	</head>
    <body>'."trade_status=".$_GET['trade_status'].'</body></html>';die;
    }
	
	
}
else {
   
	echo "验证失败";die;
}
if(!($config['sy_wapdomain'])){
	$wapdomain=$config['sy_weburl'].'/'.$config['sy_wapdir'];
}else{
	$wapdomain=$config['sy_wapdomain'];
}
$Loaction=$wapdomain."/member/index.php?c=pay";
header("Location:".$Loaction);
?>