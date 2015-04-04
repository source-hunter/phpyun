<?php
define('IN_DISCUZ', TRUE);
define('UC_CLIENT_VERSION', '1.5.0');	
define('UC_CLIENT_RELEASE', '20081031');
define('API_DELETEUSER', 1);		
define('API_RENAMEUSER', 1);		
define('API_GETTAG', 1);		
define('API_SYNLOGIN', 1);		
define('API_SYNLOGOUT', 1);		
define('API_UPDATEPW', 1);		
define('API_UPDATEBADWORDS', 1);	
define('API_UPDATEHOSTS', 1);		
define('API_UPDATEAPPS', 1);		
define('API_UPDATECLIENT', 1);		
define('API_UPDATECREDIT', 1);		
define('API_GETCREDITSETTINGS', 1);	
define('API_GETCREDIT', 1);		
define('API_UPDATECREDITSETTINGS', 1);	
define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

define('DISCUZ_ROOT', '');


if(!defined('IN_UC')) {
	error_reporting(0);
	set_magic_quotes_runtime(0);

	defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	require_once 'config.inc.php';
	$_DCACHE = $get = $post = array();

	$code = @$_GET['code'];

	parse_str(_authcode($code, 'DECODE', UC_KEY), $get);

	if(MAGIC_QUOTES_GPC) {
		$get = _stripslashes($get);
	}
	$timestamp = time();
	if($timestamp - $get['time'] > 3600) {
		exit('Authracation has expiried');
	}
	if(empty($get)) {
		exit('Invalid Request');
	}
	$action = $get['action'];
	require_once 'uc_client/lib/xml.class.php';
	$post = xml_unserialize(file_get_contents('php://input'));

	if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcreditsettings', 'updatecreditsettings'))) {
		require_once 'include/db_mysql.class.php';
		$db_uc = new dbstuff;
		include("config.inc.php");
		$db_uc->connect(UC_DBHOST,UC_DBUSER,UC_DBPW,UC_DBNAME,UC_DBCONNECT);

		if($get[username])
		{
			$db_uc->query("SET NAMES gbk");
			$query=$db_uc->query("SELECT * FROM ".UC_DBTABLEPRE."members WHERE `username`='$get[username]'");
			while($uc_rs = $db_uc->fetch_array($query))
			{
				$uc_info[] = $uc_rs;
			}

			$get['salt'] = $uc_info[0]['salt'];
			$get['email'] = $uc_info[0]['email'];
			$get['oldpass'] = $uc_info[0]['password'];
		}

		$uc_note = new uc_note();
		exit($uc_note->$get['action']($get, $post));
	} else {
		exit(API_RETURN_FAILED);
	}


} else {

	require_once 'config.inc.php';
	require_once 'include/db_mysql.class.php';
	$GLOBALS['db'] = new dbstuff;
	$GLOBALS['db']->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
	$GLOBALS['tablepre'] = $tablepre;
	unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
}

class uc_note {

	var $dbconfig = '';
	var $db = '';
	var $tablepre = '';
	var $appdir = '';

	function _serialize($arr, $htmlon = 0) {
		if(!function_exists('xml_serialize')) {
			include_once 'uc_client/lib/xml.class.php';
		}
		return xml_serialize($arr, $htmlon);
	}

