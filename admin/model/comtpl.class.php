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
class comtpl_controller extends common
{

	function index_action()
	{
		$list=$this->obj->DB_select_all("company_tpl","1 order by id desc");
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_comtpl'));
	}

	function add_action()
	{
		if($_GET['id']){
			$list=$this->obj->DB_select_once("company_tpl","id='".$_GET['id']."'");
			$this->yunset("row",$list);
		}
		$this->yuntpl(array('admin/admin_comtpl_add'));
	}
	function save_action(){
		$this->comtpl_sava_action($_POST['url']);
		unset($_POST['msgconfig']);
		if($_POST['id']){
			if(is_uploaded_file($_FILES['pic']['tmp_name'])) {
					$upload=$this->upload_pic("../upload/company/");
					$pictures=$upload->picture($_FILES['pic']);
					$s_thumb=$upload->makeThumb($pictures,120,120,'_S_');
					$_POST['pic']=str_replace("../upload/company","upload/company",$s_thumb);
					$this->obj->unlink_pic($pictures);
			}
			$id=$this->obj->update_once("company_tpl",$_POST,array("id"=>$_POST['id']));
			$msg="企业模板(ID:".$_POST['id'].")更新";
		}else{
			$_POST['pic']="";
			if(!is_uploaded_file($_FILES['pic']['tmp_name'])) {
				$this->obj->ACT_layer_msg("请上传缩略图！",8,"index.php?m=comtpl&c=add");
			}else{
				$upload=$this->upload_pic("../upload/company/");
				$pictures=$upload->picture($_FILES['pic']);
				$s_thumb=$upload->makeThumb($pictures,120,120,'_S_');
				$_POST['pic']=str_replace("../upload/company","upload/company",$s_thumb);
				$this->obj->unlink_pic($pictures);
			}
			$id=$this->obj->insert_into("company_tpl",$_POST);
			$msg="企业模板(ID:".$id.")添加";
		}
		$id?$this->obj->ACT_layer_msg($msg."成功！",9,"index.php?m=comtpl",2,1):$this->obj->ACT_layer_msg($msg."失败！",8,"index.php?m=comtpl");
	}
	
	function comtpl_sava_action($url){
		
		if(!ctype_alnum($url))
		{
			$this->obj->ACT_layer_msg("目录名称只能是字母或数字！",8,$_SERVER['HTTP_REFERER'],2,1);
		}
		if(!is_dir("../template/company/".$url)){
			mkdir("../template/company/".$url,0777,true);
		}
	}
	function del_action(){
		$this->check_token();
		$del=$_GET['id'];
		if(!$del){$this->layer_msg('请先选择！',8,0,$_SERVER['HTTP_REFERER']);}
		$this->obj->DB_delete_all("company_tpl","`id`='$del'");
		$this->layer_msg("企业模板(ID".$del.")删除成功！",9,0,$_SERVER['HTTP_REFERER']);
	}
}