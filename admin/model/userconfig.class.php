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
class userconfig_controller extends common
{
	function index_action()
	{
		$this->yuntpl(array('admin/admin_user_config'));
	}
	function com_action()
	{
		$qy_rows=$this->obj->DB_select_all("company_rating","`category`=1 order by sort desc");
		$this->yunset("qy_rows",$qy_rows);
		$this->yuntpl(array('admin/admin_com_config'));
	}
	function avatar_action()
	{
		if($_POST['submit'])
		{
			$upload=$this->upload_pic("../data/logo/");
			if (is_uploaded_file($_FILES['sy_member_icon']['tmp_name'])) {
				$logo_path = $this->logo_upload($_FILES['sy_member_icon'],$upload);
				$this->logo_reset("sy_member_icon",$logo_path);
			}
			if (is_uploaded_file($_FILES['sy_unit_icon']['tmp_name'])) {
				$mlogo_path = $this->logo_upload($_FILES['sy_unit_icon'],$upload);
				$this->logo_reset("sy_unit_icon",$mlogo_path);
			}
			if (is_uploaded_file($_FILES['sy_friend_icon']['tmp_name'])) {
				$flogo_path = $this->logo_upload($_FILES['sy_friend_icon'],$upload);
				$this->logo_reset("sy_friend_icon",$flogo_path);
			}
			$this->web_config();
			$this->obj->ACT_layer_msg("会员头像配置设置成功！",9,$_SERVER['HTTP_REFERER'],2,1);
		}
		$this->yuntpl(array('admin/admin_avatar_config'));
	}

	function logo_upload($picurl,$upload){
		$pictures=$upload->picture($picurl);
		$pic = str_replace("../data/logo","data/logo",$pictures);
		return $pic;
	}
	function logo_reset($name,$value){
		$logo = $this->obj->DB_select_once("admin_config","`name`='$name'");
		if(is_array($logo)){
			$this->obj->unlink_pic("../".$logo['config']);
			$this->obj->DB_update_all("admin_config","`config`='$value'","`name`='$name'");
		}else{
			$this->obj->DB_insert_once("admin_config","`config`='$value',`name`='$name'");
		}
	}
	function save_action()
	{
		if($_POST['config']){
			unset($_POST['config']);
			foreach($_POST as $key=>$v)
			{
				$config=$this->obj->DB_select_num("admin_config","`name`='$key'");
				if($config==false)
				{
					$this->obj->DB_insert_once("admin_config","`name`='$key',`config`='".$this->stringfilter($v)."'");
				}else{
					$this->obj->DB_update_all("admin_config","`config`='".$this->stringfilter($v)."'","`name`='$key'");
				}
			}
			$this->web_config();
			$this->obj->ACT_layer_msg("会员配置修改成功！",9,1,2,1);
		}
	}
	function jihuo_action(){
		$nid=$this->obj->DB_update_all("member","email_status='1'","usertype='1'");
		echo $nid;
	}
}

?>