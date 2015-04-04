<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class description_controller extends common{
	function index_action()
	{
		if($_GET['order'])
		{
			if($_GET['order']=="desc")
			{
				$order=" order by `".$_GET['t']."` desc";
			}else{
				$order=" order by `".$_GET['t']."` asc";
			}
		}else{
			$order=" order by `id` desc";
		}
		$descrows=$this->obj->DB_select_all("description","1 $order");
		$this->yunset("descrows",$descrows);
		
		$type=$this->obj->DB_select_all("navigation_type");
		$this->yunset("type",$type);
		$this->yuntpl(array('admin/admin_description'));
	}
	
	function add_action()
	{
		if($_GET['id']){
			$descrow=$this->obj->DB_select_once("description","`id`='".$_GET['id']."'");
		}else{
			$descrow['sort']=rand(1,50);
		}
		$this->yunset("descrow",$descrow);
		$class=$this->obj->DB_select_all("desc_class");
		$this->yunset("class",$class);
		$this->yuntpl(array('admin/admin_description_add'));
	}
	
	function save_action(){
		extract($_POST);
		$value="`name`='".$name."',";
		if($url && $is_type==1){
			
			$url = stripslashes($url);
			$url = str_replace("../","",$url);
			$url = str_replace("./","",$url);
			$p_delfiles = $this->obj->path_tidy($url);
			if($p_delfiles!=$url)
			{
				$this->obj->ACT_layer_msg("��Ч���ļ�����",8,$_SERVER['HTTP_REFERER']);
			}
			$urlArr = explode('/',$url);
			foreach($urlArr as $v)
			{
				if(!preg_match("/^[".chr(0xa1)."-".chr(0xff)." |a-z|0-9|A-Z|\@\.\_\]\[\!]+$/",$v) && $v!='') {

					$this->obj->ACT_layer_msg("��Ч���ļ�����",8,$_SERVER['HTTP_REFERER']);
				}
			}
			$urlarr=explode(".",$url);
			if(end($urlarr)!="html"){
				$this->obj->ACT_layer_msg("����ȷ��д��̬��ҳ���ƣ�",8,$_SERVER['HTTP_REFERER']);
			}
			if(substr($url,0,1)=="/"){
				$url=substr($url,1);
			}
		}
		$value.="`nid`='$nid',";
		$value.="`url`='$url',";
		$value.="`title`='$title',";
		$value.="`keyword`='$keyword',";
		$value.="`descs`='$description',";
		$value.="`top_tpl`='$top_tpl',";
		$value.="`top_tpl_dir`='$top_tpl_dir',";
		$value.="`footer_tpl`='$footer_tpl',";
		$value.="`footer_tpl_dir`='$footer_tpl_dir',";
		$value.="`ctime`='".mktime()."',";
		$value.="`sort`='$sort',";
		$value.="`is_nav`='$is_nav',";
		$value.="`is_type`='$is_type',";
		$content = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'','',''),html_entity_decode($_POST["content"],ENT_QUOTES,"GB2312"));
		$value.="`content`='".$content."'";
		if(!$id){
			$descid=$this->obj->DB_insert_once("description",$value);
			$ids=$descid;
			$alert="���";
		}else{
			$row=$this->obj->DB_select_once("description","`id`='$id'");
			if($row['is_menu']=="1")
			{
				$url = str_replace("amp;","",$url);
				$values="`url`='".$url."',";
				$values.="`furl`='".$url."',";
				$values.="`name`='".$name."'";
				$this->obj->DB_update_all("navigation",$values,"`desc`='".$id."'");
				$this->menu_cache_action();
			}
			$descid=$this->obj->DB_update_all("description",$value,"`id`='$id'");
			$ids=$id;
			$alert="����";
		}
		if($descid){
			if($is_type==1){
			$nurl=$this->config["sy_weburl"]."/index.php?m=index&c=get&id=".$ids;
			$this->obj->html("../".$url,$nurl);
			}
			$this->obj->ACT_layer_msg("����ҳ��(ID:".$ids.")".$alert."�ɹ���",9,"index.php?m=description",2,1);
		}else{
			$this->obj->ACT_layer_msg($alert."ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}
	}
	
	function del_action(){
		if(is_array($_POST['del'])){
			$linkid=@implode(',',$_POST['del']);
			foreach($del as $key=>$value){
				$info = $this->obj->DB_select_once("description","`id`='$value'");
				$filename = $info["url"];
				if(file_exists($filename)){
					@unlink($filename);
				}
				$info = "";
			}
			$layer_type='1';
		}else{
			$this->check_token();
			$linkid=$_GET["id"];
			$info = $this->obj->DB_select_once("description","`id`='$linkid'");
			$filename = APP_PATH."/".$info["url"];
			if(file_exists($filename)){
				@unlink($filename);
			}
			$layer_type='0';
		}

		$this->obj->DB_delete_all("navigation","`desc` in (".$linkid.")","");
		$this->menu_cache_action();
		$delid=$this->obj->DB_delete_all("description","`id` in ($linkid)","");
		$delid?$this->layer_msg('����ҳ��(ID:'.$linkid.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
	
	function make_action(){
		extract($_GET);
		if($id){
			$where="`id`='$id'";
		}else{
			$where="1 and is_type=1";
		}
		$rows=$this->obj->DB_select_all("description",$where);
		if(@is_array($rows)){
			foreach($rows as $row){
				$url=$this->config["sy_weburl"]."/index.php?m=index&c=get&id=".$row[id];
				$fw=$this->obj->html("../".$row["url"],$url);
			}
		}
 		$fw?$this->layer_msg("��ҳ�����ɳɹ���",9,0,$_SERVER['HTTP_REFERER']):$this->obj->layer_msg("����ʧ�ܣ�",8,0,$_SERVER['HTTP_REFERER']);
	}
	function ajax_menu_action()
	{
		if($_POST['id'])
		{
			$row=$this->obj->DB_select_once("description","`id`='".$_POST['id']."'");
			if($row['is_menu']=="1")
			{
				$info=$this->obj->DB_select_once("navigation","`desc`='".$_POST['id']."'");
				$arr['id']=$info['id'];
				$arr['nid']=$info['nid'];
				$arr['name']=iconv("gbk","utf-8",$info['name']);
				$arr['color']=$info['color'];
				$arr['url']=$info['url'];
				$arr['furl']=$info['furl'];
				$arr['type']=$info['type'];
				$arr['sort']=$info['sort'];
				$arr['eject']=$info['eject'];
				$arr['model']=$info['model'];
				$arr['bold']=$info['bold'];
				$arr['display']=$info['display'];
			}else{
				$arr['name']=iconv("gbk","utf-8",$row['name']);
				$arr['url']=$row['url'];
				$arr['furl']=$row['url'];
			}
			echo urldecode(json_encode($arr));die;
		}
	}
	function set_menu_action()
	{
	    if($_POST['submit'])
	    {
	    	if($_POST['id'])
	    	{
	    		$where=" and `id`<>'".$_POST['id']."'";
	    	}
	    	$row=$this->obj->DB_select_once("navigation","name='".$_POST['name']."' and `nid`='".$_POST['nid']."'".$where);
	    	if(!is_array($row))
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
				if($_POST['id'])
				{
					$nbid=$this->obj->DB_update_all("navigation",$value,"`id`='".$_POST['id']."'");
					$msg="���¶���ҳ�����õ���";
				}else{
					$value.=",`desc`='".$_POST['did']."'";
					$nbid=$this->obj->DB_insert_once("navigation",$value);
					$this->obj->DB_update_all("description","`is_menu`='1'","`id`='".$_POST['did']."'");
					$msg="��Ӷ���ҳ�����õ���";
				}
				$this->menu_cache_action();
				isset($nbid)?$this->obj->ACT_layer_msg( $msg."�ɹ���",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg( $msg."ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->obj->ACT_layer_msg( "�Ѿ����ڴ˵�����",8,$_SERVER['HTTP_REFERER']);
	    	}
		}
	}
	function delmenu_action()
	{
		if($_GET['id'])
		{
			$this->check_token();
			$this->obj->DB_update_all("description","`is_menu`='0'","`id`='".$_GET['id']."'");
			$this->obj->DB_delete_all("navigation","`desc`='".$_GET['id']."'");
			$this->menu_cache_action();
			$this->layer_msg("����ҳ��(ID:".$_GET['id'].")����ȡ���ɹ�",9,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('�Ƿ�����',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
	function menu_cache_action(){
		include_once(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->menu_cache("menu.cache.php");
	}
}
?>