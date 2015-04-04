<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class DBManagement{
	private $TablesName;
	private $DefaultPath;
	private $DatabaseName;
	private $db;
	private $obj;
	private $start;
	private $startfrom;
	private $tableid;
	private $rows;
	private $stop;
	private $step;
	function __construct($_DatabaseName="phpyun",$_DefaultPath="../data/backup/",$obj="",$db=""){
		if(!$_DatabaseName){
			$this->DatabaseName=$dbName;
		}else{
			$this->DatabaseName=$_DatabaseName;
		}
		$this->DefaultPath=$_DefaultPath;
		$path=realpath($this->DefaultPath);
		$this->DefaultPath=str_replace("\\","/",$path);
		$this->db=$db;
		$this->obj=$obj;
	}

	function GetTablesName(){
		$othortable=array();
		$query = $this->db->query("SHOW TABLES");
		$i=0;
		while ($rt = $this->db->fetch_array($query)){
				$value = trim(current($rt));
				$othortable[$i][name]=$value;
				$sql="select count(*) as dbnum from `".$value."` where 1";
				$tbquery = $this->db->query($sql);
				while($num=$this->db->fetch_array($tbquery)){
					
					$othortable[$i][num]=$num['dbnum'];
				}
				$i++;
		}
		return $othortable;
	}

	function backup_action($table,$sizelimit="100000000",$db_config){
			$bak="#dbname:".$db_config[dbname]."#phpyun#version:".$db_config[version]."#phpyun#def:".$db_config[def]."#phpyun#charset:".$db_config[charset]."#phpyun#Time:".date('Y-m-d H:i')."\n#phpyun# Type: \n# phpyun: http://www.phpyun.com\n#\r\n";
			$this->db->query("SET SQL_QUOTE_SHOW_CREATE = 0");
			$this->start = intval($start);
			!$tabledb && !is_array($table);
			!$tabledb && $tabledb=$table;
			!$this->step && $sizelimit/=2;
			$this->stop=1;
			$bakupdata=$this->bakupdata($tabledb,$start,$sizelimit);
			if(!$this->step){
				$tablesel=@implode("|",$tabledb);
				$this->step=1;
				$this->start=0;
				$pre='phpyun_'.date('md').'_'.$this->num_rand(10).'_';
				$bakuptable=$this->bakuptable($tabledb);
			}
			$f_num=ceil($step/2);
			$filename=$pre.$f_num.'.sql';
			$this->step++;
			$writedata=$bakuptable?$bakuptable.$bakupdata:$bakupdata;
			$t_name=$tabledb[$tableid-1];
			$c_n=$this->startfrom;

			if($this->stop==1){
				$files=$this->step-1;
				trim($writedata) && $fw=$this->writeover($this->DefaultPath."/".$filename,$bak.$writedata,'ab');
			}else{
				trim($writedata) && $fw=$this->writeover($this->DefaultPath."/".$filename,$bak.$writedata,'ab');
				if($step>1){
					for($i=1;$i<=$f_num;$i++){
						$bakfile.='<a href="data/'.$pre.$i.'.sql">'.$pre.$i.'.sql</a><br>';
					}
				}
			}
			return $fw;
	}
	function bakupdata($tabledb,$start=0,$sizelimit="1024",$stop=0){
		$this->tableid=$this->tableid?$this->tableid-1:0;
		$this->stop=0;
		$t_count=count($tabledb);
		for($i=$this->tableid;$i<$t_count;$i++){
		    if(!$this->rows){
			$ts=$this->db->query("SHOW TABLE STATUS LIKE '$tabledb[$i]'");
			$this->rows=$ts['Rows'];
	        }
			$limitadd="LIMIT $this->start,100000";
			$query = $this->db->query("SELECT * FROM $tabledb[$i] $limitadd");
			$num_F = mysql_num_fields($query);

			while ($datadb = mysql_fetch_row($query)){
				$this->start++;
				$bakupdata .= "INSERT INTO $tabledb[$i] VALUES("."'".mysql_escape_string($datadb[0])."'";
				$tempdb='';
				for($j=1;$j<$num_F;$j++){
					$tempdb.=",'".mysql_escape_string($datadb[$j])."'";
				}
				$bakupdata .=$tempdb. ");\r\n";
				if($sizelimit && strlen($bakupdata)>$sizelimit*1000){
					break;
				}
			}
			$this->db->query($query);
			if($this->start>=$rows){
				$this->start=0;
				$this->rows=0;
			}
			$bakupdata .="\r\n";
			if($sizelimit && strlen($bakupdata)>$sizelimit*1000){
				$this->start==0 && $i++;
				$this->stop=1;
				break;
			}
			$this->start=0;
		}
		if($stop==1){
			$i++;
			$this->tableid=$i;
			$this->startfrom=$this->start;
			$this->start=0;
		}
		return $bakupdata;
	}
	function bakuptable($tabledb){
		if(is_array($tabledb)){
			$creattable.="set sql_mode='';\r\n";
		foreach($tabledb as $key=>$table){
			$creattable.= "DROP TABLE IF EXISTS $table;\r\n";
			$CreatTable = $this->db->query("SHOW CREATE TABLE $table");
			$CreatTable=$this->db->fetch_array($CreatTable);
			$CreatTable['Create Table']=str_replace($CreatTable['Table'],$table,$CreatTable['Create Table']);
			$creattable.=$CreatTable['Create Table'].";\r\n";
		}
		}
		return $creattable;
	}
	function num_rand($lenth){
		mt_srand((double)microtime() * 1000000);
		for($i=0;$i<$lenth;$i++){
			$randval.= mt_rand(0,9);
		}
		$randval=substr(md5($randval),mt_rand(0,32-$lenth),$lenth);
		return $randval;
	}
	function writeover($filename,$data,$method="rb+",$iflock=1,$check=1,$chmod=1){
		$check && @strpos($filename,'..')!==false && exit('Forbidden');
		@touch($filename);
		$handle=@fopen($filename,$method);
		if($iflock){
			@flock($handle,LOCK_EX);
		}
		$fw=@fwrite($handle,$data);
		if($method=="rb+") ftruncate($handle,strlen($data));
		fclose($handle);
		$chmod && @chmod($filename,0777);
		return $fw;
	}
	function get_hander(){
		$filedb=array();
		$handle=opendir($this->DefaultPath);
		while($file = readdir($handle)){
			if((eregi("^phpyun_",$file) || eregi("^$PW",$file)) && eregi("\.sql$",$file)){
				$strlen=eregi("^$PW",$file) ? 16 + strlen($PW) : 19;
				$fp=fopen($this->DefaultPath."/$file",'rb');
				$bakinfo=@fread($fp,200);
				@fclose($fp);
				$detail=@explode("#phpyun#",$bakinfo);
				$bk['name']=$file;
				$bk['version']=str_replace("version:","",$detail[1]);

				$bk['time']=str_replace("Time:","",$detail[4]);
				$bk['charset']=str_replace("charset:","",$detail[3]);
				$bk['dbname']=str_replace("#dbname:","",$detail[0]);
				$bk['def']=str_replace("#def:","",$detail[2]);
				$bk['num']=@substr($file,$strlen,strrpos($file,'.')-$strlen);
				$filedb[]=$bk;
			}
		}
		return $filedb;
	}
	function bakindata($filename,$charset="gbk") {
		$sql=file($this->DefaultPath."/".$filename);
		$query='';
		$num=0;
		foreach($sql as $key => $value){
			$value=trim($value);
			if(!$value || $value[0]=='#') continue;
			if(eregi("\;$",$value)){
				$query.=$value;
				if(eregi("^CREATE",$query)){
					$extra = substr(strrchr($query,')'),1);
					$query = str_replace($extra,'',$query);
					if($this->db->mysql_server('8')>'4.1'){
						$extra = $charset ? "ENGINE=MyISAM DEFAULT CHARSET=$charset;" : "ENGINE=MyISAM;";
					}else{
						$extra = "TYPE=MyISAM;";
					}
					$query .=$extra;
				}elseif(eregi("^INSERT",$query)){
					$query='REPLACE '.substr($query,6);
				}
				$sql=$this->db->query($query);
				$query='';
			} else{
				$query.=$value;
			}
		}
		return $sql;
	}
}
?>