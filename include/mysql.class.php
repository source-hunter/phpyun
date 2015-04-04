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
class mysql {
	private $db_host;
	private $db_user;
	private $db_pwd;
	private $db_database; 
	private $conn;
	private $result;
	private $sql; 
	private $row;
	private $coding;
	private $bulletin = true;
	private $show_error = false; 
	private $is_error = false; 
	private $def="";
	
	public function __construct($db_host, $db_user, $db_pwd, $db_database, $conn, $coding,$def="") {
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_pwd = $db_pwd;
		$this->db_database = $db_database;
		$this->conn = $conn;
		$this->coding = $coding;
		$this->def=$def;
		$this->connect();
	}
	
	public function connect() {
		if ($this->conn == "pconn") {
			
			$this->conn = @mysql_pconnect($this->db_host, $this->db_user, $this->db_pwd) or die('数据库连接出错!');
		} else {
			
			$this->conn = @mysql_connect($this->db_host, $this->db_user, $this->db_pwd) or die('数据库连接出错!');
		}
		if (!@mysql_select_db($this->db_database, $this->conn)) {
			if ($this->show_error) {
				$this->show_error("数据库不可用：", $this->db_database);
			}
		}
		@mysql_query("SET NAMES $this->coding");
		@mysql_query("SET character_set_connection=gbk,character_set_results=gbk,character_set_client=binary", $this->conn);
	}
	
	public function query($sql) {
		if ($sql == "") {
			$this->show_error("SQL语句错误：", "SQL查询语句为空");
		}
		$this->sql = $sql;
		$result = mysql_query($this->sql,$this->conn);
		if (!$result){
		
			if ($this->show_error) {
				$this->show_error("错误SQL语句：", $this->sql);
			}
		} else {
			$this->result = $result;
		}
		
		return $this->result;
	}

	function DB_query_all($sql)
	{
		$query = $this->query($sql);
		$return=$this->fetch_array($query);
		return $return;
	}
	
	function DB_select_once($tablename, $where = 1, $select = "*") {
		$cachename=$tablename.$where;
		if(!$return=$this->Memcache_set($cachename)){

			$SQL = "SELECT $select FROM " . $this->def . $tablename . " WHERE $where limit 1";
			$query = $this->query($SQL);
			$return=$this->fetch_array($query);

			$this->Memcache_set($cachename,$return);
		}
		return $return;
	}
	function update_all($tablename, $value, $where = 1) {
		$cachename=$tablename.$where;
		if(!$return=$this->Memcache_set($cachename)){
			$SQL = "UPDATE `" . $this->def . $tablename . "` SET $value WHERE $where";
			$query = $this->query($SQL);
			$return=$this->fetch_array($query);
			$this->Memcache_set($cachename,$return);
		}
		return $return;
	}
	function insert_once($tablename, $value) {
		$cachename=$tablename.$value;
		if(!$return=$this->Memcache_set($cachename)){
			$SQL = "INSERT INTO `" . $this->def . $tablename . "` SET $value";
			$query = $this->query($SQL);
			$return=$this->fetch_array($query);
			$this->Memcache_set($cachename,$return);
		}
		return $return;
	}
	function Memcache_set($name,$value=""){
		
		
		
	}
	
	function select_num($tablename, $where = 1, $select = "*") {
		$cachename=$tablename.$where;
		if(!$return=$this->Memcache_set($cachename)){
			$SQL = "SELECT count($select) FROM " . $this->def . $tablename . " WHERE $where";
			$query = $this->query($SQL);
			while($rs = mysql_fetch_array($query))
			{
				$row = $rs;
			}
			$return=$row[0];
			$this->Memcache_set($cachename,$return);
		}
		return $return;
	}

	function select_all($tablename, $where = 1, $select = "*") {
		$row_return=array();
		$SQL = "SELECT $select FROM `" . $this->def . $tablename . "` WHERE $where";
		$query=$this->query($SQL);
		 while($row=$this->fetch_array($query)){$row_return[]=$row;}
		 return $row_return;
	}
	
