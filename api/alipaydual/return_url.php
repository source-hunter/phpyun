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
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {
	
    $out_trade_no	= $_GET['out_trade_no'];	
    $trade_no		= $_GET['trade_no'];		
    $total_fee		= $_GET['price'];			
	
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
	
	
	
	

}
else {
   
    echo "验证失败";
}
?>
<html>
    <head>
        <title>支付宝担保交易</title>
        <style type="text/css">
            .font_content{
                font-family:"宋体";
                font-size:14px;
                color:#FF6600;
            }
            .font_title{
                font-family:"宋体";
                font-size:16px;
                color:#FF0000;
                font-weight:bold;
            }
            table{
                border: 1px solid #CCCCCC;
            }
        </style>
    </head>
    <body>

        <table align="center" width="350" cellpadding="5" cellspacing="0">
            <tr>
                <td align="center" class="font_title" colspan="2">通知返回</td>
            </tr>
            <tr>
                <td class="font_content" align="right">支付宝交易号：</td>
                <td class="font_content" align="left"><?php echo $_GET['trade_no']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">订单号：</td>
                <td class="font_content" align="left"><?php echo $_GET['out_trade_no']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">付款总金额：</td>
                <td class="font_content" align="left"><?php echo $_GET['total_fee']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">商品标题：</td>
                <td class="font_content" align="left"><?php echo $_GET['subject']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">商品描述：</td>
                <td class="font_content" align="left"><?php echo $_GET['body']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">买家账号：</td>
                <td class="font_content" align="left"><?php echo $_GET['buyer_email']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">交易状态：</td>
                <td class="font_content" align="left"><?php echo $_GET['trade_status']; ?></td>
            </tr>
        </table>
 <script src="../../commanage.php?action=dingdan&pay=alipay&dingdan=<?php echo $_GET['out_trade_no']; ?>&state=<?php echo $_GET['trade_status']; ?>"></script>
    </body>
</html>