<?php
/*
 * Created on 2012
 * Link for shyflc@qq.com
 * This System Powered by PHPYUN.com
 */
class admin_company_rating_controller extends common
{
	function index_action()
	{
		$list=$this->obj->DB_select_all("company_rating","`category`='1'");
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_company_rating'));
	}
	function rating_action()
	{
		if($_GET['id'])
		{
			$row=$this->obj->DB_select_once("company_rating","`id`='".$_GET['id']."'");
			$this->yunset("row",$row);
		}
		$coupon=$this->obj->DB_select_all("coupon");
		$this->yunset("coupon",$coupon);
		$this->yuntpl(array('admin/admin_comclass_add'));
	}
	function saveclass_action()
	{
		if($_POST['useradd'])
		{
			$id=$_POST['id'];
			unset($_POST['useradd']);
			unset($_POST['id']);
			if(is_uploaded_file($_FILES['com_pic']['tmp_name'])){
				$upload=$this->upload_pic("../upload/compic/");
				$pictures=$upload->picture($_FILES['com_pic']);
				$pic = str_replace("../upload","upload",$pictures);
			}
			$_POST['time_start']=strtotime($_POST['time_start']);
			$_POST['time_end']=strtotime($_POST['time_end']);
			if(!$id){
				$_POST['com_pic']=$pic;
				$nid=$this->obj->insert_into("company_rating",$_POST);
				$name="企业会员等级（ID：".$nid."）添加";
			}else{
				if($pic!=""){$_POST['com_pic']=$pic;};
				$where['id']=$id;
				$nid=$this->obj->update_once("company_rating",$_POST,$where);
				$name="企业会员等级（ID：".$id."）更新";
			}
		}
		$nid?$this->obj->ACT_layer_msg($name."成功！",9,"index.php?m=admin_company_rating",2,1):$this->obj->ACT_layer_msg($name."失败！",8,"index.php?m=admin_company_rating");
	}
	function del_action()
	{
		$this->check_token();
		$nid=$this->obj->DB_delete_all("company_rating","`id`='".$_GET['id']."'");
 		$nid?$this->layer_msg('企业会员等级（ID：'.$_GET['id'].'）删除成功！',9):$this->layer_msg('删除失败！',8);
	}
}

?>