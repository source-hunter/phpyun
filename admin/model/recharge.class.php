<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2015 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class recharge_controller extends common{
	function index_action(){
		
		extract($_POST);
		if(isset($_POST['insert'])){
			$userarr=@explode(',',trim($userarr));
			if(is_array($userarr)){
				$uidarr=array();
				foreach($userarr as $k=>$v){
					$userarr=$this->obj->DB_select_once("member","`username`='".trim($v)."'");
					if(is_array($userarr)){
						$uidarr[$k]['uid']=$userarr['uid'];
						$uidarr[$k]['usertype']=$userarr['usertype'];
						$uids[]=$userarr['uid'];
					}
				}
			}
			unset($_POST['userarr']);
			unset($_POST['type']);
			unset($_POST['fs']);
			unset($_POST['price_int']);
			unset($_POST['order_price']);
			unset($_POST['insert']);
			unset($_POST['remark']);
			if($type=="integral"){
				$num=$price_int;
				$msg=$price_int.$this->config['integral_pricename'];
			}else{
				$num=$order_price;
				$msg=$order_price."元";
			}
			$fsmsg=$fs==1?"充值":"扣除";
			if(is_array($uidarr)){
				foreach($uidarr as $v){

					if($v['usertype']=="1"){
						$table="member_statis";
					}elseif($v['usertype']=="2"){
						$table="company_statis";
					}elseif($v['usertype']=="3"){
						$table="lt_statis";
					}
					if($fs==2){
						$statis=$this->obj->DB_select_once($table,"`uid`='".$v['uid']."'","pay,integral");
						if($type=="integral"){
							if($statis['integral']<$num){
								$num=$statis['integral'];
							}
						}else{
							if($statis['pay']<$num){
								$num=$statis['pay'];
							}
						}
					}
					$nid=$this->pay_member($table,$v['uid'],$type,$num,$remark,$v['usertype'],$fs);
				}
			}
			$nid?$this->obj->ACT_layer_msg("会员(ID:".@implode(',',$uids).")".$fsmsg.$msg."成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg($fsmsg."失败！",8,$_SERVER['HTTP_REFERER']);
		}
		$this->yuntpl(array('admin/admin_recharge'));
	}
		function pay_member($table,$uid="",$type="price",$num,$remark,$usertype,$fs){
		$dingdan=mktime().rand(10000,99999);

		if($fs==1){//增加
			$pay_v="`pay`=`pay`+$num";
			$all_pay_v="`pay`=`pay`+$num";
			if($table!='member_statis')
			{
				$all_pay_v.=",`all_pay`=`all_pay`+$num";
			}
			$integral_v="`integral`=`integral`+$num";
			$_POST['order_type']="adminpay";
		}else{//扣除
			$pay_v="`pay`=`pay`-$num";
			$all_pay_v="`pay`=`pay`-$num";
			if($table!='member_statis')
			{
				$all_pay_v.=",`all_pay`=`all_pay`-$num";
			}
			$integral_v="`integral`=`integral`-$num";
			$_POST['order_type']="admincut";
		}
		$_POST['order_id']=$dingdan;
		$_POST['order_price']=$num;
		$_POST['order_time']=mktime();
		$_POST['order_state']="2";
		$_POST['order_remark']=$remark;
		$_POST['uid']=$uid;
		
		if($type=="price"){
			$_POST['type']=4;
			$nid=$this->obj->DB_update_all($table,$all_pay_v,"`uid`='".$uid."'");
			
		}else{
			$_POST['type']=2;

			$nid=$this->obj->DB_update_all($table,$integral_v,"`uid`='".$uid."'");
		}
		if($fs==2)$_POST['type']=5;
		if($nid){
			$nid=$this->obj->insert_into("company_order",$_POST);
		}
		return $nid;
	}
	function ajax_viptype_action(){
		extract($_POST);
		$vip = $this->obj->DB_select_once("company_rating","`id`='$id'");
		if(is_array($vip)){
			if($vip['service_price']==""){
				$vip['service_price']="0";
			}
			echo $vip['service_price'];
		}
	}
}
?>