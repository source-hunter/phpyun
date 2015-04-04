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
//模板操作类
class admin_uc_controller extends common
{
	function index_action()
	{

		$this->yunset("sy_weburl",$this->config['sy_weburl']);
		$path = APP_PATH."/api/uc/config.inc.php";
		require_once $path;
		$this->yunset("ucinfo",$ucinfo);
		$this->yuntpl(array('admin/admin_uc'));
	}
	function save_action(){
		$config = "<?php \r\n";
		if($_POST){
			$parr="";
			unset($_POST['submit']);
			foreach($_POST as $key=>$value)
			{
				$config.="define(\"".$key."\",\"".$value."\"); \r\n";
				$parr .= "\"".$key."\"=>\"".$value."\",";
			}
			$parr = rtrim($parr,",");
			$config.="\$ucinfo=array(".$parr."); \r\n";
		}
		$path = APP_PATH."/api/uc/config.inc.php";
		$fp = @fopen($path,"w");
		fwrite($fp,$config);
		fclose($fp);
		$uc_type = $this->obj->DB_select_once("admin_config","`name`='sy_uc_type'");
		if(is_array($uc_type)){
			$this->obj->DB_update_all("admin_config","`config`='uc_center'","`name`='sy_uc_type'");
		}else{
			$this->obj->DB_insert_once("admin_config","`name`='sy_uc_type',`config`='uc_center'");
		}
		 $this->web_config();		
		$this->obj->ACT_layer_msg("UC整合已开启！",9,"index.php?m=admin_uc",2,1); 
	}
	function close_action(){
		$this->obj->DB_update_all("admin_config","`config`=''","`name`='sy_uc_type'");
		$this->web_config();  
		$this->layer_msg('UC整合已取消！',9,0,"index.php?m=admin_uc");
	}
	function pw_action(){
		$this->yunset("sy_weburl",$this->config['sy_weburl']);
		$path = APP_PATH."/api/pw_api/pw_config.php";
		require_once $path;
		$this->yunset("ucinfo",$ucinfo);
		$this->yuntpl(array('admin/admin_pw'));
	}
	function pwsave_action() { 
		$config = "<?php \r\n";
		if($_POST){
			$parr="";
			unset($_POST['submit']);
			foreach($_POST as $key=>$value){
				$config.="define(\"".$key."\",\"".$value."\"); \r\n";
				$parr .= "\"".$key."\"=>\"".$value."\",";
			}
			$parr = rtrim($parr,",");
			$config.="\$ucinfo=array(".$parr."); \r\n";
		}
		$path = APP_PATH."/api/pw_api/pw_config.php";
		$fp = @fopen($path,"w");
		fwrite($fp,$config);
		fclose($fp);
		$uc_type = $this->obj->DB_select_once("admin_config","`name`='sy_pw_type'");
		if(is_array($uc_type)){
			$this->obj->DB_update_all("admin_config","`config`='pw_center'","`name`='sy_pw_type'");
		
		}else{
			$this->obj->DB_insert_once("admin_config","`name`='sy_pw_type',`config`='pw_center'");
		}
		$this->obj->DB_update_all("admin_config","`config`=''","`name`='sy_uc_type'");
		$this->web_config();
		$user_arr = $this->obj->DB_select_all("member");
		require_once APP_PATH.'/api/pw_api/pw_client/class_db.php';
		$db_uc = new UcDB;
		include(APP_PATH."/api/pw_api/pw_config.php");
		$db_uc->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, UC_DBCONNECT, UC_DBCHARSET);
		$pw_query=$db_uc->query("SELECT * FROM ".UC_DBTABLEPRE."members");

		while($pw_rs = $db_uc->fetch_array($pw_query)){
			if(is_array($user_arr)){
				foreach($user_arr as $key=>$value){
					
					if($value['username']==$pw_rs['username']&&$value['pw_repeat']!="1"){
						if($value['pwuid']<1){
							
							if($value['password']==md5($pw_rs['password'].$value['salt'])){
								
								$this->obj->DB_update_all("member","`pwuid`='".$pw_rs['uid']."'","`uid`='".$value['uid']."'");
							}else{
								
								$this->obj->DB_update_all("member","`pw_repeat`='1'","`uid`='".$value['uid']."'");
							}
						}
					}
				}
			}
		} 
		$this->obj->ACT_layer_msg("PW整合已开启！",9,"index.php?m=admin_pw");
	}
	function pwclose_action()
	{
		$this->obj->DB_update_all("admin_config","`config`=''","`name`='sy_pw_type'");
		$this->web_config();  
		$this->layer_msg('PW整合已取消！',9,0,"index.php?m=admin_pw");

	}
}