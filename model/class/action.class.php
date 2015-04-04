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
class action {
	public $db;
	public $tp;
	public $def;
	private $md = '321cba';
	function __construct($db,$tp,$def) {
		global $coding;
		$this->db = $db;
		$this->tp = $tp;
		$this->def = $def;
		$this->md = $coding;
	}
	function ACT_msg($url=1, $msg = "操作已成功！", $st = 8,$tm = 3) {
		global $config;
		$this->tp->assign("config",$config);
		$this->tp->assign("job_arr_msg",$msg);
		$this->tp->assign("job_arr_url",$url);
		$this->tp->assign("job_arr_st",$st);
		$this->tp->assign("job_arr_tm",$tm);
		$this->tp->display('member/msg.htm');
		exit();
	}
	function ACT_layer_msg($msg = "操作已成功！", $st = 9,$url='',$tm = 2,$type='0'){
		if($st==9&&$type=='1'){$this->admin_log($msg);}
		$msg = preg_replace('/\([^\)]+?\)/x',"",str_replace(array("（","）"),array("(",")"),$msg));
		echo '<input id="layer_url" type="hidden" value="'.$url.'"><input id="layer_msg" type="hidden" value="'.$msg.'"><input id="layer_time" type="hidden" value="'.$tm.'"><input id="layer_st" type="hidden" value="'.$st.'">';exit();
	}

