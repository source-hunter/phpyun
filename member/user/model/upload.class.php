<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
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
					isset($nbid)?$this->obj->ACT_layer_msg("��ӳɹ���",9,"index.php?c=resume"):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,"index.php?c=resume");
				}else{
					$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,"index.php");
				}
			}else{
				$this->obj->ACT_layer_msg("���ϴ��ĵ���",8,"index.php?c=upload&id='".intval($_POST['id'])."'");
			}
		}
		$this->yunset("js_def",2);
		$this->yunset("id",$_GET['id']);
		$this->public_action();
		$this->user_tpl('upload');
	}
}
?>