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
class payment_controller extends user{
	function index_action()
	{
		if($_COOKIE['usertype']!='1' || $this->uid=='')
		{
			$this->obj->ACT_msg("index.php","非法操作！"); 
		}else{
			$order=$this->obj->DB_select_once("company_order","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' and `order_state`='1'");
			if(empty($order))
			{
 				$this->obj->ACT_msg("index.php","该订单已完成支付！"); 
			}else{
				$this->yunset("order",$order);
				$this->public_action();
				$this->yunset("comstyle","../template/member/com");
				$this->user_tpl('payment');
			}
		}
	}

}
?>