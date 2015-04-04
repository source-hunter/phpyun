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
class link_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"state","name"=>'审核状态',"value"=>array("1"=>"已审核","2"=>"未审核"));
		$lo_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"link","name"=>'发布时间',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
		extract($_GET);
		$where=1;
		if($state=='1'){
			$where.=" and `link_state`='1'";
			$urlarr['state']='1';
		}elseif($state=='2'){
			$urlarr['state']='2';
			$where.=" and `link_state`='0'";
		}
		if($_GET['link']){
			if($_GET['link']=='1'){
				$where.=" and `link_time` >='".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where .=" and `link_time`>'".strtotime('-'.intval($_GET['link']).' day')."'";
			}
			$urlarr['link']=$_GET['link'];
		}
		
		if($_GET['news_search']!=''){
			if ($_GET['type']=='1'){
				$where.=" and `link_type`='1'";
			}elseif ($_GET['type']=='2'){
				$where.=" and `link_type`='2'";
			}
			if($_GET['keyword'])
			{
				$where.=" AND `link_name` like '%".$_GET['keyword']."%'";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
			$urlarr['news_search']=$_GET['news_search'];
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr['page']="{{page}}";
		
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$linkrows=$this->get_page("admin_link",$where,$pageurl,$this->config['sy_listnum']);
		foreach($linkrows as $key=>$value)
		{
			$domain=$this->obj->DB_select_all("domain","1","`id`,`title`");
			if(!empty($domain))
			{
				foreach($domain as $v)
				{
					if($value['domain']==0)
					{
						$linkrows[$key]['d_title']='全站使用';
						$linkrows[$key]['d_name']='全站使用';
					}else{
						$did=@explode(",",$value['domain']);
						foreach($did as $val)
						{
							if($v['id']==$val)
							{
								$d_name[]=$v['title'];
								$linkrows[$key]['d_title']=$v['title'];
							}
						}
						$linkrows[$key]['d_name']=@implode(",",$d_name);
					}
				}
			}else{
				$linkrows[$key]['d_title']='全站使用';
				$linkrows[$key]['d_name']='全站使用';
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("linkrows",$linkrows);
		$this->yuntpl(array('admin/admin_link_list'));
	}

	function add_action()
	{
		$where=1;
		
		$shell=$this->obj->DB_select_once("admin_user","`uid`='".$_SESSION['auid']."'");
		$where="`id` in (".$shell['domain'].")";
		
		$domain = $this->obj->DB_select_all("domain",$where,"`id`,`title`");
		$this->yunset("domain",$domain);
		if($_GET['id']){
			$linkarr=$this->obj->DB_select_once("admin_link","id='".$_GET['id']."'");
			if($linkarr['domain']=="0")
			{
				$linkarr['domain_name']="全站使用";
			}else{
				$domains=@explode(",",$linkarr['domain']);
				foreach($domains as $v)
				{
					foreach($domain as $val)
					{
						if($v==$val['id'])
						{
							$domain_name[]=$val['title'];
						}
					}
				}
				$linkarr['domain_name']=@implode(",",$domain_name);
			}
			$this->yunset("linkrow",$linkarr);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		}
		$this->yuntpl(array('admin/admin_link_add'));
	}
	
	function del_action(){
		if(is_array($_POST['del'])){
			$linkid=@implode(',',$_POST['del']);
			$layer_type=1;
		}else{
			$this->check_token();
			$linkid=$_GET['id'];
			$layer_type=0;
		}
		$row=$this->obj->DB_select_all("admin_link","`id` in (".$linkid.") and `pic`<>''");
		if(is_array($row)){
			foreach($row as $v){
				$this->obj->unlink_pic("../".$v['pic']);
			}
		}
		$delid=$this->obj->DB_delete_all("admin_link","`id` in (".$linkid.")","");
		$this->get_cache();
		$delid?$this->layer_msg('友情连接(ID:'.$linkid.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
	
	function status_action(){
		extract($_POST);
		if($yesid){
		$update=$this->obj->DB_update_all("admin_link","`link_state`='".$status."'","id='".$yesid."'");
		$this->get_cache();
 		$update?$this->obj->ACT_layer_msg("友情链接审核成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("友情链接审核失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg( "非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	
	function save_action(){
		extract($_POST);
		$upload=$this->upload_pic("../upload/link/","22");
		
		if($link_add){
			if(preg_match("/[^\d-., ]/",$sorting)){
				$this->obj->ACT_layer_msg("请正确填写，排序是数字！",8,$_SERVER['HTTP_REFERER']);
			}else{
				if($sorting=="")
				{
					$sorting="0";
				}
				if($phototype==""){
					$phototype="0";
				}
				if(empty($domain))
				{
					$domain=0;
				}
				$value.="`domain`='$domain',";
				$value.="`link_name`='".trim($title)."',";
				$value.="`link_url`='$url',";
				$value.="`link_type`='$type',";
				$value.="`tem_type`='$tem_type',";
				$value.="`img_type`='$phototype',";
				$value.="`link_sorting`='$sorting',";
				$value.="`link_state`='1',";
				$value.="`link_time`='".mktime()."'";
				if($phototype==1){
					$pictures=$upload->picture($_FILES['uplocadpic']);
					$value.=",`pic`='".str_replace("../","",$pictures)."'";
				}else{
					$value.=",`pic`='".$uplocadpic."'";
				}
				$nbid=$this->obj->DB_insert_once("admin_link",$value);
				$this->get_cache();
 				isset($nbid)?$this->obj->ACT_layer_msg("友情连接(ID:".$nbid.")添加成功！",9,"index.php?m=link",2,1):$this->obj->ACT_layer_msg("添加失败！",8,"index.php?m=link");
			}
		}
		
		if($link_update){
			if(empty($domain))
			{
				$domain=0;
			}
			$value.="`domain`='$domain',";
			$value.="`link_name`='".trim($title)."',";
			$value.="`link_url`='$url',";
			$value.="`link_type`='$type',";
			$value.="`tem_type`='$tem_type',";
			$value.="`img_type`='$phototype',";
			$value.="`link_sorting`='$sorting',";
			$value.="`link_state`='1'";
			if($phototype==1){
				if($_FILES['uplocadpic']['tmp_name']!=""){
					$pictures=$upload->picture($_FILES['uplocadpic']);
					$value.=",`pic`='".str_replace("../","",$pictures)."'";
					$row=$this->obj->DB_select_once("admin_link","`id`='$id' and `pic`!=''");
					if(is_array($row)){
						$this->obj->unlink_pic("../".$row["pic"]);
					}
				}
			}else{
				$value.=",`pic`='".$uplocadpic."'";
			}
			$nbid=$this->obj->DB_update_all("admin_link",$value,"`id`='$id'");
			$lasturl=str_replace("&amp;","&",$lasturl);
			$this->get_cache();
			isset($nbid)?$this->obj->ACT_layer_msg("友情连接(ID:".$id.")修改成功！",9,$lasturl,2,1):$this->obj->ACT_layer_msg("修改失败！",8,$lasturl);
		}

	}
	function get_cache(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->link_cache("link.cache.php");
	}
}

?>