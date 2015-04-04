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
require_once ("./classes/PayResponseHandler.class.php");
require_once ("tenpay_data.php");


require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);


$key =$tenpay[sy_tenpaycode];


$resHandler = new PayResponseHandler();
$resHandler->setKey($key);


if($resHandler->isTenpaySign()) {

	
	$transaction_id = $resHandler->getParameter("transaction_id");

	
	$sp_billno = $resHandler->getParameter("sp_billno");

	
	$total_fee = $resHandler->getParameter("total_fee");

	
	$pay_result = $resHandler->getParameter("pay_result");
	
	$attach = $resHandler->getParameter("attach");

	if( "0" == $pay_result ) {

		
	if(!is_numeric($sp_billno))
	{
		die;
	}
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
			mysql_query("update `".$db_config[def]."company_statis` set `all_pay`=`all_pay`+".$order["order_price"].",".$value." where `uid`='".$order["uid"]."'");
	
		}else if($order['type']=='2'&&$order['integral']){  
			mysql_query("update `".$db_config[def].$table."` set `integral`=`integral`+'".$order['integral']."'".$tvalue." where `uid`='".$order["uid"]."'");
		}else if($order['type']=='3'||$order['type']=='4'){
			mysql_query("update `".$db_config[def].$table."` set `pay`=`pay`+'".$order["order_price"]."'".$tvalue." where `uid`='".$order["uid"]."'");
		}else if($order['type']=='5'&&$order['integral']&&$usertype['usertype']=='2'){ 
			mysql_query("update `".$db_config[def]."company_statis` set `all_pay`=`all_pay`+'".$order["order_price"]."',`msg_num`=`msg_num`+'".$order['integral']."' where `uid`='".$order["uid"]."'");
		}
		mysql_query("update `".$db_config[def]."company_order` set `order_state`='2' where `id`='".$order['id']."'");
	}
	@file_get_contents($tenpaydata["sy_weburl"]."/index.php?m=ajax&c=paypost&dingdan=".$sp_billno);
	mysql_query("update `".$db_config["def"]."company_order` set order_state='2' where `order_id`='$sp_billno'");





		
		$show = $tenpay[sy_weburl]."/app/tenpay/show.php";
		$resHandler->doShow($show);

	} else {
		
		echo "<br/>" . "支付失败" . "<br/>";
	}

} else {
	echo "<br/>" . "认证签名失败" . "<br/>";
}



?>