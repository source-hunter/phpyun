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
	*���ܣ�֧��������֪ͨ���õ�ҳ�棨֪ͨҳ��
	*�汾��3.0
	*���ڣ�2010-05-21
	'˵����
	'���´���ֻ��Ϊ�˷����̻����Զ��ṩ���������룬�̻����Ը����Լ���վ����Ҫ�����ռ����ĵ���д,����һ��Ҫʹ�øô��롣
	'�ô������ѧϰ���о�֧�����ӿ�ʹ�ã�ֻ���ṩһ���ο���

*/
///////////ҳ�湦��˵��///////////////
//������ҳ���ļ�ʱ�������ĸ�ҳ���ļ������κ�HTML���뼰�ո�
//��ҳ�治���ڱ������Բ��ԣ��뵽�������������ԡ���ȷ���ⲿ���Է��ʸ�ҳ�档
//��ҳ����Թ�����ʹ��д�ı�����log_result���ú����ѱ�Ĭ�Ͽ�������alipay_notify.php�еĺ���notify_verify
//TRADE_FINISHED(��ʾ�����Ѿ��ɹ�������ͨ�ü�ʱ���ʷ����Ľ���״̬�ɹ���־);
//TRADE_SUCCESS(��ʾ�����Ѿ��ɹ��������߼���ʱ���ʷ����Ľ���״̬�ɹ���־);
//��֪ͨҳ����Ҫ�����ǣ����ڷ���ҳ�棨return_url.php���������������û���յ���ҳ�淵�ص� success ��Ϣ��֧��������24Сʱ�ڰ�һ����ʱ������ط�֪ͨ
/////////////////////////////////////
error_reporting(0);
require_once("class/alipay_notify.php");
require_once("alipay_config.php");

require_once(dirname(dirname(dirname(__FILE__)))."/data/db.config.php");
require_once(dirname(dirname(dirname(__FILE__)))."/include/mysql.class.php");
$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);

$alipay = new alipay_notify($partner,$security_code,$sign_type,$_input_charset,$transport);    //����֪ͨ������Ϣ
$verify_result = $alipay->notify_verify();  //����ó�֪ͨ��֤���

if($verify_result) {
	if(!is_numeric($_POST['out_trade_no']))
	{
		die;
	}
    //��֤�ɹ�
    //��ȡ֧�����ķ�������
    $dingdan           = $_POST['out_trade_no'];	    //��ȡ֧�������ݹ����Ķ�����
    $total             = $_POST['total_fee'];	    //��ȡ֧�������ݹ������ܼ۸�
	$sql=$db->query("select * from `".$db_config["def"]."company_order` where `order_id`='".$dingdan."'");
    $row=mysql_fetch_array($sql);
    $sOld_trade_status =$row['order_state'];		    //��ȡ�̻����ݿ��в�ѯ�õ��ñʽ��׵�ǰ�Ľ���״̬
    /*���裺
	sOld_trade_status="0";��ʾ����δ����
	sOld_trade_status="1";��ʾ���׳ɹ���TRADE_FINISHED/TRADE_SUCCESS����
    */
    if($_POST['trade_status'] == 'TRADE_FINISHED' ||$_POST['trade_status'] == 'TRADE_SUCCESS') {    //���׳ɹ�����
         //���붩��������ɺ�����ݿ���³�����룬����ر�֤echo��������Ϣֻ��success
        //Ϊ�˱�֤�����ظ����ã����ظ�ִ�����ݿ���³������жϸñʽ���״̬�Ƿ��Ƕ���δ����״̬

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
				//���ݶ����Ÿ��¶������Ѷ�������ɽ��׳ɹ�
			}
			echo "success";

			//�����ã�д�ı�������¼������������Ƿ�����
			//log_result("����д����Ҫ���ԵĴ������ֵ�����������еĽ����¼");
		}
	}

    else {
        echo "success";		//����״̬�жϡ���ͨ��ʱ�����У�����״̬�����жϣ�ֱ�Ӵ�ӡsuccess��

        //�����ã�д�ı�������¼������������Ƿ�����
        //log_result ("����д����Ҫ���ԵĴ������ֵ�����������еĽ����¼");
    }
}
else {
    //��֤ʧ��
    echo "fail";

    //�����ã�д�ı�������¼������������Ƿ�����
    //log_result ("����д����Ҫ���ԵĴ������ֵ�����������еĽ����¼");
}
?>