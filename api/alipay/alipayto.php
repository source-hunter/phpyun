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
/*
 *���ܣ�������Ʒ�й���Ϣ��ȷ�϶���֧�������߹������ҳ��
 *��ϸ����ҳ���ǽӿ����ҳ�棬����֧��ʱ��URL
 *�汾��3.0
 *�޸����ڣ�2010-06-22
 '˵����
 '���´���ֻ��Ϊ�˷����̻����Զ��ṩ���������룬�̻����Ը����Լ���վ����Ҫ�����ռ����ĵ���д,����һ��Ҫʹ�øô��롣
 '�ô������ѧϰ���о�֧�����ӿ�ʹ�ã�ֻ���ṩһ���ο���

*/

////////////////////ע��/////////////////////////
//��ҳ�����ʱ���֡����Դ�����ο���http://club.alipay.com/read-htm-tid-8681712.html
//Ҫ���ݵĲ���Ҫô������Ϊ�գ�Ҫô�Ͳ�Ҫ���������������ؿؼ���URL�����
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
	echo '��¼��Ϣ��֤���������µ�¼��';die;
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
		$com_sql=$db->query("select `linkman`,`linktel`,`address` from `".$db_config["def"]."company` where `uid`='".$_COOKIE['uid']."'");//��ѯ���
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
if((int)$_POST['balance'] && $_COOKIE['uid']){//���ʹ������
	$c_sql=$db->query("select `pay` from `".$db_config["def"]."company_statis` where `uid`='".$_COOKIE['uid']."'");//��ѯ���
	$company_statis=mysql_fetch_array($c_sql); 
	if($company_statis['pay']>=$row['order_price']){//��������ڶ������ 
		$up_sql=$db->query("update `".$db_config["def"]."company_statis` set `pay`=`pay`-'".$row['order_price']."' where `uid`='".$_COOKIE['uid']."'");
		mysql_fetch_array($up_sql);//�����˻���� 
		$up_order=$db->query("update `".$db_config["def"]."company_order` set `order_price`='0'".$invoice_title." where `order_id`='".$row['order_id']."'");
		mysql_fetch_array($up_order);//���Ķ���δ�����
		$price=$row['order_price'];
	}else{ 
		$price=$company_statis['pay'];
		$up_sql=$db->query("update `".$db_config["def"]."company_statis` set `pay`='0' where `uid`='".$_COOKIE['uid']."'");
		$up_sql_status=mysql_fetch_array($up_sql);//�����˻���� 
		$up_order=$db->query("update `".$db_config["def"]."company_order` set `order_price`=`order_price`-'".$price."'".$invoice_title." where `order_id`='".$row['order_id']."'");
		mysql_fetch_array($up_order); //���Ķ���δ�����
	} 
	$insert_company_pay=$db->query("insert into `".$db_config["def"]."company_pay`(order_id,order_price,pay_time,pay_state,com_id,pay_remark,type) values('".$row['order_id']."','-".$price."','".time()."','2','".$_COOKIE['uid']."','".$row['order_remark']."','2')");
	mysql_fetch_array($insert_company_pay);
	$new_sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$row['order_id']."'");//�ٴβ�ѯ���
	$row=mysql_fetch_array($new_sql); 
}else if($invoice_title){
	$up_order=$db->query("update `".$db_config["def"]."company_order` set `is_invoice`='".$_POST['is_invoice']."' where `order_id`='".$row['order_id']."'");
	mysql_fetch_array($up_order);//���Ķ�����Ʊ��Ϣ 
} 

/*���²�������Ҫͨ���µ�ʱ�Ķ������ݴ���������*/
//�������
 $out_trade_no = $_POST['dingdan'];	//�������վ����ϵͳ�е�Ψһ������ƥ��
$subject      = $_POST['dingdan'];		//�������ƣ���ʾ��֧��������̨��ġ���Ʒ���ơ����ʾ��֧�����Ľ��׹���ġ���Ʒ���ơ����б��
$body         = $row['order_remark'];		//����������������ϸ��������ע����ʾ��֧��������̨��ġ���Ʒ��������
$total_fee    = $row['order_price'];		//�����ܽ���ʾ��֧��������̨��ġ�Ӧ���ܶ��

//��չ���ܲ�������������ǰ
$pay_mode	  = $_POST['pay_bank'];

