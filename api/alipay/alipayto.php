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
 *功能：设置商品有关信息（确认订单支付宝在线购买入口页）
 *详细：该页面是接口入口页面，生成支付时的URL
 *版本：3.0
 *修改日期：2010-06-22
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

*/

////////////////////注意/////////////////////////
//该页面测试时出现“调试错误”请参考：http://club.alipay.com/read-htm-tid-8681712.html
//要传递的参数要么不允许为空，要么就不要出现在数组与隐藏控件或URL链接里。
/////////////////////////////////////////////////
error_reporting(0);
require_once("alipay_config.php");
require_once("class/alipay_service.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.safety.php");
require_once(dirname(dirname(dirname(__FILE__)))."/plus/config.php");

require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);
if(!is_numeric($_POST['dingdan'])){die;}
 
$_COOKIE['uid']=(int)$_COOKIE['uid'];
$_POST['is_invoice']=(int)$_POST['is_invoice'];
$_POST['balance']=(int)$_POST['balance']; 
$member_sql=$db->query("SELECT * FROM `".$db_config["def"]."member` WHERE `uid`='".$_COOKIE['uid']."' limit 1");
$member=mysql_fetch_array($member_sql);  
if($member['username'] != $_COOKIE['username'] || $member['usertype'] != $_COOKIE['usertype']||md5($member['username'].$member['password'].$member['salt'])!=$_COOKIE['shell']){  
	echo '登录信息验证错误，请重新登录！';die;
}  
$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$_POST['dingdan']."' AND `order_price`>=0");
$row=mysql_fetch_array($sql);
if(!$row['uid'] || $row['uid']!=$_COOKIE['uid'])
{
	die;
}
if((int)$_POST['is_invoice']=='1'&&$config["sy_com_invoice"]){
	$invoice_title=",`is_invoice`='".$_POST['is_invoice']."'";
	if($_POST['linkway']=='1'){
		$com_sql=$db->query("select `linkman`,`linktel`,`address` from `".$db_config["def"]."company` where `uid`='".$_COOKIE['uid']."'");//查询余额
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
if((int)$_POST['balance'] && $_COOKIE['uid']){//如果使用余额付款
	$c_sql=$db->query("select `pay` from `".$db_config["def"]."company_statis` where `uid`='".$_COOKIE['uid']."'");//查询余额
	$company_statis=mysql_fetch_array($c_sql); 
	if($company_statis['pay']>=$row['order_price']){//如果余额大于订单金额 
		$up_sql=$db->query("update `".$db_config["def"]."company_statis` set `pay`=`pay`-'".$row['order_price']."' where `uid`='".$_COOKIE['uid']."'");
		mysql_fetch_array($up_sql);//更改账户余额 
		$up_order=$db->query("update `".$db_config["def"]."company_order` set `order_price`='0'".$invoice_title." where `order_id`='".$row['order_id']."'");
		mysql_fetch_array($up_order);//更改订单未付金额
		$price=$row['order_price'];
	}else{ 
		$price=$company_statis['pay'];
		$up_sql=$db->query("update `".$db_config["def"]."company_statis` set `pay`='0' where `uid`='".$_COOKIE['uid']."'");
		$up_sql_status=mysql_fetch_array($up_sql);//更改账户余额 
		$up_order=$db->query("update `".$db_config["def"]."company_order` set `order_price`=`order_price`-'".$price."'".$invoice_title." where `order_id`='".$row['order_id']."'");
		mysql_fetch_array($up_order); //更改订单未付金额
	} 
	$insert_company_pay=$db->query("insert into `".$db_config["def"]."company_pay`(order_id,order_price,pay_time,pay_state,com_id,pay_remark,type) values('".$row['order_id']."','-".$price."','".time()."','2','".$_COOKIE['uid']."','".$row['order_remark']."','2')");
	mysql_fetch_array($insert_company_pay);
	$new_sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$row['order_id']."'");//再次查询金额
	$row=mysql_fetch_array($new_sql); 
}else if($invoice_title){
	$up_order=$db->query("update `".$db_config["def"]."company_order` set `is_invoice`='".$_POST['is_invoice']."' where `order_id`='".$row['order_id']."'");
	mysql_fetch_array($up_order);//更改订单发票信息 
} 

/*以下参数是需要通过下单时的订单数据传入进来获得*/
//必填参数
 $out_trade_no = $_POST['dingdan'];	//请与贵网站订单系统中的唯一订单号匹配
$subject      = $_POST['dingdan'];		//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
$body         = $row['order_remark'];		//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
$total_fee    = $row['order_price'];		//订单总金额，显示在支付宝收银台里的“应付总额”里

//扩展功能参数——网银提前
$pay_mode	  = $_POST['pay_bank'];

if ($pay_mode == "directPay") {
	$paymethod    = "directPay";	//默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH(网点支付)
	$defaultbank  = "";
}
else {
	$paymethod    = "bankPay";		//默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH(网点支付)
	$defaultbank  = $pay_mode;		//默认网银代号，代号列表见http://club.alipay.com/read.php?tid=8681379
}

//扩展功能参数——防钓鱼
$encrypt_key  = '';					//防钓鱼时间戳，初始值
$exter_invoke_ip = '';				//客户端的IP地址，初始值
if($antiphishing == 1){
    $encrypt_key = query_timestamp($partner);
	$exter_invoke_ip = '';			//获取客户端的IP地址，建议：编写获取客户端IP地址的程序
}

//扩展功能参数——其他
$extra_common_param =$_POST['pay_type'];			//自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
$buyer_email		= '';			//默认买家支付宝账号

/////////////////////////////////////////////////

//构造要请求的参数数组
$parameter = array(
        "service"         => "create_direct_pay_by_user",	//接口名称，不需要修改
        "payment_type"    => "1",               			//交易类型，不需要修改

        //获取配置文件(alipay_config.php)中的值
        "partner"         => $partner,
        "seller_email"    => $seller_email,
        "return_url"      => $return_url,
        "notify_url"      => $notify_url,
        "_input_charset"  => $_input_charset,
        "show_url"        => $show_url,

        //从订单数据中动态获取到的必填参数
        "out_trade_no"    => $out_trade_no,
        "subject"         => $subject,
        "body"            => $body,
        "total_fee"       => $total_fee, 

        //扩展功能参数——网银提前
        "paymethod"	      => $paymethod,
        "defaultbank"	  => $defaultbank,

        //扩展功能参数——防钓鱼
        "anti_phishing_key"=> $encrypt_key,
		"exter_invoke_ip" => $exter_invoke_ip,

        //扩展功能参数——分润(若要使用，请取消下面两行注释)
        //$royalty_type   => "10",	  //提成类型，不需要修改
        //$royalty_parameters => "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二",
        /*提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
	提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
        */

        //扩展功能参数——自定义超时(若要使用，请取消下面一行注释)。该功能默认不开通，需联系客户经理咨询
        //$it_b_pay	      => "1c",	  //超时时间，不填默认是15天。八个值可选：1h(1小时),2h(2小时),3h(3小时),1d(1天),3d(3天),7d(7天),15d(15天),1c(当天)

		//扩展功能参数——自定义参数
		"buyer_email"	 => $buyer_email,
        "extra_common_param" => $extra_common_param
); 
//构造请求函数
$alipay = new alipay_service($parameter,$security_code,$sign_type);


//若改成GET方式传递
$url = $alipay->create_url();
$sHtmlText = "<a href=".$url."><img border='0' src='images/alipay.gif' /></a>";
echo "<script>window.location =\"$url\";</script>";


//POST方式传递，得到加密结果字符串，请取消下面一行的注释
//$sHtmlText = $alipay->build_postform();

?>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
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
                <td align="center" class="font_title" colspan="2">订单确认</td>
            </tr>
            <tr>
                <td class="font_content" align="right">订单号：</td>
                <td class="font_content" align="left"><?php echo $out_trade_no; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">付款总金额：</td>
                <td class="font_content" align="left"><?php echo $total_fee; ?></td>
            </tr>
            <tr>
                <td align="center" colspan="2"><?php echo $sHtmlText; ?></td>
            </tr>
        </table>
    </body>
</html>