	function uc_note() {
		$this->appdir = substr(dirname(__FILE__), 0, -3);
		$this->dbconfig = $this->appdir.'api/uc/config.inc.php';
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];

	}

	function test($get, $post) {
		return API_RETURN_SUCCEED;
	}

	function deleteuser($get, $post) {
		$uids = $get['ids'];
		!API_DELETEUSER && exit(API_RETURN_FORBIDDEN);

		return API_RETURN_SUCCEED;
	}

	function renameuser($get, $post) {
		$uid = $get['uid'];
		$usernameold = $get['oldusername'];
		$usernamenew = $get['newusername'];
		if(!API_RENAMEUSER) {
			return API_RETURN_FORBIDDEN;
		}

		return API_RETURN_SUCCEED;
	}

	function gettag($get, $post) {
		$name = $get['id'];
		if(!API_GETTAG) {
			return API_RETURN_FORBIDDEN;
		}

		$return = array();
		return $this->_serialize($return, 1);
	}

	function synlogin($get, $post) {

		if(!API_SYNLOGIN) {
			return API_RETURN_FORBIDDEN;
		}

		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');


		require_once ("../../data/db.config.php");
		require_once ("../../include/mysql.class.php");
		require_once ("../../include/public.function.php");
		require_once ("../../plus/config.php");
		$ip = fun_ip_get();
		$time = time();
		if($config[sy_uc_type]=="uc_center"){
			$db = new mysql($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname'], ALL_PS, $db_config['charset']);
			$user_query = $db->query("SELECT * FROM $db_config[def]member WHERE `username`='$get[username]'");
			while($userrs = $db->fetch_array($user_query))
			{
				$userinfo[] = $userrs;
			}
			if($config[sy_onedomain]!=""){
				$weburl=str_replace("http://www","",$config[sy_onedomain]);
			}elseif($config[sy_indexdomain]!=""){
				$weburl=str_replace("http://www","",$config[sy_indexdomain]);
			}else{
				$weburl=str_replace("http://www","",$config[sy_weburl]);
			}
			if(is_array($userinfo))
			{
				$uid = $userinfo[0][uid];
				$certokquery=$db->query("SELECT * FROM $db_config[def]company_cert where `uid`='$uid ' and `type`='1'");
				while($certrow = $db->fetch_array($certokquery))
				{
					$certstatus = $certrow[status];
				}
				

				if($userinfo[0][username]==$get[username]&&$userinfo[0][name_repeat]!="1")
				{
					$this->unset_cookie($weburl);

					if($userinfo[0][password]==$get[password])
					{
						if($certstatus=="1" || $config[user_status]!="1" || $userinfo[0][usertype]=="2")
						{
							$this->add_cookie($weburl,$userinfo[0][uid],$userinfo[0][username],$userinfo[0][salt],$userinfo[0][email],$userinfo[0][password],$userinfo[0][usertype]);

						}

					}else{

						$db->query("UPDATE $db_config[def]member SET `password`='$get[password]',`email`='$get[email]',`salt`='$get[salt]' WHERE `uid`='$uid'");
						if($certstatus=="1" || $config[user_status]!="1")
						{
						  $this->add_cookie($weburl,$userinfo[0][uid],$userinfo[0][username],$get[salt],$get[email],$get[password],$userinfo[0][usertype]);
						}
					}
					$db->query("UPDATE $db_config[def]member SET `login_ip`='$ip',`login_date`='$time',`login_hits`=`login_hits`+1 where `uid`='$uid'");
				}
			}else{
				$db->query("INSERT INTO $db_config[def]member SET `username`='$get[username]',`password`='$get[password]',`email`='$get[email]',`salt`='$get[salt]',`usertype`='1',`reg_ip`='$ip',`reg_date`='$time'");
				$uid = $db->insert_id();
				$db->query("INSERT INTO $db_config[def]resume SET `uid`='".$uid."'");
				$db->query("INSERT INTO $db_config[def]member_statis SET `uid`='".$uid."'");
				$randstr=rand(10000000,99999999);
				$db->query("INSERT INTO $db_config[def]company_cert SET `status`='0',`step`='1',`check`='$get[email]',`check2`='$randstr',`ctime`='".mktime()."',`type`='1',`uid`='".$uid."'");

				$this->unset_cookie($weburl);
				if($config[user_status]!="1"){$this->add_cookie($weburl,$uid,$get[username],$get[salt],$get[email],$get[password]);}

		}
		}

	}
	function add_cookie($weburl,$uid,$username,$salt,$email,$pass,$usertype="1")
	{
		include ("../../plus/config.php");
		if($config[sy_web_site]=="1"){
			SetCookie("uid",$uid,time() + 86400,"/",$weburl);
			SetCookie("username",$username,time() + 86400,"/",$weburl);
			SetCookie("salt",$salt,time() + 86400,"/",$weburl);
			SetCookie("email",$email,time() + 86400,"/",$weburl);
			SetCookie("shell",md5($username.$pass.$salt), time() + 86400,"/",$weburl);
			setCookie("usertype",$usertype,time()+86400,"/",$weburl);
		}else{
			SetCookie("uid",$uid,time() + 86400,"/");
			SetCookie("username",$username,time() + 86400,"/");
			SetCookie("salt",$salt,time() + 86400,"/");
			SetCookie("email",$email,time() + 86400,"/");
			SetCookie("shell",md5($username.$pass.$salt), time() + 86400,"/");
			setCookie("usertype",$usertype,time()+86400,"/");
		}
		$this->remind_msg($uid,$usertype);
	}
	function unset_cookie($weburl)
	{
		include ("../../plus/config.php");
		if($config[sy_web_site]=="1"){
			SetCookie("uid", "", time() - 604800, "/",$weburl);
			SetCookie("username", "", time() - 604800, "/",$weburl);
			SetCookie("salt", "", time() - 604800, "/",$weburl);
			SetCookie("email", "", time() - 604800, "/",$weburl);
			SetCookie("shell", "", time() - 604800, "/",$weburl);
			SetCookie("usertype", "", time() - 604800, "/",$weburl);

			SetCookie("friend1","",time() - 3600, "/",$weburl);
			SetCookie("friend2","",time() - 3600, "/",$weburl);
			SetCookie("friend3","",time() - 3600, "/",$weburl);
			SetCookie("friend_message1","",time() - 3600, "/",$weburl);
			SetCookie("friend_message2","",time() - 3600, "/",$weburl);
			SetCookie("friend_message3","",time() - 3600, "/",$weburl);
			SetCookie("message1","",time() - 3600, "/",$weburl);
			SetCookie("message2","",time() - 3600, "/",$weburl);
			SetCookie("message3","",time() - 3600, "/",$weburl);
			SetCookie("userid_msg","",time() - 3600, "/",$weburl);
			SetCookie("usermsg","",time() - 3600, "/",$weburl);
			SetCookie("userid_job","",time() - 3600, "/",$weburl);
			SetCookie("commsg","",time() - 3600, "/");
			SetCookie("userid_job3","",time() - 3600, "/",$weburl);
			SetCookie("entrust","",time() - 3600, "/",$weburl);
			SetCookie("commsg3","",time() - 3600, "/",$weburl);
			SetCookie("remind_num","",time() - 3600, "/",$weburl);
		}else{
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
	}
	function synlogout($get, $post) {
		if(!API_SYNLOGOUT) {
			return API_RETURN_FORBIDDEN;
		}
		include ("../../plus/config.php");
		if($config[sy_onedomain]!=""){
			$weburl=str_replace("http://www","",$config[sy_onedomain]);
		}elseif($config[sy_indexdomain]!=""){
			$weburl=str_replace("http://www","",$config[sy_indexdomain]);
		}else{
			$weburl=str_replace("http://www","",$config[sy_weburl]);
		}
		if($config[sy_uc_type]=="uc_center")
		{
			$this->unset_cookie($weburl);
		}
	
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		_setcookie('Example_auth', '', -86400 * 365);
	}

	function updatepw($get, $post) {
		if(!API_UPDATEPW) {
			return API_RETURN_FORBIDDEN;
		}
		$username = $get['username'];
		$password = $get['password'];

		return API_RETURN_SUCCEED;
	}

	function updatebadwords($get, $post) {
		if(!API_UPDATEBADWORDS) {
			return API_RETURN_FORBIDDEN;
		}
		$cachefile = $this->appdir.'api/uc/uc_client/data/cache/badwords.php';
		$fp = fopen($cachefile, 'w');
		$data = array();
		if(is_array($post)) {
			foreach($post as $k => $v) {
				$data['findpattern'][$k] = $v['findpattern'];
				$data['replace'][$k] = $v['replacement'];
			}
		}
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'badwords\'] = '.var_export($data, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		return API_RETURN_SUCCEED;
	}

	function updatehosts($get, $post) {
		if(!API_UPDATEHOSTS) {
			return API_RETURN_FORBIDDEN;
		}
		$cachefile = $this->appdir.'api/uc/uc_client/data/cache/hosts.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'hosts\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		return API_RETURN_SUCCEED;
	}

	function updateapps($get, $post) {
		if(!API_UPDATEAPPS) {
			return API_RETURN_FORBIDDEN;
		}
		$UC_API = $post['UC_API'];

	
		$cachefile = $this->appdir.'api/uc/uc_client/data/cache/apps.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'apps\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);

	
		if(is_writeable($this->appdir.'api/uc/config.inc.php')) {
			$configfile = trim(file_get_contents($this->appdir.'api/uc/config.inc.php'));
			$configfile = substr($configfile, -2) == '?>' ? substr($configfile, 0, -2) : $configfile;
			$configfile = preg_replace("/define\('UC_API',\s*'.*?'\);/i", "define('UC_API', '$UC_API');", $configfile);
			if($fp = @fopen($this->appdir.'api/uc/config.inc.php', 'w')) {
				@fwrite($fp, trim($configfile));
				@fclose($fp);
			}
		}

		return API_RETURN_SUCCEED;
	}

	function updateclient($get, $post) {
		if(!API_UPDATECLIENT) {
			return API_RETURN_FORBIDDEN;
		}
		$cachefile = $this->appdir.'api/uc/uc_client/data/cache/settings.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'settings\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		return API_RETURN_SUCCEED;
	}

	function updatecredit($get, $post) {
		if(!API_UPDATECREDIT) {
			return API_RETURN_FORBIDDEN;
		}
		$credit = $get['credit'];
		$amount = $get['amount'];
		$uid = $get['uid'];
		return API_RETURN_SUCCEED;
	}

	function getcredit($get, $post) {
		if(!API_GETCREDIT) {
			return API_RETURN_FORBIDDEN;
		}
	}

	function getcreditsettings($get, $post) {
		if(!API_GETCREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}
		$credits = array();
		return $this->_serialize($credits);
	}

	function updatecreditsettings($get, $post) {
		if(!API_UPDATECREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}
		return API_RETURN_SUCCEED;
	}
}


function _setcookie($var, $value, $life = 0, $prefix = 1) {
	global $cookiepre, $cookiedomain, $cookiepath, $timestamp, $_SERVER;
	setcookie(($prefix ? $cookiepre : '').$var, $value,
		$life ? $timestamp + $life : 0, $cookiepath,
		$cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
				return '';
			}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function _stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = _stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}