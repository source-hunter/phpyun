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
	*���ܣ���������ת��ҳ�棨����ҳ��
	*�汾��3.0
	*���ڣ�2010-05-21
	'˵����
	'���´���ֻ��Ϊ�˷����̻����Զ��ṩ���������룬�̻����Ը����Լ���վ����Ҫ�����ռ����ĵ���д,����һ��Ҫʹ�øô��롣
	'�ô������ѧϰ���о�֧�����ӿ�ʹ�ã�ֻ���ṩһ���ο���

*/
///////////ҳ�湦��˵��///////////////
//��ҳ����ڱ������Բ���
//��ҳ�����������ҳ��������֧����������ͬ�����ã��ɵ�����֧����ɺ����ʾ��Ϣҳ���硰����ĳĳĳ���������ٽ����֧���ɹ�����
//�ɷ���HTML������ҳ��Ĵ���Ͷ���������ɺ�����ݿ���³������
//��ҳ�����ʹ��PHP�������ߵ��ԣ�Ҳ����ʹ��д�ı�����log_result���е��ԣ��ú����ѱ�Ĭ�Ϲرգ���alipay_notify.php�еĺ���return_verify
//TRADE_FINISHED(��ʾ�����Ѿ��ɹ�������Ϊ��ͨ��ʱ���ʵĽ���״̬�ɹ���ʶ);
//TRADE_SUCCESS(��ʾ�����Ѿ��ɹ�������Ϊ�߼���ʱ���ʵĽ���״̬�ɹ���ʶ);
///////////////////////////////////
error_reporting(0);
require_once("class/alipay_notify.php");
require_once("alipay_config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);


//����֪ͨ������Ϣ
$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);
//����ó�֪ͨ��֤���
$verify_result = $alipay->return_verify();
if($verify_result) {

    //��֤�ɹ�
    //��ȡ֧������֪ͨ���ز���
    $dingdan           = $_GET['out_trade_no'];    //��ȡ������
    $total_fee         = $_GET['total_fee'];	    //��ȡ�ܼ۸�
    $sOld_trade_status = "0";		    //��ȡ�̻����ݿ��в�ѯ�õ��ñʽ��׵�ǰ�Ľ���״̬

    /*���裺
	sOld_trade_status="0";��ʾ����δ����
	sOld_trade_status="1";��ʾ���׳ɹ���TRADE_FINISHED/TRADE_SUCCESS����
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
    //��֤ʧ��
    //��Ҫ���ԣ��뿴alipay_notify.phpҳ���return_verify�������ȶ�sign��mysign��ֵ�Ƿ���ȣ����߼��$veryfy_result��û�з���true
    //echo "fail";

}
?>
<html>
    <head>
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
                <td align="center" class="font_title" colspan="2">֪ͨ����</td>
            </tr>
            <tr>
                <td class="font_content" align="right">֧�������׺ţ�</td>
                <td class="font_content" align="left"><?php echo $_GET['trade_no']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">�����ţ�</td>
                <td class="font_content" align="left"><?php echo $_GET['out_trade_no']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">�����ܽ�</td>
                <td class="font_content" align="left"><?php echo $_GET['total_fee']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">��Ʒ���⣺</td>
                <td class="font_content" align="left"><?php echo $_GET['subject']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">��Ʒ������</td>
                <td class="font_content" align="left"><?php echo $_GET['body']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">����˺ţ�</td>
                <td class="font_content" align="left"><?php echo $_GET['buyer_email']; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">����״̬��</td>
                <td class="font_content" align="left"><?php echo $_GET['trade_status']; ?></td>
            </tr>
        </table>
 <script src="../../commanage.php?action=dingdan&pay=alipay&dingdan=<?php echo $_GET['out_trade_no']; ?>&state=<?php echo $_GET['trade_status']; ?>"></script>
    </body>
</html>