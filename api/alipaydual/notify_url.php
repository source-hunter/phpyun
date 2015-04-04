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
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);
$alipayNotify = new AlipayNotify($aliapy_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {
	
	if(!is_numeric($_POST['out_trade_no']))
	{
		die;
	}
    $out_trade_no	= $_POST['out_trade_no'];	    
    $trade_no		= $_POST['trade_no'];	    	
    $total			= $_POST['price'];				
	$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='$out_trade_no'");
    $row=mysql_fetch_array($sql);
	$sOld_trade_status = $row['order_state'];
	if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {

	

        echo "success";		

       
        logResult("没有付款");
    }
	else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
	   

        echo "success";		

        
        logResult("未发货");
    }
	else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
	

        echo "success";		

        
       logResult("未确认收货");
    }
	else if($_POST['trade_status'] == 'TRADE_FINISHED') {
	
				logResult($sql.$out_trade_no.$db_config["def"]);
		if($sOld_trade_status=="1")
		{
			$mselect=$db->query("select  `usertype` from `".$db_config[def]."member` where `uid`='".$row['uid']."'");
			$member=mysql_fetch_array($mselect);
			if($member['usertype']=='1'){
				$table='member_statis';
			}else if($member['usertype']=='2'){
				$table='company_statis'; 
				$tvalue=",`all_pay`=`all_pay`+".$order["order_price"];
			}
			if($row['type']=="1"&&$row['rating']&&$member['usertype']!='1'){
				$select=$db->query("select * from `".$db_config["def"]."company_rating` where `id`='".$row["rating"]."'");
				$comuid=mysql_fetch_array($select);
				$value="`rating`='".$comuid["id"]."',";
				$value.="`rating_name`='".$comuid["name"]."',";
				$value.="`rating_type`='".$comuid['type']."',";
				if($comuid['service_time']>0){
					$viptime=time()+$comuid['service_time']*86400;
				}else{
					$viptime=0;
				}
				$value.="`vip_time`='".$viptime."',";
				$value.="`job_num`=".$comuid["job_num"].",";
				$value.="`down_resume`=".$comuid["resume"].",";
				$value.="`invite_resume`=".$comuid["interview"].",";
				$value.="`editjob_num`=".$comuid["editjob_num"].",";
				$value.="`breakjob_num`=".$comuid["breakjob_num"];
				mysql_query("update `".$db_config["def"]."company_statis` SET `all_pay`=`all_pay`+".$row["order_price"].",".$value." where `uid`='".$row["uid"]."'");

			}elseif($row["type"]=="2"&&$row['integral']){ 
				mysql_query("update `".$db_config[def].$table."` set `integral`=`integral`+'".$row['integral']."'".$tvalue." where `uid`='".$row["uid"]."'");
			}else if($row['type']=='3'||$row['type']=='4'){
				mysql_query("update `".$db_config[def].$table."` set `pay`=`pay`+".$row["order_price"].$tvalue." where `uid`='".$row["uid"]."'");
			}else if($row['type']=='5'&&$row['integral']&&$usertype['usertype']=='2'){ 
				mysql_query("update `".$db_config[def]."company_statis` set `msg_num`=`msg_num`+'".$row['integral']."'".$tvalue." where `uid`='".$row["uid"]."'");
			}
			@file_get_contents($alipaydata["sy_weburl"]."/index.php?m=ajax&c=paypost&dingdan=".$row[order_id]);
			mysql_query("update `".$db_config["def"]."company_order` set order_state='2' where `order_id`='$row[order_id]'");

			if($sOld_trade_status < 1) {
				
			}
			echo "success";

			
			
		}
        echo "success";		

        

    }
    else {
		
        echo "success";

        
    }

	

	
}
else {
    
    echo "fail";

   
    
}
?>