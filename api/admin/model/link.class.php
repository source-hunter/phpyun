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
class link_controller extends appadmin
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
			$where.=" and `link_name` LIKE '%".$keyword."%'";
		}
		if($_POST['status'])
		{
			if($_POST['status']=="1")
			{
				$where.=" and `link_state`='1'";
			}else{
				$where.=" and `link_state`='0'";
			}
		}
		if($order){
			$where.=" order by ".$order;
		}else{
			$where.=" order by `link_state` asc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_all("admin_link",$where);
		if(!empty($rows))
		{
			include(PLUS_PATH."domain_cache.php");
			foreach($rows as $k=>$v)
			{
				if(is_array($site_domain))
				{
					foreach($site_domain as $val)
					{
						if($v['domain']=='0')
						{
							$list[$k]['host']=iconv("gbk","UTF-8","ȫ����ʹ��");
						}else if($v['domain']==$val['id']){
							$list[$k]['host']=iconv("gbk","UTF-8",$val['host']);
						}
					}
				}else{
					$list[$k]['host']=iconv("gbk","UTF-8","ȫ����ʹ��");
				}
				$list[$k]['id']=$v['id'];
				$list[$k]['link_name']=iconv("gbk","UTF-8",$v['link_name']);
				$list[$k]['link_url']=$v['link_url'];
				$list[$k]['link_type']=$v['link_type'];
				$list[$k]['link_time']=$v['link_time'];
				$list[$k]['link_sorting']=$v['link_sorting'];
				$list[$k]['link_state']=$v['link_state'];
				$list[$k]['status']=$v['link_state'];
				$list[$k]['tem_type']=$v['tem_type']?$v['tem_type']:'';
				$list[$k]['pic']=$v['pic']?$v['pic']:'';
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
		$row=$this->obj->DB_select_all("admin_link","`id` in (".$_POST['ids'].") and `pic`<>''");
		if(is_array($row))
		{
			foreach($row as $v)
			{
				$this->obj->unlink_pic("../".$v['pic']);
			}
		}
		$delid=$this->obj->DB_delete_all("admin_link","`id` in (".$_POST['ids'].")","");
		$this->get_cache();
		$this->write_appadmin_log("��������(ID:".$_POST['ids'].")ɾ���ɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function add_action()
	{
		if(!$_POST['link_name']||!$_POST['link_url']||!$_POST['link_type']||!$_POST['tem_type'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$link_name=$this->stringfilter($_POST['link_name']);
		$value="`link_name`='".$link_name."',";
		$value.="`link_url`='".$_POST['link_url']."',";
		$value.="`link_type`='".$_POST['link_type']."',";
		$value.="`tem_type`='".$_POST['tem_type']."',";
		$value.="`img_type`='".$_POST['img_type']."',";
		$value.="`domain`='".$_POST['domain']."',";
		$value.="`link_sorting`='".$_POST['link_sorting']."',";
		$value.="`link_state`='1',";
		$value.="`link_time`='".mktime()."',";
		$value.="`pic`='".$_POST['pic']."'";
		$nbid=$this->obj->DB_insert_once("admin_link",$value);
		$this->get_cache();
		$this->write_appadmin_log("��������(ID:".$nbid.")��ӳɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function save_action()
	{
		if(!$_POST['link_name']||!$_POST['link_url']||!$_POST['link_type']||!$_POST['tem_type'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		$link_name=$this->stringfilter($_POST['link_name']);
		$value="`link_name`='".$link_name."',";
		$value.="`link_url`='".$_POST['link_url']."',";
		$value.="`link_type`='".$_POST['link_type']."',";
		$value.="`tem_type`='".$_POST['tem_type']."',";
		$value.="`img_type`='".$_POST['img_type']."',";
		$value.="`domain`='".$_POST['domain']."',";
		$value.="`link_sorting`='".$_POST['link_sorting']."',";
		$value.="`link_state`='1',";
		$value.="`link_time`='".mktime()."',";
		$value.="`pic`='".$_POST['pic']."'";
		
		if($_POST['id']){
			$nbid=$this->obj->DB_update_all("admin_link",$value,"`id`='".$_POST['id']."'");
			$this->write_appadmin_log("��������(ID:".$_POST['id'].")�޸ĳɹ���");
		}else{
			$data['error1']=$value; 
			$nbid=$this->obj->DB_insert_once("admin_link",$value);
			$this->write_appadmin_log("��������(ID:".$nbid.")��ӳɹ���");
		}
		$this->get_cache();
		$data['error']=1;
		echo json_encode($data);die;
	}
	function status_action()
	{
		if(!$_POST['ids']||!$_POST['status'])
		{
			$this->return_appadmin_msg(2,"��������");
		}
		if($_POST['status']=="1")
		{
			$status="1";
		}else{
			$status="0";
		}
		$update=$this->obj->DB_update_all("admin_link","`link_state`='".$status."'","id in (".$_POST['ids'].")");
		$this->get_cache();
		$this->write_appadmin_log("��������(ID:".$_POST['ids'].")������óɹ���");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function domainlist_action()
	{
		$username=$_POST['username'];
		$userlist=$this->get_appadmin_cache($data);
		$shell=$this->obj->DB_select_once("admin_user","`uid`='".$userlist[$username]['uid']."'");
		
		$where="`id` in (".$shell['domain'].")";
		$shelldomain=@explode(",",$shell['domain']);
		if(in_array(0,$shelldomain)){
			$list[0]['title']=iconv("gbk","UTF-8",'ȫ����ʹ��');
			$list[0]['id']='0';
		}
		$rows = $this->obj->DB_select_all("domain",$where,"`id`,`title`");
		foreach($rows as $k=>$v){
			$list[$k+1]['title']=iconv("gbk","UTF-8",$v['title']);
			$list[$k+1]['id']=$v['id']?$v['id']:'';
		}
		if(count($list)>0){
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
	function get_cache(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../../plus/",$this->obj);
		$makecache=$cacheclass->link_cache("link.cache.php");
	}
}
?>