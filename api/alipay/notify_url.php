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
	*功能：支付宝主动通知调用的页面（通知页）
	*版本：3.0
	*日期：2010-05-21
	'说明：
	'以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
	'该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

*/
///////////页面功能说明///////////////
//创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
//该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
//该页面调试工具请使用写文本函数log_result，该函数已被默认开启，见alipay_notify.php中的函数notify_verify
//TRADE_FINISHED(表示交易已经成功结束，通用即时到帐反馈的交易状态成功标志);
//TRADE_SUCCESS(表示交易已经成功结束，高级即时到帐反馈的交易状态成功标志);
//该通知页面主要功能是：对于返回页面（return_url.php）做补单处理。如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
/////////////////////////////////////
error_reporting(0);
require_once("class/alipay_notify.php");
require_once("alipay_config.php");

require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);

$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);    //构造通知函数信息
$verify_result = $alipay->notify_verify();  //计算得出通知验证结果

if($verify_result) {
	if(!is_numeric($_POST['out_trade_no']))
	{
		die;
	}
    //验证成功
    //获取支付宝的反馈参数
    $dingdan           = $_POST['out_trade_no'];	    //获取支付宝传递过来的订单号
    $total             = $_POST['total_fee'];	    //获取支付宝传递过来的总价格
	$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$dingdan."'");
    $row=mysql_fetch_array($sql);
    $sOld_trade_status =$row['order_state'];		    //获取商户数据库中查询得到该笔交易当前的交易状态
    /*假设：
	sOld_trade_status="0";表示订单未处理；
	sOld_trade_status="1";表示交易成功（TRADE_FINISHED/TRADE_SUCCESS）；
    */
    if($_POST['trade_status'] == 'TRADE_FINISHED' ||$_POST['trade_status'] == 'TRADE_SUCCESS') {    //交易成功结束
         //放入订单交易完成后的数据库更新程序代码，请务必保证echo出来的信息只有success
        //为了保证不被重复调用，或重复执行数据库更新程序，请判断该笔交易状态是否是订单未处理状态

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
			if($row['type']=="1"&&$row['rating']&&$member['usertype']=='2'){
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
				mysql_query("update `".$db_config[def]."company_statis` set `msg_num`=`msg_num`+'".$row['integral']."',`all_pay`=`all_pay`+".$row["order_price"]." where `uid`='".$row["uid"]."'");
			}
			@file_get_contents($alipaydata["sy_weburl"]."/index.php?m=ajax&c=paypost&dingdan=".$dingdan);
			mysql_query("update `".$db_config["def"]."company_order` set order_state='2' where `order_id`='$row[order_id]'");

			if($sOld_trade_status < 1) {
				//根据订单号更新订单，把订单处理成交易成功
			}
			echo "success";

			//调试用，写文本函数记录程序运行情况是否正常
			//log_result("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}

    else {
        echo "success";		//其他状态判断。普通即时到帐中，其他状态不用判断，直接打印success。

        //调试用，写文本函数记录程序运行情况是否正常
        //log_result ("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
}
else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //log_result ("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>