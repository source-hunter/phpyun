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
class com_controller extends company
{
	function index_action()
	{
		include(CONFIG_PATH."db.data.php");
		$this->yunset("arr_data",$arr_data);
		$this->public_action();
		$statis = $this->company_satic();
		$urlarr=array("c"=>"com","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		if($statis['vip_etime']>time())
		{
			$statis['vip_time'] = date("Y��m��d��",$statis['vip_etime']);
		}else{
			$statis['vip_time'] = "�ѹ���";
		}
		$this->yunset("statis",$statis);
		$rows = $this->get_page("company_order","uid='".$this->uid."' and `type`='1' order by id desc",$pageurl,"10");
		$this->yunset("rows",$rows);
		$pay1=$this->obj->DB_select_all("company_pay","`com_id`='".$this->uid."' and `type`='1' and `order_price`<0","SUM(order_price) as allprice");
		$pay2=$this->obj->DB_select_all("company_pay","`com_id`='".$this->uid."' and `type`='2' and `order_price`<0","SUM(order_price) as allprice");
		$this->yunset("price",str_replace("-","", $pay2[0]['allprice']));
		$this->yunset("integral",str_replace("-","", $pay1[0]['allprice']));
		$this->yunset("js_def",4);
		$this->com_tpl('com');
	}
}
?>