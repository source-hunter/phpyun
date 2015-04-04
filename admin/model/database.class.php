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
class database_controller extends common
{
	function get_table(){
		include(LIB_PATH."dbbak.class.php");
		$dbbak=new DBManagement("phpyun",CONFIG_PATH."backup/",$this->obj,$this->db);
		return $dbbak;
	}
	function index_action(){
		$dbbak=$this->get_table();
		$table=$dbbak->GetTablesName();
		$this->yunset("table",$table);
		$this->yuntpl(array('admin/admin_database'));
	}
	function down_sql_action(){
		$file = $this->config[sy_weburl]."/data/backup/$_GET[name]";
		header('Content-type: application/sql');
		header('Content-Disposition: attachment; filename="'.$_GET[name].'"');
		readfile($file);
	}
	function backup_action(){
		global $db_config;
		extract($_POST);
		$dbbak=$this->get_table();
		$fw=$dbbak->backup_action($table,10000000000,$db_config);
		$fw?$this->layer_msg('备份成功！',9,1,'index.php?m=database&c=backin'):$this->layer_msg('备份失败！',8,1,$_SERVER['HTTP_REFERER']);
	}
	function backin_action(){
		$filedb=array();
		$dbbak=$this->get_table();
		$sqlarr=$dbbak->get_hander();
		$this->yunset("sqlarr",$sqlarr);
		$this->yuntpl(array('admin/admin_database_back'));
	}
	function del_action(){

		$this->check_token();
		$delid=@unlink(CONFIG_PATH."backup/".$_GET['sql']);
		$delid?$this->layer_msg('数据库备份删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
	}
	function sql_action(){
		extract($_GET);
		if($type==1){
			global $db_config;
			$this->check_token();
			$dbbak=$this->get_table();
			$fw=$dbbak->backup_action(array($name),10000000000,$db_config);
			$type_name="备份".$name;
		}
		if($type==2){
			$fw=mysql_query("REPAIR TABLE `".$name."`");
			$type_name="修复".$name;
		}
		if($type==3){
			$fw=mysql_query("OPTIMIZE TABLE  `".$name."`");
			$type_name="优表".$name;
		}
 		$fw?$this->layer_msg($type_name."成功！",9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg($type_name."失败！",8,0,$_SERVER['HTTP_REFERER']);
	}
	function backincheck_action(){
		$this->check_token();
		global $db_config;
		extract($_GET);
		if($db_config["version"]!=$ver){
			$this->layer_msg("备份版本和当前系统不同，无法导入！",8,0,$_SERVER['HTTP_REFERER']);
		}
		$dbbak=$this->get_table();
		$dbbak=$dbbak->bakindata($sql);
		$dbbak?$this->layer_msg("数据库恢复成功！",9,0,$_SERVER['HTTP_REFERER']):$this->obj->ACT_msg("恢复成功！",8,0,$_SERVER['HTTP_REFERER']);
	}
}

?>