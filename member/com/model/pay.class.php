<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class pay_controller extends company
{ 
	function index_action()
	{
		$this->public_action();
		$statis=$this->company_satic();
		if($_POST['usertype']=='price')
		{
			$rows=$this->obj->DB_select_all("company_rating","`service_price`<>'' and `display`='1' and `category`=1 order by sort desc","name,service_price,id");
			$this->yunset("rows",$rows);
		}
		$this->yunset("statis",$statis);
		$remark="������\n��ϵ�绰��\n���ԣ�";
		$this->yunset("remark",$remark);
		$this->yunset("js_def",4);
		$this->com_tpl('pay');
	}
	function dingdan_action(){
		if($_POST['price']){
			if($_POST['comvip']){
				$comvip=(int)$_POST['comvip'];
				$ratinginfo =  $this->obj->DB_select_once("company_rating","`id`='".$comvip."'");
				$price = $ratinginfo['service_price'];
				$data['type']='1';
			}elseif($_POST['price_int']){
				$integral=intval($_POST['price_int']);
				$price = $integral/$this->config['integral_proportion'];
				$data['type']='2';
			}elseif($_POST['price_msg']){
				$integral=intval($_POST['price_msg']);
				$price = $integral/$this->config['integral_msg_proportion'];
				$data['type']='5';
			}else{
 				$this->obj->ACT_layer_msg("��������ȷ������ȷ��д��",8,$_SERVER['HTTP_REFERER']);
			}
			if(($data['type']=='2'||$data['type']=='5')&&$integral<1){
				$this->obj->ACT_layer_msg("����ȷ��д����������",8,$_SERVER['HTTP_REFERER']);
			}
			$dingdan=mktime().rand(10000,99999);
			$data['order_id']=$dingdan;
			$data['order_price']=$price;
			$data['order_time']=mktime();
			$data['order_state']="1";
			$data['order_remark']=trim($_POST['remark']);
			$data['uid']=$this->uid;
			$data['rating']=$_POST['comvip'];
			$data['integral']=$integral;
			$id=$this->obj->insert_into("company_order",$data);
			if($id){
				$this->obj->member_log("�µ��ɹ�,����ID".$dingdan);
				$this->obj->ACT_layer_msg("�µ��ɹ����븶�",9,"index.php?c=payment&id=".$id);
			}else{
				$this->obj->ACT_layer_msg("�ύʧ�ܣ��������ύ������",8,$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->obj->ACT_layer_msg("��������ȷ������ȷ��д��",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function duihuan_action(){
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`pay`");
		$num=(int)$_POST['price_int'];
		$price=$num/$this->config['integral_proportion'];
		if($statis['pay']>$price){
			$this->obj->DB_update_all("company_statis","`pay`=`pay`-$price,`integral`=`integral`+$num","`uid`='".$this->uid."'");
			$this->insert_company_pay($price,2,$this->uid,'����'.$num.$this->config['integral_pricename'],2,3);
			$this->obj->member_log("�һ����");
			$this->obj->ACT_layer_msg("�һ��ɹ���",9,"index.php?c=com");
		}else{
			$this->obj->ACT_layer_msg("���㣡",8,"index.php?c=com");
		}
	}
}
?>