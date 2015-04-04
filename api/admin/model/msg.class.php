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
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function mobliedel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$this->obj->DB_delete_all("moblie_msg","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("短信记录(ID:".$_POST['ids'].")删除成功！");
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
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function emaildel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$this->obj->DB_delete_all("email_msg","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("邮件记录(ID:".$_POST['ids'].")删除成功！");
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
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function messagedel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$this->obj->DB_delete_all("message","`id` in (".$_POST['ids'].") or `keyid` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("留言反馈(ID:".$_POST['ids'].")删除成功！");
		$data['error']=1;
		echo json_encode($data);die;
	}
}
?>