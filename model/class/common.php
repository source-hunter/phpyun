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
class common
{
	public $tpl='';
	public $db='';
	public $obj='';
	public $config = '';
	public $uid="";
	public $data="";
	public $username="";

	function common($tpl,$db,$def="",$model="index",$m="") {

		global $config;
		$this->config=$config;
		$this->tpl=$tpl;
		$this->db=$db;
		$this->username=$_COOKIE['username'];
		$this->def=$def;
		$this->m=$m;
		require(MODEL_PATH.'class/action.class.php');
		$this->obj= new action($this->db,$tpl,$def);

		if($_COOKIE['uid'])
		{
			$shell=$this->obj->GET_user_shell($_COOKIE['uid'],$_COOKIE['shell']);
			if(!is_array($shell) || empty($shell)){
				$this->unset_cookie();
				$this->uid='';
			}else{
				$this->uid=intval($_COOKIE['uid']);
				$this->yunset("uid",intval($_COOKIE['uid']));
				$this->yunset("usertype",intval($_COOKIE['usertype']));
			}
		}else{
			$this->uid='';
		}
		if(!file_exists(PLUS_PATH."config.php")){
			$this->web_config();

			$this->advertise_cache();
		}else{
			$this->config = $config;
		}

		$this->yunset("style",$this->config['sy_weburl']."/template/".$this->config['style']);
		$this->yunset("client",$this->config['sy_weburl']."/about");
		$this->yunset("tplstyle",APP_PATH."template/".$this->config['style']);
		$this->yunset("tpldir",$this->tpl->template_dir);
		$this->yunset("askstyle",APP_PATH."template/ask");
		$this->yunset("ask_style",$this->config['sy_weburl']."/template/ask");
		$this->yunset("friendstyle",APP_PATH."template/friend/default");
		$this->yunset("friend_style",$this->config['sy_weburl']."/template/friend/default");
		$this->yunset("adminstyle",APP_PATH."template/admin");
		$this->yunset("wap_style",$this->config['sy_weburl']."/template/wap");
		$this->yunset("userstyle","../template/member/user");
		$this->yunset("wapstyle",APP_PATH."template/wap");
		$this->remind_msg($_COOKIE['uid'],$_COOKIE['usertype']);
		$this->yunset("cookie",$_COOKIE);

		if(!($this->config['sy_wapdomain'])){
			$this->yunset("config_wapdomain",$this->config['sy_weburl'].'/'.$this->config['sy_wapdir']);
		}else{
			$this->config['sy_wapdomain'] = "http://".$this->config['sy_wapdomain'];
			$this->yunset("config_wapdomain",$this->config['sy_wapdomain']);
		}
		$this->$model();

		$this->yunset("config",$this->config);
		$this->cron();
		$this->job_auto();
		$this->subscribe();

	}
	function DoException(){
		$this->obj->ACT_msg("index.php","您请求的页面不存在！");
	}

	function yuntpl($tplarr=array()){
		if(is_array($tplarr) && $tplarr!=''){
			foreach($tplarr as $v){
				$this->tpl->display($v.".htm");
			}
		}else{
			echo "模版不能为空！";die;
		}
	}

