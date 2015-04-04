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
class message_controller extends common{
	function index_action(){
		$this->yuntpl(array('admin/admin_user_message'));
	}

	function save_action(){
		extract($_POST);
		$uidarr=array();
		if($all==1){
			$userrows=$this->obj->DB_select_all("member","`usertype`='1'");
			if(is_array($userrows)){
				foreach($userrows as $v){
					$uidarr[]=$v["uid"];
				}
			}
		}
		if($all==2){
			$userrows=$this->obj->DB_select_all("member","`usertype`='2'");
			if(is_array($userrows)){
				foreach($userrows as $v){
					$uidarr[]=$v["uid"];
				}
			}
		}
		if($all==3){
			$useremail=@explode(',',$userarr);
			if(is_array($useremail)){
				foreach($useremail as $v){
					$userarr=$this->obj->DB_select_once("member","`username`='".$v."'");
					if(is_array($userarr)){
						$uidarr[]=$userarr["uid"];
					}
				}
			}
		}
		if(!count($uidarr)){
			$this->obj->ACT_layer_msg("没有符合条件的用户，请先检查！",8,$_SERVER['HTTP_REFERER']); 
		}
		$emailid=$this->send_message($uidarr,$message_title,$content,true);
	}
}
?>