	function select_only($tablename, $where = 1, $select = "*") {
			$row_return=array();
			$SQL = "SELECT $select FROM " .$tablename . " WHERE $where";
			$query=$this->query($SQL);
			 while($row=$this->fetch_array($query)){$row_return[]=$row;}
		 return $row_return;
	}
	
	function select_alls($tablename1,$tablename2, $where = 1, $select = "*") {
			$SQL = "SELECT $select FROM " . $this->def . $tablename1. " as a," . $this->def . $tablename2 . " as b WHERE $where";
			$query=$this->query($SQL);
			 while($row=$this->fetch_array($query)){$row_return[]=$row;}
		 return $row_return;
	}
	
	public function create_database($database_name) {
		$database = $database_name;
		$sqlDatabase = 'create database ' . $database;
		$this->query($sqlDatabase);
	}
	function cacheget()
	{
		include APP_PATH."/plus/city.cache.php";
		include APP_PATH."/plus/com.cache.php";
		include APP_PATH."/plus/job.cache.php";
		include APP_PATH."/plus/user.cache.php";
		include APP_PATH."/plus/industry.cache.php";
		$array["comclass_name"] = $comclass_name;
		$array["city_name"] = $city_name;
		$array["user_classname"] = $userclass_name;
		$array["job_name"] = $job_name;
		$array["industry_name"] = $industry_name;
		return $array;
	}
	
	function array_action($job_info,$array=array())
	{
		if(!empty($array))
		{
			$comclass_name = $array["comclass_name"];
			$city_name = $array["city_name"];
			$industry_name = $array["industry_name"];
			$job_name = $array["job_name"];
		}else{
			include APP_PATH."/plus/city.cache.php";
			include APP_PATH."/plus/com.cache.php";
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/industry.cache.php";
		}
		$job_info[job_class_one] = $job_name[$job_info["job1"]];
		$job_info[job_class_two] = $job_name[$job_info[job1_son]];
		$job_info[job_class_three] = $job_name[$job_info[job_post]];
		$job_info[job_exp] = $comclass_name[$job_info["exp"]];
		$job_info[job_edu] = $comclass_name[$job_info[edu]];
		$job_info[job_salary] = $comclass_name[$job_info[salary]];
		$job_info[job_number] = $comclass_name[$job_info[number]];
		$job_info[job_mun] = $comclass_name[$job_info[mun]];
		$job_info[job_sex] = $comclass_name[$job_info[sex]];
		$job_info[job_age] = $comclass_name[$job_info[age]];
		$job_info[job_type] = $comclass_name[$job_info[type]];
		$job_info[job_marriage] = $comclass_name[$job_info[marriage]];
		$job_info[job_report] = $comclass_name[$job_info[report]];
		$job_info[job_city_one] = $city_name[$job_info[provinceid]];
		$job_info[com_city] = $city_name[$job_info[com_provinceid]];
		$job_info[job_pr] = $comclass_name[$job_info[pr]];
		$job_info[job_city_two] = $city_name[$job_info[cityid]];
		$job_info[job_city_three] = $city_name[$job_info[three_cityid]];
		$job_info[job_hy] = $industry_name[$job_info[hy]];
		$job_info[edate]=date("Y年m月d日",$job_info[edate]);
		if($job_info[lang]!="")
		{
			$lang = @explode(",",$job_info[lang]);
			foreach($lang as $key=>$value)
			{
				$langinfo[]=$comclass_name[$value];
			}
			$job_info[lang_info] = @implode(",",$langinfo);
			$job_info[lang] =$lang;
		}else{
			$job_info[lang_info] ="";
		}
		if($job_info[welfare]!="")
		{
			$welfare = @explode(",",$job_info[welfare]);
			foreach($welfare as $key=>$value)
			{
				$welfareinfo[]=$comclass_name[$value];
			}
			$job_info[welfare_info] = @implode(",",$welfareinfo);
			$job_info[welfare] =$welfare;
		}else{
			$job_info[welfare_info] ="";
		}
		return $job_info;
	}

	
	function newsurl($data=array(),$once=true){
		$dataarr=array();
		if($once){
			if(is_array($data))
			{
				foreach($data as $v){
				$v["url"]="/news/".date("Ymd",$v["datetime"])."/".$v[id].".html";
				$dataarr[]=$v;
				}
			}
		}else{
			$data["url"]="/news/".date("Ymd",$data["datetime"])."/".$data[id].".html";
			$dataarr=$data;
		}
		return $dataarr;
	}
	
