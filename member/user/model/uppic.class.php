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