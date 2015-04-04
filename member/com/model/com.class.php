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
			$statis['vip_time'] = date("Y年m月d日",$statis['vip_etime']);
		}else{
			$statis['vip_time'] = "已过期";
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