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
class comclass_controller extends common
{
	function index_action(){
		$position=$this->obj->DB_select_all("comclass","`keyid`='0'");
		$this->yunset("position",$position);
		$this->yuntpl(array('admin/admin_comclass'));

	}
	
	function save_action(){
		if(!is_array($this->obj->DB_select_once("comclass","`name`='".$_POST['position']."'"))){
			$value="`name`='".$this->stringfilter(trim($_POST['position']))."',";
			if((int)$_POST['sort']==""){
				$_POST['sort']="0";
			}
			$value.="`sort`='".trim($_POST['sort'])."',";
			if($_POST['ctype']=='1'){
				$value.="`variable`='".trim($_POST['variable'])."'";
			}else{
				$value.="`keyid`='".$_POST['nid']."'";
			}
			$add=$this->obj->DB_insert_once("comclass",$value);
			$this->cache_action();
			$add?$msg=2:$msg=3;
			$this->obj->admin_log("企业会员分类(ID:".$add.")添加成功");
		}else{
			$msg=1;
		}
		echo $msg;die;
	}

	function up_action(){
		
		if($_GET['id']){
			$id=$_GET['id'];
			$class1=$this->obj->DB_select_once("comclass","`id`='".$_GET['id']."'");
			$class2=$this->obj->DB_select_all("comclass","`keyid`='".$_GET['id']."'");
			$this->yunset("id",$id);
			$this->yunset("class1",$class1);
			$this->yunset("class2",$class2);
		}
		$position=$this->obj->DB_select_all("comclass","`keyid`='0'");
		$this->yunset("position",$position);
		$this->yuntpl(array('admin/admin_comclass'));

	}
	
	function upp_action(){
		if($_POST['update']){
			if(!empty($_POST['position'])){
				if(preg_match("/[^\d-., ]/",$_POST['sort'])){
					$this->obj->ACT_layer_msg("请正确填写，排序是数字！",8,$_SERVER['HTTP_REFERER']);
				}else{
					if($_POST['sort']){
						$value.="`sort`='".$_POST['sort']."',";
					}
					$value="`name`='".$_POST['position']."'";
					$where="`id`='".$_POST['id']."'";
					$up=$this->obj->DB_update_all("comclass",$value,$where);
					$this->cache_action();
				   $up?$this->obj->ACT_layer_msg("企业会员分类(ID:".$_POST['id'].")更新成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("更新失败，请销后再试！",8,$_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->obj->ACT_layer_msg("请正确填写你要更新的分类！",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	function del_action()
	{
		if($_GET['delid'])
		{
			$this->check_token();
			$id=$this->obj->DB_delete_all("comclass","`id`='".$_GET['delid']."' or `keyid`='".$_GET['delid']."'","");
			$this->cache_action();
		    isset($id)?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['del'])
		{
			$del=@implode(",",$_POST['del']);
			$id=$this->obj->DB_delete_all("comclass","`id` in (".$del.") or `keyid` in (".$del.")","");
			$this->cache_action();
			isset($id)?$this->layer_msg('删除成功！',9,1,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,1,$_SERVER['HTTP_REFERER']);
		}
	}
	function cache_action()
	{
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->com_cache("com.cache.php");
	}
	function ajax_action()
	{
		if($_POST['sort'])
		{
			$this->obj->DB_update_all("comclass","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
		}
		if($_POST['name'])
		{
			$_POST['name']=$this->stringfilter($_POST['name']);
			$this->obj->DB_update_all("comclass","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
		}
		$this->cache_action();
	}
}
?>