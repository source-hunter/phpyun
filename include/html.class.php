<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
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
      echo "��ģ��ҳ ".$temp." ���� ".$html.$this->error($err);
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
  $message=" ��̬ҳ���ɳɹ�";
  break;
 case 1 :
  $message=" ģ��ҳ��ʧ�ܣ������Ƿ����";
  break;
 case 2 :
  $message=" �ļ�����ʧ�ܣ�����Ŀ¼Ȩ��";
  break;
 default:
  $message=" δ֪����";
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