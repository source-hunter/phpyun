<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */


define('S_DIR',dirname(__FILE__)."/");
define('P_W','admincp');
@require_once(S_DIR.'security.php');
@require_once(S_DIR.'pw_common.php');
@require_once(S_DIR.'pw_config.php');
@include_once(S_DIR.'pw_client/uc_client.php');

class PwClientAPI{
	   public $username;
       public $password;
       public $usermail;
	   public $db;
	   function __construct($username='',$password='',$usermail=''){
		   $this->username=$username;
		   $this->password=$password;
		   $this->usermail=$usermail;

		   error_reporting('E_ALL ^ E_NOTICE');
	   }
	   function register(){
		   return uc_user_register($this->username,md5($this->password),$this->usermail);
	   }
	   function get_user($userid='',$type=0){
			return uc_user_get($userid,$type);
	   }
	   function checkename(){
	       $checkuser =uc_check_username($this->username);
		   if($checkuser==1){
			   return 1;
		   }elseif($checkuser==-1){
			   return -1;
		   }else{
			   return -3;
		   }
	   }
	   function checkemail(){
		   $emailcount=uc_check_email($this->usermail);
		   if($emailcount>0){
			   return 1;
		   }elseif($emailcount==-3){
			   return -4;
		   }else{
			   return -6;
		   }
	   }
	   function logout(){
	       return uc_user_synlogout();
	   }
	   function user_login(){
           $userarr =uc_user_get($this->username,0);
		   return uc_user_check($userarr['uid'],md5(UC_KEY.md5($this->password)));
	   }
	   function login($userid){
		   $user_login=uc_user_login($userid,md5($this->password),1);
		   return $user_login['synlogin'];
	   }
	   function getavatar($userid,$type){
		   $userarr =uc_user_get($userid,1);
		   $usericonarr=explode('|',$userarr['avatar']);
		   if($usericonarr[0]=='none.gif' || empty($usericonarr[0]) || $usericonarr[1]==1){
			    if($type){
	                 $avatar='<img src="'.TURL.'member/images/no_avatar_middle.gif"/>';
                }else{
	                 $avatar='<img src="'.TURL.'member/images/no_avatar_small.gif"/>';
                }
		   }else{
			    if($usericonarr[1]==2){
					 $avatar='<img src="'.$usericonarr[0].'" />';
				}else{
					  if($type){
						   $avatar='<img src="'.UC_API.'/attachment/upload/middle/'.$usericonarr[0].'" />';
					  }else{
						   $avatar='<img src="'.UC_API.'/attachment/upload/small/'.$usericonarr[0].'" />';
					  }
				}
		   }
		   return $avatar;

	   }
	   function avatar($userid){
	       return '<p><a href="'.UC_API.'/profile.php?action=modify&info_type=face" class="my_avatar_icon" style="font-weight:bold;">[点击同步修改头像]</a></p>';
	   }
	   function send_sms($fromuid=0,$msgto=0,$subject='',$message=''){
		    $user=$this->GetMysqlOne('username'," ".$this->GetTable('member')."  WHERE uid='$fromuid'");
	        return  uc_msg_send($fromuid,$user['username'],$msgto,$subject,$message);
	   }
	   function credit_add($uid,$point){
		  $credit=array("$uid"=> array('credit'=>$point));
		  return uc_credit_add($credit);
	   }
       function user_edit($newpassword='',$ignoreoldpw=0){
		    $userarr=uc_user_get($this->username,0);
		    $checkedit=uc_user_edit($userarr['uid'],$this->username,md5($newpassword),$this->usermail);
			if($checkedit==1){
				return 1;
			}else{
				return -1;
			}
       }
	   function get_creditsettings(){
		   return false;
	   }
	   	   function feed_add($feed){
		   return false;
	   }
}
?>