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
class coupon_controller extends common
{
	function index_action()
	{
		$where="1";
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by `id` desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("coupon",$where,$pageurl,$this->config['sy_listnum']);
		$this->yuntpl(array('admin/admin_coupon'));
	}
	function save_action()
	{
		if(trim($_POST['name'])==''){
			$this->obj->ACT_layer_msg("优惠券名称不能为空！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$value="`name`='".$_POST['name']."',";
			$value.="`time`='".$_POST['time']."',";
			$value.="`amount`='".$_POST['amount']."',";
			$value.="`scope`='".$_POST['scope']."'";
			if($_POST['id'])
			{
				$oid=$this->obj->DB_update_all("coupon",$value,"`id`='".$_POST['id']."'");
				$oid?$this->obj->ACT_layer_msg("优惠券(ID:".$_POST['id'].")更新成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("保存失败！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$oid=$this->obj->DB_insert_once("coupon",$value);
				$oid?$this->obj->ACT_layer_msg("优惠券(ID:".$oid.")添加成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("保存失败！",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function gift_action()
	{
		if($_POST['submit'])
		{
			$member=$this->obj->DB_select_once("member","`username`='".$_POST['username']."'");
			if($member['uid']>0)
			{
				if($_POST['coupon']>0)
				{
					$coupon=$this->obj->DB_select_once("coupon","`id`='".$_POST['coupon']."'");
					$data.="`uid`='".$member['uid']."',";
					$data.="`number`='".time()."',";
					$data.="`ctime`='".time()."',";
					$data.="`coupon_id`='".$coupon['id']."',";
					$data.="`coupon_name`='".$coupon['name']."',";
					$validity=time()+$coupon['time']*86400;
					$data.="`validity`='".$validity."',";
					$data.="`coupon_amount`='".$coupon['amount']."',";
					$data.="`coupon_scope`='".$coupon['scope']."'";
					$this->obj->DB_insert_once("coupon_list",$data);
					$this->obj->ACT_layer_msg("赠送给“".$_POST['username']."”".$coupon['name']."成功！",9,$_SERVER['HTTP_REFERER'],2,1);
				}else{
					$this->obj->ACT_layer_msg("请选择优惠券！",8,$_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->obj->ACT_layer_msg("请正确输入用户名！",8,$_SERVER['HTTP_REFERER']);
			}
		}
		$coupon=$this->obj->DB_select_all("coupon");
		$this->yunset("coupon",$coupon);
		$this->yuntpl(array('admin/admin_coupon_gift'));
	}
	function del_action()
	{
		if($_GET['del'])
		{
			$this->check_token();
			$del=$_GET['del'];
			if(is_array($del)){
				$del=@implode(',',$del);
				$layer_type=1;
			}else{
				$layer_type=0;
			}
			$del=$this->obj->DB_delete_all("coupon","`id` in (".$del.")"," ");
			$del?$this->layer_msg('优惠券(ID:'.$del.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('请选择要删除的内容！',8);
		}
	}
}
?>