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
class payment_controller extends user{
	function index_action()
	{
		if($_COOKIE['usertype']!='1' || $this->uid=='')
		{
			$this->obj->ACT_msg("index.php","�Ƿ�������"); 
		}else{
			$order=$this->obj->DB_select_once("company_order","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' and `order_state`='1'");
			if(empty($order))
			{
 				$this->obj->ACT_msg("index.php","�ö��������֧����"); 
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