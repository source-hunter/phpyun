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
class order_controller extends appadmin
{
	function list_action()
	{
		$where="1";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (`order_id` LIKE '%".$keyword."%' or `order_remark` LIKE '%".$keyword."%')";
		}
		if($_POST['status'])
		{
			if($_POST['status']=="4")
			{
				$where.=" and `order_state`='0'";
			}else{
				$where.=" and `order_state` in('".(int)$_POST['status']."')";
			}
	    }
		if($order){
			$where.=" order by ".$order;
		}else{
			$where.=" order by id desc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_all("company_order",$where);
		if(!empty($rows))
		{
			include (APP_PATH."/data/db.data.php");
			foreach($rows as $k=>$v)
			{
				$uid[]=$v['uid'];
				$company=$this->obj->DB_select_all("company","`uid` in (".$this->pylode(",",$uid).")","`uid`,`name`");
				$lt=$this->obj->DB_select_all("lt_info","`uid` in (".$this->pylode(",",$uid).")","`uid`,`realname`");
				$member=$this->obj->DB_select_all("member","`uid` in (".$this->pylode(",",$uid).")","`uid`,`username`");
				foreach($rows as $key=>$val)
				{
					foreach($member as $value)
					{
						if($val['uid']==$value['uid'])
						{
							$list[$k]['name']=iconv("gbk","UTF-8",$value['username']);
						}
					}
					foreach($company as $value)
					{
						if($val['uid']==$value['uid'])
						{
							$list[$k]['name']=iconv("gbk","UTF-8",$value['name']);
						}
					}
					foreach($lt as $value)
					{
						if($val['uid']==$value['uid'])
						{
							$list[$k]['name']=iconv("gbk","UTF-8",$value['realname']);
						}
					}
				}
				$list[$k]['order_state']=$v['order_state'];
				$list[$k]['status']=$v['order_state'];
				$list[$k]['order_type']=iconv("gbk","UTF-8",$arr_data['pay'][$v['order_type']]);
				$list[$k]['id']=$v['id'];
				$list[$k]['order_id']=$v['order_id'];
				$list[$k]['type']=$v['type'];
				$list[$k]['order_time']=$v['order_time'];
				$list[$k]['order_price']=$v['order_price'];
				$list[$k]['is_invoice']=$v['is_invoice'];
			}
			$data['error']=1;
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"û�л����Ϣ");
		}
	}
	function show_action()
	{
		if(!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$id=(int)$_POST['id'];
		$row=$this->obj->DB_select_once("company_order","`id`='".$id."'");
		if(!empty($row))
		{
			$member=$this->obj->DB_select_once("member","`uid`='".$row['uid']."'","username");
			include (APP_PATH."/data/db.data.php");
			$list['id']		=$row['id'];
			$list['order_type']	=iconv("gbk","UTF-8",$arr_data['pay'][$row['order_type']]);
			$list['order_id']	=$row['order_id'];
			$list['order_price']=$row['order_price'];
			$list['order_time']	=$row['order_time'];
			$list['is_invoice']=$row['is_invoice'];
			$list['order_remark']=iconv("gbk","UTF-8",$row['order_remark']);
			$list['username']=iconv("gbk","UTF-8",$member['username']);
			$list['uid']=$row['uid'];
			$list['type']=$row['type'];
			$list['order_state']=$row['order_state'];
			$list['status']=$row['order_state'];
			if($row['is_invoice']=="1")
			{
				$invoice=$this->obj->DB_select_once("invoice_record","`order_id`='".$row['order_id']."'");
				$list['inid']	=$invoice['id'];
				$list['title']	=$invoice['title'];
				$list['link_man']=iconv("gbk","UTF-8",$invoice['link_man']);
				$list['link_moblie']	=$invoice['link_moblie'];
				$list['address']=iconv("gbk","UTF-8",$invoice['address']);
				$list['addtime']=$invoice['addtime'];
				$list['status']=$invoice['status'];
			}
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"û�л����Ϣ");
		}
	}
	function save_action()
	{
		if(!$_POST['order_price']||!$_POST['order_remark']||!(isset($_POST['is_invoice']))||!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$values="`order_price`='".$_POST['order_price']."',";
		$values.="`order_remark`='".$this->stringfilter($_POST['order_remark'])."',";
		$values.="`is_invoice`='".$_POST['is_invoice']."'";
		$r_id=$this->obj->DB_update_all("company_order",$values,"id='".(int)$_POST['id']."'");
		if($_POST['is_invoice']=='0')
		{
			$this->obj->DB_delete_all("invoice_record","`id`='".(int)$_POST['inid']."'");
		}else{
			$value="`title`='".$this->stringfilter($_POST['title'])."',";
			$value.="`link_man`='".$this->stringfilter($_POST['link_man'])."',";
			$value.="`link_moblie`='".$_POST['link_moblie']."',";
			$value.="`address`='".$this->stringfilter($_POST['address'])."',";
			if((int)$_POST['inid'])
			{
				$value.="`lasttime`='".time()."',";
				$value.="`status`='".$_POST['status']."'";
				$this->obj->DB_update_all('invoice_record',$value,"`id`='".(int)$_POST['inid']."'");
			}else{
				$value.="`order_id`='".$_POST['order_id']."',";
				$value.="`uid`='".$_POST['uid']."',";
				$value.="`status`='0',";
				$value.="`addtime`='".time()."'";
				$this->obj->DB_insert_once("invoice_record",$value);
			}
		}
		$this->write_appadmin_log("��ֵ��¼(ID:".$_POST['id'].")�޸ĳɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function setpay_action()
	{
		if(!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$del=(int)$_POST['id'];
		$row=$this->obj->DB_select_once("company_order","`id`='".$del."'");
		if($row['order_state']=='1'||$row['order_state']=='3')
		{
			$nid=$this->upuser_statis($row);
			if($nid)
			{
				$this->write_appadmin_log("��ֵ��¼(ID:".$del.")ȷ�ϳɹ���");
				$data['error']=1;
				echo json_encode($data);die;
			}else{
				$this->return_appadmin_msg(2,"ȷ��ʧ��,���������ԣ�");
			}
		}else{
			$this->return_appadmin_msg(2,"������ȷ�ϣ������ظ�������");
		}
	}
	function del_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$company_order=$this->obj->DB_select_all("company_order","`id` in (".$_POST['ids'].")","`order_id`");
		if($company_order&&is_array($company_order))
		{
			foreach($company_order as $val)
			{
				$order_ids[]=$val['order_id'];
			}
			$this->obj->DB_delete_all("invoice_record","`order_id` in(".$this->pylode(',',$order_ids).")","");
			$this->obj->DB_delete_all("company_order","`id` in(".$_POST['ids'].")","");
		}
		$this->write_appadmin_log("��ֵ��¼(ID:".$_POST['ids'].")ɾ���ɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
}
?>