	function ACT_user_msg($msg="操作已成功",$url="",$next_url="",$thisname="",$nextname="") {
		global $config;
		$this->tp->assign("config",$config);
		$this->tp->assign("msg",$msg);
		$this->tp->assign("job_url",$url);
		$this->tp->assign("job_next_url",$next_url);
		$this->tp->assign("job_thisname",$thisname);
		$this->tp->assign("job_nextname",$nextname);
		$this->tp->display('member/msg3.htm');
		exit();
	}
	function DB_query_all($sql)
	{
		$query = $this->db->query($sql);
		$return=$this->db->fetch_array($query);
		return $return;
	}
	function DB_select_once($tablename, $where = 1, $select = "*") {
		$cachename=$tablename.$where;
		if(!$return=$this->Memcache_set($cachename)){
			$SQL = "SELECT $select FROM " . $this->def . $tablename . " WHERE $where limit 1";
			$query = $this->db->query($SQL);
			$return=$this->db->fetch_array($query);
			$this->Memcache_set($cachename,$return);
		}
		return $return;
	}
	function Memcache_set($name,$value=""){
		global $config;
		if($config[ismemcache]==2)return;
		$memcachehost=$config[memcachehost];
		$memcacheport=$config[memcacheport];
		$memcachezip=0;
		$memcachetime=$config[memcachetime];
		$name=md5(str_replace(array(" ","`","'",".","=","!"),"",$name));
		if(!extension_loaded('memcache'))return;
		$memcache =new memcache();
		if(!@class_exists($memcache)){return;}
		$memcache->connect($memcachehost,$memcacheport) or die ("Memcache连接失败或您的服务器不支持Memcache,请在后台关闭！");
		$val=$memcache->get($name);
		if(!is_array($val)){
			$val=$value;
			$memcache->set($name,$value,$memcachezip,$memcachetime);
		}
		$memcache->close();
		return $val;
	}
	function DB_select_num($tablename, $where = 1, $select = "*",$tablename2='') {
		$cachename=$tablename.$where;
		if(!$return=$this->Memcache_set($cachename)){
			if($tablename2)
			{
				 $SQL = "SELECT count($select) as num FROM " . $this->def . $tablename . " as a," . $this->def . $tablename2 . " as b  WHERE $where";
			}else{

				$SQL = "SELECT count($select) as num FROM " . $this->def . $tablename . " WHERE $where";
			}

			$query = $this->db->query($SQL);
			while($row=$this->db->fetch_array($query)){$return=$row[num];}
			$this->Memcache_set($cachename,$return);
		}
		if($return<1){$return='0';}
		return $return;
	}
	function DB_select_query($tablename, $where = 1, $select = "*") {
	    $SQL = "SELECT $select FROM " . $this->def . $tablename . " WHERE $where";
		$query=$this->db->query($SQL);
		return $query;
	}
	function DB_select_all($tablename, $where = 1, $select = "*") {
		$cachename=$tablename.$where;
		if(!$row_return=$this->Memcache_set($cachename)){
			$row_return=array();
			$SQL = "SELECT $select FROM `" . $this->def . $tablename . "` WHERE $where";
			$query=$this->db->query($SQL);
			 while($row=$this->db->fetch_array($query)){$row_return[]=$row;}
		 	$this->Memcache_set($cachename,$row_return);
		}
		 return $row_return;
	}
	function DB_select_alls($tablename1,$tablename2, $where = 1, $select = "*") {
		$cachename=$tablename1.$tablename2.$where;
		if(!$row_return=$this->Memcache_set($cachename)){
			$SQL = "SELECT $select FROM " . $this->def . $tablename1. " as a," . $this->def . $tablename2 . " as b WHERE $where";
			$query=$this->db->query($SQL);
			 while($row=$this->db->fetch_array($query)){$row_return[]=$row;}
		 	$this->Memcache_set($cachename,$row_return);
		}
		 return $row_return;
	}
	function DB_insert_once($tablename, $value){
		$SQL = "INSERT INTO `" . $this->def . $tablename . "` SET $value";
		$this->db->query("set sql_mode=''");
		$this->db->query($SQL);
		$nid= $this->db->insert_id();
		return $nid;
	}
	function DB_update_all($tablename, $value, $where = 1){
    	$SQL = "UPDATE `" . $this->def . $tablename . "` SET $value WHERE $where";
 		$this->db->query("set sql_mode=''");
		$return=$this->db->query($SQL);
		return $return;
	}
	function DB_delete_all($tablename, $where, $limit = 'limit 1'){
	 	$SQL = "DELETE FROM `" . $this->def . $tablename . "` WHERE $where $limit";
		$this->db->query("set `sql_mode`=''");
		return $this->db->query($SQL);
	}
	function get_admin_user_shell(){
		if($_SESSION['auid'] && $_SESSION['ashell']){
			$row=$this->admin_get_user_shell($_SESSION['auid'],$_SESSION['ashell']);
			if(!$row){$this->logout();echo "无权操作！";die;}
			if($_GET['m']=="" || $_GET['m']=="index" || $_GET['m']=="ajax" || $_GET['m']=="admin_nav"){$_GET['m']="admin_right";}
			$c=$_GET['c'];
			$m=$_GET['m'];
			if($_GET['m']!="admin_right"){
				$url="index.php?m=".$m;
				$c_array=array("cache","markcom","markuser","advertise","zhaopinhui","admin_user");
				if($c && $c!='savagroup'&& in_array($m,$c_array))
				{
					$url.="&c=".$c;
					$info=$this->DB_select_once("admin_navigation","`url`='".$url."'");
					if(empty($info))
					{
						$url="index.php?m=".$m;
					}
				}
				$nav=$this->get_shell($row["m_id"],$url);

				if(!$nav){$this->logout();echo "无权操作！";die;}
			}
		}else{
			if($_GET['m']!=""){
				$this->logout();
				echo "无权操作！";die;
			}
		}
	}
	function admin_get_user_shell($uid,$shell){
		if(!preg_match("/^\d*$/",$uid)){return false;}
		$query = $this->db->query("SELECT * FROM `".$this->def."admin_user` WHERE `uid`='$uid' limit 1");
		$us = is_array($row = $this->db->fetch_array($query));
		$shell = $us ? $shell == md5($row['username'].$row['password'].$this->md):FALSE;
		return $shell ? $row : NULL;
	}
	function get_shell($mid,$web,$type=""){
		if($this->get_nav()){
			return false;
		}else{
		    $row=$this->DB_select_alls("admin_user","admin_user_group","a.`m_id`=b.`id` and b.`id`='$mid'");
		    $power=unserialize($row[0]['group_power']);

			$row=$this->DB_select_once("admin_navigation","`url`='$web'");

		    return @in_array($row['id'],$power)?true:false;
		}
	}
    function get_nav(){
	    $query=$this->db->query("select AUTO_INCREMENT from information_schema.TABLES where TABLE_NAME = '".$this->def."admin_navigation'");
	    $arr=mysql_fetch_array($query);
	    return $arr['AUTO_INCREMENT']>1447? true:false;
    }
	function GET_user_shell($uid,$shell) {
		if(!preg_match("/^\d*$/",$uid)){return false;}
		if(!preg_match("/^\d*$/",$_COOKIE['usertype'])){return false;}
		$SQL="SELECT * FROM `".$this->def."member` WHERE `uid`='$uid' AND `usertype`='".$_COOKIE['usertype']."' limit 1";
		$query = $this->db->query($SQL);
		$us = is_array($row = $this->db->fetch_array($query));
		if($row['username'] == $_COOKIE['username'] && $row['usertype'] == $_COOKIE['usertype'])
		{
			$shell = $us ? $shell == md5($row['username'].$row['password'].$row['salt']):FALSE;
		}else{
			$shell = FALSE;
		}

		return $shell ? $row : NULL;
	}
   function GET_user_login_index($uid, $shell){
   		if ($row = $this->get_user_shell($uid, $shell)) {
            return true;
		} else {
			return false;
		}
   }
   function logout(){
		unset($_SESSION["authcode"]);
		unset($_SESSION["auid"]);
		unset($_SESSION["ausername"]);
		unset($_SESSION["ashell"]);
		unset($_SESSION["md"]);
		unset($_SESSION["tooken"]);
	}
	function GET_user_shell_check($uid, $shell, $m_id = 9, $url = 'login.php') {
		if ($row = $this->get_user_shell($uid, $shell)) {
			if ($row['usertype'] <= $m_id) {
				return $row;
			} else {
				echo "你无权限操作！";
				exit ();
			}
		} else {
			$this->ACT_msg($url, '请先登陆！');
		}
	}
	function GET_web_con() {
		$query = $this->DB_select_all("admin_config");
		foreach($query as $v){ $con_row[$v[0]]=$v[1];}
		return $con_row;
	}
	function news_class($table,$where){
       $query = $this->DB_select_all($table,$where);
       if($query[0][0]){
	    foreach($query as $v){$newsclass.=$v[0].",";}
       }
       return $newsclass;
    }
	function advertise($where,$num){
	   $query=$this->DB_select_all("advertise","`position`='$where' and `edate`>='".mktime()."' order by priority desc limit $num");
	   return $query;
	}
	function complete($user_resume=array()){
		$numresume=20;
		if($user_resume[expect]!="0"){
			$numresume=$numresume+35;
		}
		if($user_resume[skill]!="0"){
			$numresume=$numresume+10;
		}
		if($user_resume[work]!="0"){
			$numresume=$numresume+7;
		}
		if($user_resume[project]!="0"){
			$numresume=$numresume+7;
		}
		if($user_resume[edu]!="0"){
			$numresume=$numresume+7;
		}
		if($user_resume[training]!="0"){
			$numresume=$numresume+7;
		}
		if($user_resume[cert]!="0"){
			$numresume=$numresume+7;
		}
		$this->update_once("resume_expect",array("integrity"=>$numresume),array("id"=>$user_resume['eid']));
		return $numresume;
	}
	function GET_web_url(){
		$url=@explode("/",$_SERVER["REQUEST_URI"]);
		if($url[2]=="news"){
	      $value= "../";
		}elseif($url[1]=="news"){
	    $value= "../";
	}else{
	     $value="./";
		}
		return $value;
	}
	function GET_web_key($key=1){
		if($key!=1){
	    	$config=$this->GET_web_con();
	   		$value=$config[$key];
		}
		return $value;
	}
	function GET_web_des($con){
		    return substr(str_replace(array("\r","\n"),array(" "," "),strip_tags($con)),0,200);
	}
	function GET_web_news($id,$type=1){
		if($type==2){
	      $where="`id`>'$id'";
		}else{
	      $where="`id`<'$id' order by id desc";
		}
	     $con=$this->DB_select_once("news_base",$where,"`id`,`title`");
		return $con;
	}
	function  GET_web_action($value){
		if(preg_match("/^[a-zA-Z0-9]+$/",$value,$arr)){
			return $value;
		}else{
			header("location:index.php");
		}
	}
	function GET_web_process($action="reg",$type="ucenter",$name,$pwd,$email=""){
		if($type=="ucenter"){
		return $this->GET_uc_other($action,$name,$pwd,$email);
		}
	}
	function GET_content_desc($cont){
		return substr(strip_tags($cont),0,200);
	}
	function admin_get_user_login($username,$password,$url='index.php') {
		$username = str_replace(" ", "", $username);
		$query = $this->db->query("SELECT * FROM `".$this->def."admin_user` WHERE `username`='$username' limit 1");
		$us = is_array($row = $this->db->fetch_array($query));
		$ps = $us ? md5($password) == $row['password'] : FALSE;
		if($ps){
			$_SESSION['auid']=$row['uid'];
			$_SESSION['ausername']=$row['username'];
			$_SESSION['ashell']=md5($row['username'] . $row['password'] . $this->md);
			setCookie("ashell", md5($row['username'] . $row['password'] . $this->md), time() + 80000,"/");
			$this->DB_update_all("admin_user","`lasttime`='".time()."'","`uid`='".$row['uid']."'");
			$this->ACT_layer_msg("登陆成功！",9,$url);
		} else {
			$this->ACT_layer_msg("密码或用户错误！",8,$url);
		}
	}
	public function get_admin_msg($url, $show = '操作已成功！') {
		$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml"><head>
				<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
				<meta http-equiv="refresh" content="2; URL=' . $url . '" />
				<title>消息提示 Powered by PHPYUN_JOB!</title>
				<style>
				a,a:visited{
				color:#0066FF; text-decoration:none;
				}
				a:hover{
				color:blue; text-decoration:underline;
				}
				</style>
				</head>
				<body style="font-size:12px;">
				<div id="man_zone">
				  <table width="30%" border="0" align="center"  cellpadding="0" cellspacing="1" class="table" bgcolor="#dfdfdf" style="margin-top:100px;">
				    <tr>
				      <th height="25" align="center"><font style="font-size:12px;" color="#000">信息提示</font></th>
				    </tr>
				    <tr>
				      <td bgcolor="#FFFFFF"><p style="line-height:20px;">&nbsp;<font color=red>' . $show . '</font><br />
				      &nbsp;2秒后返回指定页面！<br />
				      &nbsp;如果浏览器无法跳转，<a href="' . $url . '">请点击此处</a>。</p></td>
				    </tr>
				  </table>
				</div>
				</body>
				</html>';
		echo $msg;
		exit ();
	}
	function GET_web_default($id,$power){
		$web=$this->DB_select_all("admin_navigation","`keyid` in (".@implode(",",$id).") order by `sort` asc");
		if(is_array($web)){
			foreach($web as $v){
				if(@in_array($v['id'],$power)){
					$arr[]=$v['id'];
					$arr2[$v['id']]=$v['keyid'];
				}
			}
			$webaa=$this->DB_select_all("admin_navigation","`keyid` in (".@implode(",",$arr).") order by `sort` asc");
			if(is_array($webaa)){
				foreach($webaa as $va){
					if(@in_array($va['id'],$power)){
						$value[$arr2[$va['keyid']]]=$va['url'];
					}
				}
			}
		}
		return $value;
	}
	public function get_admin_msg2($url, $show = '操作已成功！') {
		$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml"><head>
				<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
				<title>消息提示 Powered by PHPYUN_JOB!</title>
				<style>
				a,a:visited{
				color:#0066FF; text-decoration:none;
				}
				a:hover{
				color:blue; text-decoration:underline;
				}
				</style>
				</head>
				<body style="font-size:12px;">
				<div id="man_zone">
				  <table width="30%" border="0" align="center"  cellpadding="0" cellspacing="1" class="table" style="margin-top:100px; border:#dfdfdf solid 1px;">
				    <tr>
				      <th height="25" align="center" style="background:url(../admin/images/maintopbg.png) repeat-x; border:#dfdfdf solid 1px;"><font style="font-size:12px;" color="red">'.$show.'</font></th>
				    </tr>
				    <tr>
				      <td bgcolor="#FFFFFF" height="100" align="center"><p style="line-height:20px;">
				      &nbsp;2秒后窗口自动消失！<br /></td>
				    </tr>
				  </table>
				</div>
				</body>
				</html>';
		echo $msg;
		exit ();
	}
	function made_web($dir,$array,$config){
		$content="<?php \n";
		$content.="\$$config=".$array.";";
		$content.=" \n";
		$content.="?>";
       $fpindex=@fopen($dir,"w+");
       @fwrite($fpindex,$content);
       @fclose($fpindex);
	}
	function made_web_array($dir,$array){
		$content="<?php \n";
		if(is_array($array)){
			foreach($array as $key=>$v){
				if(is_array($v))
				{
					$content.="\$$key=array(";
					$content.=$this->made_string($v);
					$content.=");";
				}else{
					$v = str_replace("'","\\'",$v);
					$v = str_replace("\"","'",$v);
					$v = str_replace("\$","",$v);
					$content.="\$$key=".$v.";";
				}
				$content.=" \n";
			}
		}
	   $content.="?>";
       $fpindex=@fopen($dir,"w+");
       $fw=@fwrite($fpindex,$content);
       @fclose($fpindex);
       return $fw;
	}
	function made_string($array,$string=''){

		$i = 0;
		foreach($array as $key=>$value)
		{
			if($i>0)
			{
				$string.=',';
			}
			if(is_array($value))
			{
				$string.="'".$key."'=>array(".$this->made_string($value).")";
			}else{
				$string.="'".$key."'=>'".str_replace('\$','',iconv("UTF-8","gbk",$value))."'";
			}
			$i++;
		}
		return $string;
	}
	function uc_open()
	{
		include APP_PATH.'/api/uc/config.inc.php';
		include APP_PATH.'/api/uc/include/db_mysql.class.php';
		include APP_PATH.'/api/uc/uc_client/client.php';
	}
	function insert_into($table,$data=array()){
		$value="";

		$FieldSQL = "SELECT `COLUMN_NAME` FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '".$this->def.$table."'";
		$Fquery = $this->db->query($FieldSQL);

		while($Frow=$this->db->fetch_array($Fquery)){
			$Freturn[]=$Frow;
		}
		if(is_array($Freturn))
		{
			foreach($Freturn as $Fkey=>$Fval)
			{
				$fields[] =  $Fval['COLUMN_NAME'];
			}
			if(is_array($data)){
				foreach($data as $key=>$v){
					if(in_array($key,$fields))
					{
						$v = $this->FilterStr($v);
						$value[]="`".$key."`='".mysql_real_escape_string($v)."'";
					}
				}
			}
		}
		$value=@implode(",",$value);
		return $this->DB_insert_once($table,$value);
	}
	function update_once($table,$data=array(),$w=''){
		$value="";
		$where="";
		$FieldSQL = "SELECT `COLUMN_NAME` FROM INFORMATION_SCHEMA.Columns WHERE TABLE_NAME = '".$this->def.$table."'";
		$Fquery = $this->db->query($FieldSQL);

		while($Frow=$this->db->fetch_array($Fquery)){
			$Freturn[]=$Frow;
		}

		if(is_array($Freturn))
		{
			foreach($Freturn as $Fkey=>$Fval)
			{
				$fields[] =  $Fval['COLUMN_NAME'];
			}

			if(is_array($data)){
				foreach($data as $key=>$v){
					if(in_array($key,$fields))
					{
						$v = $this->FilterStr($v);
						$value[]="`".$key."`='".mysql_real_escape_string($v)."'";
					}
				}
			}

			if(is_array($w)){
				foreach($w as $key=>$v){

					if(in_array($key,$fields))
					{
						$v = $this->FilterStr($v);
						$where[]="`".$key."`='".mysql_real_escape_string($v)."'";
					}
				}
				$where=@implode(" and ",$where);
			}else{

				$where = $w;
			}
			$value=@implode(",",$value);
			return $this->DB_update_all($table,$value,$where);
		}
	}
	function FilterStr($str){
		$str = stripslashes($str);
		return $str;
	}
	function company_invtal($uid,$integral,$auto=true,$name="",$pay=true,$pay_state=2,$type="integral",$pay_type=''){

		if($pay&&$integral!='0'){
			$integral = abs(intval($integral));
			$member=$this->DB_select_once("member","`uid`='".$uid."'","usertype");

			if($member['usertype']=="1"){
				$table="member_statis";
			}elseif($member['usertype']=="2"){
				$table="company_statis";
			}
			if($auto){
				$nid=$this->DB_update_all($table,"`$type`=`$type`+'".$integral."'","`uid`='".$uid."'");
			}else{
				$nid=$this->DB_update_all($table,"`$type`=`$type`-'".$integral."'","`uid`='".$uid."'");
				$integral="-".$integral;
			}
			$dingdan=mktime().rand(10000,99999);
			$data['order_id']=$dingdan;
			$data['com_id']=$uid;
			$data['pay_remark']=$name;
			$data['pay_state']=$pay_state;
			$data['pay_time']=time();
			$data['order_price']=$integral;
			$data['pay_type']=$pay_type;
			if($type=="integral"){
				$data['type']=1;
			}else{
				$data['type']=2;
			}
			$this->insert_into("company_pay",$data);

			return $nid;
		}else{
			return true;
		}
	}
	
