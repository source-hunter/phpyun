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