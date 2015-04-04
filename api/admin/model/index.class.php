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
class index_controller extends appadmin{
	function login_action(){
		$username=$_POST['username'];
		$password=$_POST['password'];
		if(!$username || !$password)
		{
			echo $username;
			$this->return_appadmin_msg(2,"用户名或密码不能为空");
		}
		$userlist=$this->get_appadmin_cache($data);
		if(is_array($userlist[$username]) && $userlist[$username]['hits']<=0){
		$this->return_appadmin_msg(2,"您已经超过登录次数，请24小时后再登录，您上次登录时间为：".date("Y-m-d H:i",$userlist[$username]['ctime']));
		}
		$row=$this->obj->DB_select_once("admin_user","`username`='".$username."'");
		if(!is_array($row)){
			$this->return_appadmin_msg(2,"用户名不存在，您还有2次登录机会");
		}
		if(!is_array($row)){
			$this->return_appadmin_msg(2,"用户名不存在");
		}
		if($row['password']!=md5($password)){
			$hits=$userlist[$username]['hits']?$userlist[$username]['hits']-1:3;
			$this->return_appadmin_msg(2,"密码错误,您还有".$hits."次机会");
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
			$this->return_appadmin_msg(1,"登录成功",$cont);
		}
	}
	function loginout_action(){
		$username=$_POST['username'];
		$userlist=$this->get_appadmin_cache($data);
		unset($userlist[$username]);
		$this->write_appadmin_cache($userlist);
		$this->return_appadmin_msg(1,"退出成功");
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