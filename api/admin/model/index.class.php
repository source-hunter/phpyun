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
class index_controller extends appadmin{
	function login_action(){
		$username=$_POST['username'];
		$password=$_POST['password'];
		if(!$username || !$password)
		{
			echo $username;
			$this->return_appadmin_msg(2,"�û��������벻��Ϊ��");
		}
		$userlist=$this->get_appadmin_cache($data);
		if(is_array($userlist[$username]) && $userlist[$username]['hits']<=0){
		$this->return_appadmin_msg(2,"���Ѿ�������¼��������24Сʱ���ٵ�¼�����ϴε�¼ʱ��Ϊ��".date("Y-m-d H:i",$userlist[$username]['ctime']));
		}
		$row=$this->obj->DB_select_once("admin_user","`username`='".$username."'");
		if(!is_array($row)){
			$this->return_appadmin_msg(2,"�û��������ڣ�������2�ε�¼����");
		}
		if(!is_array($row)){
			$this->return_appadmin_msg(2,"�û���������");
		}
		if($row['password']!=md5($password)){
			$hits=$userlist[$username]['hits']?$userlist[$username]['hits']-1:3;
			$this->return_appadmin_msg(2,"�������,������".$hits."�λ���");
			$data_user[$username]['ctime']=mktime();
			$data_user[$username]['status']=2;
			$data_user[$username]['hits']=$hits;
			$this->write_appadmin_cache($data_user);
		}else{
			$mktime=time();
			$tokey=md5($mktime.$row['password']);
			unset($userlist[$tokey]);
			$userlist[$username]['ctime']=$mktime;
			$userlist[$username]['actiontime']=$mktime;
			$userlist[$username]['username']=$username;
			$userlist[$username]['uid']=$row['uid'];
			$userlist[$username]['status']=1;
			$userlist[$username]['hits']=3;
			$userlist[$username]['tokey']=$tokey;
			$this->write_appadmin_cache($userlist);
			$this->obj->DB_update_all("admin_user","`lasttime`='".time()."'","`uid`='".$row['uid']."'");
			$cont['tokey']=$tokey;
			$this->return_appadmin_msg(1,"��¼�ɹ�",$cont);
		}
	}
	function loginout_action(){
		$username=$_POST['username'];
		$userlist=$this->get_appadmin_cache($data);
		unset($userlist[$username]);
		$this->write_appadmin_cache($userlist);
		$this->return_appadmin_msg(1,"�˳��ɹ�");
	}
	function getall_action()
	{
		$today=strtotime(date("Y-m-d"));
		$yesterday=$today-86400;
		$list['com_num_now']=$this->obj->DB_select_num("member","usertype='2' and reg_date>'".$today."'");
		$list['com_num']=$this->obj->DB_select_num("member","usertype='2' and reg_date<'".$today."' and reg_date>'".$yesterday."'");
		$list['job_num_now']=$this->obj->DB_select_num("company_job","sdate>'".$today."'");
		$list['job_num']=$this->obj->DB_select_num("company_job","sdate<'".$today."' and sdate>'".$yesterday."'");
		$list['user_num_now']=$this->obj->DB_select_num("member","usertype='1' and reg_date>'".$today."'");
		$list['user_num']=$this->obj->DB_select_num("member","usertype='1' and reg_date<'".$today."' and reg_date>'".$yesterday."'");
		$list['resume_num_now']=$this->obj->DB_select_num("resume_expect","ctime>'".$today."'");
		$list['resume_num']=$this->obj->DB_select_num("resume_expect","ctime<'".$today."' and ctime>'".$yesterday."'");
		$list['job_num_dsh']=$this->obj->DB_select_num("company_job","state='0'");
		$list['link_num_dsh']=$this->obj->DB_select_num("admin_link","link_state='0'");
		$list['msg_num']=$this->obj->DB_select_num("company_order","`keyid`='0' or `status`='0'");
		$list['order_num_dsh']=$this->obj->DB_select_num("company_order","`order_state`='3' or `order_state`='1'");
		$list['comcert_num_dsh']=$this->obj->DB_select_num("company_cert","`type`='3' and `status`='0'");
		$list['usercert_num_dsh']=$this->obj->DB_select_num("resume","`idcard_pic`<>'' and `idcard_status`='0'");
		foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
		$data['list']=$list;
		$data['error']=1;
		echo json_encode($data);die;
	}
}
?>