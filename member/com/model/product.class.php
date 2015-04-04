<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class product_controller extends company{
	function index_action(){
		$where="`uid`='".$this->uid."'";
		if(!empty($_GET['keyword'])){
			$urlarr['keyword']=$_GET['keyword'];
			$where.=" and `title` like '%".$_GET['keyword']."%'";
		}
		$urlarr['c']="product";
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("company_product",$where,$pageurl,"10","`title`,`id`,`status`,`ctime`,`statusbody`");
		$this->public_action();
		$this->yunset("js_def",2);
		$this->com_tpl("product");
	}
	function add_action(){
		$this->public_action();
		$this->yunset("js_def",2);
		$this->com_tpl("addproduct");
	}
	function save_action(){
		if($_POST['submit']){
			$sql['title']=$_POST['title'];
			$body = str_replace("&amp;","&",html_entity_decode($_POST['body'],ENT_QUOTES,"GB2312"));
			$sql['body']=$body;
			if($_FILES['pic']['tmp_name'])
			{
				$upload=$this->upload_pic("../upload/product/",false,$this->config['com_uppic']);
				$pictures=$upload->picture($_FILES['pic']);
				$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
				$sql['pic']=str_replace("../","/",$pictures);
			}
			if(!$_POST['id']){
				$sql['uid']=$this->uid;
				$sql['ctime']=mktime();
				$oid=$this->obj->insert_into("company_product",$sql);
				$msg="添加";
			}else{
				$where['uid']=$this->uid;
				$where['id']=(int)$_POST['id'];
				$sql['status']=0;
				if($_FILES['pic']['tmp_name']){
					$pictures=$upload->picture($_FILES['pic']);
					$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
					$sql['pic']=str_replace("../","/",$pictures);
					$row=$this->obj->DB_select_once("company_product","`id`='".(int)$_POST['id']."' and `uid`='".$this->uid."'","pic");
					if(is_array($row)){
						$this->obj->unlink_pic("..".$row['pic']);
					}
				}
				$oid=$this->obj->update_once("company_product",$sql,$where);
				$msg="修改";
			}
			if($oid)
			{
				$this->obj->member_log($msg."企业产品");
				$this->obj->ACT_layer_msg($msg."成功！",9,"index.php?c=product");
			}else{
				$this->obj->ACT_layer_msg($msg."失败，请稍后再试！",8,"index.php?c=product");
			}
		}
	}
	function edit_action(){
		$this->public_action();
		$editrow=$this->obj->DB_select_once("company_product","`id`='".(int)$_GET['id']."'");
		$this->yunset("editrow",$editrow);
		$this->yunset("js_def",2);
		$this->com_tpl("addproduct");
	}
	function del_action(){
		if(is_array($_GET['delid'])){
			$ids=$this->pylode(',',$_GET['delid']);
			$layer_type=1;
		}else{
			$ids=(int)$_GET['id'];
			$layer_type=0;
		}
		$row=$this->obj->DB_select_all("company_product","`id` in (".$ids.") and `uid`='".$this->uid."'","`pic`");
		if(is_array($row)){
			foreach($row as $k=>$v){
				$this->obj->unlink_pic("..".$v['pic']);
			}
		}
		$oid=$this->obj->DB_delete_all("company_product","`id` in (".$ids.") and `uid`='".$this->uid."'","");
		if($oid){
			$this->obj->member_log("删除企业产品");
			$this->layer_msg('删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>