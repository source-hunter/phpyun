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
class msg_controller extends appadmin
{
	function moblielist_action()
	{
		$where="1";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (`name` LIKE '%".$keyword."%' or `cname` LIKE '%".$keyword."%' or `moblie` LIKE '%".$keyword."%')";
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
		$rows=$this->obj->DB_select_all("moblie_msg",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['moblie']=$v['moblie'];
				$list[$k]['cname']=iconv("gbk","UTF-8",$v['cname']);
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['state']=$v['state'];
				$list[$k]['status']=$v['state'];
				$list[$k]['ctime']=$v['ctime'];
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
	function mobliedel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$this->obj->DB_delete_all("moblie_msg","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("���ż�¼(ID:".$_POST['ids'].")ɾ���ɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function emaillist_action()
	{
		$where="1";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (`name` LIKE '%".$keyword."%' or `cname` LIKE '%".$keyword."%' or `email` LIKE '%".$keyword."%' or `title` LIKE '%".$keyword."%')";
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
		$rows=$this->obj->DB_select_all("email_msg",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['email']=$v['email'];
				$list[$k]['cname']=iconv("gbk","UTF-8",$v['cname']);
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['title']=iconv("gbk","UTF-8",$v['title']);
				$list[$k]['state']=$v['state'];
				$list[$k]['status']=$v['state'];
				$list[$k]['ctime']=$v['ctime'];
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
	function emaildel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$this->obj->DB_delete_all("email_msg","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("�ʼ���¼(ID:".$_POST['ids'].")ɾ���ɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function messagelist_action()
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
		if($_POST['status'])
		{
			if($_POST['status']=="1")
			{
				$where.=" and `status`='1'";
			}else{
				$where.=" and `status`='0'";
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
		$rows=$this->obj->DB_select_all("message",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['username']=iconv("gbk","UTF-8",$v['username']);
				$list[$k]['content']=iconv("gbk","UTF-8",$v['content']);
				$list[$k]['status']=$v['status'];
				$list[$k]['ctime']=$v['ctime'];
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
	function messagedel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$this->obj->DB_delete_all("message","`id` in (".$_POST['ids'].") or `keyid` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("���Է���(ID:".$_POST['ids'].")ɾ���ɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
}
?>