	function yun_tpl($tplarr=array()){
		if(is_array($tplarr) && $tplarr!=''){
			foreach($tplarr as $v){
				$rand=mktime();
				$this->tpl->display($this->config['style']."/".$this->m."/".$v.".htm");
			}
		}else{
			echo "模版不能为空！";die;
		}
	}
	function integrated()
	{
		$city_area =$this->obj->DB_select_all("city_class","`keyid`='0'");
		return $city_area;
	}
	function add_cookie($uid,$username,$salt,$email,$pass,$type,$expire="1"){
		include_once(LIB_PATH."public.function.php");
		if($this->config['sy_onedomain']!=""){
			$weburl=get_domain($this->config['sy_onedomain']);
		}elseif($this->config['sy_indexdomain']!=""){
			$weburl=get_domain($this->config['sy_indexdomain']);
		}else{
			$weburl=get_domain($this->config['sy_weburl']);
		}
		if($expire=='7'){
			$expire_date=7*86400;
		}else{
			$expire_date=86400;
		}
		if($this->config['sy_web_site']=="1"){
			SetCookie("uid",$uid,time() + $expire_date,"/",$weburl);
			SetCookie("username",$username,time() + $expire_date,"/",$weburl);
			SetCookie("salt",$salt,time() + $expire_date,"/",$weburl);
			SetCookie("email",$email,time() + $expire_date,"/",$weburl);
			SetCookie("shell",md5($username.$pass.$salt), time() + $expire_date,"/",$weburl);
			SetCookie("usertype",$type,time()+$expire_date,"/",$weburl);
		}else{
			SetCookie("uid",$uid,time() + $expire_date,"/");
			SetCookie("username",$username,time() + $expire_date,"/");
			SetCookie("salt",$salt,time() + $expire_date,"/");
			SetCookie("email",$email,time() + $expire_date,"/");
			SetCookie("shell",md5($username.$pass.$salt), time() + $expire_date,"/");
			SetCookie("usertype",$type,time()+$expire_date,"/");
		}
		$this->remind_msg($uid,$type,$weburl);
	}
	function remind_msg($uid,$usertype,$weburl='')
	{

		$num=0;
		if($_COOKIE['friend'.$usertype]==""){
			$friend=$this->obj->DB_select_num("friend","`nid`='".$uid."' and `status`='0'");
			if($this->config['sy_web_site']=="1"){
				$this->msgcookie("friend".$usertype,$friend,time()+3600,"/",$weburl);
			}else{
				$this->msgcookie("friend".$usertype,$friend,time()+3600,"/");
			}
		}

		if($_COOKIE['friend_message'.$usertype]==""){
			$friend_message=$this->obj->DB_select_num("friend_message","`fid`='".$uid."' and `remind_status`='0'");
			if($this->config['sy_web_site']=="1"){
				$this->msgcookie("friend_message".$usertype,$friend_message,time()+3600,"/",$weburl);
			}else{
				$this->msgcookie("friend_message".$usertype,$friend_message,time()+3600,"/");
			}
		}

		if($_COOKIE['sysmsg'.$usertype]==""){
			$message=$this->obj->DB_select_num("sysmsg","`fa_uid`='".$uid."' and `remind_status`='0'");
			if($this->config['sy_web_site']=="1"){
				$this->msgcookie("sysmsg".$usertype,$message,time()+3600,"/",$weburl);
			}else{
				$this->msgcookie("sysmsg".$usertype,$message,time()+3600,"/");
			}
		}
		if($usertype=="1")
		{
			if($_COOKIE['userid_msg']==""){
				$userid_msg=$this->obj->DB_select_num("userid_msg","`uid`='".$uid."' and `is_browse`='1'");
				if($this->config['sy_web_site']=="1"){
					$this->msgcookie("userid_msg",$userid_msg,time()+3600,"/",$weburl);
				}else{
					$this->msgcookie("userid_msg",$userid_msg,time()+3600,"/");
				}
			}
			if($_COOKIE['usermsg']==""){
				$msg=$this->obj->DB_select_num("msg","`uid`='".$uid."' and `user_remind_status`='0'");
				if($this->config['sy_web_site']=="1"){
					$this->msgcookie("usermsg",$msg,time()+3600,"/",$weburl);
				}else{
					$this->msgcookie("usermsg",$msg,time()+3600,"/");
				}
			}
		}elseif($usertype=="2"){
			if($_COOKIE['userid_job']==""){
				$userid_job=$this->obj->DB_select_num("userid_job","`com_id`='".$uid."' and `is_browse`='1'");
				if($this->config['sy_web_site']=="1"){
					$this->msgcookie("userid_job",$userid_job,time()+3600,"/",$weburl);
				}else{
					$this->msgcookie("userid_job",$userid_job,time()+3600,"/");
				}
			}
			if($_COOKIE['commsg']==""){
				$msg=$this->obj->DB_select_num("msg","`job_uid`='".$uid."' and `com_remind_status`='0'");
				if($this->config['sy_web_site']=="1"){
					$this->msgcookie("commsg",$msg,time()+3600,"/",$weburl);
				}else{
					$this->msgcookie("commsg",$msg,time()+3600,"/");
				}
			}
		}
		$num=$num+$_COOKIE['friend'.$usertype];
		$num=$num+$_COOKIE['friend_message'.$usertype];
		$num=$num+$_COOKIE['sysmsg'.$usertype];
		if($usertype==1){
			$num=$num+$_COOKIE['userid_msg'];
			$num=$num+$_COOKIE['usermsg'];
		}elseif($usertype==2){
			$num=$num+$_COOKIE['commsg'];
			$num=$num+$_COOKIE['userid_job'];
		}elseif($usertype==3){
			$num=$num+$_COOKIE['userid_job3'];
			$num=$num+$_COOKIE['entrust'];
			$num=$num+$_COOKIE['commsg3'];
		}
		if($this->config['sy_web_site']=="1"){
			$this->msgcookie("remind_num",$num,time()+3600,"/",$weburl);
		}else{
			$this->msgcookie("remind_num",$num,time()+3600,"/");
		}

		$time=(time()-strtotime(date("Y-m-d")))%1800;
		if($time=="0")
		{
			if($this->config['sy_web_site']=="1"){
				SetCookie("friend".$usertype,"",time() - 3600, "/",$weburl);
				SetCookie("friend_message".$usertype,"",time() - 3600, "/",$weburl);
				SetCookie("sysmsg".$usertype,"",time() - 3600, "/",$weburl);
				SetCookie("userid_msg","",time() - 3600, "/",$weburl);
				SetCookie("usermsg","",time() - 3600, "/",$weburl);
				SetCookie("userid_job","",time() - 3600, "/",$weburl);
				SetCookie("commsg","",time() - 3600, "/");
				SetCookie("remind_num","",time() - 3600, "/",$weburl);
			}else{
				SetCookie("friend".$usertype,"",time() - 3600, "/");
				SetCookie("friend_message".$usertype,"",time() - 3600, "/");
				SetCookie("sysmsg".$usertype,"",time() - 3600, "/");
				SetCookie("userid_msg","",time() - 3600, "/");
				SetCookie("usermsg","",time() - 3600, "/");
				SetCookie("userid_job","",time() - 3600, "/");
				SetCookie("commsg","",time() - 3600, "/");
				SetCookie("remind_num","",time() - 3600, "/");
			}
		}
	}
	function msgcookie($var, $value = '', $time = 0, $path = '', $domain = '', $s = false)
	{
		$_COOKIE[$var] = $value;
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				setcookie($var . '[' . $k . ']', $v, $time, $path, $domain, $s);
			}
		} else {
			setcookie($var, $value, $time, $path, $domain, $s);
		}
	}
	function unset_remind($cooke,$usertype){
		if($this->config['sy_onedomain']!=""){
			$weburl=str_replace("http://www","",$this->config['sy_onedomain']);
		}elseif($this->config['sy_indexdomain']!=""){
			$weburl=str_replace("http://www","",$this->config['sy_indexdomain']);
		}else{
			$weburl=str_replace("http://www","",$this->config['sy_weburl']);
		}
		if($this->config['sy_web_site']=="1"){
			SetCookie($cooke,"",time() - 3600, "/",$weburl);
		}else{
			SetCookie($cooke,"",time() - 3600, "/");
		}
		$this->remind_msg($this->uid,$usertype);
	}

	function uc_edit_pw($post,$old="1",$url)
	{
		$old_info = $this->obj->DB_select_once("member","`uid`='".$post['uid']."'","`name_repeat`,`username`");
		if($post['password']!="")
		{
			if($this->config['sy_uc_type']=="uc_center" &&$old_info['name_repeat']!="1")
			{
				$this->obj->uc_open();
				$ucresult = uc_user_edit($old_info['username'], $post['oldpw'], $post['password'], $post['email'],$old);
				if($ucresult == -1)
				{
					$msg= '旧密码不正确';
				} elseif($ucresult == -4) {
					$msg= 'Email 格式有误';
				} elseif($ucresult == -5) {
					$msg= 'Email 不允许注册';
				} elseif($ucresult == -6) {
					$msg= '该 Email 已经被注册';
				}
				if($msg!="")
				{
					$this->obj->ACT_msg($url, $msg = $msg);
				}
			}else{
				$salt = substr(uniqid(rand()), -6);
				$pass = md5(md5($post['password']).$salt);
				$where="`password`='$pass',`salt`='$salt',";
			}
		}
			if(is_array($post))
			{
				foreach($post as $k=>$v)
				{
					if($k!="password"&&$k!="oldpw")
					{
						$where.="`$k`='$v',";
					}
				}
				$where.="`username`='".$old_info['username']."'";
			}
			$nid = $this->obj->DB_update_all("member",$where,"`uid`='".$post['uid']."'");
			return $nid;
	}
	function unset_cookie()
	{
		if($this->config['sy_onedomain']!=""){
			$weburl=str_replace("http://www","",$this->config['sy_onedomain']);
		}elseif($this->config['sy_indexdomain']!=""){
			$weburl=str_replace("http://www","",$this->config['sy_indexdomain']);
		}else{
			$weburl=str_replace("http://www","",$this->config['sy_weburl']);
		}
		if($this->config['sy_web_site']=="1"){
			SetCookie("uid","",time() - 604800,"/",$weburl);
			SetCookie("username","",time()-604800,"/",$weburl);
			SetCookie("salt", "", time() - 604800,"/",$weburl);
			SetCookie("email", "", time() - 604800,"/",$weburl);
			SetCookie("shell", "", time() - 604800,"/",$weburl);
			SetCookie("usertype","",time() - 604800,"/",$weburl);
			SetCookie("friend1","",time() - 3600, "/",$weburl);
			SetCookie("friend2","",time() - 3600, "/",$weburl);
			SetCookie("friend_message1","",time() - 3600, "/",$weburl);
			SetCookie("friend_message2","",time() - 3600, "/",$weburl);
			SetCookie("sysmsg1","",time() - 3600, "/",$weburl);
			SetCookie("sysmsg2","",time() - 3600, "/",$weburl);
			SetCookie("userid_msg","",time() - 3600, "/",$weburl);
			SetCookie("usermsg","",time() - 3600, "/",$weburl);
			SetCookie("userid_job","",time() - 3600, "/",$weburl);
			SetCookie("commsg","",time() - 3600, "/");
			SetCookie("remind_num","",time() - 3600, "/",$weburl);
		}else{
			SetCookie("uid","",time() - 604800,"/");
			SetCookie("username","",time()-604800,"/");
			SetCookie("salt", "", time() - 604800,"/");
			SetCookie("email", "", time() - 604800,"/");
			SetCookie("shell", "", time() - 604800,"/");
			SetCookie("usertype","",time() - 604800,"/");
			SetCookie("friend1","",time() - 3600, "/");
			SetCookie("friend2","",time() - 3600, "/");
			SetCookie("friend_message1","",time() - 3600, "/");
			SetCookie("friend_message2","",time() - 3600, "/");
			SetCookie("sysmsg1","",time() - 3600, "/");
			SetCookie("sysmsg2","",time() - 3600, "/");
			SetCookie("userid_msg","",time() - 3600, "/");
			SetCookie("usermsg","",time() - 3600, "/");
			SetCookie("userid_job","",time() - 3600, "/");
			SetCookie("commsg","",time() - 3600, "/");
			SetCookie("remind_num","",time() - 3600, "/");
		}
	}

	function yunset($name,$value){

		$this->tpl->assign($name,$value);
	}

	function city_info($city,$city_name){
		if(is_array($city)){
			foreach($city as $key=>$value){
				$city_area[] = array("id"=>$value,"name"=>$city_name[$value]);
			}
			return $city_area;
		}
	}
	function admin(){
		$r=$this->obj->get_admin_user_shell();
		$this->registrs();
		if($_POST){
			if($_POST['pytoken']!=$_SESSION['pytoken']){
				unset($_POST['pytoken']);
				$this->obj->ACT_layer_msg("来源地址非法！",3,$this->config['sy_weburl']);
			}
		}
		if(!$_SESSION['pytoken']){
			$_SESSION['pytoken'] = substr(md5(uniqid().$_SESSION['auid'].$_SESSION['ausername'].$_SESSION['ashell']), 8, 12);
		}

		$this->yunset('pytoken',$_SESSION['pytoken']);


	}

	function company(){
		$this->tpl->is_fun();
		$company=$this->obj->DB_select_once("company","`uid`='".$_GET['id']."'","`name`,`content`");
		$data['company_name']=$company['name'];
		$data['company_name_desc']=$company['content'];
		if($_GET['nid']){
			$news=$this->obj->DB_select_once("company_news","`id`='".$_GET['nid']."'","`title`");
			$data['company_news']=$news['title'];
		}
		if($_GET['pid']){
			$product=$this->obj->DB_select_once("company_product","`id`='".$_GET['pid']."'","`title`");
			$data['company_product']=$product['title'];
		}
		$this->data=$data;
	}
	function friend(){
		if($this->config['sy_friend_web']=="2")
		{
			header("location:".$this->config['sy_weburl']."/".$this->url("index","error"));
		}
		$shell=$this->obj->GET_user_shell($this->uid,$_COOKIE['shell']);
		if(!is_array($shell)){
			$this->obj->ACT_msg("../index.php","请先登录!");
		}
	}
	function appadmin(){
		$this->get_appadmin_source();
	}

	function index(){

		$this->tpl->is_fun();


		if($this->config['sy_web_site']=="1")
		{
			include(PLUS_PATH."city.cache.php");
			include(PLUS_PATH."domain_cache.php");

			if(is_array($site_domain)){
				foreach($site_domain as $d){
					if($d['type']==1){
						$indexsite[]=$d;
					}
				}
			}
			$this->yunset("citycache",$city_name);
			$this->yunset("indexsite",$indexsite);
			if($_SESSION['weblogo'])
			{
				$this->config['sy_logo'] =$_SESSION['weblogo'];
			}
			if($_SESSION['webtitle'])
			{
				$this->config['sy_webname'] =$_SESSION['webtitle'];
			}
			if($_SESSION['host'])
			{
				$this->config['sy_weburl'] = $_SESSION['host'];
			}
			$this->yunset('config',$this->config);
		}

		$qq=@explode(",",$this->config['sy_qq']);
		$this->yunset("qq",$qq);
		$site_url = $this->config['sy_weburl']."/".$this->url("index","index",array("c"=>"site"),"1");
		$this->yunset("site_url",$site_url);
		$this->registrs();
	}
	function wap_member()
	{
		if($this->config['sy_wap_web']=="2")
		{
			header("location:".$this->config['sy_weburl']);
		}
		$UA = strtoupper($_SERVER['HTTP_USER_AGENT']);
		if(strpos($UA, 'WINDOWS NT') !== false){
			header("location:".$this->config['sy_weburl']."/index.php?c=wap");
		}
		if(!$this->uid || !$this->username)
		{
			$this->unset_cookie();
			$this->wapheaderLayer($this->config['sy_weburl'].'/wap/index.php?m=login&','请先登录！');
			$data['msg']='请先登录！';
			$data['url']=$this->config['sy_weburl'].'/wap/index.php?m=login';

			$this->yunset("layer",$data);
		}else{
			$shell=$this->obj->GET_user_shell($this->uid,$_COOKIE['shell']);
			if(!is_array($shell)){
				$this->unset_cookie();

				$data['msg']='你无权操作，请重新登录！';
				$data['url']=$this->config['sy_weburl'].'/wap/index.php?m=login';
				$this->yunset("layer",$data);
			}else{
				$this->yunset("uid",$this->uid);
				$this->yunset("username",$this->username);
			}
			$this->getYears(1900,(int)date("Y"));
		}
	}
	function member() {
		$this->tpl->is_fun();
		if(!$this->uid && !$this->username){
			$login=$this->url("index","login",array("usertype"=>"1"),"1");
			$this->obj->ACT_msg($login,"请先登录");
		}else{
			$shell=$this->obj->GET_user_shell($this->uid,$_COOKIE['shell']);
			if(!is_array($shell)){
				$this->obj->ACT_msg("../index.php","你无权操作，请重新登录");
			}else{
				if($_COOKIE['usertype']==2 || $_COOKIE['usertype']==3)
				{
					if($_COOKIE['usertype']==2)
					{
						$this->obj->DB_update_all("company_job","`state`='2'","`edate`<'".time()."' and `uid`='".$this->uid."' and `state`<>'2'");
					}

				}
			}
		}
		$this->yunset("uid",$this->uid);
		$this->yunset("username",$this->username);
		$this->yunset("useremail",$_COOKIE['email']);
	}
	function upload_pic($dir="../upload/news/",$water="",$size=""){
		include(APP_PATH."/plus/config.php");
		include_once(LIB_PATH."upload.class.php");
		$config["watermark_online"]?$addwatermark=true:$addwatermark=false;
		if($watermark_site=="10"){$watermark_site=rand(1,9);}
		$paras["upfiledir"]=$dir;
		if($size){
			$paras["maxsize"]=$size;
		}
		$paras["addpreview"]=false;
		$upload=new Upload($paras);
		return $upload;
	}
	function web_config()
	{
		include_once(LIB_PATH."/public.function.php");
		$config=$this->obj->DB_select_all("admin_config");
		if(is_array($config)){
			foreach($config as $v){
				$configarr[$v['name']]=$v['config'];
			}
		}
		if($this->config['autodate'])
		{
			$configarr['autodate'] = $this->config['autodate'];
		}

		$this->obj->made_web(PLUS_PATH."config.php",ArrayToString($configarr),"config");

		$this->config = $configarr;
	}
	function advertise_cache(){
		include_once(APP_PATH."admin/model/model/advertise_class.php");
		$adver = new advertise($this->obj);
		$adver->model_ad_arr_action();
	}
	function send_email($email=array(),$emailtitle="",$emailcoment="",$emailalert=false,$userinfo=array()){
		if($this->config["sy_smtpserver"]=="" || $this->config["sy_smtpemail"]=="" || $this->config["sy_smtpuser"]==""){
			$this->obj->get_admin_msg($_SERVER['HTTP_REFERER'],"还没有配置邮箱，请联系管理员！");
		}
		$smtp=$this->email_set();
		$smtpusermail =$this->config["sy_smtpemail"];
		$sendok=0;$sendno=0;
		if(is_array($email)){
			foreach($email as $key=>$v){
				$sendid = $smtp->sendmail($v,$smtpusermail,$emailtitle,$emailcoment);
				if($sendid){
					$state=1;
					$sendok++;
				}else{
					$state=0;
					$sendno++;
				}
				$data=array();
				$content = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'background-color:','background-color:','white-space:'),html_entity_decode($emailcoment,ENT_QUOTES,"GB2312"));
				$data['uid']=$key;
				$data['name']=$userinfo[$key];
				$data['cuid']=$userinfo['cuid'];
				$data['cname']=$userinfo[$userinfo['cuid']];
				$data['email']=$v;
				$data['title']=$emailtitle;
				$data['content']=$content;
				$data['state']=$state;
				$this->insert_emailmsg($data);
			}
		}
		if($emailalert){
			$this->obj->ACT_layer_msg($sendok."位发送成功，".$sendno."位发送失败！",1,$_SERVER['HTTP_REFERER']);
		}else{
			return $sendok;
		}
	}
	function send_ceshi_email($email,$emailtitle="",$emailcoment="",$emailalert=false){
		if($this->config["sy_smtpserver"]=="" || $this->config["sy_smtpemail"]=="" || $this->config["sy_smtpuser"]==""){
 			$data['msg']=iconv('gbk','utf-8','还没有配置邮箱，请联系管理员！');
			$data['type']='8';
		}
		$smtp=$this->email_set();
		$smtpusermail =$this->config["sy_smtpemail"];
 		$sendid = $smtp->sendmail($email,$smtpusermail,$emailtitle,$emailcoment);
 		if($sendid){
			$data['msg']=iconv('gbk','utf-8','测试发送成功！');
			$data['type']='9';
		}else{
			$data['msg']=iconv('gbk','utf-8','测试发送失败！');
			$data['type']='8';
		}
		echo json_encode($data);
	}
	function send_message($uidarr=array(),$title="",$content="",$messagealert=false,$user="admin"){
		if(is_array($uidarr)){
			foreach($uidarr as $v){
				$data=array();
				$data['uid']=$v;
				$data['title']=$title;
				$data['content']=$content;
				$data['status']=0;
				$data['user']=$user;
				$data['user']=time();
				$insert_id=$this->obj->insert_into("message",$data);
			}
			if($messagealert){
				$this->obj->ACT_layer_msg("发送成功！",9,$_SERVER['HTTP_REFERER']);
			}else{
				return $insert_id;
			}
		}else{
			$this->obj->ACT_layer_msg("参数有误，请检查参数！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function sqjobmsg($jobid,$comid){
		$comarr=$this->obj->DB_select_once("company_job","`id`='".$jobid."' and `r_status`<>'2' and `status`<>'1'");
		$uid=$this->obj->DB_select_once("company","`uid`='$comid'","linkmail,linktel,`name`");
		$data["uid"]=$comid;
		$data["name"]=$uid['name'];
		$data["cuid"]=$this->uid;
		$data["cname"]=$this->username;
		$data["type"]="sqzw";
		$data["jobname"]=$comarr["name"];
		$data["date"]=date("Y-m-d");
		$data["moblie"]=$uid["linktel"];
		$data["email"]=$uid["linkmail"];
		$this->send_msg_email($data);
	}

	function header_desc($title="",$keyword="",$description=""){
		$this->yunset("title",$title);
		$this->yunset("keywords",$keyword);
		$this->yunset("description",$description);
	}
	function url($con='index',$m='index',$paramer=array(),$index=""){


		if($index!='1')
		{
			if($con=='index' && $m=='index'){
				$url.='index.php';
			}elseif($con=='index'){
				$url.='index.php?m='.$m;
			}elseif($m=='index'){
				$url.='index.php?con='.$con;
			}else{
				$url.='index.php?con='.$con.'&m='.$m;
			}
			if($paramer){
				$p='';
				foreach($paramer as $k=>$v){
					if(!empty($v)){
						$p.='&'.$k.'='.$v;
					}
				}
				if(strpos($url,'?')){
					$url.=$p;
				}else{
					$url.='?'.substr($p,1);
				}
			}

			return $url;

		}else{
			global $seo;
			$paramer['con'] = $con;
			$paramer['m'] = $m;
			$url  =  get_index_url($paramer,$this->config,$seo,"",$index);
			return $url;
		}

	}
	function aurl($paramer){

		global $seo;
		$url  =  get_url($paramer,$this->config,$seo,"ask");
		return $url;
	}
	function curl($paramer){
		global $seo;
		$url  =  get_url($paramer,$this->config,$seo,"company");
		return $url;
	}
	function furl($paramer){
		global $seo;
		$url  =  get_url($paramer,$this->config,$seo,"friend");
		return $url;
	}
	function get_page($table,$where="",$pageurl="",$limit=20,$field="*",$rowsname="rows",$pagenavname="pagenav"){
		include_once(LIB_PATH."page3.class.php");
		$rows=array();
		$page=$_GET['page']<1?1:$_GET['page'];
		$ststrsql=($page-1)*$limit;
		$num=$this->obj->DB_select_num($table,$where);
		$this->yunset("total",$num);
		if($num>$limit){
			$pages=ceil($num/$limit);
			$this->yunset("pages",$pages);
			$page = new page($page,$limit,$num,$pageurl);
			$pagenav=$page->numPage();
			$this->yunset($pagenavname,$pagenav);
		}
		$rows=$this->obj->DB_select_all($table,"$where limit $ststrsql,$limit",$field);
		$this->yunset($rowsname,$rows);
		return $rows;
	}
	function array_action($job_info,$array=array())
	{
		if(!empty($array))
		{
			$comclass_name = $array["comclass_name"];
			$city_name = $array["city_name"];
			$job_name = $array["job_name"];
			$industry_name = $array["industry_name"];
		}else{
			include APP_PATH."/plus/city.cache.php";
			include APP_PATH."/plus/com.cache.php";
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/industry.cache.php";
		}
		$job_info['exp_info'] = $comclass_name[$job_info['exp']];
		$job_info['edu_info'] = $comclass_name[$job_info['edu']];
		$job_info['age_info'] = $comclass_name[$job_info['age']];
		$job_info['salary_info'] = $comclass_name[$job_info['salary']];
		$job_info['number_info'] = $comclass_name[$job_info['number']];
		$job_info['mun_info'] = $comclass_name[$job_info['mun']];
		$job_info['sex_info'] = $comclass_name[$job_info['sex']];
		$job_info['type_info'] = $comclass_name[$job_info['type']];
		$job_info['marriage_info'] = $comclass_name[$job_info['marriage']];
		$job_info['report_info'] = $comclass_name[$job_info['report']];
		$job_info['prv_info'] = $city_name[$job_info['provinceid']];
		$job_info['comprv_info'] = $city_name[$job_info['com_provinceid']];
		$job_info['prov_info'] = $city_name[$job_info['prov']];
		$job_info['cty_info'] = $city_name[$job_info['city']];
		$job_info['pr_info'] = $comclass_name[$job_info['pr']];
		$job_info['city_info'] = $city_name[$job_info['cityid']];
		$job_info['city2_info'] = $city_name[$job_info['three_cityid']];
		$job_info['three_info'] = $city_name[$job_info['three_city']];
		$job_info['hy_info'] = $industry_name[$job_info['hy']];
		$job_info['pr_info'] = $comclass_name[$job_info['pr']];
		$job_info['mun_info'] = $comclass_name[$job_info['mun']];
		$job_info['edate']=date("Y年m月d日",$job_info['edate']);
		if($job_info['lang']!="")
		{
			$lang = @explode(",",$job_info['lang']);
			foreach($lang as $key=>$value)
			{
				$langinfo[]=$comclass_name[$value];
			}
			$job_info['lang_info'] = @implode(",",$langinfo);
		}else{
			$job_info['lang_info'] ="";
		}
		if($job_info['welfare']!="")
		{
			$welfare = @explode(",",$job_info['welfare']);
			foreach($welfare as $key=>$value)
			{
				$welfareinfo[]=$comclass_name[$value];
			}
			$job_info['welfare_info'] = @implode(",",$welfareinfo);
		}else{
			$job_info['welfare_info'] ="";
		}
		return $job_info;
	}

	function user_cache(){
		include APP_PATH."/plus/user.cache.php";
		$this->yunset("userdata",$userdata);
		$this->yunset("userclass_name",$userclass_name);
	}
	function com_cache(){
		include APP_PATH."/plus/com.cache.php";
		$this->yunset("comdata",$comdata);
		$this->yunset("comclass_name",$comclass_name);
	}
	function city_cache(){
		include(PLUS_PATH."city.cache.php");
		$this->yunset("city_type",$city_type);
		$this->yunset("city_index",$city_index);
		$this->yunset("city_name",$city_name);
	}
	function job_cache(){
		include(PLUS_PATH."job.cache.php");
		$this->yunset("job_type",$job_type);
		$this->yunset("job_index",$job_index);
		$this->yunset("job_name",$job_name);
	}



	function industry_cache(){
		include(PLUS_PATH."industry.cache.php");
		$this->yunset("industry_index",$industry_index);
		$this->yunset("industry_name",$industry_name);
	}


	function send_msg_email($data=array(),$smtp=""){
		$tpl=$this->obj->get_email_tpl();
 		if($this->config["sy_email_".$data["type"]]==1 && $data["email"]){
			if($this->config['sy_smtpserver']!="" && $this->config['sy_smtpemail']!="" && $this->config['sy_smtpuser']!=""){
				$title_tpl=$tpl["email".$data["type"]]["title"];
				$content_tpl=$tpl["email".$data["type"]]["content"];
				$to=$data["email"];
				$title=$this->msgemail_tpl($title_tpl,$data);
				$content=$this->msgemail_tpl($content_tpl,$data);
				if($this->config["sy_email_online"]==1){
					if($smtp=="")
					{
						$smtp=$this->email_set();
					}
					if($this->CheckRegEmail($data['email']))
					{
						$sendid =$smtp->sendmail($to,$this->config["sy_smtpemail"],$title,html_entity_decode($content,ENT_QUOTES,"GB2312"));
					}
				}else if($this->config["sy_email_online"]==2){
					if($this->CheckRegEmail($data['email']))
					{
						$sendid =sendmail($to,$title,$content);
					}
				}
				$sendid?$state=1:$state=0;
				if($this->CheckRegEmail($data['email']))
				{
					$this->insert_emailmsg(array('uid'=>$data['uid'],'name'=>$data['name'],'cuid'=>$data['cuid'],'cname'=>$data['cname'],'email'=>$data['email'],'title'=>$title,'content'=>$content,'state'=>$state));
				}
			}else{
				$this->obj->ACT_layer_msg( "还没有配置邮箱，请联系管理员！",8,$_SERVER['HTTP_REFERER']);
			}
		}
		if($data["moblie"]&&$this->config["sy_msg_".$data["type"]]==1){
			if(!$this->config["sy_msguser"] || !$this->config["sy_msgpw"] || !$this->config["sy_msgkey"]){
				$this->obj->ACT_layer_msg( "还没有配置邮箱，请联系管理员！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$msguser=$this->config["sy_msguser"];
				$msgpw=$this->config["sy_msgpw"];
				$msgkey=$this->config["sy_msgkey"];
				$moblie=$data["moblie"];
				$content_tpl=$tpl["msg".$data["type"]]["content"];
				$content=$this->msgemail_tpl($content_tpl,$data);
				if($moblie!=""){
					$status=$this->obj->sendSMS($msguser,$msgpw,$msgkey,$moblie,$content,'','',$data);
				}
				return $status;
			}
		}
	}
	function msgemail_tpl($tpl,$data=array()){
		unset($data["type"]);
		unset($data["moblie"]);
		unset($data["emile"]);
		$re=array("{webname}","{weburl}","{webtel}");
		$re2[]=$this->config["sy_webname"];
		$re2[]=$this->config["sy_weburl"];
		$re2[]=$this->config["sy_webtel"];
		$tpl=str_replace($re,$re2,$tpl);
		foreach($data as $k=>$v){
			$tpl=str_replace("{".$k."}",$v,$tpl);
		}
		return $tpl;
	}
	function email_set(){
		include_once(LIB_PATH."email.class.php");
		$smtpserver = $this->config["sy_smtpserver"];
		$smtpserverport =$this->config["sy_smtpserverport"];
		$smtpusermail =$this->config["sy_smtpemail"];
		$smtpuser = $this->config["sy_smtpuser"];
		$smtppass = $this->config["sy_smtppass"];
		$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
		return $smtp;
	}
	function logout($result=true){
		$uiddir=APP_PATH."cache/im/";
		include($uiddir."/status.php");
		$liststatus=unserialize(base64_decode($statusdata));

		if(!empty($liststatus[$this->uid])){
			unset($liststatus[$this->uid]);
			unset($liststatus[$this->uid."time"]);
		}
		$content=base64_encode(serialize($liststatus));
		$cont="<?php";
		$cont.="\r\n";
		$cont.="\$statusdata='".$content."';";
		$cont.="?>";
		$fp=@fopen($uiddir."/status.php","w+");
		$filetouid=@fwrite($fp,$cont);
		@fclose($fp);
		if($this->config['sy_uc_type']=="uc_center"){
			$this->obj->uc_open();
			$logout = uc_user_synlogout();
		}elseif($this->config["sy_pw_type"]){
			include(APP_PATH."/api/pw_api/pw_client_class_phpapp.php");
			$username=$_SESSION["username"];
			$pw=new PwClientAPI($username,"","");
			$logout=$pw->logout();
			$this->unset_cookie();
		}else{
			$this->unset_cookie();
		}

		if($result){echo 1;die;}
	}
	function del_dir($dir_adds='',$del_def=0) {
	    $result = false;
	    if(! is_dir($dir_adds)){
	   		return false;
	    }
	    $handle = opendir($dir_adds);
	    while(($file = readdir($handle)) !== false){
		    if($file != '.' && $file != '..') {
		        $dir = $dir_adds . DIRECTORY_SEPARATOR . $file;
		        is_dir($dir) ? $this->del_dir($dir) : @unlink($dir);
		    }
	    }
	    closedir($handle);
	    if($del_def==0){
			$result = @rmdir($dir_adds) ? true : false;
	    }else {
	    	$result = true;
	    }
	    return $result;
	}

	function CacheInclude($name=array(),$set='1',$isreturn="1"){
		if(!empty($name)){
			foreach($name as $key=>$value){
				if(is_array($value)){
					include APP_PATH."/plus/".$key.".cache.php";
					foreach($value as $v){
						if($set=="1"){
							$this->yunset($v,$$v);
						}
						if($isreturn=="1"){
							$Array[$v] = $$v;
						}
					}
				}
			}
			return $Array;
		}
	}
	function seo($ident,$title='',$keyword='',$description='')
	{
		include APP_PATH."/plus/seo.cache.php";
		$seo=$seo[$ident];
		if(is_array($seo)){
			foreach($seo as $k=>$v){
				if($v['affiliation']!=""){
					$did=@explode(",",$v['affiliation']);
					if($_SESSION['did']!="" && in_array($_SESSION['did'],$did)){
						$fzseo=$v;
					}elseif(in_array("0",$did)){
						$seo=$v;
					}
				}else{
					$seo=$v;
				}
			}
			if($fzseo){
				$seo=$fzseo;
			}
		}
		$data=$this->data;

		if(is_array($seo))
		{
			$cityname = $_SESSION['cityname']?$_SESSION['cityname']:$this->config["sy_indexcity"];
			if($title)
			{
				$this->config['sy_webname'] = $title;
				$seo['title'] = $title;
			}
			if($keyword)
			{
				$this->config['sy_webkeyword'] = $keyword;
				$seo['keywords'] = $keyword;
			}
			if($description)
			{
				$this->config['sy_webmeta'] = $description;
				$seo['description'] = $description;
			}
			foreach($seo as $key=>$value)
			{
				$seo[$key] = str_replace("{webname}",$this->config['sy_webname'],$seo[$key]);
				$seo[$key] = str_replace("{webkeyword}",$this->config['sy_webkeyword'],$seo[$key]);
				$seo[$key] = str_replace("{webdesc}",$this->config['sy_webmeta'],$seo[$key]);
				$seo[$key] = str_replace("{weburl}",$this->config['sy_weburl'],$seo[$key]);
				$seo[$key] = str_replace("{city}",$cityname,$seo[$key]);

				if(is_array($data)){
					foreach($data as $k=>$v){
						$seo[$key] = str_replace("{".$k."}",$v,$seo[$key]);
					}
				}
				if(!@strpos('{seacrh_class}',$seo[$key])){
					$rdata=$this->get_search_class($ident,$key);
					$seo[$key] = str_replace("{seacrh_class}",$rdata,$seo[$key]);
				}
				$seo[$key]=str_replace('  ',' ',$seo[$key]);
				$seo[$key]=str_replace(array("-  -","- -"),'-',$seo[$key]);
				$seo[$key]=str_replace(array("-  -","- -"),'-',$seo[$key]);
			}
		}
		$this->yunset("title",$seo['title']." - Powered by PHPYun.");
		$this->yunset("keywords",$seo['keywords']);
		$this->yunset("description",mb_substr(str_replace("	","",str_replace("\r","",str_replace("\n","",strip_tags($seo['description'])))),0,200,'gbk'));
	}

	function get_search_class($ident,$type="title"){
		include APP_PATH."/plus/city.cache.php";
		if($ident=="com" || $ident=="part"){
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/com.cache.php";
			include APP_PATH."/plus/industry.cache.php";
		}
		if($ident=="user"){
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/user.cache.php";
			include APP_PATH."/plus/industry.cache.php";
		}
		foreach($_GET as $key=>$v){
			switch($key){
				case "hy":
				$data[]=$industry_name[$v];
				break;
				case "job1":
				case "job1_son":
				case "job_post":
				$data[]=$job_name[$v];
				break;
				case "provinceid":
				case "cityid":
				case "three_cityid":
				$data[]=$city_name[$v];
				break;
				default:
				$data[]=is_array($comdata["job_".$key])?$comclass_name[$v]:$userclass_name[$v];
				break;
			}
		}
		if($type=="title"){
			$data=@implode(' - ',$data);
		}else{
			$data=@implode(',',$data);
		}
		return $data;
	}
	function delweb($table){
	    if($_GET['delsub'])
	    {
	    	$del=$_GET['del'];
	    	if($del)
	    	{
	    		if(is_array($del))
	    		{
			    	foreach($del as $v)
			    	{
			    	    $this->obj->DB_delete_all($table,"`id`='$v'");
			    	}
		    	}else{
		    		$this->obj->DB_delete_all($table,"`id`='$del'");
		    	}
	    		$this->layer_msg("删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
	    		$this->obj->get_admin_msg($_SERVER['HTTP_REFERER'],"请选择您要记录");
	    		$this->layer_msg("请选择您要记录！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id']))
	    {
			$where="`id`='".$_GET['id']."'";
			$nid=$this->obj->DB_delete_all("iweb_list","`id`='".$_GET['id']."'");
			isset($nid)?$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('非法操作！',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
	function registrs(){
		include(LIB_PATH."web.libs.php");
		$this->tpl->register_function("sublen","sublen");
		$this->tpl->register_function("htmlentitydecode","htmlentitydecode");
		$this->tpl->register_function("totime","totime");
		$this->tpl->register_function("seacrh_url","seacrh_url");
		if($_GET['c']=="list" && $_GET['nid']){
			$class=$this->obj->DB_select_once("news_group","`id`='".$_GET['nid']."'");
			$data['news_class']=$class['name'];
			$this->data=$data;
		}
		if(($_GET['m'] && $_GET['id']) || ($_GET['m']=="news" && $_GET['nid'])){
			$c=$_GET['c']?$_GET['c']:"index";
			$name=$_GET['m']."_".$c."_article";
			$act_array=array("resume","com","show","zph","announcement");
			$task_array=array("index","recommend","comapply","show","resumeshare","sendresume");
			if(@in_array($_GET['m'],$act_array) && @in_array($c,$task_array)){
				$this->$name($_GET['id']);
			}
		}
		if($_GET['m']=="com" && ($_GET['c']=="search" || $_GET['c']==""))
		{
			extract($_GET);
			if($hy){
				include APP_PATH."/plus/industry.cache.php";
				$seacrh_class[]=$industry_name[$hy];
			}
			if($jobids || $job1_son ||$job_post){
				include APP_PATH."/plus/job.cache.php";
				if($job1_son){
					$seacrh_class[]=$job_name[$job1_son];
				}
				if($job_post){
					$seacrh_class[]=$job_name[$job_post];
				}
				if($jobids){
					$jobids=@explode(",",$jobids);
					foreach($jobids as $v){
						$seacrh_class[]=$job_name[$v];
					}
				}
			}
			if($cityid){
				include APP_PATH."/plus/city.cache.php";
				$seacrh_class[]=$city_name[$cityid];
			}
			if($pr || $mun || $edu || $exp || $salary || $type){
				include APP_PATH."/plus/com.cache.php";
				if($pr){
					$seacrh_class[]=$comclass_name[$pr];
				}
				if($mun){
					$seacrh_class[]=$comclass_name[$mun];
				}
				if($edu){
					$seacrh_class[]=$comclass_name[$edu];
				}
				if($exp){
					$seacrh_class[]=$comclass_name[$exp];
				}
				if($salary){
					$seacrh_class[]=$comclass_name[$salary];
				}
				if($type){
					foreach($type as $v){
						$seacrh_class[]=$comclass_name[$v];
					}
				}
			}
			if($keyword){
				$seacrh_class[]=$keyword;
			}
			if($uptime=="1"){
				$seacrh_class[]="今天";
			}elseif($uptime=="3"){
				$seacrh_class[]="最近3天";
			}elseif($uptime=="7"){
				$seacrh_class[]="最近7天";
			}elseif($uptime=="30"){
				$seacrh_class[]="最近一个月";
			}elseif($uptime=="90"){
				$seacrh_class[]="最近三个月";
			}

			if(is_array($seacrh_class)){
				$seacrh_class=@implode(",",$seacrh_class);
			}
			$data['seacrh_class']=$seacrh_class;
			$this->data=$data;
		}
		if($_GET['m']=="user" && ($_GET['c']=="search" || $_GET['c']==""))
		{
			extract($_GET);
			if($hy){
				include APP_PATH."/plus/industry.cache.php";
				$seacrh_class[]=$industry_name[$hy];
			}
			if($jobids || $job1_son ||$job_post){
				include APP_PATH."/plus/job.cache.php";
				if($job1_son){
					$seacrh_class[]=$job_name[$job1_son];
				}
				if($job_post){
					$seacrh_class[]=$job_name[$job_post];
				}
				if($jobids){
					$jobids=@explode(",",$jobids);
					foreach($jobids as $v){
						$seacrh_class[]=$job_name[$v];
					}
				}
			}
			if($cityid){
				include APP_PATH."/plus/city.cache.php";
				$seacrh_class[]=$city_name[$cityid];
			}
			if($report || $sex || $edu || $exp || $salary || $type){
				include APP_PATH."/plus/user.cache.php";
				if($report){
					$seacrh_class[]=$userclass_name[$report];
				}
				if($sex){
					$seacrh_class[]=$userclass_name[$sex];
				}
				if($edu){
					$seacrh_class[]=$userclass_name[$edu];
				}
				if($exp){
					$seacrh_class[]=$userclass_name[$exp];
				}
				if($salary){
					$seacrh_class[]=$userclass_name[$salary];
				}
				if($type){
					foreach($type as $v){
						$seacrh_class[]=$userclass_name[$v];
					}
				}
			}
			if($keyword){
				$seacrh_class[]=$keyword;
			}
			if($uptime=="1"){
				$seacrh_class[]="今天";
			}elseif($uptime=="3"){
				$seacrh_class[]="最近3天";
			}elseif($uptime=="7"){
				$seacrh_class[]="最近7天";
			}elseif($uptime=="30"){
				$seacrh_class[]="最近一个月";
			}elseif($uptime=="90"){
				$seacrh_class[]="最近三个月";
			}
			if(is_array($seacrh_class)){
				$seacrh_class=@implode(",",$seacrh_class);
			}
			$data['seacrh_class']=$seacrh_class;
			$this->data=$data;
		}
	}
	function resume_index_article($id){
		$this->resume_select($id);
	}
	function resume_resumeshare_article($id){
		$this->resume_select($id);
	}
	function resume_sendresume_article($id){
		$this->resume_select($id);
	}

	function resume_select($id){
		$user_jy=$this->obj->DB_select_once("resume_expect","`id`='".$id."'");
		$user=$this->obj->DB_select_once("resume","`r_status`<>'2' and `uid`='".$user_jy['uid']."'");
		$member=$this->obj->DB_select_once("member","`uid`='".$user_jy['uid']."'");

		if(is_array($user_jy)||is_array($user))
		{
			include APP_PATH."/plus/city.cache.php";
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/user.cache.php";
			include APP_PATH."/plus/industry.cache.php";

			if($this->config['user_name']==3)
			{
				$user["username_n"] = "NO.".$user_jy['id'];
			}elseif($this->config['user_name']==2){
				if($user['sex']=='6')
				{
					$user['username_n'] = mb_substr($user['name'],0,2)."先生";
				}else{
					$user['username_n'] = mb_substr($user['name'],0,2)."女士";
				}
			}else{
				$user['username_n'] = $user['name'];
			}
			if($this->config['sy_usertype_1']=='1'&&$this->uid!=$user['uid']){ 
				if($_COOKIE['usertype']=='2'){
					$my_down=$this->obj->DB_select_all("down_resume","`comid`='".$_COOKIE['uid']."'","uid");
				}
				if(!empty($my_down))
				{
					foreach($my_down as $m_k=>$m_v)
					{
						$my_down_uid[]=$m_v['uid'];
					}
				}
				if(@in_array($user['uid'],$my_down_uid)==false ||$user['resume_photo']==""||file_exists($user['resume_photo'])==false){
					$user['resume_photo']=$this->config['sy_weburl'].'/'.$this->config['sy_member_icon'];
				}  
			}else if($user['resume_photo']==""||file_exists($user['resume_photo'])==false){
				$user['resume_photo']=$this->config['sy_weburl'].'/'.$this->config['sy_member_icon'];
			}

			$user['username']=$member['username'];
			$user['user_sex']=$userclass_name[$user['sex']];
			$user['user_exp']=$userclass_name[$user['exp']];
			$user['user_marriage']=$userclass_name[$user['marriage']];
			$user['useredu']=$userclass_name[$user['edu']];
			$a=date('Y',strtotime($user['birthday']));
			$user['age']=date("Y")-$a;
			$user['city_one']=$city_name[$user_jy['provinceid']];
			$user['city_two']=$city_name[$user_jy['cityid']];
			$user['city_three']=$city_name[$user_jy['three_cityid']];
			$user['salary']=$userclass_name[$user_jy['salary']];
			$user['report']=$userclass_name[$user_jy['report']];
			$user['type']=$userclass_name[$user_jy['type']];
			$user['hy']=$industry_name[$user_jy['hy']];
			$user['lastupdate']=date("Y-m-d",$user_jy['lastupdate']);
			$user['r_name'] = $user_jy['name'];
			$user['doc'] = $user_jy['doc'];
			$user['hits']=$user_jy['hits'];
			$user['dnum']=$user_jy['dnum'];
			$resume_diy=split('[|]',$user_jy['resume_diy']);
			$user['resume_diy']=$resume_diy[0];
			$user['dom_sort']=$user_jy['dom_sort'];
			$user['works_upload']=$user_jy['works_upload'];
			$user['background_image']=$resume_diy[1];
			$user['id']=$id;
			$jy=@explode(",",$user_jy['job_classid']);
			if(@is_array($jy))
			{
				foreach($jy as $v)
				{
					$jobname[]=$job_name[$v];
				}
				$user['jobname']=@implode(",",$jobname);
			}
			if($user_jy['doc'])
			{
				$user_doc=$this->obj->DB_select_once("resume_doc","`eid`='".$user['id']."'");
			}else{
				$user_edu=$this->obj->DB_select_all("resume_edu","`eid`='$user_jy[id]'");
				$user_training=$this->obj->DB_select_all("resume_training","`eid`='$user_jy[id]'");
				$user_work=$this->obj->DB_select_all("resume_work","`eid`='$user_jy[id]'");
				$user_other=$this->obj->DB_select_all("resume_other","`eid`='$user_jy[id]'");
				$user_project=$this->obj->DB_select_all("resume_project","`eid`='$user_jy[id]'");
				$user_skill=$this->obj->DB_select_all("resume_skill","`eid`='$user_jy[id]'");
				$user_xm=$this->obj->DB_select_all("resume_project","`eid`='".$user_jy['id']."'");
				$user_show=$this->obj->DB_select_all("resume_show","`eid`='".$user_jy['id']."'");
			}
		}
		if(is_array($user_skill))
		{
			foreach($user_skill as $k=>$v)
			{
				$user_skill[$k]['skill_n']=$userclass_name[$v['skill']];
				$user_skill[$k]['ing_n']=$userclass_name[$v['ing']];
			}
			$user_cert=$this->obj->DB_select_all("resume_cert","`eid`='".$user_jy['id']."'");
		}
		$userid_job=$this->obj->DB_select_once("userid_job","`com_id`='".$this->uid."' and `eid`='".$user_jy['id']."'");
		if(!empty($userid_job))
		{
			$user['m_status']=1;
		}
		if($this->uid==$user['uid'] && $this->username && $_COOKIE['usertype']==1)
		{
			$user['m_status']=1;
		}
		if($this->uid && $this->username && ($_COOKIE['usertype']==2 || $_COOKIE['usertype']==3))
		{
			$row=$this->obj->DB_select_once("down_resume","`eid`='".$id."' and comid='".$this->uid."'");
			if(is_array($row)){
				$user['m_status']=1;
				$user['username_n'] = $user['name'];
			}else{
				$user['link_msg']="<a href='javascript:void(0)' onclick=\"for_link('$id')\">查看联系方式</a>";
			}
		}
		if($this->uid && $this->username && $this->uid==$user_jy['uid'])
		{

			$user['diy_status']=1;
		}else{

			$user['diy_status']=2;
		}
		if($this->uid && $this->username && $_COOKIE['usertype']==1)
		{
			$user['link_msg']="您不是企业用户！";
		}
		if(!$this->uid && !$this->username)
		{
			$login_com_url = $this->url("index","login",array("usertype"=>"2"),"1");
			$user['link_msg']="您还没有登录，请点击<a href='".$login_com_url."'>登录</a>！";
		}
		if($_GET['look']){
			$row=$this->obj->admin_get_user_shell($_SESSION['auid'],$_SESSION['ashell']);
			if(!$row)
			{
				echo "您无权查看！";die;
			}else{
				$user['m_status']=1;
			}
		}
		$is_browse=$this->obj->DB_select_once("userid_job","`eid`='".$id."' and com_id='".$this->uid."' and `is_browse`='1'",'id');
		if($is_browse['id']){
			$this->obj->update_once("userid_job",array("is_browse"=>"2"),array("id"=>$is_browse['id']));
		}
		$user['per']=sprintf('%.2f%%',($user_jy['dnum']/$user_jy['hits'])*100);
		$user['user_doc']=$user_doc;
		$user['user_jy']=$user_jy;
		$user['user_edu']=$user_edu;
		$user['user_tra']=$user_training;
		$user['user_work']=$user_work;
		$user['user_other']=$user_other;
		$user['user_xm']=$user_xm;
		$user['user_skill']=$user_skill;
		$user['user_cert']=$user_cert;
		$user['user_show']=$user_show;
		$data['resume_username']=$user['username_n'];
		$data['resume_city']=$user['city_one'].",".$user['city_two'];
		$data['resume_job']=$user['hy'];
		$this->data=$data;
		$this->yunset("Info",$user);
	}

	function com_recommend_article($id){
		$Info = $this->obj->DB_select_alls("company_job","company","a.`state`='1' and a.`r_status`<>'2' and a.`id`='$id' and b.`uid`=a.`uid`","a.*,b.*,a.name as jobname,a.provinceid as provinceid,a.cityid as cityid,a.three_cityid as three_cityid");
		if(is_array($Info)){

			$cache_array = $this->db->cacheget();
			$Job = $this->db->array_action($Info[0],$cache_array);
		}
		$Job['now'] = date("Y-m-d H:i:s");
		$data['job_name']=$Job['jobname'];
		$data['industry_class']=$Job['job_hy'];
		$data['job_class']=$Job['job_class_one'].",".$Job['job_class_two'].",".$Job['job_class_three'];
		$data['job_desc']=$this->obj->GET_content_desc($Job['description']);
		$this->data=$data;
		$this->yunset("Info",$Job);
	}

	function com_comapply_article($id){
		$Info = $this->obj->DB_select_alls("company_job","company"," a.`r_status`<>'2' and a.`id`='$id' and b.`uid`=a.`uid`","a.*,b.*,a.name as jobname,a.provinceid as provinceid,a.cityid as cityid,a.three_cityid as three_cityid,a.hy as hy,a.lastupdate as lastupdate,a.rec as jobrec");
		if(empty($Info)){
 			    $this->obj->get_admin_msg($this->config['sy_weburl'],"没有该职位！");
		}else{
			if($Info[0]['state']=="0"){
				$this->obj->get_admin_msg($_SERVER['HTTP_REFERER'],"职位审核中！");
			}elseif($Info[0]['state']=="2"){
				$this->yunset('entype','1');


			}elseif($Info[0]['state']=="3"){
				$this->obj->get_admin_msg($_SERVER['HTTP_REFERER'],"该职位未通过审核！");

			}elseif($Info[0]['status']=="1"){
				$this->yunset('stop','1');
			}
		}
		if(is_array($Info)){
 			$cache_array = $this->db->cacheget();
			$Job = $this->db->array_action($Info[0],$cache_array);
			if($Job['is_link']=="1"){
				if($Job['link_type']==1){
					$link=$this->obj->DB_select_once("company","`uid`='".$Job['uid']."'","`linkman`,`linktel`");
					$Job['linkman']=$link['linkman'];
					$Job['linktel']=$link['linktel'];
				}else{
					$link=$this->obj->DB_select_once("company_job_link","`jobid`='".$Job['id']."'","`link_man`,`link_moblie`");
					$Job['linkman']=$link['link_man'];
					$Job['linktel']=$link['link_moblie'];
				}
			}
		}
		$com=$this->obj->DB_select_once("member","`uid`='".$Job['uid']."'","`username`");
		$Job['cert_n'] = @explode(",",$Job['cert']);
		$Job['uid'] = $Job['uid'];
		$Job['com_url'] = $this->Curl(array("url"=>"id:".$Job[uid]));
		$Job['username'] = $com['username'];
		$data['job_name']=$Job['jobname'];
		$data['company_name']=$Job['name'];
		$data['industry_class']=$Job['job_hy'];
		$data['job_class']=$Job['job_class_one'].",".$Job['job_class_two'].",".$Job['job_class_three'];
		$data['job_desc']=$this->obj->GET_content_desc($Job['description']);
		$this->data=$data;
		$this->yunset("Info",$Job);
	}

	function announcement_index_article($id){
		$gonggao=$this->obj->DB_select_once("admin_announcement","`id`='$id'");
		$annou_last=$this->obj->DB_select_once("admin_announcement","`id`<'$id' order by `id` desc");
		if(!empty($annou_last)){
			$annou_last['url']='index.php?m=announcement&id='.$annou_last['id'];
		}
		$annou_next=$this->obj->DB_select_once("admin_announcement","`id`>'$id' order by `id` asc");
		if(!empty($annou_next)){
			$annou_next['url']='index.php?m=announcement&id='.$annou_next['id'];
		}
		$info=$gonggao;
		$data['news_title']=$gonggao['title'];
		$this->data=$data;
		$info["last"]=$annou_last;
		$info["next"]=$annou_next;
		$this->yunset("Info",$info);
	}

	function show_index_article($id){
	}

	function zph_show_article($id){
		$row=$this->obj->DB_select_once("zhaopinhui","`id`='".$id."'");
		$row["stime"]=strtotime($row['starttime'])-mktime();
		$row["etime"]=strtotime($row['endtime'])-mktime();
		$rows=$this->obj->DB_select_all("zhaopinhui_pic","`zid`='".$id."'");
		$data['zph_title']=$row['title'];
		$data['zph_desc']=$this->obj->GET_content_desc($row['body']);
		$this->data=$data;
		$this->yunset("Info",$row);
		$this->yunset("Image_info",$rows);
	}
	function assignhtm($contents,$id)
	{
		$job_info = $this->obj->DB_select_alls("company_job","company","a.`state`='1' and a.`r_status`!=2 and a.`id`='$id' and b.`uid`=a.`uid`","a.*,b.*,a.name as comname,a.cityid as cityid,a.three_cityid as three_cityid");
		$job_info = $this->array_action($job_info[0]);
		if(is_array($job_info))
		{
			foreach($job_info as $key=>$value)
			{
				$contents = str_replace("{yun:}".$key."{/yun}",$value,$contents);
			}
			$contents = str_replace("{yun:}now{/yun}",date("Y-m-d H:i:s"),$contents);
			$contents = str_replace("{yun:}sy_weburl{/yun}",$this->config['sy_weburl'],$contents);
			$contents = str_replace("{yun:}sy_webname{/yun}",$this->config['sy_webname'],$contents);
			$contents = str_replace("{yun:}comurljob{/yun}",$this->config['sy_weburl']."/".$this->url("index","com",array("c"=>"comapply","id"=>$id),"1"),$contents);
			$contents = str_replace("{yun:}comurl{/yun}",$this->curl(array("url"=>"id:".$job_info[uid])),$contents);
		}else{
			$contents = "";
		}
		return $contents;
	}
	function addkeywords($type,$keyword)
	{
		$info = $this->obj->DB_select_once("hot_key","`key_name`='$keyword' AND `type`='$type'");
		if(is_array($info))
		{
			$where['key_name']=$keyword;
			$where['type']=$type;
			$this->obj->DB_update_all("hot_key","`num`=`num`+1","`key_name`='".$keyword."' and `type`='".$type."'");
		}else{
			$data['key_name']=$keyword;
			$data['num']=1;
			$data['type']=$type;
			$data['check']=0;
			$this->obj->insert_into("hot_key",$data);
		}
	}

	function addstate($content,$type=1,$uid='')
	{
		$uid=$this->uid?$this->uid:$uid;
		$data['uid']=$uid;
		$data['content']=$content;
		$data['type']=$type;
		$data['ctime']=time();
		$this->obj->insert_into("friend_state",$data);
	}

	function automsg($content,$uid)
	{
		$member=$this->obj->DB_select_all("member","`uid`='".$uid."'","`username`");
		$data['fa_uid']=$uid;
		$data['content']=$content;
		$data['username']=$member['username'];
		$data['ctime']=time();
		$this->obj->insert_into("sysmsg",$data);
	}
	function picmsg($pic,$url,$type="")
	{
		$error = array("1"=>"文件太大","2"=>"文件类型不符","3"=>"同名文件已经存在","4"=>"移动文件出错,请检查upload目录权限");
		if($error[$pic]!="")
		{
			if($type=="ajax")
			{
				echo "{";
				echo				"url: '".$pic."',\n";
				echo				"s_thumb: '".$error[$pic]."'\n";
				echo "}";
				die;
			}else{
				$this->obj->ACT_layer_msg( $msg = $error[$pic],8,$url);
			}
		}
	}
	function post_trim($data){
		foreach($data as $d_k=>$d_v){
			if(is_array($d_v)){
				$data[$d_k]=$this->post_trim($d_v);
			}else{
				$data[$d_k]=trim($data[$d_k]);
			}
		}
		return $data;
	}
	function get_moblie(){
		if($this->config['sy_wap_web']=="2")
		{
			header("location:".$this->config['sy_weburl']);
		}
		$UA = strtoupper($_SERVER['HTTP_USER_AGENT']);
		if(strpos($UA, 'WINDOWS NT') !== false){
			header("location:".$this->config['sy_weburl']."/index.php?c=wap");
		}
		$now_url=@explode("/",$_SERVER['REQUEST_URI']);
		$now_url=$now_url[count($now_url)-1];
		$this->yunset("now_url",$now_url);
	}
	function send_dingyue($id,$type){
		if($type=="2"){
			$job=$this->obj->DB_select_once("company_job","`id`='".$id."'","`name`,`hy`,`uid`");
			if($job['hy']>0){
				$user=$this->obj->DB_select_all("resume","FIND_IN_SET('".$job['hy']."',hy_dy)","`email_dy`,`msg_dy`,`email`,`telphone`,`uid`,`name`");
				if(is_array($user)&&$user){
					foreach($user as $v){
						$data['uid']=$v['uid'];
						$data['name']=$v['name'];
						$data['type']="userdy";
						$data['jobname']=$job['name'];
						if($v['email_dy']=="1"){
							$data['email']=$v['email'];
							$this->send_msg_email($data);
						}
						if($v['msg_dy']=="1"){
							$data['moblie']=$v['telphone'];
							$this->send_msg_email($data);
						}
					}
				}
			}
		}else{
			$expect=$this->obj->DB_select_once("resume_expect","`id`='".$id."'","`hy`,`name`");
			$user=$this->obj->DB_select_all("company","FIND_IN_SET('".$expect['hy']."',hy_dy)","`email_dy`,`msg_dy`,`uid`,`email`,`linktel`,`name`");
			if(is_array($user)&&$user){
				foreach($user as $v){
					$data['uid']=$v['uid'];
					$data['name']=$v['name'];
					$data['type']="comdy";
					$data['resumename']=$expect['name'];
					if($v['email_dy']=="1"){
						$data['email']=$v['email'];
						$this->send_msg_email($data);
					}
					if($v['msg_dy']=="1"){
						$data['moblie']=$v['linktel'];
						$this->send_msg_email($data);
					}
				}
			}
		}
	}

	function layer_msg($msg,$st='9',$type='0',$url='1',$tm='2'){
		if($type=='1'){
			$this->obj->ACT_layer_msg($msg,$st,$url);
		}else{
			if($st==9){$this->obj->admin_log($msg);}
			$msg = preg_replace('/\([^\)]+?\)/x',"",str_replace(array("（","）"),array("(",")"),$msg));
			$layer_msg['msg']=iconv("gbk","utf-8",$msg);
			$layer_msg['tm']=$tm;
			$layer_msg['st']=$st;
			$layer_msg['url']=$url;
			$msg = json_encode($layer_msg);
			echo $msg;die;
		}
	}

	function nextexe($value){

		if($value["type"]=='1' && $value["week"]>0)
		{
			$W   = date("w",time());
			if($value["week"]>=$W)
			{
				$Day = date("Ymd", strtotime("+".($value["week"]-$W)." days", time()));
			}else{
				$Day = date("Ymd", strtotime("+".($value["week"]-$W+7)." days", time()));//下周执行日期
			}
		}elseif($value['type']=='2' && $value["month"]>0){

			if($value["month"]<10)
			{
				$Day  = date('Ym')."0".$value["month"];
			}else{
				$Day  = date('Ym')."".$value["month"];
			}
		}else{

			$Day = date('Ymd');
		}

		$Date = $Day;
		if($value["hour"]>0)
		{
			if($value["hour"]<10)
			{
				$Date .= "0".$value["hour"];
			}else{
				$Date .= $value["hour"];
			}
		}else{

			$Date .= '00';
		}
		if($value["minute"]>0)
		{
			if($value["minute"]<10)
			{
				$Date .= "0".$value["minute"];
			}else{
				$Date .= $value["minute"];
			}
		}else{

			$Date .= '00';
		}

		if($Date<=date('YmdHi'))
		{
			if($value["type"]=='1' && $value["week"]>0)
			{
				$Dates = date('Ymd',strtotime("+1 week",$Date));

			}elseif($value['type']=='2' && $value["month"]>0){

				$nextmonth = $this->GetMonth();
				if($value["month"]<10)
				{
					$Dates  = $nextmonth.'0'.$value["month"];

				}else{

					$Dates  = $nextmonth.$value["month"];
				}

			}else{
				$Dates = date('Ymd',strtotime("+1 days",strtotime($Day)));
			}


			if($value["hour"]>0)
			{
				if($value["hour"]<10)
				{
					$Dates  .= '0'.$value["hour"];
				}else{
					$Dates  .= $value["hour"];
				}
			}else{
				$Dates  .= "00";
			}

			if($value["minute"]>0)
			{
				if($value["minute"]<10)
				{
					$Dates  .= '0'.$value["minute"];
				}else{
					$Dates  .= $value["minute"];
				}
			}else{
				$Dates  .= "00";
			}
			return 	$Dates;
		}else{
			return 	$Date;
		}
	}

	function GetMonth()
	{
		$tmp_date=date("Ym");
		$tmp_year=substr($tmp_date,0,4);
		$tmp_mon =substr($tmp_date,4,2);
		$tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);
		$tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);

		return $fm_next_month=date("Ym",$tmp_nextmonth);

	}

	function cron($id=''){
		@include PLUS_PATH.'cron.cache.php';

		if(is_array($cron) && !empty($cron))
		{
			foreach($cron as $key=>$value){

				if($id)
				{
					if($value['id']==$id)
					{
						$timestamp[$value['nexttime']] = $value;
						$timestamp[$value['nexttime']]['cronkey'] = $key;
					}
				}else{
					if($value['nexttime']<=time())
					{
						$timestamp[$value['nexttime']] = $value;
						$timestamp[$value['nexttime']]['cronkey'] = $key;
					}
				}
			}
			if($timestamp)
			{
				krsort($timestamp);
				$croncache = current($timestamp);

				ignore_user_abort();
				set_time_limit(600);

 				if(file_exists(LIB_PATH.'cron/'.$croncache['dir']))
				{
					include(LIB_PATH.'cron/'.$croncache['dir']);
					if($croncache['dir']=="notice.php")
					{
						$notice = new notice($this->obj);
						$notice->index();
					}
				}
				$nexttime = $this->nextexe($croncache);

				$this->obj->DB_update_all("cron","`nowtime`='".time()."',`nexttime`='".strtotime($nexttime)."'","`id`='".$value['id']."'");

				include_once(LIB_PATH."public.function.php");
				$cron[$croncache['cronkey']]['nexttime'] = strtotime($nexttime);
				$data['cron'] = ArrayToString2($cron);
				$this->obj->made_web_array(PLUS_PATH.'cron.cache.php',$data);
			}
		}

	}
	function wapheader($url,$point=''){
		if($point!='')
		{
			$point = 'point='.$point;
		}
		header('Location: '.$url.$point);
		exit();
	}
	function wapheaderLayer($url,$point=''){
		if($point!='')
		{
			$point = 'layer='.$point;
		}
		header('Location: '.$url.$point);
		exit();
	}
	function check_token(){

		if($_SESSION['pytoken']!=$_GET['pytoken'] || !$_SESSION['pytoken'])
		{
			unset($_SESSION['pytoken']);
			$this->obj->ACT_layer_msg("来源地址非法！",8,'index.php');
			exit();
		}
	}
	function job_auto(){
		if($this->config['autodate'] != date('Ymd'))
		{
			$sqlCase ="lastupdate = case when autotype = 1  then '".time()."' ";
			$sqlCase.="when autotype = 5 AND lastupdate<=".(time()-5*86400)." then  '".time()."'  ";
			$sqlCase.="when autotype = 10 AND lastupdate<=".(time()-10*86400)." then  '".time()."'  ";
			$sqlCase.="else lastupdate end";
			$this->obj->DB_update_all('company_job',$sqlCase,"`autotime`>='".time()."'");
 			$config=$this->obj->DB_select_all("admin_config");
			if(is_array($config)){
				foreach($config as $v){
					$configarr[$v['name']]=$v['config'];
				}
			}
			$configarr['autodate'] = date('Ymd');
			include_once(LIB_PATH."/public.function.php");
			$this->obj->made_web(PLUS_PATH."config.php",ArrayToString($configarr),"config");
		}
	}
	function insert_company_pay($integral,$pay_state,$uid,$msg,$type,$pay_type=''){
		if($integral!='0'){
			$pay['order_id']=time().rand(10000,99999);
			$pay['order_price']='-'.$integral;
			$pay['pay_time']=time();
			$pay['pay_state']=$pay_state;
			$pay['com_id']=$uid;
			$pay['pay_remark']=$msg;
			$pay['type']=$type;
			$pay['pay_type']=$pay_type;
			return $this->obj->insert_into("company_pay",$pay);
		}else{
			return true;
		}
	}
	function insertfinder($para,$id='',$name=''){
		$data['name']=$name;
		$data['uid']=$this->uid;
		$data['para']=$para;
		if($id){
			$this->obj->member_log("修改搜索器");
			return $this->obj->update_once("finder",$data,"`id`='".$id."'");
		}else{
			$data['usertype']=$_COOKIE['usertype'];
			$data['addtime']=time();
			$this->obj->member_log("添加搜索器");
			return $this->obj->insert_into("finder",$data);
		}
	}
	function stringfilter($string){

		$str=iconv("utf-8","gbk",trim($string));

		$regex = "/\\$|\'|\\\|/";
		$str=preg_replace($regex,"",$str);
		return $str;
	}
	function pylode($string,$array){

		$str = @implode($string,$array);
		if(!preg_match("/^[0-9,]+$/",$str) && $string==',')
		{
			$str = 0;
		}
		return $str;
	}
	function CheckRegUser($str){

		if(!preg_match("/^[".chr(0xa1)."-".chr(0xff)."a-zA-Z0-9_]+$/",$str)){

			return false;

		}else{

			return true;
		}
	}
	function CheckRegEmail($email){

		if(!preg_match('/^([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9\-]+@([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/',$email)) {
			return false;

		}else{

			return true;
		}
	}
	function upuser_statis($order){
		if($order['order_state']!='2'){
			$usertype=$this->obj->DB_select_once("member","`uid`='".$order["uid"]."'","usertype");
			if($usertype['usertype']=='1'){
				$table='member_statis';
			}else if($usertype['usertype']=='2'){
				$table='company_statis'; 
				$tvalue=",`all_pay`=`all_pay`+".$order["order_price"];
			} 
			if($order['type']=='1'&&$order['rating']&&$usertype['usertype']=='2'){
				$row=$this->obj->DB_select_once("company_rating","`id`='".$order['rating']."'");
				$value="`rating`='".$row['id']."',";
				$value.="`rating_name`='".$row['name']."',";
				$value.="`rating_type`='".$row['type']."',";
				if($row['service_time']>0){
					$viptime=time()+$row['service_time']*86400;
				}else{
					$viptime=0;
				}
				$value.="`vip_etime`='".$viptime."',";
				$value.="`job_num`='".$row['job_num']."',";
				$value.="`down_resume`='".$row['resume']."',";
				$value.="`invite_resume`='".$row['interview']."',";
				$value.="`editjob_num`='".$row['editjob_num']."',";
				$value.="`breakjob_num`='".$row['breakjob_num']."'";
				$status=$this->obj->DB_update_all("company_statis",$value,"`uid`='".$order["uid"]."'");
			}else if($order['type']=='2'&&$order['integral']){ 
				$status=$this->obj->DB_update_all($table,"`integral`=`integral`+'".$order['integral']."'".$tvalue,"`uid`='".$order["uid"]."'");
			}else if($order['type']=='3'||$order['type']=='4'){
				$status=$this->obj->DB_update_all($table,"`pay`=`pay`+".$order["order_price"].$tvalue,"`uid`='".$order["uid"]."'");
			}else if($order['type']=='5'&&$order['integral']&&$usertype['usertype']=='2'){ 
				$status=$this->obj->DB_update_all("company_statis","`msg_num`=`msg_num`+'".$order['integral']."',`all_pay`=`all_pay`+".$order["order_price"],"`uid`='".$order["uid"]."'");
			}
			if($this->config['sy_msg_fkcg']=='1'||$this->config['sy_email_fkcg']=='1'){
				$member=$this->obj->DB_select_once("member","`uid`='".$order['uid']."'","`email`,`moblie`,`uid`,`usertype`");
				$fdata=$this->forsend($member);
				$data=array();
				$data["uid"]=$order['uid'];
				$data["name"]=$fdata['name'];
				$data["type"]="fkcg";
				$data["order_id"]=$order['order_id'];
				$data["price"]=$order['order_price'];
				$data['webtel']=$this->config['sy_freewebtel'];
				$data['webname']=$this->config['sy_webname'];
				if($this->config['sy_msg_fkcg']=='1'&&$member['moblie']){$data["moblie"]=$member["moblie"]; }
				if($this->config['sy_email_fkcg']=='1'&&$member['email']){$data["email"]=$member["email"]; }
				$this->send_msg_email($data);
			}
			$this->obj->DB_update_all("company_order","`order_state`='2'","`id`='".$order['id']."'");
			return $status;
		}
	}
	function insert_emailmsg($info){
		$data_msg["uid"]=$info['uid'];
		$data_msg["name"]=$info['name'];
		$data_msg["cuid"]=$info['cuid'];
		$data_msg["cname"]=$info['cname'];
		$data_msg["email"]=$info['email'];
		$data_msg["ctime"]=time();
		$data_msg["state"]=$info['state'];
		$data_msg["title"]=$info['title'];
		$data_msg["content"]=$info['content'];
		$this->obj->insert_into("email_msg",$data_msg);
	}
	function forsend($data){
		if($data['usertype']=='1'){
			$info=$this->obj->DB_select_once("resume","`uid`='".$data['uid']."'","`name`,`uid`");
		}
		if($data['usertype']=='2'){
			$info=$this->obj->DB_select_once("company","`uid`='".$data['uid']."'","`name`,`uid`");
		}
		return $info;
	}
	function get_integral_action($uid,$type,$msg)
	{
		if($this->config[$type.'_type']=="1"){
			$auto=true;
		}else{
			$auto=false;
		}

		if($_COOKIE['usertype']=="1"){
			$this->obj->company_invtal($uid,$this->config[$type],$auto,$msg,true,2,'integral');
		}elseif($_COOKIE['usertype']=="2"){
			$this->obj->company_invtal($uid,$this->config[$type],$auto,$msg,true,2,'integral');
		}
	}
	function subscribe()
	{
		$time=strtotime(date("Y-m-d"));
		if($this->config['subscribe_time']<$time)
		{
			$subscribe = $this->obj->DB_select_all("subscribe","status='1'");
			if(is_array($subscribe))
			{
				foreach($subscribe as $v)
				{
					$time=86400*$v['time']+$v['ctime'];
					if($time<time())
					{
						if($v['type']=="1")
						{
							$this->select_job($v);
						}else{
							$this->select_resume($v);
						}
					}
				}
			}
			$this->obj->DB_update_all("admin_config","`config`='".time()."'","`name`='subscribe_time'");
			$this->web_config();
		}

	}
	function select_job($v)
	{
		$where.="`state`='1' and `edate`>'".time()."' and `job1`='".$v['job1']."' and `job1_son`='".$v['job1_son']."' and `job_post`='".$v['job_post']."' and `provinceid`='".$v['provinceid']."'";
		if($v['salary']>"0")
		{
			$where.=" and `salary`='".$v['salary']."'";
		}
		if($v['cityid']>"0")
		{
			$where.=" and `cityid`='".$v['cityid']."'";
		}
		if($v['three_cityid']>"0")
		{
			$where.=" and `three_cityid`='".$v['three_cityid']."'";
		}
		$job=$this->obj->DB_select_all("company_job",$where." order by sdate desc limit 5","name");
		if(!empty($job))
		{
			foreach($job as $val)
			{
				$name[]=$val['name'];
			}
			$data['jobname']=@implode(",",$name);
			$data['email']=$v['email'];
			$data['type']="userdy";
			$this->send_msg_email($data);
		}
		$this->obj->DB_update_all("subscribe","`ctime`='".time()."'","`id`='".$v['id']."'");
	}
	function select_resume($v)
	{
		$where = "a.status<>'2' and a.`r_status`<>'2' and b.`open`='1' and a.`uid`=b.`uid`";
		$where.=" and b.provinceid='".$v['provinceid']."' and FIND_IN_SET('".$v['job_post']."',b.job_classid)";
		if($v['salary']>"0")
		{
			$where.=" and b.`salary`='".$v['salary']."'";
		}
		if($v['cityid']>"0")
		{
			$where.=" and b.`cityid`='".$v['cityid']."'";
		}
		if($v['three_cityid']>"0")
		{
			$where.=" and b.`three_cityid`='".$v['three_cityid']."'";
		}
		$userlist=$this->obj->DB_select_alls("resume","resume_expect",$where." order by b.ctime desc limit 5","b.name");
		if(is_array($userlist))
		{
			foreach($userlist as $val)
			{
				$name[]=$val['name'];
			}
			$data['resumename']=@implode(",",$name);
			$data['email']=$v['email'];
			$data['type']="comrdy";
			$this->send_msg_email($data);
		}
		$this->obj->DB_update_all("subscribe","`ctime`='".time()."'","`id`='".$v['id']."'");
	}


	function warning($type)
	{
		$time=strtotime(date("Y-m-d"));
		if($type==1)
		{
			$num=$this->obj->DB_select_num("company_job","`uid`='".$this->uid."' and `sdate`>'".$time."'");
			if($num>=$this->config['warning_addjob'])
			{
				$this->send_warning($type);
			}
		}elseif($type==2){
			$num=$this->obj->DB_select_num("down_reume","`comid`='".$this->uid."' and `downtime`>'".$time."'");
			if($num>=$this->config['warning_downresume'])
			{
				$this->send_warning($type);
			}
		}elseif($type==3){
			$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."' and `ctime`>'".$time."'");
			if($num>=$this->config['warning_addresume'])
			{
				$this->send_warning($type);
			}
		}elseif($type==4){
			$this->send_warning($type);
		}
	}
	function send_warning($type)
	{
		$time=strtotime(date("Y-m-d"));
		$row=$this->obj->DB_select_once("warning","`type`='".$type."' and `uid`='".$this->uid."' and `ctime`>='".$time."'");
		if(empty($row))
		{
			$this->obj->DB_insert_once("warning","`type`='".$type."',`uid`='".$this->uid."',`ctime`>='".$time."'");
			$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","email");
			if($type=="1")
			{
				$emailcoment="用户：【".$this->username."】发布职位超出规定数目，请检查是否有问题";
				if($this->config['warning_addjob_type']=="1")
				{
					$this->obj->DB_update_all("company_job","`r_status`='2'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company","`r_status`='2'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("member","`status`='2',`lock_info`='发布职位超出规定数目'","`uid`='".$this->uid."'");
					$this->send_msg_email(array("email"=>$member['email'],"uid"=>$this->uid,"name"=>$this->username,"lock_info"=>'发布职位超出规定数目',"type"=>"lock"));
				}
			}elseif($type=="2"){
				$emailcoment="用户：【".$this->username."】下载简历超出规定数目，请检查是否有问题";
				if($this->config['warning_downresume_type']=="1")
				{
					$this->obj->DB_update_all("company_job","`r_status`='2'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company","`r_status`='2'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("member","`status`='2',`lock_info`='下载简历超出规定数目'","`uid`='".$this->uid."'");
					$this->send_msg_email(array("email"=>$member['email'],"uid"=>$this->uid,"name"=>$this->username,"lock_info"=>'下载简历超出规定数目',"type"=>"lock"));
				}
			}elseif($type=="3"){
				$emailcoment="用户：【".$this->username."】简历发布超出规定数目，请检查是否有问题";
				if($this->config['warning_addresume_type']=="1")
				{
			 		$this->obj->DB_update_all("member","`status`='2',`lock_info`='简历发布超出规定数目'","`uid`='".$this->uid."'");
			 		$this->obj->DB_update_all("resume","`r_status`='2'","`uid`='".$this->uid."' ");
					$this->send_msg_email(array("email"=>$member['email'],'uid'=>$this->uid,'name'=>$this->username,"lock_info"=>'简历发布超出规定数目',"type"=>"lock"));
				}
			}elseif($type=="4"){
				$emailcoment="用户：【".$this->username."】充值超出规定金额，请检查是否有问题";
				if($this->config['warning_recharge_type']=="1")
				{
					$this->obj->DB_update_all("company_job","`r_status`='2'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company","`r_status`='2'","`uid`='".$this->uid."'");
					$this->obj->DB_update_all("member","`status`='2',`lock_info`='充值超出规定金额'","`uid`='".$this->uid."'");
					$this->send_msg_email(array("email"=>$member['email'],"uid"=>$this->uid,"name"=>$this->username,"lock_info"=>'充值超出规定金额',"type"=>"lock"));
				}
			}

			if($this->config['sy_smtpserver']!="" && $this->config['sy_smtpemail']!="" && $this->config['sy_smtpuser']!="" && $this->config['sy_webemail']!="")
			{
				$smtp=$this->email_set();
				$smtpusermail =$this->config['sy_smtpemail'];
		 		$sendid = $smtp->sendmail($this->config['sy_webemail'],$smtpusermail,"预警提醒",$emailcoment);
			}
		}
	}

	function getToken()
	{
		$Token = $this->config['token'];
		$TokenTime = $this->config['token_time'];
		$NowTime = time();

		if(($NowTime-$TokenTime)>7000)
		{
			$Appid       = $this->config['wx_appid'];
			$Appsecert   = $this->config['wx_appsecret'];


			$Url         = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$Appid.'&secret='.$Appsecert;
			$CurlReturn  = $this->CurlPost($Url);
			$Token       = json_decode($CurlReturn);

			$this->config['token']      = $Token->access_token;
			$this->config['token_time'] = time();
			include_once(LIB_PATH."/public.function.php");
			$this->obj->made_web(PLUS_PATH."config.php",ArrayToString($this->config),"config");
			return $this->config['token'];
		}else{
			return $Token;
		}
	}
	function CurlPost($url,$data='')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		if($data!='')
		{
			curl_setopt($ch, CURLOPT_POST, 1);
   			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$Return=curl_exec ($ch);
		if (curl_errno($url)) {
		   echo 'Errno'.curl_error($url);
		}
		curl_close($ch);

		return $Return;
	}
	function getYears($startYear=0,$endYear=0){
		$list=array();
		$month_list=array();
		if($endYear>0){
			if($startYear<=0){
				$startYear=	$endYear-150;
			}
			for($i=$endYear;$i>=$startYear;$i--){
				$list[]=$i;
			}
		}
		for($i=12;$i>=1;$i--){
			$month_list[]=$i;
		}
		$this->yunset("year_list",$list);
		$this->yunset("month_list",$month_list);
		return $list;
	}
}
?>