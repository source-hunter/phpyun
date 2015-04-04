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
ob_start();  
class Phpyun_Cache{  
	private $cache_dir="./cache";
	private $web_dir='';
	private $cache_time=3600;
	public function __construct($cache_dir,$web_dir,$cache_time=3600) {  
	   ob_start();
	   $this->web_dir=$web_dir; 
	   $this->cache_dir=$cache_dir; 
	   $this->cache_time=$cache_time; 
	} 
	public function Read_Cache(){
		if(count($_POST)>0 || $_GET[m]=="ajax" || $_GET[m]=="includejs"){
			return false; 
		}
	   try{     
			if(self::Create_Dir($this->cache_dir)){  
				 self::Get_Cache();
			 }else{  
				 echo "缓存文件夹创建失败!"; 
				 return false; 
			}
	   }catch(Exception $e){ 
			echo $e;  
			return false; 
	   } 
	} 
	private function Exist_Dir($foler){ 
	   if(@file_exists($this->web_dir."/".$foler)){ 
			return true; 
	   }else {  
			return false; 
	   }     
	} 
	private function Create_Dir($foler){
	   if(!self::Exist_Dir($foler)){  
			try{  
				@ mkdir($this->web_dir."/".$foler,0777); 
				 @chmod($this->web_dir."/".$foler,0777); 
				 return true;  
			}catch(Exception $e){  
				 self::Get_Cache();
				 return false; 
			}  
			return false; 
	   }else{  
			return true; 
	   } 
	} 
	private function Get_Cache(){ 
	 $file_name=self::get_CacheName();   
	   if(@file_exists($file_name)&&((filemtime($file_name)+$this->cache_time)>time())){  
		$content=@file_get_contents($file_name); 
		   if($content){
				echo $content;   
				ob_end_flush(); 
				exit; 
		   }else{  
				echo "缓存文件读取失败"; 
				exit; 
		   } 
	   }elseif(@file_exists($file_name)){
	   
			$this->Del_Cache();
	   } 
	} 
	private function get_CacheName(){
		if(is_array($_GET)){
			foreach($_GET as $key=>$v){
				$name.=$key.$v;
			}
		}
		if($_SESSION['cityid'])
		{
			$name.=$_SESSION['cityid'].$_SESSION['host'];
		}
	   $filename=$file_name=$this->web_dir.'/'.$this->cache_dir.'/'.md5($name).".html";  
	   return $filename; 
	} 
    public function CacheCreate(){
		$filename=self::get_CacheName(); 
		if($filename!=""){  
		   try{  
			   @file_put_contents($filename,ob_get_contents());  
			   return true;  
		   }catch (Exception $e){  
				echo "缓存文件写入失败:".$e; 
				exit(); 
		   }  
		   return true; 
		}  
    } 
	public function CacheList(){
	   $path=$this->cache_dir;  
	   if ($handle=opendir($path)) {   
		   while (false!==($file=readdir($handle))){ 
				if($file!="." && $file!="..") { 
					 $path1=$path."/".$file;         
					 if(@file_exists($path1)){         
					  	$result[]=$file; 
					 }
				}
		   }
		   @closedir($handle); 
	   }  
	   return $result;  
	} 
	public function Del_Cache(){
	   $path=$this->web_dir.$this->cache_dir;  
	   if($handle = @opendir($path)){   
		   while(false!==($file=@readdir($handle))){ 
				if($file!="." && $file!=".."){ 
					 $path1=$path."/".$file;
					 if(@file_exists($path1)){           
					  	@unlink($path1);          
					 }
				}
			}
		   @closedir($handle); 
	   }  
	   return true;
	}
}
?>