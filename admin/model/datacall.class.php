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
class datacall_controller extends common{
	function index_action(){
		$limit=$this->config["sy_listnum"];
		$where="1";
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET["m"],$urlarr);
		$this->get_page("outside",$where." order by id desc",$pageurl,$limit);
		include CONFIG_PATH."/db.data.php";
		$this->yunset("datacall",$arr_data['datacall']);
		$this->yuntpl(array('admin/admin_datacall'));
	}
	function add_action(){
		extract($_POST);
		include CONFIG_PATH."/db.data.php";
		if($_GET[id]){
			$info = $this->obj->DB_select_once("outside","`id`='$_GET[id]'");
			$w=@explode(",",$info[where]);
			if(is_array($w)){
				foreach($w as $key=>$va){
					$arr=@explode("_",$va);
					$t[$arr[0]]=$arr[1];
				}
			}
			$this->yunset("info",$info);
			$this->yunset("where",$t);
		}
		if($submit){
			if(empty($row)){
				$value .= "`name`='".$name."',";
				$value .= "`type`='".$type."',";
				$value .= "`byorder`='".$byorder."',";
				$value .= "`code`='".$code."',";
				$value .= "`num`='".$num."',";
				$value .= "`titlelen`='".$titlelen."',";
				$value .= "`infolen`='".$infolen."',";
				$value .= "`edittime`='".$edittime."',";
				$value .= "`urltype`='".$urltype."',";
				$value .= "`timetype`='".$timetype."'";
				if(is_array($arr_data["datacall"][$type]["where"])){
					foreach($arr_data["datacall"][$type]["where"] as $key=>$va){
						$cont[]=$key."_".$$key;
					}
					$value .= ",`where`='".implode(',',$cont)."'";
				}
				include LIB_PATH."/datacall.class.php";
				$call= new datacall("../plus/data/",$this->obj);
				if($id){
					$this->obj->DB_update_all("outside",$value,"`id`='$id'");
					$call->editcache($id);
					$this->obj->ACT_layer_msg("���ݵ���(ID:".$id.")�޸ĳɹ���",9,"index.php?m=datacall",2,1);
				}else{
					$id=$this->obj->DB_insert_once("outside",$value);
					$call->editcache($id);
					$this->obj->ACT_layer_msg("���ݵ���(ID:".$id.")��ӳɹ���",9,"index.php?m=datacall",2,1);
				}
			}else{
				$this->obj->ACT_layer_msg("���������ظ������������룡",8,$_SERVER['HTTP_REFERER']);
			}
		}
		$this->yunset("datacall",$arr_data[datacall][$_GET[type]]);
		$this->yuntpl(array('admin/admin_datacall_add'));
	}
	function preview_action(){
		$src = $this->config["sy_weburl"]."/plus/outside.php?id=".$_GET["name"];
		$src = str_replace(" ","",$src);
		$this->yunset("src",$src);
		$this->yuntpl(array('admin/admin_datacall_preview'));
	}
	function del_action(){
		$this->check_token();
		if($_GET[id]){
			@unlink(PLUS_PATH."/data/".$_GET["id"].".php");
			$id=$this->obj->DB_delete_all("outside","`id`='".$_GET["id"]."'");
			$id?$this->layer_msg('���ݵ���(ID:'.$_GET["id"].')ɾ���ɹ���',9,0,"index.php?m=datacall"):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,"index.php?m=datacall");
		}
	}
	function cache_action(){
		if($_GET["aid"]==1){
			$one = $this->obj->DB_select_once("outside","1 order by id asc");
			$two = $this->obj->DB_select_once("outside","`id`>'".$one["id"]."' order by id asc");
			$this->update_cache($one["id"],$two);
		}
		if($_GET["id"]){
			$next = $this->obj->DB_select_once("outside","`id`>'".$_GET["id"]."' order by id asc");
			$this->update_cache($_GET["id"],$next);
		}else{
			$this->obj->ACT_layer_msg("������³ɹ���",9,"index.php?m=datacall",2,1);
		}
	}
	function make_action(){
		extract($_GET);
		$this->update_cache($id,''); 
	}
	function update_cache($one,$two){
		include LIB_PATH."/datacall.class.php";
		$call= new datacall("../plus/data/",$this->obj);
		$row=$call->editcache($one);
		if($two[id]!=""){
			echo "���ڸ���".$row[name]."...���Ժ�";
		}
		if($two[id]){
			echo "<script>location.href='index.php?m=datacall&c=cache&id=".$two[id]."'</script>";die;
		}else{
			$this->layer_msg('������³ɹ���',9,0,"index.php?m=datacall");
		}
	}
}
?>