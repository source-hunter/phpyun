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
class html{
	public $temp;              
	public $html;              
	public $err;              
	public $test;              
	public $arr;                
  function html(){
      $this->temp="";
      $this->html="";
      $this->err=0;
      $this->test="";
   }
   function templatehtml($temp,$html,$arr=""){
	  $err=$this->chkfile($temp);
	  if((int)$err==0)
	  {
		  $fp=fopen($temp,"r");                      
		  $test=fread($fp,filesize($temp));         
		  $test=$this->arr_replace($arr,$test);            
		  $err=$this->writefile($html,$test);            
	  }
      echo "由模板页 ".$temp." 生成 ".$html.$this->error($err);
  	 return;
   }

	function chkfile($file){
	 if (file_exists($file)){
	  return 0;
	 }
	  return 1;
	 }

   function arr_replace($arr,$test){
      $ss=$test;
      foreach ($arr as $key => $value){
   $ss= str_replace($key,$value,$ss);
  	 }
      return $ss;
   }

   function writefile($html,$test){
      $stat=2;
      if($this->chkfile($html)==0){  
     	 $stat=0;                   
   	  }
	   if($f=fopen($html,"w")){      
		    fputs($f,$test);
			fclose($f);
		   $stat=0;               
	   }else{
		   $stat=2;             
	   }
      return $stat;
   }

function error($err)
{
 $message="";
 switch((int)$err)
 {
 case 0 :
  $message=" 静态页生成成功";
  break;
 case 1 :
  $message=" 模板页打开失败，请检查是否存在";
  break;
 case 2 :
  $message=" 文件生成失败，请检查目录权限";
  break;
 default:
  $message=" 未知错误";
 }
 return $message;
}

function delete_file($file) {
    if (file_exists($file))
    {
        $delete = chmod ($file, 0777);
        $delete = @unlink($file);
        if(file_exists($file))
        {
            $filesys = eregi_replace("/","",$file);
            $delete = system("del $filesys");
            clearstatcache();
            if(file_exists($file))
            {
                $delete = chmod ($file, 0777);
                $delete = @unlink($file);
                $delete = system("del $filesys");
            }
        }
        clearstatcache();
    }
}
}
?>