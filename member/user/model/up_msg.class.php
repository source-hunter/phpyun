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