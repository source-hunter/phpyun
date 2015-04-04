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
class collection_controller extends common
{
	function index_action()
	{

		$this->city_cache();
		$this->job_cache();
		$this->industry_cache();
		include(PLUS_PATH."com.cache.php");
		$this->yunset("comdata",$comdata);
		$this->yunset("comclass_name",$comclass_name);
		include(PLUS_PATH."user.cache.php");
		$this->yunset("userdata",$userdata);
		$this->yunset("userclass_name",$userclass_name);
		$qy_rows=$this->obj->DB_select_all("company_rating","`category`=1 order by sort desc");
		$this->yunset("qy_rows",$qy_rows);
		$this->yunset("sy_weburl",$this->config['sy_weburl']);
		
		$path = APP_PATH."/api/locoy/locoy_config.php";
		require_once $path;
		$this->yunset("locoyinfo",$locoyinfo);
		
		$this->yuntpl(array('admin/admin_collection_list'));
	}
	function save_action()
	{
		$config = "<?php \r\n";
		$path = APP_PATH."/api/locoy/locoy_config.php";
		require_once $path;
		unset($_POST['resumeconfig']);
		unset($_POST['waterconfig']);
		unset($_POST['userconfig']);
		unset($_POST['mapconfig']);
		unset($_POST['config']);
		unset($_POST['otherconfig']);
		if($_POST)
		{
			$parr="";
			unset($_POST['submit']);
			foreach($_POST as $key=>$value)
			{
				$locoyinfo[$key]=$value;
			}
			foreach($locoyinfo as $key=>$value)
			{
				$parr .= "\"".$key."\"=>\"".$value."\",";
			}
			$parr = rtrim($parr,",");
			$config.="\$locoyinfo=array(".$parr."); \r\n";
		}
		
		$path = APP_PATH."/api/locoy/locoy_config.php";
		$fp = @fopen($path,"w");
		fwrite($fp,$config);
		fclose($fp);
     	if(is_array($locoy_type))
		 
		include(APP_PATH."/api/locoy/locoy_config.php"); 
		$this->obj->ACT_layer_msg("保存成功！",9,"index.php?m=collection",2,1);
	}
}

?>