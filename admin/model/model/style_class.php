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
class style
{
	function __construct($obj)
	{
		$this->obj = $obj;
	}
	function model_list_action()
	{
		$path = APP_PATH."/template/";
		$handle = @opendir($path);

		while($file = @readdir($handle))
		{
			if($file=="."||$file==".."||$file==".svn"||$file=="member"||$file=="admin"||$file=="company"||$file=="im"||$file=="wap"||$file=="ask"||$file=="friend") continue;
			if(is_dir($path.$file))
			{
				$list[] = $file;
			}
		}
		if(is_array($list))
		{
			foreach($list as $key=>$value)
			{
				$filepath =$path.$value."/info.txt";
				if(!file_exists($filepath))
				{
					$fopen = @fopen($filepath,"w+");
					fclose($fopen);
				}
				$size = @filesize($filepath);
				$fp = @fopen($filepath,"r+");

				$text = @fread($fp,$size);
				if($text=="")
				{
					$text= "��δ����||�������������Ϣ||".$value."||"."../template/".$value."/images/preview.jpg";
					@fwrite($fp,$text);
				}
				@fclose($fp);
				$content = @explode("||",$text);
				$text="";
				$lists[$key]['name'] = $content[0];
				$lists[$key]['author'] = $content[1];
				$lists[$key]['dir'] = $content[2];
				$lists[$key]['img'] = $content[3];

			}
		}
		return $lists;
	}
	function model_modify_action($dir)
	{
		$path = APP_PATH."/template/".$dir."/info.txt";
		$fp = @fopen($path,r);
		$text = @fread($fp,filesize($path));
		@fclose($fp);
		$content = @explode("||",$text);
		$style_info = array("name"=>$content[0],"author"=>$content[1],"dir"=>$content[2],"img"=>$content[3],);
		return $style_info;

	}

	function model_save_action($arr)
		{
		extract($arr);
		$path = APP_PATH."/template/".$dir."/info.txt";

		if(is_uploaded_file($_FILES['img2']['tmp_name']))
		{
			if(!is_dir("../template/".$dir."/images"))
			{
				mkdir("../template/".$dir."/images",0777,true);
			}
			move_uploaded_file($_FILES['img2']['tmp_name'],APP_PATH."/template/".$dir."/images/preview.jpg");
		}
		$text = $name."||".$author."||".$dir."||../template/".$dir."/images/preview.jpg";
		$fp = @fopen($path,w);
		@fwrite($fp,$text);
		@fclose($fp);
		$this->obj->ACT_layer_msg("��Ϣ�޸ĳɹ���",9,"index.php?m=admin_style",2,1);

	}

}