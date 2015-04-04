<?php
function smarty_function_image($paramer,&$smarty){
		global $db,$config;
	    $width=$paramer[width];
		$height=$paramer[height];
		$uid=$paramer[uid];
		$alt=$paramer[alt];
		$alt=$alt?"alt='".$alt."'":"";
		$action=$paramer[action];//moblie手机，linkqq联系QQ，linktel企业电话,telphone简历电话,telhome家庭电话,idcard身份证
		//1.判断图片是否存在
		//2.查询数据库并生成图片
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
					return iconv('utf8','gbk',"用户未填写");
				}
				if($action=="idcard"){
					$p=substr($p,0,strlen($p)-6).'******';
				}

				$nwidth=$width?$width:130;

				$nheight=$height?$height:23;
				$im=@imagecreate($nwidth,$nheight) or die("Can't initialize new GD image stream"); //建立图象
				//图片色彩设置
				$background_color=imagecolorallocate($im,255,255,255); //匹配颜色
				$text_color=imagecolorallocate($im,255,0,0);
				//绘制图片边框
				imagefilledrectangle($im,0,0,$nwidth-1,$nheight-1,$background); //矩形区域着色
				imagerectangle($im,0,0,$nwidth-1,$nheight-1,$background_color); //绘制矩形
				$randval=$p; //5位数
				imagestring($im,8,10,2,$randval,$text_color); //绘制横式字串
				@imagegif($im,$dir.$dir2.$name); //建立png图型
				@imagedestroy($im); //结束图型
			}else{
				return iconv('utf8','gbk',"用户未填写");
			}
		}

			return  "<img src='".$config[sy_weburl]."/upload/tel/".$uid."/".$dir2.$name."' ".$alt."/>";


}
?>