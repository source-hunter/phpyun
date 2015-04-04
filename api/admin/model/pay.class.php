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
class pay_controller extends appadmin
{
	function list_action()
	{
		$where="a.com_id=b.uid";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (a.`order_id` LIKE '%".$keyword."%' or b.`username` LIKE '%".$keyword."%')";
		}
		if($order){
			$where.=" order by a.".$order;
		}else{
			$where.=" order by a.id desc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_alls("company_pay","company",$where,"a.*,b.name as username");
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['order_id']=$v['order_id'];
				$list[$k]['pay_time']=$v['pay_time'];
				$list[$k]['order_price']=$v['order_price'];
				$list[$k]['type']=$v['type'];
				$list[$k]['username']=iconv("gbk","UTF-8",$v['username']);
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
	function del_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$this->obj->DB_delete_all("company_pay","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("���Ѽ�¼(ID:".$_POST['ids'].")ɾ���ɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
}
?>