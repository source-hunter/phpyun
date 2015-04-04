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
class navmap_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"type","name"=>'链接类型',"value"=>array("1"=>"站内链接","2"=>"原链接"));
		$search_list[]=array("param"=>"eject","name"=>'弹出窗口',"value"=>array("1"=>"新窗口","2"=>"原窗口"));
		$search_list[]=array("param"=>"display","name"=>'显示状态',"value"=>array("1"=>"是","2"=>"否"));
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$where=1;
		$this->set_search();
		if($_GET['type']){
			$where .=" and `type`='".(int)$_GET['type']."'";
			$urlarr['type']=$_GET['type'];
		}
		if($_GET['eject']){
			if($_GET['eject']=='2'){
				$where .=" and `eject`='0'";
			}else{
				$where .=" and `eject`='".(int)$_GET['eject']."'";
			}
			$urlarr['eject']=$_GET['eject'];
		}
		if($_GET['display']){
			if($_GET['display']=='2'){
				$where .=" and `display`='0'";
			}else{
				$where .=" and `display`='".(int)$_GET['display']."'";
			}
			$urlarr['display']=$_GET['display'];
		}
		if ($_GET['keyword'])
		{
			$where.=" and `name` like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$where.=" order by sort desc";
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index","".$_GET['m']."",$urlarr);
		$nav=$this->get_page("navmap",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($nav))
		{
			foreach($nav as $key=>$value)
			{
				foreach($nav as $k=>$v)
				{
					if($value['id']==$v['nid'])
					{
						$nav[$k]['typename']=$value['name'];
					}
				}
			}
		}
		$this->yunset("nav",$nav);
		$this->yuntpl(array('admin/admin_navmap'));
	}
	function add_action()
	{
		$type=$this->obj->DB_select_all("navmap","`nid`='0'","id,name");
	    $this->yunset("type",$type);
		if($_GET['id'])
		{
			$types = $this->obj->DB_select_once("navmap", "`id`='".$_GET['id']."'");
			$this->yunset("types",$types);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		}
       $this->yuntpl(array('admin/admin_navmap_add'));
	}


	function save_action()
	{
		if($_POST['submit'])
		{
			$value.="`nid`='".$_POST['nid']."',";
			$value.="`eject`='".$_POST['eject']."',";
			$value.="`display`='".$_POST['display']."',";
			$value.="`name`='".$_POST['name']."',";
			$url = str_replace("amp;","",$_POST['url']);
			$value.="`url`='".$url."',";
			$value.="`furl`='".$_POST['furl']."',";
			$value.="`sort`='".$_POST['sort']."',";
			$value.="`type`='".$_POST['type']."'";
			if($_POST['id'])
			{
				$nbid=$this->obj->DB_update_all("navmap",$value,"`id`='".$_POST['id']."'");
				$msg="更新";
			}else{
				$nbid=$this->obj->DB_insert_once("navmap",$value);
				$msg="添加";
			}
			$this->cache_action();
			isset($nbid)?$this->obj->ACT_layer_msg($msg."网站地图(ID:".$_POST['id'].")成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg($msg."失败！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function del_action()
	{
		$this->check_token();
		
	    if($_GET['del'])
	    {
	    	$del=$_GET['del'];
	    	if(is_array($del))
	    	{
	    		$del=@implode(",",$del);
	    		$layer_status=1;
	    	}else{
	    		$layer_status=0;
	    	}
			$this->obj->DB_delete_all("navmap","`id` in (".$del.") or `nid` in (".$del.")","");
			$this->cache_action();
			$this->layer_msg("网站地图(ID:".$del.")删除成功！",9,$layer_status,$_SERVER['HTTP_REFERER']);
    	}else{
			$this->layer_msg("请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
    	}
	}

	function cache_action()
	{
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->navmap_cache("navmap.cache.php");
	}
}


?>