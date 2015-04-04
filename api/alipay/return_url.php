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
/*
	*功能：付完款后跳转的页面（返回页）
	*版本：3.0
	*日期：2010-05-21
	'说明：
	'以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
	'该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

*/
///////////页面功能说明///////////////
//该页面可在本机电脑测试
//该页面称作“返回页”，是由支付宝服务器同步调用，可当作是支付完成后的提示信息页，如“您的某某某订单，多少金额已支付成功”。
//可放入HTML等美化页面的代码和订单交易完成后的数据库更新程序代码
//该页面可以使用PHP开发工具调试，也可以使用写文本函数log_result进行调试，该函数已被默认关闭，见alipay_notify.php中的函数return_verify
//TRADE_FINISHED(表示交易已经成功结束，为普通即时到帐的交易状态成功标识);
//TRADE_SUCCESS(表示交易已经成功结束，为高级即时到帐的交易状态成功标识);
///////////////////////////////////
error_reporting(0);
require_once("class/alipay_notify.php");
require_once("alipay_config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);


//构造通知函数信息
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
//计算得出通知验证结果
$verify_result = $alipay->return_verify();
if($verify_result) {

    //验证成功
    //获取支付宝的通知返回参数
    $dingdan           = $_GET['out_trade_no'];    //获取订单号
    $total_fee         = $_GET['total_fee'];	    //获取总价格
    $sOld_trade_status = "0";		    //获取商户数据库中查询得到该笔交易当前的交易状态

    /*假设：
	sOld_trade_status="0";表示订单未处理；
	sOld_trade_status="1";表示交易成功（TRADE_FINISHED/TRADE_SUCCESS）；
    */
    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {

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
			}
			if($order['type']=='1'&&$order['rating']&&$member['usertype']=='2'){
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
				mysql_query("update `".$db_config[def]."company_statis` set `all_pay`=`all_pay`+'".$order["order_price"]."',".$value." where `uid`='".$order['uid']."'"); 
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
      echo "trade_status=".$_GET['trade_status'];
    }
}else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的return_verify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
    //echo "fail";

}
?>
<html>
    <head>
        <title>支付宝即时支付</title>
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