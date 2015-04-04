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
class config_controller extends common{
	function index_action(){
		$this->yunset("config",$this->config);
		$this->yuntpl(array('admin/admin_web_config'));
	}
	function save_logo_action(){
		$upload=$this->upload_pic("../data/logo/");
		if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
			$logo_path = $this->logo_upload($_FILES['logo'],$upload);
			$this->logo_reset("sy_logo",$logo_path);
		}
		if (is_uploaded_file($_FILES['member_logo']['tmp_name'])) {
			$mlogo_path = $this->logo_upload($_FILES['member_logo'],$upload);
			$this->logo_reset("sy_member_logo",$mlogo_path);
		}
	
		if (is_uploaded_file($_FILES['friend_logo']['tmp_name'])) {
			$flogo_path = $this->logo_upload($_FILES['friend_logo'],$upload);
			$this->logo_reset("sy_friend_logo",$flogo_path);
		}
		if (is_uploaded_file($_FILES['unit_logo']['tmp_name'])) {
			$ulogo_path = $this->logo_upload($_FILES['unit_logo'],$upload);
			$this->logo_reset("sy_unit_logo",$ulogo_path);
		}
		if (is_uploaded_file($_FILES['rz_logo']['tmp_name'])) {
			$rzlogo_path = $this->logo_upload($_FILES['rz_logo'],$upload);
			$this->logo_reset("sy_rz_logo",$rzlogo_path);
		}

		
		if (is_uploaded_file($_FILES['wap_logo']['tmp_name'])) {
			$waplogo_path = $this->logo_upload($_FILES['wap_logo'],$upload);
			$this->logo_reset("sy_wap_logo",$waplogo_path);
		}
		$this->web_config();
		$this->obj->ACT_layer_msg("��վLOGO�������óɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
	}
	function logo_upload($picurl,$upload){
		$pictures=$upload->picture($picurl);
		$pic = str_replace("../data/logo","data/logo",$pictures);
		return $pic;
	}
	function logo_reset($name,$value){
		$logo = $this->obj->DB_select_once("admin_config","`name`='".$name."'");
		if(is_array($logo)){
			$this->obj->unlink_pic("../".$logo['config']);
			$this->obj->DB_update_all("admin_config","`config`='".$value."'","`name`='".$name."'");
		}else{
			$this->obj->DB_insert_once("admin_config","`config`='".$value."',`name`='".$name."'");
		}
	}

	function save_action(){
 		if($_POST['config']){
			unset($_POST['config']);
			foreach($_POST as $key=>$v){
		    	$config=$this->obj->DB_select_num("admin_config","`name`='$key'");
			   if($config==false){
				$this->obj->DB_insert_once("admin_config","`name`='$key',`config`='".$this->stringfilter($v)."'");
			   }else{
				$this->obj->DB_update_all("admin_config","`config`='".$this->stringfilter($v)."'","`name`='$key'");
			   }
		 	}
			$this->web_config();
			$this->layer_msg("��վ�������óɹ���",9,1);
		 }
	}
}
?>