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
class navigation_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"type","name"=>'导航类型',"value"=>array("1"=>"站内链接","2"=>"原链接"));
		$search_list[]=array("param"=>"eject","name"=>'弹出窗口',"value"=>array("1"=>"新窗口","2"=>"原窗口"));
		$search_list[]=array("param"=>"display","name"=>'显示状态',"value"=>array("1"=>"是","2"=>"否"));
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
	
		$where=1;
		$this->set_search();
		if ($_GET['type']!=""){
			$where.=" and `type`='".$_GET['type']."'";
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
		if ($_GET['nid']!=""){
			$where.=" and `nid`='".$_GET['nid']."'";
			$urlarr['nid']=$_GET['nid'];
		}
		if($_GET['news_search']){

			if ($_GET['keyword']){
				$where.=" and `name` like '%".$_GET['keyword']."%'";
				$urlarr['keyword']=$_GET['keyword'];
			}
			$urlarr['news_search']=$_GET['news_search'];
		}

		$where.=" order by sort asc";
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index","".$_GET['m']."",$urlarr);
		$nav=$this->get_page("navigation",$where,$pageurl,$this->config['sy_listnum']);
		$navinfo=$this->obj->DB_select_all("navigation_type");
		$nclass=array();
		foreach($navinfo as $key=>$value){
			foreach($nav as $k=>$v){
				if($value['id']==$v['nid']){
					$nav[$k]['typename']=$value['typename'];
				}
			}
			$nclass[$value['id']]=$value['typename'];
		}
		$this->yunset("nclass",$nclass);
		$this->yunset("navinfo",$navinfo);
		$this->yunset("get_type", $_GET);
		$this->yunset("nav",$nav);
		$this->yuntpl(array('admin/admin_navigation_list'));
	}


	function add_action(){
		
		$type=$this->obj->DB_select_all("navigation_type");
	    $this->yunset("type",$type);
		
		if($_GET['id']){
			$group = $this->obj->DB_select_alls("navigation_type","navigation", " b.`id`='".$_GET['id']."'");
			$this->yunset("types",$group[0]);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		}
       $this->yuntpl(array('admin/admin_navigation_add'));
	}

	function save_action()
	{
	
		if($_POST['update'])
		{
			$value.="`nid`='".$_POST['nid']."',";
			$value.="`eject`='".$_POST['eject']."',";
			$value.="`display`='".$_POST['display']."',";
			$value.="`name`='".$_POST['name']."',";
			$url = str_replace("amp;","",$_POST['url']);
			$value.="`url`='".$url."',";
			$value.="`furl`='".$_POST['furl']."',";
			$value.="`sort`='".$_POST['sort']."',";
			$value.="`color`='".$_POST['color']."',";
			$value.="`model`='".$_POST['model']."',";
			$value.="`bold`='".$_POST['bold']."',";
			$value.="`type`='".$_POST['type']."'";

			$nbid=$this->obj->DB_update_all("navigation",$value,"`id`='".$_POST['id']."'");
			$this->cache_action();
			$lasturl=str_replace("&amp;","&",$_POST['lasturl']);
			isset($nbid)?$this->obj->ACT_layer_msg( "网站导航(ID:".$_POST['id'].")更新成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "更新失败！",8,$_SERVER['HTTP_REFERER']);
		}
	
	    if($_POST['add'])
	    {
	    	if(!is_array($this->obj->DB_select_once("navigation","name='".$_POST['name']."' and `nid`='".$_POST['nid']."'")))
	    	{
				$value.="`nid`='".$_POST['nid']."',";
				$value.="`eject`='".$_POST['eject']."',";
				$value.="`display`='".$_POST['display']."',";
				$value.="`name`='".$_POST['name']."',";
				$url = str_replace("amp;","",$_POST['url']);
				$value.="`url`='".$url."',";
				$value.="`furl`='".$_POST['furl']."',";
				$value.="`sort`='".$_POST['sort']."',";
				$value.="`color`='".$_POST['color']."',";
				$value.="`model`='".$_POST['model']."',";
				$value.="`bold`='".$_POST['bold']."',";
				$value.="`type`='".$_POST['type']."'";
				$nbid=$this->obj->DB_insert_once("navigation",$value);
				$this->cache_action();
				isset($nbid)?$this->obj->ACT_layer_msg( "网站导航(ID:".$nbid.")添加成功！",9,"index.php?m=navigation",2,1):$this->obj->ACT_layer_msg( "添加失败！",8,"index.php?m=navigation");
	    	}else{
				$this->obj->ACT_layer_msg( "已经存在此导航！",8,$_SERVER['HTTP_REFERER']);
	    	}
		}
	}


	function group_action(){
		$type = $this->obj->DB_select_all("navigation_type","1 order by `id` desc");
		$this->yunset("type",$type);

		$this->yuntpl(array('admin/admin_navigation_type'));
	}

	function addtype_action(){
	
        if($_POST['sub']){
			if($_POST['typename']!=""){
				if(!is_array($this->obj->DB_select_once("navigation_type","typename='".$_POST['typename']."'"))){
			       $va="`typename`='".$_POST['typename']."'";
			       $nbid=$this->obj->DB_insert_once("navigation_type",$va);
			       $this->cache_action();
			       isset($nbid)?$this->obj->ACT_layer_msg( "导航类别(ID:".$nbid.")添加成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "添加失败！",9,$_SERVER['HTTP_REFERER']);
			     }else{
				   $this->obj->ACT_layer_msg( "已经存在此类别！",8,$_SERVER['HTTP_REFERER']);
			    }
			}else{
				$this->obj->ACT_layer_msg( "请正确填写你的类别！",8,$_SERVER['HTTP_REFERER']);
		     }
        }

	 
	    if($_POST['update']){
		    $update=$this->obj->DB_update_all("navigation_type","`typename`='".$_POST['typename']."'","`id`='".$_POST['id']."'");
		    $this->cache_action();
		    isset($update)?$this->obj->ACT_layer_msg( "导航类别(ID:".$_POST['id'].")更新成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "更新失败！",8,$_SERVER['HTTP_REFERER']);
	    }
	}

	
	function del_action(){
		$this->check_token();
		
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if(is_array($del)){
				
				$rows=$this->obj->DB_select_all("navigation","`id` in (".@implode(",",$del).") and (`desc`<>'' or `news`<>'')");
				if(is_array($rows))
				{
					foreach($rows as $v)
					{
						if($v['desc']!="")
						{
							$desc[]=$v['desc'];
						}
						if($v['news']!="")
						{
							$news[]=$v['news'];
						}
						$this->obj->DB_update_all("description","`is_menu`='0'","`id` in (".@implode(",",$desc).")");
						$this->obj->DB_update_all("news_group","`is_menu`='0'","`id` in (".@implode(",",$desc).")");
					}
				}
				$this->obj->DB_delete_all("navigation","`id` in (".@implode(",",$del).")","");
				$this->cache_action();
				$this->layer_msg( "导航(ID:".@implode(',',$_GET['del']).")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	
	    if(isset($_GET['id'])){
			$where="`id`='".$_GET['id']."'";
			
			$row=$this->obj->DB_select_once("navigation", $where);
			if($row['desc']!="")
			{
				$this->obj->DB_update_all("description","`is_menu`='0'","`id`='".$row['desc']."'");
			}
			if($row['news']!="")
			{
				$this->obj->DB_update_all("news_group","`is_menu`='0'","`id`='".$row['news']."'");
			}
			$result=$this->obj->DB_delete_all("navigation", $where);
			$this->cache_action();
			isset($result)?$this->layer_msg('导航(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}

	function deltype_action(){
		$this->check_token();
	   if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("navigation_type","`id`='".$_GET['id']."'");
			
			$rows=$this->obj->DB_select_all("navigation","`nid`='".$_GET['id']."' and (`desc`<>'' or `news`<>'')");
			if(is_array($rows))
			{
				foreach($rows as $v)
				{
					if($v['desc']!="")
					{
						$desc[]=$v['desc'];
					}
					if($v['news']!="")
					{
						$news[]=$v['news'];
					}
					$this->obj->DB_update_all("description","`is_menu`='0'","`id` in (".@implode(",",$desc).")");
					$this->obj->DB_update_all("news_group","`is_menu`='0'","`id` in (".@implode(",",$desc).")");
				}
			}
			$this->obj->DB_delete_all("navigation","`nid`='".$_GET['id']."'",""); 
			$this->cache_action();
			isset($result)?$this->layer_msg('导航类别(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function cache_action(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->menu_cache("menu.cache.php");
	}
	function ajax_action()
	{
		if($_POST['name']){
			$_POST['name']=$this->stringfilter($_POST['name']);
			$this->obj->DB_update_all("navigation_type","`typename`='".$_POST['name']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("导航类别(ID:".$_POST['id'].")修改成功");
		}
		$this->cache_action();
		echo '1';die;
	}
}


?>