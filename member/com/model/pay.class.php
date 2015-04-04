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
		$remark="姓名：\n联系电话：\n留言：";
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
 				$this->obj->ACT_layer_msg("参数不正确，请正确填写！",8,$_SERVER['HTTP_REFERER']);
			}
			if(($data['type']=='2'||$data['type']=='5')&&$integral<1){
				$this->obj->ACT_layer_msg("请正确填写购买数量！",8,$_SERVER['HTTP_REFERER']);
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
				$this->obj->member_log("下单成功,订单ID".$dingdan);
				$this->obj->ACT_layer_msg("下单成功，请付款！",9,"index.php?c=payment&id=".$id);
			}else{
				$this->obj->ACT_layer_msg("提交失败，请重新提交订单！",8,$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->obj->ACT_layer_msg("参数不正确，请正确填写！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function duihuan_action(){
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`pay`");
		$num=(int)$_POST['price_int'];
		$price=$num/$this->config['integral_proportion'];
		if($statis['pay']>$price){
			$this->obj->DB_update_all("company_statis","`pay`=`pay`-$price,`integral`=`integral`+$num","`uid`='".$this->uid."'");
			$this->insert_company_pay($price,2,$this->uid,'购买'.$num.$this->config['integral_pricename'],2,3);
			$this->obj->member_log("兑换金币");
			$this->obj->ACT_layer_msg("兑换成功！",9,"index.php?c=com");
		}else{
			$this->obj->ACT_layer_msg("余额不足！",8,"index.php?c=com");
		}
	}
}
?>