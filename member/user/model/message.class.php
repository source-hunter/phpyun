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
class message_controller extends user{
	function index_action()
	{
		if($_POST['add_message'])
		{
			$data['content']=$_POST['content'];
			$data['uid']=$this->uid;
			$data['username']=$this->username;
			$data['ctime']=mktime();
			$nid=$this->obj->insert_into("message",$data);
			if($nid)
			{
				$this->obj->member_log("发布留言反馈");
				$this->obj->ACT_layer_msg("留言发送成功！",9,"index.php?c=seemessage");
			}else{
				$this->obj->ACT_layer_msg("留言发送失败！",8,"index.php?c=seemessage");
			}
		}
		$this->public_action();
		$this->user_tpl('message');
	}

}
?>