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
class saveresumebackground_controller extends user{

	function index_action()
    {
    	$user_expect=$this->obj->DB_select_once("resume_expect","`id`='".$_POST['eid']."'");
		if($user_expect['uid']==$this->uid&&($this->uid!='')){
    		$update=$this->obj->DB_update_all("resume_expect","`resume_diy`='".$_POST['background']."'","`id`='".intval($_POST['eid'])."'");
			echo empty($update)?1:2;
		}else{
			echo 0;
		}
    }
}
?>