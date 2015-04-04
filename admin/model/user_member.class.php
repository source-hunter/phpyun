<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2015 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class user_member_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"lotime","name"=>'最近登录',"value"=>array("1"=>"今天","3"=>"最近三天","7"=>"最近七天","15"=>"最近半月","30"=>"最近一个月"));
		$search_list[]=array("param"=>"adtime","name"=>'最近注册',"value"=>array("1"=>"今天","3"=>"最近三天","7"=>"最近七天","15"=>"最近半月","30"=>"最近一个月"));
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
		if(trim($_GET['keyword'])){
			if($_GET['type']=="1"){
				$where .=" and b.`username` LIKE '%".$_GET['keyword']."%'";
			}elseif($_GET['type']=="2"){
				$where .=" and a.`name` LIKE '%".$_GET['keyword']."%'";
			}elseif($_GET['type']=="3"){
				$where .=" and b.`email` LIKE '%".$_GET['keyword']."%'";
			}else{
				$where .=" and b.`moblie` LIKE '%".$_GET['keyword']."%'";
			}
			$page_url['keyword']=$_GET['keyword'];
			$page_url['type']=$_GET['type'];
		}
		if($_GET['adtime']){
			if($_GET['adtime']=='1'){
				$where .=" and b.`reg_date`>'".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where .=" and b.`reg_date`>'".strtotime('-'.intval($_GET['adtime']).' day')."'";
			}
			$page_url['adtime']=$_GET['adtime'];
		}
		if($_GET['lotime']){
			if($_GET['lotime']=='1'){
				$where .=" and b.`login_date`>'".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where .=" and b.`login_date`>'".strtotime('-'.intval($_GET['lotime']).' day')."'";
			}
			$page_url['lotime']=$_GET['lotime'];
		}
		if($_GET['r_uid'])
		{
			$where.=" and a.`uid`='".$_GET['r_uid']."'";
			$page_url['r_uid']=$_GET['r_uid'];
		}
		if($_GET['order'])
		{
			$where.=" order by b.".$_GET['t']." ".$_GET['order'];
			$page_url['order']=$_GET['order'];
			$page_url['t']=$_GET['t'];
		}else{
			$where.=" order by b.uid desc";
		}
		include(LIB_PATH."page3.class.php");
		$limit=$this->config['sy_listnum'];
		$page=$_GET['page']<1?1:$_GET['page'];
		$ststrsql=($page-1)*$limit;
		$page_url['page']="{{page}}";
		$pageurl=$this->url("index","user_member",$page_url);
		$count=$this->obj->DB_select_alls("resume","member","a.`uid`=b.`uid` ".$where,"a.uid");
 		$num = count($count);
 		$page = new page($page,$limit,$num,$pageurl);
		$pagenav=$page->numPage();
		$userrows=$this->obj->DB_select_alls("resume","member","a.`uid`=b.`uid` $where limit $ststrsql,$limit","a.name,a.telphone,b.*");
		$this->yunset("pagenav",$pagenav);
		$this->yunset("userrows",$userrows);
		$nav_user=$this->obj->DB_select_alls("admin_user","admin_user_group","a.`m_id`=b.`id` and a.`uid`='".$_SESSION['auid']."'");
		$power=unserialize($nav_user[0]['group_power']);
		if(in_array('141',$power)){
			$this->yunset("email_promiss", '1');
		}
		if(in_array('163',$power)){
			$this->yunset("moblie_promiss", '1');
		}
		$adtime=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$lotime=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');

		$this->yunset("adtime",$adtime);
		$this->yunset("lotime",$lotime);
		$this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_member_userlist'));
	}
	function lockinfo_action(){
		$userinfo = $this->obj->DB_select_once("member","`uid`=".$_GET['uid'],"`lock_info`");
		echo $userinfo['lock_info'];die;
	}
	function status_action()
	{
		extract($_POST);
 		$id=$this->obj->DB_update_all("member","`status`='".$_POST['status']."',`lock_info`='".$lock_info."'","`uid`='".$uid."'");
 		$this->obj->DB_update_all("resume","`r_status`='".$_POST['status']."'","`uid`='".$uid."' ");
 		if($this->config['sy_email_lock']=='1'){
			$userinfo = $this->obj->DB_select_once("member","`uid`=".$uid,"`email`,`uid`,`name`,`usertype`");
			$data=$this->forsend($userinfo);
			$this->send_msg_email(array("email"=>$userinfo['email'],'uid'=>$data['uid'],'name'=>$data['name'],"certinfo"=>$statusbody,"username"=>$userinfo['name'],"type"=>"lock"));
		}
 		$id?$this->obj->ACT_layer_msg("个人会员锁定(ID:".$uid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "设置失败！",8,$_SERVER['HTTP_REFERER']);
	}

	function edit_action(){
		if((int)$_GET['id']){
			$com_info = $this->obj->DB_select_once("member","`uid`='".$_GET['id']."'");
			$this->yunset("com_info",$com_info);
			$row=$this->obj->DB_select_once("resume","`uid`='".$_GET['id']."'");
			$this->yunset("row",$row);
			$CacheArr['user'] =array('userdata','userclass_name');
			$CacheArr['city'] =array('city_index','city_type','city_name');
			$this->CacheInclude($CacheArr);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		}
		if($_POST['com_update'])
		{
			$lasturl=str_replace("&amp;","&",$_POST['lasturl']);
			$post['uid']=$_POST['uid'];
			$post['password']=$_POST['password'];
			$post['email']=$_POST['email'];
			$post['moblie']=$_POST['moblie'];
			$post['status']=$_POST['status'];
			$post['address']=$_POST['address'];
			$nid = $this->uc_edit_pw($post,1,"index.php?m=user_member");

			$value.="`name`='".$_POST['name']."',";
			$value.="`sex`='".$_POST['sex']."',";
			$value.="`living`='".$_POST['living']."',";
			$value.="`domicile`='".$_POST['domicile']."',";
			$value.="`r_status`='".$_POST['status']."',";
			$value.="`birthday`='".$_POST['birthday']."',";
			$value.="`marriage`='".$_POST['marriage']."',";
			$value.="`height`='".$_POST['height']."',";
			$value.="`nationality`='".$_POST['nationality']."',";
			$value.="`weight`='".$_POST['weight']."',";
			$value.="`idcard`='".$_POST['idcard']."',";
			$value.="`exp`='".$_POST['exp']."',";
			$value.="`email`='".$_POST['email']."',";
			$value.="`telphone`='".$_POST['moblie']."',";
			$value.="`telhome`='".$_POST['telhome']."',";
			$value.="`edu`='".$_POST['edu']."',";
			$value.="`address`='".$_POST['address']."',";
			$value.="`homepage`='".$_POST['homepage']."',";
			$value.="`description`='".$_POST['description']."'";
			$this->obj->DB_update_all("resume",$value,"`uid`='".$_POST['uid']."'");
			$statis = $this->obj->DB_select_all("member_statis","`uid`='".$_POST['uid']."'");
			if(!is_array($statis))
			{
				$this->obj->DB_insert_once("member_statis","`uid`='".$_POST['uid']."'");
			}
			$this->obj->ACT_layer_msg( "个人会员(ID:".$_POST['uid'].")修改成功",9,$lasturl,2,1);

		}
		$this->yuntpl(array('admin/admin_member_useredit'));
	}

	function add_action()
	{
		if($_POST['submit'])
		{
			extract($_POST);
			if($username==""||strlen($username)<2||strlen($username)>15)
			{
				$msg = "会员名不能为空或不符合要求！";$msg_type=8;
			}elseif($password==""||strlen($username)<2||strlen($username)>15){
				$msg = "密码不能为空或不符合要求！";$msg_type=8;
			}else{
				if($this->config['sy_uc_type']=="uc_center"){
					$this->obj->uc_open();
					$user = uc_get_user($username);
				}else{
					$user = $this->obj->DB_select_once("member","`username`='$username'");
				}

				if(is_array($user))
				{
					$msg = "该会员已经存在！";$msg_type=8;

				}else{
					$time = time();
					$ip = $this->obj->fun_ip_get();
					if($this->config['sy_uc_type']=="uc_center")
					{
						$uid=uc_user_register($_POST['username'],$_POST['password'],$_POST['email']);
						if($uid<0)
						{
							$msg="uc_center已存在该邮箱！";$msg_type=8;
						}else{
							list($uid,$username,$email,$password,$salt)=uc_get_user($username);
							$value = "`username`='$username',`password`='$password',`email`='$email',`usertype`='1',`salt`='$salt',`moblie`='$moblie',`reg_date`='$time',`reg_ip`='$ip'";
						}
					}else{
						$salt = substr(uniqid(rand()), -6);
						$pass = md5(md5($password).$salt);
						$value = "`username`='$username',`password`='$pass',`email`='$email',`usertype`='1',`status`='$satus',`salt`='$salt',`moblie`='$moblie',`reg_date`='$time',`reg_ip`='$ip'";
					}
					$nid = $this->obj->DB_insert_once("member",$value);
					if($nid>0)
					{
						$this->obj->DB_insert_once("resume","`uid`='$nid',`email`='$email',`telphone`='$moblie'");
						$this->obj->DB_insert_once("member_statis","`uid`='$nid'");
						$this->obj->DB_insert_once("friend_info","`uid`='$nid',`nickname`='$name',`usertype`='1'");
						$msg="个人会员(ID:".$nid.")添加成功";
						$msg_type=9;
					}
				}
			}
			$this->obj->ACT_layer_msg($msg,$msg_type,"index.php?m=user_member&c=add",2,1);
			die;
		}
		$this->yuntpl(array('admin/admin_member_useradd'));
	}
	function del_action()
	{
		$this->check_token();
		
	    if($_GET['del'] && !$_GET['send_email'] && !$_GET['send_msg']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
	    			foreach($del as $k=>$v){

	    				$this->obj->delfiledir("../upload/tel/".intval($v));
	    			}
		    		$uids = @implode(",",$del);
		    		$resume=$this->obj->DB_select_all("resume","`uid` in ($uids) and `photo`<>''","`photo`,`resume_photo`");
		    		if(is_array($resume)){
		    	    	foreach($resume as $val){
		    	    		$this->obj->unlink_pic(".".$val['photo']);
		    	    		$this->obj->unlink_pic(".".$val['resume_photo']);
		    	    	}
		    	    }
		    		$friend_pic = $this->obj->DB_select_all("friend_info","`uid` in ($uids) and `pic`<>''","`pic`,`pic_big`");
		    		if(is_array($friend_pic)){
		    	    	foreach($friend_pic as $val){
		    	    		$this->obj->unlink_pic($val['pic']);
		    	    		$this->obj->unlink_pic($val['pic_big']);
		    	    	}
		    		}
		    		$show=$this->obj->DB_select_all("resume_show","`uid` in ($uids) and `picurl`<>''","`picurl`");
		    		if(is_array($show)){
		    	    	foreach($show as $val){
		    	    		$this->obj->unlink_pic(".".$val['picurl']);
		    	    	}
		    	    }
					$del_array=array("member","resume","member_statis","look_resume","look_job","resume_show","userid_msg","userid_job","resume_expect","resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume","resume_show","down_resume","userid_job","friend_info","friend_message","friend_state","question","msg","attention","company_msg");
					foreach($del_array as $value){
						$this->obj->DB_delete_all($value,"`uid` in ($uids)","");
					}
					$this->obj->DB_delete_all("atn","`uid` in ($uids) or `scid` in ($uids)","");
		    	    $this->obj->DB_delete_all("message","`fa_uid` in ($uids)","");
		    	    $this->obj->DB_delete_all("friend_reply","`fid` in ($uids)","");
		    	    $this->obj->DB_delete_all("friend_foot","`uid` in ($uids) or `fid` in ($uids)","");
		    	    $this->obj->DB_delete_all("blacklist","`p_uid` in ($uids)","");
		    	    $this->obj->DB_delete_all("friend","`uid` in ($uids) or `nid` in ($uids)","");
		    	    $this->obj->DB_delete_all("report","`p_uid` in ($uids) or `c_uid` in ($uids)","");
					$layer_type=1;

		    	}else{
					$del = intval($del);
					$uids = intval($del);
		    		$this->obj->delfiledir("../upload/tel/".$del);
		    		$resume=$this->obj->DB_select_once("resume","`uid`='".$del."' and `photo`<>''");
		    		if(is_array($resume)){
		    			$this->obj->unlink_pic('.'.$resume['photo']);
		    			$this->obj->unlink_pic(".".$resume['resume_photo']);
		    		}

		    		$friend_pic = $this->obj->DB_select_once("friend_info","`uid`='$del' and `pic`<>''");
		    		if(is_array($friend_pic)){
		    			$this->obj->unlink_pic($friend_pic['pic']);
		    			$this->obj->unlink_pic($friend_pic['pic_big']);
		    		}
		    		$show=$this->obj->DB_select_all("resume_show","`uid`='".$del."' and `picurl`<>''","`picurl`");
		    		$this->obj->unlink_pic(".".$show['picurl']);
					$del_array=array("member","resume","member_statis","look_resume","look_job","resume_show","userid_msg","userid_job","resume_expect","resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume","resume_show","down_resume","userid_job","friend_info","friend_message","friend_state","question","msg","attention","company_msg");
					foreach($del_array as $value){
						$this->obj->DB_delete_all($value,"`uid`='".$del."'","");
					}
					$this->obj->DB_delete_all("friend_foot","`uid`='$del' or `fid`='$del'","");
					$this->obj->DB_delete_all("atn","`uid`='$del' or `scid`='$del'","");
		    	    $this->obj->DB_delete_all("message","`fa_uid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend","`uid`='$del' or `nid`='$del'");
		    	    $this->obj->DB_delete_all("friend_reply","`fid`='$del'","");
		    	    $this->obj->DB_delete_all("blacklist","`p_uid`='$del'","");
		    	    $this->obj->DB_delete_all("report","`p_uid`='$del' or `c_uid`='$del'");
		    	    $layer_type=0;
		    	}
		    	$this->layer_msg( "个人会员(ID:".$uids.")删除成功！",9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg('请选择您要删除的会员！',8,0,$_SERVER['HTTP_REFERER']);

	    	}
	    }

	}

	function reset_pw_action()
	{
		$this->check_token();
		$data['password']="123456";
		$data['uid']=$_GET['uid'];
		$this->uc_edit_pw($data,1,"index.php?m=user_member");
		$this->obj->admin_log("会员(ID:".$_GET['uid'].")重置密码成功");
		echo "1";
	}

	function Imitate_action(){
		extract($_GET);
		$user_info = $this->obj->DB_select_once("member","`uid`='".$uid."'");
		$this->unset_cookie();
		$this->add_cookie($user_info['uid'],$user_info['username'],$user_info['salt'],$user_info['email'],$user_info['password'],$user_info['usertype']);

		header('Location: '.$this->config['sy_weburl'].'/member');
	}
}

?>