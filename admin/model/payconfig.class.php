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
class payconfig_controller extends common
{
	function index_action(){
		$this->yunset("config",$this->config);
		$this->yuntpl(array('admin/admin_pay_config'));
	}
	function alipay_action(){
		include_once(LIB_PATH."public.function.php");
		extract($_POST);
		if($pay_config){
		   $alipaya['sy_weburl']=$this->config['sy_weburl'];
		   $alipaya['sy_alipayid']=$this->stringfilter(trim($_POST['sy_alipayid']));
		   $alipaya['alipaytype']=$this->stringfilter(trim($_POST['alipaytype']));
		   $alipaya['sy_alipaycode']=$this->stringfilter(trim($_POST['sy_alipaycode']));
		   $alipaya['sy_alipayemail']=$this->stringfilter(trim($_POST['sy_alipayemail']));
		   $alipaya['sy_alipayname']=trim($_POST['sy_alipayname']);
		   if($alipaytype=="1")
		   {
				$dir = "alipay";
		   }else{
		   		$dir = "alipaydual";
		   		$alipaya['receive_address']=$this->config['sy_webadd'];
		   		$alipaya['receive_phone']=$this->config['receive_phone'];
		   		$alipaya['receive_mobile']=$this->config['receive_mobile'];
		   }
		   $alipay_v = $this->obj->DB_select_once("admin_config","`name`='alipaytype'");
		   if(empty($alipay_v))
		   {
			 $this->obj->DB_insert_once("admin_config","`config`='$alipaytype',`name`='alipaytype'");
		   }else{
			 $this->obj->DB_update_all("admin_config","`config`='$alipaytype'","`name`='alipaytype'");
		   }
		   $this->web_config();
		   $this->obj->made_web("../api/".$dir."/alipay_data.php",ArrayToString($alipaya),"alipaydata");
		   $this->obj->ACT_layer_msg( "支付宝配置成功！",9,$_SERVER['HTTP_REFERER'],2,1);
		}
		if($this->config['alipaytype']=="1")
		{
				$dir = "alipay";
		}else{
		   		$dir = "alipaydual";
		}
		@include(APP_PATH."/api/".$dir."/alipay_data.php");
		$this->yunset("alipaydata",$alipaydata);
		$this->yuntpl(array('admin/admin_alipay_config'));
	}
	function tenpay_action(){
		extract($_POST);
		if($pay_config){
		   include(LIB_PATH."public.function.php");
 			$tenpay['sy_weburl']=$this->config['sy_weburl'];
	   		$tenpay['sy_tenpayid']=$this->stringfilter(trim($_POST['sy_tenpayid']));
	   		$tenpay['sy_tenpaycode']=$this->stringfilter(trim($_POST['sy_tenpaycode']));
		    $this->obj->made_web("../api/tenpay/tenpay_data.php",ArrayToString($tenpay),"tenpaydata");
			$this->obj->ACT_layer_msg( "财付通配置成功！",9,$_SERVER['HTTP_REFERER'],2,1);
		}
		@include(APP_PATH."/api/tenpay/tenpay_data.php");
		$this->yunset("tenpaydata",$tenpaydata);
		$this->yuntpl(array('admin/admin_tenpay_config'));
	}
	function bank_action(){
		extract($_POST);
		if($pay_bank){
			$value="`name`='$sy_bankuser',";
			$value.="`bank_name`='$sy_bankname',";
			$value.="`bank_number`='$sy_bankdnumber',";
			$value.="`bank_address`='$sy_bankdeposit'";
			if(!$bankid){
				$bank=$this->obj->DB_insert_once("bank",$value);
				$this->obj->ACT_layer_msg( "银行卡(ID:".$bankid.")添加成功！",9,"index.php?m=payconfig&c=bank",2,1);
			}else{
				$bank=$this->obj->DB_update_all("bank",$value,"`id`='$bankid'");
				$this->obj->ACT_layer_msg( "银行卡(ID:".$bankid.")修改成功！",9,"index.php?m=payconfig&c=bank",2,1);
			}
		}
		if($_GET['id']){
			$bankone=$this->obj->DB_select_once("bank","id='".$_GET['id']."'");
			$this->yunset("bankone",$bankone);
		}
		$bankrows=$this->obj->DB_select_all("bank");
		$this->yunset("bankrows",$bankrows);
		$this->yuntpl(array('admin/admin_bank_config'));
	}
	function save_action(){
		if($_POST['config']){
			unset($_POST['config']);
			foreach($_POST as $key=>$v){
				$config=$this->obj->DB_select_num("admin_config","`name`='".$key."'");
				if($config==false){
					$this->obj->DB_insert_once("admin_config","`name`='".$key."',`config`='".$this->stringfilter($v)."'");
				}else{
					$this->obj->DB_update_all("admin_config","`config`='".$this->stringfilter($v)."'","`name`='".$key."'");
				}
			}
			$this->web_config();
			$this->obj->ACT_layer_msg( "修改成功！",9,$_SERVER['HTTP_REFERER'],2,1);
		}
	}
	function del_action(){
		$this->check_token();
		$this->obj->DB_delete_all("bank","`id`='".$_GET['id']."'");
		$this->layer_msg( "银行卡(ID:".$_GET['id'].")删除成功！",9,0,"index.php?m=payconfig&c=bank");
	}
}
?>