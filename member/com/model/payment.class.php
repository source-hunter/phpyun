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
class payment_controller extends company
{
	function index_action(){
		if($_COOKIE['usertype']!='2' || $this->uid=='')
		{
			$this->obj->ACT_msg("index.php","�Ƿ�������");
		}else{
			$c_order=$this->obj->DB_select_once("company_order","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' and `order_state`='1'");
			if(empty($c_order)){
 				$this->obj->ACT_msg("index.php","�Ƿ�������");
			}else{
				$statis=$this->company_satic();

				$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`linkman`,`linktel`,`address`");

				$order_remark="�����������У�\n�һ�����ʺţ�\n����\n���ʱ�䣺\n������\n";
				if($company['linkman']==''||$company['linktel']==''||$company['address']==''){
					$company['link_null']='1';
				}

				if($c_order['order_time']>strtotime("-7 day")){
					$c_order['invoice']='1';
				}

				$this->yunset("company",$company);
				$this->yunset("order",$c_order);
				$this->yunset("order_remark",$order_remark);
				$this->yunset("statis",$statis);
				$this->yunset("js_def",4);
				$this->public_action();
				$this->com_tpl('payment');
			}
		}
	} 
	function paybank_action(){
		$order=$this->obj->DB_select_once("company_order","`id`='".(int)$_POST['oid']."' and `uid`='".$this->uid."'");
		if($order['id']){
			$company_order="`order_type`='bank',`order_state`='3',`order_remark`='".$_POST['order_remark']."'";
			if($_POST['is_invoice']=='1'&&$this->config['sy_com_invoice']=='1'){
				$company_order.=",`is_invoice`='".$_POST['is_invoice']."'";
				$this->add_invoice_record($_POST,$order['order_id'],$order['id']);
			}
			if($_POST['balance']){
				$statis=$this->company_satic();
				if($statis['pay']>=$order['order_price']){
					$this->obj->DB_update_all("company_statis","`pay`=".($statis['pay']-$order['order_price'])."'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company_order","`order_price`='0',".$company_order,"`order_id`='".$order['order_id']."'");
					$this->upuser_statis($order);
				}else{
					$price=$statis['pay'];
					$this->obj->DB_update_all("company_statis","`pay`='0'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company_order","`order_price`=`order_price`-'".$price."',".$company_order,"`order_id`='".$order['order_id']."'");
				}
				$this->insert_company_pay($price,2,$this->uid,"���֧����".$order['order_id'],2,$order['type']);
			}else{
				$this->obj->DB_update_all("company_order",$company_order,"`order_id`='".$order['order_id']."'");
			}
			$this->obj->ACT_layer_msg("�����ɹ�����ȴ�����Ա��ˣ�",9,"index.php?c=paylog");
		}else{
			$this->obj->ACT_layer_msg("�Ƿ�������",8,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>