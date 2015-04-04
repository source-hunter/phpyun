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
class sysnews_controller extends company
{
	function index_action(){

		$urlarr=array("c"=>"sysnews","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("sysmsg","`fa_uid`='".$this->uid."' order by id desc",$pageurl,"15");
		$this->obj->DB_update_all("sysmsg","`remind_status`='1'","`fa_uid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("sysmsg2",'2');
		$this->public_action();
		$this->yunset("js_def",7);
		$this->com_tpl('sysnews');
	}
	function del_action(){
		if ($_POST['del']||$_GET['id']){
			if(is_array($_POST['del'])){
				$ids=$this->pylode(',',$_POST['del']);
				$layer_type='1';
			}else if($_GET['id']){
				$ids=(int)$_GET['id'];
				$layer_type='0';
			}
			$nid=$this->obj->DB_delete_all("sysmsg","`id` in(".$ids.") AND `fa_uid`='".$this->uid."'"," ");
 			if($nid)
 			{
 				$this->unset_remind("sysmsg2",'2');
 				$this->obj->member_log("删除系统消息");
 				$this->layer_msg('删除成功！',9,$layer_type);
 			}else{
 				$this->layer_msg('删除失败！',8,$layer_type);
 			}
		}
	}
}
?>