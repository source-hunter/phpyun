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
!defined('P_W') && exit('Forbidden');


define('API_USER_USERNAME_NOT_UNIQUE', 100);

class User {

	var $base;
	var $db;

	function User($base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function getInfo($uids, $fields = array()) {
		if (!$uids) {
			return new ApiResponse(false);
		}
		require_once(R_P.'require/showimg.php');

		$uids = is_numeric($uids) ? array($uids) : explode(",",$uids);

		if (!$fields) $fields = array('uid', 'username', 'icon', 'gender', 'location', 'bday');

		$userService = L::loadClass('UserService', 'user'); 
		$users = array();
		foreach ($userService->getByUserIds($uids) as $rt) {
			list($rt['icon']) = showfacedesign($rt['icon'], 1, 'm');
			$rt_a = array();
			foreach ($fields as $field) {
				if (isset($rt[$field])) {
					$rt_a[$field] = $rt[$field];
				}
			}
			$users[$rt['uid']] = $rt_a;
		}
		return new ApiResponse($users);
	}

	function alterName($uid, $newname) {
		$userService = L::loadClass('UserService', 'user'); 
		$userName = $userService->getUserNameByUserId($uid);
		if (!$userName || $userName == $newname) {
			return new ApiResponse(1);
		}
		$existUserId = $userService->getUserIdByUserName($newname);
		if ($existUserId) {
			return new ApiResponse(API_USER_USERNAME_NOT_UNIQUE);
		}
		$userService->update($uid, array('username' => $newname));

		$user = L::loadClass('ucuser', 'user');
		$user->alterName($uid, $userName, $newname);

		return new ApiResponse(1);
	}

	function deluser($uids) {
		$user = L::loadClass('ucuser', 'user');
		$user->delUserByIds($uids);

		return new ApiResponse(1);
	}

	function synlogin($user){

		global $timestamp,$uc_key;
		list($winduid, $windid, $windpwd) = explode("\t", $this->base->strcode($user, false));

		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

		require_once ("../../data/db.config.php");
		require_once ("../../include/mysql.class.php");
		require_once ("../../include/public.function.php");
		require_once ("../../plus/config.php");
		$ip = fun_ip_get();
		$time = time();
		if($config[sy_pw_type]=="pw_center"){
			$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);
			$user_query = $db->query("SELECT * FROM $db_config[def]member WHERE `username`='$windid'");
			while($userrs = $db->fetch_array($user_query))
			{
				$userinfo = $userrs;
			}
			
			if($userinfo["uid"]>0)
			{
				if($userinfo["pw_repeat"]!="1")
				{
					
					if($userinfo["password"]==md5($windpwd.$userinfo[salt]))
					{
						$db->query("UPDATE $db_config[def]member SET `pwuid`='$winduid' WHERE `uid`='$userinfo[uid]'");
						$this->unset_cookie();
						$this->add_cookie($userinfo[uid],$userinfo[username],$userinfo[salt],$userinfo[email],$userinfo[password]);

					}else{

						
						$db->query("UPDATE $db_config[def]member SET `pw_repeat`='1' WHERE `uid`='$userinfo[uid]'");
					}
				}
			}else{
				
				$salt = substr(uniqid(rand()), -6);
				$pass = md5($windpwd.$salt);
				$db->query("INSERT INTO $db_config[def]member SET `username`='$windid',`password`='$pass',`salt`='$salt',`usertype`='1',`reg_ip`='$ip',`reg_date`='$time',`pwuid`='$winduid'");
				$uid = $db->insert_id();
				$db->query("INSERT INTO $db_config[def]resume SET `uid`='".$uid."'");
				$db->query("INSERT INTO $db_config[def]member_statis SET `uid`='".$uid."'");
				$this->unset_cookie();
				$this->add_cookie($winduid,$windid,$salt,"",$pass);
			}


		}

	}
	function add_cookie($uid,$username,$salt,$email,$pass,$usertype="1")
	{
		SetCookie("uid",$uid,time() + 86400, "/");
		SetCookie("username",$username,time() + 86400, "/");
		SetCookie("salt",$salt,time() + 86400, "/");
		SetCookie("email",$email,time() + 86400, "/");
		SetCookie("usertype",$usertype,time() + 86400, "/");
		SetCookie("shell",md5($username.$pass.$salt), time() + 86400, "/");
		$this->remind_msg($uid,$usertype);
	}
	function unset_cookie()
	{

		SetCookie("uid", "", time() - 604800, "/");
		SetCookie("username", "", time() - 604800, "/");
		SetCookie("salt", "", time() - 604800, "/");
		SetCookie("email", "", time() - 604800, "/");
		SetCookie("shell", "", time() - 604800, "/");
		SetCookie("usertype", "", time() - 604800, "/");

		SetCookie("friend1","",time() - 3600, "/");
		SetCookie("friend2","",time() - 3600, "/");
		SetCookie("friend3","",time() - 3600, "/");
		SetCookie("friend_message1","",time() - 3600, "/");
		SetCookie("friend_message2","",time() - 3600, "/");
		SetCookie("friend_message3","",time() - 3600, "/");
		SetCookie("message1","",time() - 3600, "/");
		SetCookie("message2","",time() - 3600, "/");
		SetCookie("message3","",time() - 3600, "/");
		SetCookie("userid_msg","",time() - 3600, "/");
		SetCookie("usermsg","",time() - 3600, "/");
		SetCookie("userid_job","",time() - 3600, "/");
		SetCookie("commsg","",time() - 3600, "/");
		SetCookie("userid_job3","",time() - 3600, "/");
		SetCookie("entrust","",time() - 3600, "/");
		SetCookie("commsg3","",time() - 3600, "/");
		SetCookie("remind_num","",time() - 3600, "/");
	}
	function synlogout() {

		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
   		require_once ("../../plus/config.php");
		if($config[sy_pw_type]=="pw_center")
		{
			$this->unset_cookie();
		}
	}
    function getusergroup() {
        $usergroup = array();
        $query = $this->db->query("SELECT gid,gptype,grouptitle FROM pw_usergroups ");
        while($rt= $this->db->fetch_array($query)) {
            $usergroup[$rt['gid']] = $rt;
        }
        return new ApiResponse($usergroup);
    }
	function getphpyun(){

	}
}
?>