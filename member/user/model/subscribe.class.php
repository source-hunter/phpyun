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
class subscribe_controller extends user{

	function index_action()
	{

		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'");
		if($resume['hy_dy']!=""){
			$resume['hylist']=@explode(",",$resume['hy_dy']);
		}
		$this->yunset("resume",$resume);
		$this->public_action();
		$this->industry_cache();
		$this->yunset("js_def",2);
		$this->user_tpl('subscribe');
	}
	function set_action(){
		if($_POST['status']!='' && $_POST['type']){
            if($_POST['type']=='msg_dy' || $_POST['type']=='email_dy'){
				if($this->config['sy_email_userdy']=='2'){
					$this->layer_msg('�ʼ����Ĺ����ѹرգ���ȴ�����Ա��ͨ��ʾ��',8,0,"index.php?c=subscribe");die;
				}else if($this->config['sy_msg_userdy']=='2'){
					$this->layer_msg('���Ŷ��Ĺ����ѹرգ���ȴ�����Ա��ͨ��ʾ��',8,0,"index.php?c=subscribe");die;
				}else{
					$this->obj->member_log("���ö���״̬");
					$nid=$this->obj->DB_update_all("resume","`$_POST[type]`='".(int)$_POST['status']."'","`uid`='".$this->uid."'");
					$nid?$this->layer_msg('���óɹ���',9,0,"index.php?c=subscribe"):$this->layer_msg('����ʧ�ܣ�',9,0,"index.php?c=subscribe");
				}
            }
		}
		if($_POST['hyid']){
			$this->obj->member_log("���ö�����ҵ���");
			$nid=$this->obj->DB_update_all("resume","`hy_dy`='".intval($_POST['hyid'])."'","`uid`='".$this->uid."'");
			$nid?$this->layer_msg('���óɹ���',9,0,"index.php?c=subscribe"):$this->layer_msg('����ʧ�ܣ�',9,0,"index.php?c=subscribe");
		}

		if($_POST['unsetid']){
			$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","hylist");
			foreach($resume['hylist'] as $v){
				if($v!=$_POST['unsetid'])
				{
					$hy[]=$v;
				}
				$hyid=@implode(",",$hy);
			}
			$this->obj->member_log("���ö�����ҵ���");
			$nid=$this->obj->DB_update_all("resume","`hy_dy`='".intval($hyid)."'","`uid`='".$this->uid."'");
			$nid?$this->layer_msg('���óɹ���',9,0,"index.php?c=subscribe"):$this->layer_msg('����ʧ�ܣ�',9,0,"index.php?c=subscribe");
		}
	}
}
?>