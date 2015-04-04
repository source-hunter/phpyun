<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class payment_controller extends company
{
	function index_action(){
		if($_COOKIE['usertype']!='2' || $this->uid=='')
		{
			$this->obj->ACT_msg("index.php","非法操作！");
		}else{
			$c_order=$this->obj->DB_select_once("company_order","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' and `order_state`='1'");
			if(empty($c_order)){
 				$this->obj->ACT_msg("index.php","非法操作！");
			}else{
				$statis=$this->company_satic();

				$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`linkman`,`linktel`,`address`");

				$order_remark="我所汇款的银行：\n我汇入的帐号：\n汇款金额：\n汇款时间：\n其他：\n";
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
				$this->insert_company_pay($price,2,$this->uid,"余额支付：".$order['order_id'],2,$order['type']);
			}else{
				$this->obj->DB_update_all("company_order",$company_order,"`order_id`='".$order['order_id']."'");
			}
			$this->obj->ACT_layer_msg("操作成功，请等待管理员审核！",9,"index.php?c=paylog");
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>