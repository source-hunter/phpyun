<?php
function smarty_function_image($paramer,&$smarty){
		global $db,$config;
	    $width=$paramer[width];
		$height=$paramer[height];
		$uid=$paramer[uid];
		$alt=$paramer[alt];
		$alt=$alt?"alt='".$alt."'":"";
		$action=$paramer[action];//moblie�ֻ���linkqq��ϵQQ��linktel��ҵ�绰,telphone�����绰,telhome��ͥ�绰,idcard���֤
		//1.�ж�ͼƬ�Ƿ����
		//2.��ѯ���ݿⲢ����ͼƬ
		$action=$action?$action:"moblie";
		
		$dir=APP_PATH."upload/tel/".$uid."/";
		if(!is_dir($dir))@mkdir($dir,true);
		@chmod($dir,0777);
		if($paramer[jobid])
		{
			$dir2=$paramer[jobid]."/";
			if(!is_dir($dir.$dir2))@mkdir($dir.$dir2,true);
		}
		
		$name=$action.".gif";
		
		@chmod($dir.$dir2,0777);
		if(!file_exists($dir.$dir2.$name)){
				
			if(!$paramer[number])
			{
				switch($action){
					case "":
					case "moblie":
					$table="member";
					break;
					case "linkqq":
					case "linktel":
					case "linkphone":
					$table="company";
					break;
					case "telhome":
					case "telphone":
					case "idcard":
					$table="resume";
					break;
				}

				$Info = $db->select_alls("member",$table,"a.`uid`=b.`uid` and a.`uid`='".$uid."'");
			}else{
				$p = $paramer[number];
			}
			

			if(is_array($Info) || $p){
				if(!$p)
				{
					$p=$Info[0][$action];
				}
				
				if($p==""){
					return iconv('utf8','gbk',"�û�δ��д");
				}
				if($action=="idcard"){
					$p=substr($p,0,strlen($p)-6).'******';
				}

				$nwidth=$width?$width:130;

				$nheight=$height?$height:23;
				$im=@imagecreate($nwidth,$nheight) or die("Can't initialize new GD image stream"); //����ͼ��
				//ͼƬɫ������
				$background_color=imagecolorallocate($im,255,255,255); //ƥ����ɫ
				$text_color=imagecolorallocate($im,255,0,0);
				//����ͼƬ�߿�
				imagefilledrectangle($im,0,0,$nwidth-1,$nheight-1,$background); //����������ɫ
				imagerectangle($im,0,0,$nwidth-1,$nheight-1,$background_color); //���ƾ���
				$randval=$p; //5λ��
				imagestring($im,8,10,2,$randval,$text_color); //���ƺ�ʽ�ִ�
				@imagegif($im,$dir.$dir2.$name); //����pngͼ��
				@imagedestroy($im); //����ͼ��
			}else{
				return iconv('utf8','gbk',"�û�δ��д");
			}
		}

			return  "<img src='".$config[sy_weburl]."/upload/tel/".$uid."/".$dir2.$name."' ".$alt."/>";


}
?>