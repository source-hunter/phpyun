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
class company_order_controller extends common
{

	function set_search(){

		$search_list[]=array("param"=>"typezf","name"=>'支付类型',"value"=>array("alipay"=>"支付宝","tenpay"=>"财富通","bank"=>"银行转帐"));
		$search_list[]=array("param"=>"typedd","name"=>'订单类型',"value"=>array("1"=>"会员充值","2"=>"积分充值","3"=>"银行转帐","4"=>"金额充值"));
		$search_list[]=array("param"=>"order_state","name"=>'订单状态',"value"=>array("0"=>"支付失败","1"=>"等待付款","2"=>"支付成功","3"=>"等待确认"));

		$lo_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"time","name"=>'充值时间',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
	
		$this->set_search();
		$where="1";
		if($_GET['typezf']){
			$where .=" `order_type`='".$_GET['typezf!']."'";
			$urlarr['typezf']=$_GET['typezf!'];
		}
		if($_GET['typedd']){
			$where .=" and `type`='".$_GET['typedd']."'";
			$urlarr['typedd']=$_GET['typedd'];
		}
		if($_GET['news_search']){
			if ($_GET['keyword']!=""){
				if ($_GET['typeca']=='2'){
				    $where .=" and `order_remark` like '%".$_GET['keyword']."%'";
			     }elseif($_GET['typeca']=='1'){
				     $where .=" and `order_id` like '%".$_GET['keyword']."%'";
			     }
			     $urlarr['typeca']=$_GET['typeca'];
			     $urlarr['keyword']=$_GET['keyword'];
			}
			 $urlarr['news_search']=$_GET['news_search'];
		}
		if($_GET['time']){
			if($_GET['time']=='1'){
				$where.=" and `order_time` >='".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where .=" and `order_time`>'".strtotime('-'.intval($_GET['time']).' day')."'";
			}
			$urlarr['time']=$_GET['time'];
		}
		if($_GET['order_state']!=""){
            $where.=" and `order_state`='".$_GET['order_state']."'";
			$urlarr['order_state']=$_GET['order_state'];
	    }
		if($_GET['fb']!=""){
            $where.=" and `is_invoice`='".$_GET['fb']."'";
			$urlarr['fb']=$_GET['fb'];
	    }
	   
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("company_order",$where,$pageurl,$this->config['sy_listnum']);
		include (APP_PATH."/data/db.data.php");
		if(is_array($rows))
		{
			foreach($rows as $k=>$v)
			{
				$rows[$k]['order_state_n']=$arr_data['paystate'][$v['order_state']];
				$rows[$k]['order_type_n']=$arr_data['pay'][$v['order_type']];
				$classid[]=$v['uid'];
			}
			$company=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$classid).")","`uid`,`name`");
			foreach($rows as $k=>$v)
			{
				foreach($company as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$rows[$k]['comname']=$val['name'];
					}
				}
				
			}
		}
        $this->yunset("get_type", $_GET);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_company_order'));
	}
	function edit_action(){
		$id=(int)$_GET['id'];
		$row=$this->obj->DB_select_alls('member',"company_order","b.`id`='".$id."' and a.`uid`=b.`uid`","a.username,b.*");
		$this->yunset("row",$row[0]);
		$this->yuntpl(array('admin/admin_company_order_edit'));
	}
	function save_action(){
		$r_id=$this->obj->DB_update_all("company_order","`order_price`='".$_POST['order_price']."',`order_remark`='".$_POST['order_remark']."',`is_invoice`='".$_POST['is_invoice']."'","id='".$_POST['id']."'");
		isset($r_id)?$this->obj->ACT_layer_msg("充值记录(ID:".$_POST['id'].")修改成功！",9,"index.php?m=company_order",2,1):$this->obj->ACT_layer_msg("修改失败,请销后再试！",8,"index.php?m=company_order");
	}
	function setpay_action(){
		$del=(int)$_GET['id'];
		$this->check_token();
		$row=$this->obj->DB_select_once("company_order","`id`='$del'");
		if($row['order_state']=='1'||$row['order_state']=='3'){
			$nid=$this->upuser_statis($row);
			isset($nid)?$this->layer_msg("充值记录(ID:".$del.")确认成功！",9):$this->layer_msg("确认失败,请销后再试！",8);
		}else{
			$this->layer_msg("订单已确认，请勿重复操作！",8);
		}
	}
	
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if(is_array($del)){
				$company_order=$this->obj->DB_select_all("company_order","`id` in(".@implode(',',$del).")","`order_id`");
				if($company_order&&is_array($company_order)){
					foreach($company_order as $val){
						$order_ids[]=$val['order_id'];
					}

					$this->obj->DB_delete_all("invoice_record","`order_id` in(".@implode(',',$order_ids).")","");
					$this->obj->DB_delete_all("company_order","`id` in(".@implode(',',$del).")","");
				}

				$this->layer_msg( "充值记录(ID:".@implode(',',$del).")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$company_order=$this->obj->DB_select_once("company_order","`id`='".$_GET['id']."'" ,"`order_id`");
			$this->obj->DB_delete_all("invoice_record","`order_id`='".$company_order['order_id']."'" );
			$result=$this->obj->DB_delete_all("company_order","`id`='".$_GET['id']."'" );
			isset($result)?$this->layer_msg('充值记录(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>