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
class log_controller extends appadmin
{
	function memberlist_action()
	{
		$where="a.uid=b.uid";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and b.`username` LIKE '%".$keyword."%'";
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
		$rows=$this->obj->DB_select_alls("member_log","member",$where,"a.*,b.username");
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['ip']=$v['ip'];
				$list[$k]['ctime']=$v['ctime'];
				$list[$k]['content']=iconv("gbk","UTF-8",$v['content']);
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
	function memberdel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$this->obj->DB_delete_all("member_log","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("��Ա��־(ID:".$_POST['ids'].")ɾ���ɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function adminlist_action()
	{
		$where="1";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and `username` LIKE '%".$keyword."%'";
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
		$rows=$this->obj->DB_select_all("admin_log",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['ctime']=$v['ctime'];
				$list[$k]['content']=iconv("gbk","UTF-8",$v['content']);
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
	function admindel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$this->obj->DB_delete_all("admin_log","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("��̨��־(ID:".$_POST['ids'].")ɾ���ɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
}
?>