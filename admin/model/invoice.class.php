<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class invoice_controller extends common
{
	
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'发票状态',"value"=>array("0"=>"未审核","1"=>"已审核","2"=>"未通过","3"=>"已打印","4"=>"已邮寄"));
		
		$lo_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"time","name"=>'申请时间',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$list=$this->obj->DB_select_all("invoice_record");
		foreach($list as $v)
		{
			$row=$this->obj->DB_select_once("company_order","`order_id`='".$v['order_id']."'");
			$this->obj->DB_update_all("invoice_record","`oid`='".$row['id']."'","`order_id`='".$v['order_id']."'");
		}

		$this->set_search();
		$where=1;
		if($_GET['keyword'])
		{
			$where.=" and `order_id` like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['status']!="")
		{
			$where.=" and `status`='".$_GET['status']."'";
			$urlarr['status']=$_GET['status'];
		}
		if($_GET['time']){
			if($_GET['time']=='1'){
				$where.=" and `addtime` >='".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where .=" and `addtime`>'".strtotime('-'.intval($_GET['time']).' day')."'";
			}
			$urlarr['time']=$_GET['time'];
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("invoice_record",$where,$pageurl,$this->config['sy_listnum']);
		include (APP_PATH."/data/db.data.php");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				if($v['oid']!="")
				{
					$oid[]=$v['oid'];
				}
				$uid[]=$v['uid'];
			}
			$order=$this->obj->DB_select_all("company_order","`id` in (".@implode(",",$oid).")","order_state,order_price,id");
			$company=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$uid).")","`uid`,`name`");
			$member=array_merge($company,$lt);
			foreach($rows as $k=>$v)
			{
				foreach($order as $val)
				{
					if($v['oid']==$val['id'])
					{
						$rows[$k]['order_state']=$arr_data['paystate'][$val['order_state']];
						$rows[$k]['order_price']=$val['order_price'];
					}
				}
				foreach($member as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$rows[$k]['comname']=$val['name'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_invoice'));
	}
	function status_action()
	{
		$nid=$this->obj->DB_update_all("invoice_record","`status`='".$_POST['status']."'","`id` in (".$_POST['pid'].")");
		$nid?$this->obj->ACT_layer_msg("发票状态设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("发票状态设置失败！",8,$_SERVER['HTTP_REFERER']);
	}

	function edit_action()
	{
		$invoice=$this->obj->DB_select_once("invoice_record","`id`='".$_GET['id']."'");
		$this->yunset("invoice",$invoice);
		$row=$this->obj->DB_select_once("company_order","`order_id`='".$invoice['order_id']."'","order_price,order_state");
		include (APP_PATH."/data/db.data.php");
		$row['order_state_n']=$arr_data['paystate'][$row['order_state']];
		$this->yunset("row",$row);
		$this->yuntpl(array('admin/admin_invoice_show'));
	}
}
?>