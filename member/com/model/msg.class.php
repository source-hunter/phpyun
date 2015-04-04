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
class msg_controller extends company
{
	function index_action(){
		$urlarr=array("c"=>"msg","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("msg","`job_uid`='".$this->uid."' and `del_status`<>'1' order by datetime desc",$pageurl,"15");
		if(is_array($rows)&&$rows){
			foreach($rows as $key=>$val){
				$rows[$key]['content']=strip_tags(trim($val['content']));
			}
		}
		$this->obj->DB_update_all("msg","`com_remind_status`='1'","`job_uid`='".$this->uid."' and `com_remind_status`='0'");
		$this->unset_remind("commsg",'2');
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('msg');
	}
	function del_action(){
		if($_GET['id']){
			$where['id']=(int)$_GET['id'];
			$where['job_uid']=$this->uid;
			$nid=$this->obj->update_once("msg",array("del_status"=>"1"),$where);
 			if($nid)
 			{
 				$this->unset_remind("commsg",'2');
 				$this->obj->member_log("删除求职咨询");
 				$this->layer_msg('删除成功！',9,0,"index.php?c=msg");
 			}else{
 				$this->layer_msg('删除失败！',8,0,"index.php?c=msg");
 			}
		}
	}
	function save_action(){
		if($_POST['submit']){
			$data['reply']=$_POST['reply'];
			$data['reply_time']=time();
			$data['user_remind_status']='0';
			$where['id']=(int)$_POST['id'];
			$where['job_uid']=$this->uid;
			$id=$this->obj->update_once("msg",$data,$where);
 			if($id){
 				$this->obj->member_log("回复求职咨询");
 				$this->obj->ACT_layer_msg("回复成功！",9,"index.php?c=msg");
 			}else{
 				$this->obj->ACT_layer_msg("添加失败！",8,"index.php?c=msg");
 			}
		}
	}
}
?>