	public function show_databases() {
		$this->query("show databases");
		echo "现有数据库：" . $amount = $this->db_num_rows($rs);
		echo "<br />";
		$i = 1;
		while ($row = $this->fetch_array($rs)) {
			echo "$i $row[Database]";
			echo "<br />";
			$i++;
		}
	}

	public function databases() {
		$rsPtr = mysql_list_dbs($this->conn);
		$i = 0;
		$cnt = mysql_num_rows($rsPtr);
		while ($i < $cnt) {
			$rs[] = mysql_db_name($rsPtr, $i);
			$i++;
		}
		return $rs;
	}
	
	public function show_tables($database_name) {
		$this->query("show tables");
		echo "现有数据库：" . $amount = $this->db_num_rows($rs);
		echo "<br />";
		$i = 1;
		while ($row = $this->fetch_array($rs)) {
			$columnName = "Tables_in_" . $database_name;
			echo "$i $row[$columnName]";
			echo "<br />";
			$i++;
		}
	}
	

	public function mysql_result_li() {
		return mysql_result($str);
	}
	
	public function fetch_array($sql="") {
		if(!$sql){
			return @mysql_fetch_array($this->result);
		}else{
			return @mysql_fetch_array($sql);
		}
	}
	
	public function fetch_assoc() {
		return mysql_fetch_assoc($this->result);
	}

	public function fetch_row() {
		return mysql_fetch_row($this->result);
	}

	public function fetch_Object() {
		return mysql_fetch_object($this->result);
	}

	public function insert_id() {
		return mysql_insert_id();
	}
	
	public function db_data_seek($id) {
		if ($id > 0) {
			$id = $id -1;
		}
		if (!@ mysql_data_seek($this->result, $id)) {
			$this->show_error("SQL语句有误：", "指定的数据为空");
		}
		return $this->result;
	}
	
	public function db_num_rows() {
		if ($this->result == null) {
			if ($this->show_error) {
				$this->show_error("SQL语句错误", "暂时为空，没有任何内容！");
			}
		} else {
			return mysql_num_rows($this->result);
		}
	}

	public function db_affected_rows() {
		return mysql_affected_rows();
	}
	public function show_error($message = "", $sql = "") {
	  //header("location:'".$_SERVER['HTTP_REFERER']."'");
	  //关闭调试器
	}
	
	public function free() {
		@ mysql_free_result($this->result);
	}

	public function select_db($db_database) {
		return mysql_select_db($db_database);
	}
	
	public function num_fields($table_name) {
		
		$this->query("select * from $table_name");
		echo "<br />";
		echo "字段数：" . $total = mysql_num_fields($this->result);
		echo "<pre>";
		for ($i = 0; $i < $total; $i++) {
			print_r(mysql_fetch_field($this->result, $i));
		}
		echo "</pre>";
		echo "<br />";
	}
	
	public function mysql_server($num = '') {
		switch ($num) {
			case 1 :
				return mysql_get_server_info(); //MySQL 服务器信息
				break;
			case 2 :
				return mysql_get_host_info(); //取得 MySQL 主机信息
				break;
			case 3 :
				return mysql_get_client_info(); //取得 MySQL 客户端信息
				break;
			case 4 :
				return mysql_get_proto_info(); //取得 MySQL 协议信息
				break;
			default :
				return mysql_get_client_info(); //默认取得mysql版本信息
		}
	}
	
	function get_usertype($uid){
		$sql=$this->query("select * form `member` where `uid`='".$uid."'");
		$row=$this->fetch_array($sql);
		return $row['uid'];
	}
	
	public function __destruct() {
		if (!empty ($this->result)) {
			$this->free();
		}
		@mysql_close($this->conn);
	}
	function getmicrotime(){
		list($usec, $sec) = @explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>