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
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function del_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$this->obj->DB_delete_all("company_pay","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("消费记录(ID:".$_POST['ids'].")删除成功！");
		$data['error']=1;
		echo json_encode($data);die;
	}
}
?>