if ($pay_mode == "directPay") {
	$paymethod    = "directPay";	//Ĭ��֧����ʽ���ĸ�ֵ��ѡ��bankPay(����); cartoon(��ͨ); directPay(���); CASH(����֧��)
	$defaultbank  = "";
}
else {
	$paymethod    = "bankPay";		//Ĭ��֧����ʽ���ĸ�ֵ��ѡ��bankPay(����); cartoon(��ͨ); directPay(���); CASH(����֧��)
	$defaultbank  = $pay_mode;		//Ĭ���������ţ������б��http://club.alipay.com/read.php?tid=8681379
}

//��չ���ܲ�������������
$encrypt_key  = '';					//������ʱ�������ʼֵ
$exter_invoke_ip = '';				//�ͻ��˵�IP��ַ����ʼֵ
if($antiphishing == 1){
    $encrypt_key = query_timestamp($partner);
	$exter_invoke_ip = '';			//��ȡ�ͻ��˵�IP��ַ�����飺��д��ȡ�ͻ���IP��ַ�ĳ���
}

//��չ���ܲ�����������
$extra_common_param =$_POST['pay_type'];			//�Զ���������ɴ���κ����ݣ���=��&�������ַ��⣩��������ʾ��ҳ����
$buyer_email		= '';			//Ĭ�����֧�����˺�

/////////////////////////////////////////////////

//����Ҫ����Ĳ�������
$parameter = array(
        "service"         => "create_direct_pay_by_user",	//�ӿ����ƣ�����Ҫ�޸�
        "payment_type"    => "1",               			//�������ͣ�����Ҫ�޸�

        //��ȡ�����ļ�(alipay_config.php)�е�ֵ
        "partner"         => $partner,
        "seller_email"    => $seller_email,
        "return_url"      => $return_url,
        "notify_url"      => $notify_url,
        "_input_charset"  => $_input_charset,
        "show_url"        => $show_url,

        //�Ӷ��������ж�̬��ȡ���ı������
        "out_trade_no"    => $out_trade_no,
        "subject"         => $subject,
        "body"            => $body,
        "total_fee"       => $total_fee, 

        //��չ���ܲ�������������ǰ
        "paymethod"	      => $paymethod,
        "defaultbank"	  => $defaultbank,

        //��չ���ܲ�������������
        "anti_phishing_key"=> $encrypt_key,
		"exter_invoke_ip" => $exter_invoke_ip,

        //��չ���ܲ�����������(��Ҫʹ�ã���ȡ����������ע��)
        //$royalty_type   => "10",	  //������ͣ�����Ҫ�޸�
        //$royalty_parameters => "111@126.com^0.01^����עһ|222@126.com^0.01^����ע��",
        /*�����Ϣ��������Ҫ����̻���վ���������̬��ȡÿ�ʽ��׵ĸ������տ��˺š��������������˵�������ֻ������10��
	�����Ϣ����ʽΪ���տEmail_1^���1^��ע1|�տEmail_2^���2^��ע2
        */

        //��չ���ܲ��������Զ��峬ʱ(��Ҫʹ�ã���ȡ������һ��ע��)���ù���Ĭ�ϲ���ͨ������ϵ�ͻ�������ѯ
        //$it_b_pay	      => "1c",	  //��ʱʱ�䣬����Ĭ����15�졣�˸�ֵ��ѡ��1h(1Сʱ),2h(2Сʱ),3h(3Сʱ),1d(1��),3d(3��),7d(7��),15d(15��),1c(����)

		//��չ���ܲ��������Զ������
		"buyer_email"	 => $buyer_email,
        "extra_common_param" => $extra_common_param
); 
//����������
$alipay = new alipay_service($parameter,$security_code,$sign_type);


//���ĳ�GET��ʽ����
$url = $alipay->create_url();
$sHtmlText = "<a href=".$url."><img border='0' src='images/alipay.gif' /></a>";
echo "<script>window.location =\"$url\";</script>";


//POST��ʽ���ݣ��õ����ܽ���ַ�������ȡ������һ�е�ע��
//$sHtmlText = $alipay->build_postform();

?>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
        <title>֧������ʱ֧��</title>
        <style type="text/css">
            .font_content{
                font-family:"����";
                font-size:14px;
                color:#FF6600;
            }
            .font_title{
                font-family:"����";
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
                <td align="center" class="font_title" colspan="2">����ȷ��</td>
            </tr>
            <tr>
                <td class="font_content" align="right">�����ţ�</td>
                <td class="font_content" align="left"><?php echo $out_trade_no; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">�����ܽ�</td>
                <td class="font_content" align="left"><?php echo $total_fee; ?></td>
            </tr>
            <tr>
                <td align="center" colspan="2"><?php echo $sHtmlText; ?></td>
            </tr>
        </table>
    </body>
</html>
