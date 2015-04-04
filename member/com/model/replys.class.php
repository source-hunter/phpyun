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
class replys_controller extends company
{ 
	function index_action(){
		if($_GET['id']){
			$urlarr=array("c"=>"replys","id"=>$_GET['id'],"page"=>"{{page}}");
			$pageurl=$this->url("index","index",$urlarr);
			$this->get_page("message","`keyid`='".(int)$_GET['id']."' or `id`='".(int)$_GET['id']."' order by `id` desc",$pageurl,"10");
			$this->yunset("id",$_GET['id']);
			$this->public_action();
			$this->yunset("js_def",7);
			$this->com_tpl('replys');
		}else{
			$this->obj->ACT_layer_msg("该信息不存在！",9,"index.php?c=seemessage");
		}
	}
	function save_action(){
		if($_POST['replys_message']){
			$user_message = $this->obj->DB_select_once("message","(`fa_uid`='".$this->uid."' or `uid`='".$this->uid."') AND `id`='".(int)$_POST['keyid']."'");
			if(empty($user_message)){
 				$this->obj->ACT_layer_msg("该信息不存在！",9,"index.php?c=seemessage");
			}
			$data['keyid']=$_POST['keyid'];
			$data['content']=$_POST['content'];
			$data['fa_uid']=$this->uid;
			$data['username']=$this->username;
			$data['ctime']=mktime();
			$nid=$this->obj->insert_into("message",$data);
			$where['id']=(int)$_POST['keyid'];
			$where['fa_uid']=$this->uid;
			$this->obj->update_once("message",array("status"=>"1"),$where);
 			if($nid)
 			{
 				$this->obj->member_log("回复留言信息");
 				$this->obj->ACT_layer_msg("回复成功！",9,$_SERVER['HTTP_REFERER']);
 			}else{
 				$this->obj->ACT_layer_msg("添加失败！",8,$_SERVER['HTTP_REFERER']);
 			}
		}
	}
}
?>