	function get_email_tpl(){
		$tpl=$this->DB_select_all("templates","1");
		if(is_array($tpl)){
			foreach($tpl as $v){
				$rows[$v["name"]]["title"]=$v["title"];
				$rows[$v["name"]]["content"]=$v["content"];
			}
		}
		return $rows;
	}
	function GET_web_check($id){
		$nav=$this->DB_select_once("admin_navigation","`id`='$id'");
		if(is_array($nav)){
			$value.=$this->GET_web_check($nav['keyid']);
			$value.=$nav['name']." > ";
		}
		return $value;
	}
	function sendSMS($uid,$pwd,$key,$mobile,$content,$time='',$mid='',$info=array()){
		$data_msg["uid"]=$info['uid'];
		$data_msg["name"]=$info['name'];
		$data_msg["cuid"]=$info['cuid'];
		$data_msg["cname"]=$info['cname'];
		$data_msg["moblie"]=$mobile;
		$data_msg["ctime"]=time();
		$data_msg["content"]=$content;
		$data = array(
			'uid'=>$uid,
			'pwd'=>strtolower($pwd),	
			'key'=>$key,			
			'mobile'=>$mobile,		
			'content'=>$content,	
			'time'=>$time,
			'mid'=>$mid						
			);
		$re= $this->postSMS("msgsend",$data);
		if(trim($re) =='1'){
			$data_msg["state"]="1";
			$this->insert_into("moblie_msg",$data_msg);
			return "发送成功!";
		}else{
			$result=array("1"=>"短信发送成功","-2"=>'用户名密码错误',"-3"=>'短信不足',"-4"=>'没有可用的手机号码',"-5"=>'没有短信内容',"-6"=>'没有签名内容',"17"=>'发送信息失败',"18"=>'发送定时信息失败',"303"=>'客户端网络故障',"305"=>'服务器端返回错误，错误的返回值（返回值不是数字字符串）','307'=>'目标电话号码不符合规则，电话号码必须是以0、1开头',"997"=>'平台返回找不到超时的短信，该信息是否成功无法确定',"998"=>'由于客户端网络问题导致信息发送超时，该信息是否成功下发无法确定');
			$data_msg["state"]="0";
			$this->insert_into("moblie_msg",$data_msg);
			if($result[$re]){
				return "发送失败！状态：".$result[$re];
			}else{
				return "发送失败！状态：".$re;
			}
		}
	}
	function postSMS($type="msgsend",$data=''){
		$url='http://msg.phpyun.com/send.php';
	    $url.='?user='.$data['uid'].'&pass='.$data['pwd'].'&code='.$data['key'].'&moblie='.$data['mobile'].'&content='.$data['content'].'&time='.$data['time'].'';
	    if(function_exists('file_get_contents')){
	    	$file_contents = file_get_contents($url);
	    }else{
		    $ch = curl_init();
		    $timeout = 5;
		    curl_setopt ($ch, CURLOPT_URL, $url);
		    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		    $file_contents = curl_exec($ch);
		    curl_close($ch);
	    }
	    return $file_contents;
	}
	function fun_ip_get() {
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} else
			if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
				$ip = getenv("HTTP_X_FORWARDED_FOR");
			} else
				if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
					$ip = getenv("REMOTE_ADDR");
				} else
					if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
						$ip = $_SERVER['REMOTE_ADDR'];
					} else {
						$ip = "unknown";
					}
		if (preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',$ip)) {
			return ($ip);
		} else {
			return 'unknown';
		}

	}
	function replace_key($str)
	{
		$fkey = @explode($this->config['sy_fkeyword']);
		$rep_key = $this->config['sy_fkeyword_all'];
		if(is_array($fkey))
		{
			foreach($fkey as $k=>$v)
			{
				$str = str_replace($v,$rep_key,$str);
			}
		}
		return $str;
	}
	function html($dir,$url){

		$dirarray = explode('.',$dir);
		if(end($dirarray)!='html')
		{
			$this->ACT_layer_msg( "非法文件名！",8,$_SERVER['HTTP_REFERER'],2,1);
			exit();
		}
		$dirarr=@explode("/",$dir);
		if(is_array($dirarr)){
			foreach($dirarr as $v){
				if(!strstr($v,"html")){
					$dir2.=$v."/";
					$this->mkdirs($dir2);
				}
			}
		}
		$fp=@fopen($dir,"w+");
		if(function_exists('file_get_contents')){
			$content=file_get_contents($url);
		}else if(function_exists('curl_init')){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$content=curl_exec($ch);
			curl_close($ch);
		}else{
			$this->get_admin_msg($_SERVER['HTTP_REFERER'],"请开启CURL模块或者file_get_contents函数");
		}
		$fw=@fwrite($fp,$content);
		@fclose($fp);
		return $fw;
	}
	function mkdirs($dir, $mode = 0777){
		if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
		if (!mkdirs(dirname($dir), $mode)) return FALSE;
		return @mkdir($dir, $mode);
	}
	function newsurl($data=array(),$once=true){
		$dataarr=array();
		if($once){
			if(is_array($data))
			{
				foreach($data as $v){
					$v["url"]="/news/".date("Ymd",$v["datetime"])."/".$v['id'].".html";
					$dataarr[]=$v;
				}
			}
		}else{
			$data["url"]="/news/".date("Ymd",$data["datetime"])."/".$data['id'].".html";
			$dataarr=$data;
		}
		return $dataarr;
	}
	function get_comname($id){
		$row=$this->DB_select_once("company","`uid`='$id'","name");
		return $row['name'];
	}
	function delfiledir($delfiles){
		$delfiles = stripslashes($delfiles);
		$delfiles = str_replace("../","",$delfiles);
		$delfiles = str_replace("./","",$delfiles);
		$delfiles = "../".$delfiles;
		$p_delfiles = $this->path_tidy($delfiles);
		if($p_delfiles!=$delfiles)
		{
			die;
		}
		if(is_file($delfiles)){
			@unlink($delfiles);
		}else{
		    $dh=@opendir($delfiles);
		    while($file=@readdir($dh)){
		        if($file!="."&&$file!=".."){
		            $fullpath=$delfiles."/".$file;
		            if(@is_dir($fullpath)){
		                $this->delfiledir($fullpath);
		            }else{
		                @unlink($fullpath);
		            }
		        }
		    }
		    @closedir($dh);
		    if(@rmdir($delfiles)){
		        return  true;
		    }else{
		        return false;
		    }
		}
	}
	function arraySet($array,$parentid=0,$space=""){
		foreach($array AS $key=>$value){
			if($parentid == $value["pid"]){
				$value["space"] = $space;
				$this->arrayCategory[] = $value;
				unset($array[$key]);
				$this->arraySet($array,$value["id"],$space."&nbsp; &nbsp; ");
			}
		}
		return $this->arrayCategory;
	}
	function unlink_pic($pic){
		$pictype=getimagesize($pic);
		$pictype = strtolower($pictype[2]);
		
		if($pictype=="jpg" || $pictype=="jpeg" || $pictype=="gif" || $pictype=="png"){

			@unlink($pic);
		}
	}
	function admin_log($data){
		$value="`uid`='".$_SESSION['auid']."',";
		$value.="`username`='".$_SESSION['ausername']."',";
		$value.="`content`='".$data."',";
		$value.="`ctime`='".time()."'";
		if($_SESSION['auid'] && $_SESSION['ausername']){$this->DB_insert_once("admin_log",$value);}
	}
	function seltree($table,$key,$id_arr){
		if(!empty($id_arr)){
			mysql_query("drop FUNCTION IF EXISTS `getChildLst`");
			mysql_query("CREATE FUNCTION `getChildLst`(rootId INT)
						 RETURNS varchar(1000)
						 BEGIN
						   DECLARE sTemp VARCHAR(1000);
						   DECLARE sTempChd VARCHAR(1000);
						   SET sTemp = '$';
						   SET sTempChd =cast(rootId as CHAR);
						   WHILE sTempChd is not null DO
						       SET sTemp = concat(sTemp,',',sTempChd);
						         SELECT group_concat(id) INTO sTempChd FROM ".$table." where FIND_IN_SET(".$key.",sTempChd)>0;
						      END WHILE;
						       RETURN sTemp;
						 END ");
			foreach($id_arr as $key=>$value){
				$query = mysql_query("select getChildLst(".$value.") as id");
				while($row = mysql_fetch_array($query)){
					$row['id'] = str_replace("\$,","",$row['id']);
					$ids[]=  $row['id'];
				}
			}
			$allid =@implode(",",$ids);
			mysql_query("drop FUNCTION `getChildLst`");
			return $allid;
		}
	}
	function path_tidy($path) {
		$tidy = array();
		$path = strtr($path, '\\', '/');
		foreach(explode('/', $path) as $i => $item) {
			if($item == '' || $item == '.' ) {
				continue;
			}
			if($item == '..' && end($tidy) != '..' && $i > 0) {
				array_pop($tidy);
				continue;
			}
			$tidy[] = $item;
		}
		 return ($path[0]=='/'?'/':'').implode('/', $tidy);
	}
	function member_log($content,$opera='',$type=''){
		if($_COOKIE['uid']){
			$value="`uid`='".(int)$_COOKIE['uid']."',";
			$value.="`usertype`='".(int)$_COOKIE['usertype']."',";
			$value.="`content`='".$content."',";
			$value.="`opera`='".$opera."',";
			$value.="`type`='".$type."',";
			$value.="`ip`='".$this->fun_ip_get()."',";
			$value.="`ctime`='".time()."'";
			$this->DB_insert_once("member_log",$value);
		}
	}
} 
?>