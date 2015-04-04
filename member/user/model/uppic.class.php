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
class uppic_controller extends user{
	function index_action(){
		if($_FILES['Filedata']){
			$upload=$this->upload_pic("../upload/user/",false,$this->config['user_pickb']);
			$picture=$upload->picture($_FILES['Filedata']);
			$pictures = @explode("/",$picture);
			echo $pic_ids = end($pictures);
			echo '<script type="text/javascript">window.parent.hideLoading();window.parent.buildAvatarEditor("'.$pic_ids.'","'.$picture.'","photo");</script>';
		}
		$this->public_action();
		$this->user_tpl('uppic');
	}
}
?>