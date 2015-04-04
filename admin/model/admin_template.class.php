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
class admin_template_controller extends common
{
	function public_action()
	{
		include_once("model/model/tmp_class.php");

	}
	function index_action()
	{
		$publicdir = "../template/";

		if($_GET['dir'])
		{
			$dir = str_replace('.',"",$_GET['dir']);
		}
		if(!$dir)
		{
			$hostdir = '';
		}else{
			
			$hostdir = $dir.'/';
			$row=explode('/',$hostdir);
			if(count($row)>2)
			{
				$str_dir = array_slice($row,-2,1);
				$retrundir=str_replace("/".$str_dir[0]."/","",$hostdir);
				
			}else{
				
				$retrundir=str_replace($row[0]."/","",$hostdir);
			}
			
			
			$floder[] = array('name'=>"������һ��",'url'=>$retrundir);
		}

		$filesnames = @scandir($publicdir.$hostdir);
		
		if(is_array($filesnames))
		{
			foreach($filesnames as $key=>$value)
			{ 
			if($value!='.' && $value!='..' ){
			  if(is_dir($publicdir.$hostdir.$value)){
				 
				  $floder[] = array('name'=>$value,'url'=>$hostdir.$value);
			  }elseif(is_file($publicdir.$hostdir.$value)){
				 
				 $typearr = explode('.',$hostdir.$value);
				 if(in_array(end($typearr),array('txt','htm','html','xml','js','css')))
				 {
					
					   $file[] = array('name'=>$value,'url'=>$hostdir.$value,'size'=>round((filesize($publicdir.$hostdir.$value)/1024),2)."KB",'time'=>date("Y-m-d H:i:s",filemtime($publicdir.$hostdir.$value)));
				 }
				 }
			  }
			}
		}
		$this->yunset("floder",$floder);
		$this->yunset("file",$file);
		$this->yuntpl(array('admin/admin_template'));
	}

	function modify_action()
	{
		$hostdir = "../template/";
		$_GET['path'] = str_replace(array('./','../'),'',$_GET['path']);
		if(count(@explode('.',$_GET['path']))>2)
		{
			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"�Ƿ����ļ�����");
		}
		if(file_exists($hostdir.$_GET['path']) && $_GET['name']){
			
			 $path = $hostdir.$_GET['path'];
			 $typearr = explode('.', $path);
			 if(!in_array(end($typearr),array('txt','htm','html','xml','js','css')))
			 {
				 $this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"�Ƿ����ļ�����");
			 }
			$tp_info['name'] = $_GET['name'];
			$tp_info['path'] = $_GET['path'];

			$fp=@fopen($path,"r"); 
			$tp_info['content'] =@fread($fp,filesize($path));
			$tp_info['content'] = str_replace(array('<textarea>','</textarea>'),array('&lt;textarea&gt;','&lt;/textarea&gt;'),$tp_info['content']);
			fclose($fp);
			$this->yunset("safekey",$safekey);
			
			$tp_info['safekey'] = md5(md5($this->config['sy_safekey']).'admin_template');
			$this->yunset("tp_info",$tp_info);
			$this->yuntpl(array('admin/admin_template_modify'));
		}else{
			 $this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"�ļ������ڣ�");
		}
		
	}

	function savetp_action()
	{
		$hostdir = "../template/";
		$_GET['path'] = str_replace(array('./','../'),'',$_POST['tp_path']);
		if(count(@explode('.',$_POST['tp_path']))>2)
		{
			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"�Ƿ����ļ�����");
		}

		if(file_exists($hostdir.$_POST['tp_path']) && $_POST['code'] && md5(md5($this->config['sy_safekey']).'admin_template') == $_POST['safekey'])
		{
			$path = $hostdir.$_POST['tp_path'];

			 $typearr = explode('.',$path);
			 if(!in_array(end($typearr),array('txt','htm','html','xml','js','css')))
			 {
				 $this->obj->ACT_layer_msg($_SERVER['HTTP_REFERER'],"�Ƿ����ļ�����");
			 }

			$fp=@fopen($path,"w");
			
			fwrite($fp,stripslashes($_POST['code']));
	
			fclose($fp);
			
			$this->obj->ACT_layer_msg("ģ��(".$_POST['tp_path'].")���³ɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
			
		}else{
			
			$this->obj->ACT_layer_msg("ģ�岻��Ϊ�գ�",8,$_SERVER['HTTP_REFERER']);
		}
	}
}