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
class template
{
	function __construct($obj)
	{
		$this->obj = $obj;
	}
	public function model_dir_action($path,$dir=NULL,$type=NULL)
	{
		$handle = opendir($path);
		while($file = readdir($handle))
		{
			  if($file=="."||$file==".."||$file==".svn") continue;
			  $newFilePath = $path.$file;
			  if($type==NULL)
			  {
				  if(is_dir($newFilePath))
				  {
				  	$list.=$this->model_dir_action($newFilePath."/",$file);
				  }
			  }
			  if(is_file($newFilePath))
			  {
			  	 if(strrchr($file,".")==".htm")
				  {
						$list.= $file."+".$path."+".$newFilePath.",";
				  }
			  }
		 }
		 closedir($handle);
		 return $list;

	}
	public function model_list_action($style="default",$path=NULL,$file=NULL)
	{
		if($path=="")
		{
			if($file!="")
			{
				$path=APP_PATH."/template/".$style."/".$file."/";
			}else{
				$path=APP_PATH."/template/".$style."/";
			}
		}
		$list = $this->model_dir_action($path,$file);
		$lists =@ explode(",",$list);
		if(is_array($lists))
		{
			foreach($lists as $key=>$value)
			{
				$v = @explode("+",$value);
				$tp_list[$key]['name'] = $v[0];
				$tp_list[$key]['path'] = $v[1];
				$tp_list[$key]['file_path'] = str_replace(APP_PATH,"..",$v[1]);
				$tp_list[$key]['dir'] = $tp_list[$key]['file_path'];
				$tp_list[$key]['tp_name'] = "暂无说明";
				$tp_list[$key][time] = "暂未做修改";
			}

		}
		$temp = $this->obj->DB_select_all("admin_template");
		if(is_array($temp)){
			foreach($temp as $key=>$value)
			{
				foreach($tp_list as $k=>$v)
				{
					if($value['name']==$v['name'] && $value['dir']==$v['dir'])
					{
						$tp_list[$k]['tp_name']=$value['tp_name'];
						$tp_list[$k]['time']=date("Y-m-d h:i:s",$value['update_time']);
					}
				}

			}
		}
		return $tp_list;
	}
	public function model_modify_action($name,$style,$dir)
	{
		$list = $this->model_list_action($style,NULL,NULL,$obj);
		foreach($list as $key=>$value)
		{
				if($name==$value['name'] && $dir==$value['dir'])
				{
					$tp_info['name'] = $value['name'];
					$tp_info['dir'] = $dir;
					$tp_info['path'] = $value['path'];
					$tp_info['file_path'] = $value['file_path'];
					$tp_info['tp_name'] = $value['tp_name'];
					break;
				}
		}
		$fp=@fopen($tp_info['path']."/".$tp_info['name'],"r");
  		$content_text=@fread($fp,filesize($tp_info['path']."/".$tp_info['name']));
		fclose($fp);
	    $tp_info['content'] = htmlspecialchars($content_text);
		return $tp_info;
	}
	public function model_savetp_action($post,$table,$style)
	{
		extract($post);
		$sql = "`name`='$name' and `dir`='$tp_path'";
		$tp = $this->obj->DB_select_all($table,$sql);
		$time = time();
		$value = "`name`='$name',`tp_name`='$tp_name',`update_time`='$time'";
		if(is_array($tp))
		{
			$this->obj->DB_update_all($table,$value,"`name`='$name' AND `dir`='$tp_path'");
		}else{
			$value.=",`dir`='$tp_path'";
			$this->obj->DB_insert_once($table,$value);
		}
		$content = html_entity_decode($content,ENT_QUOTES,"GB2312");
		$fp = fopen($tp_path."/".$name,"w");
		fwrite($fp,$content);
		fclose($fp);
		$this->obj->ACT_layer_msg("模板修改成功！",9,"index.php?m=admin_template",2,1);
	}
	function model_tpaddsave_action($post,$table)
	{
		extract($post);
		$time = time();
		if($name!="")
		{
			if($dir=="")
			{
				$dir = $style;
				$file_path = $path."/";
			}else{
				$file_path = $path.$dir."/";
			}
			$tpname = $name.".htm";
			$file_list = @explode(",",$this->model_dir_action($file_path,NULL,1));
			if(is_array($file_list))
			{
				foreach($file_list as $key=>$value)
				{
					$v = @explode("+",$value);
					if($v[0]==$tpname)
					{
						$this->obj->get_admin_msg("index.php?m=admin_template&c=tpadd","该文件已经存在");
					}
				}
			}
			$dir = str_replace(APP_PATH,"..",$file_path);
			$value = "`name`='$tpname',`tp_name`='$tp_name',`update_time`='$time',`dir`='$dir'";
			$nid = $this->obj->DB_insert_once($table,$value);
			if($nid>0)
			{
				if(!is_dir($file_path))
				{
					mkdir($file_path,0777,true);
				}
				if(!file_exists($file_path.$name.".htm"))
				{
					$fp = @fopen($file_path.$name.".htm","w");
					$content = html_entity_decode($content,ENT_QUOTES,"GB2312");
					fwrite($fp,$content);
					fclose($fp);
					$this->obj->ACT_layer_msg("模板添加成功！",9,"index.php?m=admin_template",2,1);
				}
			}else{
				$this->obj->ACT_layer_msg("模板添加失败！",8,"index.php?m=admin_template");
			}
		}else{
			$this->obj->ACT_layer_msg("文件名称不得为空！",8,"index.php?m=admin_template&c=tpadd");
		}
	}
}