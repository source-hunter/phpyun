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
class up_msg_controller extends user{
	function index_action()
	{
		$id=(int)$_POST['id'];
		$u_id=$this->obj->update_once('userid_msg',array('is_browse'=>'2'),array('id'=>$id,'uid'=>$this->uid));
		if($u_id){
			$this->unset_remind("userid_msg","1");
			echo 1;die;
		}else{
			echo 0;die;
		}
	}
}
?>