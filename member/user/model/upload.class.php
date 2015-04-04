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
class upload_controller extends user{
	function index_action(){
	  if($_POST['submit']){
            if($_POST['url']){
				$userurl=$this->obj->DB_select_once("resume_expect","`uid`='".$this->uid."' and `id`='".intval($_POST['id'])."'");
				if(is_array($userurl) && !empty($userurl))
				{
					if($userurl['works_upload'])
					{
						@unlink(APP_PATH.$userurl['works_upload']);
					}
					$url=str_replace($this->config['sy_weburl'],"",$_POST['url']);
					$nbid=$this->obj->DB_update_all("resume_expect","`works_upload`='".$url."'","`uid`='".$this->uid."' AND `id`='".intval($_POST['id'])."'");
					isset($nbid)?$this->obj->ACT_layer_msg("添加成功！",9,"index.php?c=resume"):$this->obj->ACT_layer_msg("添加失败！",8,"index.php?c=resume");
				}else{
					$this->obj->ACT_layer_msg("操作失败！",8,"index.php");
				}
			}else{
				$this->obj->ACT_layer_msg("请上传文档！",8,"index.php?c=upload&id='".intval($_POST['id'])."'");
			}
		}
		$this->yunset("js_def",2);
		$this->yunset("id",$_GET['id']);
		$this->public_action();
		$this->user_tpl('upload');
	}
}
?>