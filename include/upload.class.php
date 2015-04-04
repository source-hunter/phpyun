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
class Upload {
	var $imgname='';
	var $previewname='';			// 添加这两个变量用于记录，图片全名，以及缩略图全名
	var $upfiledir='/upload';
	var $maxsize='10240';				//图片最大小KB
	var $addpreview=true;			//是否生成缩略图
	var $addwatermark=false;		//$addwatermark=1加水印，其它不加水印
	var $watertype='img';			//水印类型(txt为文字,img为图片)
    var $waterimg='/images/logo.png';//水印图片
	var $waterstring='www.phpyun.com';	//水印字符
	var $ttf='';		//默认字体
	var $alpha='50';				//透明度
    var $position=1;				//水印位置 1左上,2:上中3:右上4:中左5居中6中右7左下8底中9底右
	var $destination_folder;
	var $errorType;
	function Upload($paras){
		foreach($paras as $key => $value){
			$key=strtolower($key);
			$this->$key=$value;
		}
		$this->upfiledir=rtrim($this->upfiledir,'/');
		$this->maxsize=intval($this->maxsize);

	
		$this->errorType=0;
	}
	/*
		上传图片
		$file
	*/
	function picture($file,$index=false,$type=''){
		if($index===false){
			if($type){
				$imageInfo	= $this->getInfo($file);
				$fileName=$imageInfo["name"];
				$filetmpname=$file;
			}else{
				$fileName=$file["name"];
				$filetmpname=$file["tmp_name"];
			}
		}else{
			$fileName=$file["name"][$index];
			$filetmpname=$file["tmp_name"][$index];
		}
		$uptypes=array('jpg','png','jpeg','bmp','gif');
		$nameArr=@explode(".",$fileName);
		$num=count($nameArr)-1;
		$this->destination_folder=$this->upfiledir.'/'.date('Ymd').'/'; //上传文件路径
		if(!file_exists($this->upfiledir)){
			@mkdir($this->upfiledir,0777,true);
		}
		if(!file_exists($this->destination_folder)){
			@mkdir($this->destination_folder,0777,true);
		}
		$imgpreviewsize=1/2;    //缩略图比例
		if(@filesize($filetmpname) > ($this->maxsize*1024))//检查文件大小
    	{
			return $this->errorType=1;//文件太大
   		 }
		 if(!in_array(strtolower($nameArr[$num]),$uptypes))
		//检查文件类型
		{
			return $this->errorType=2;//文件类型不符

		}
		$image_size = getimagesize($filetmpname);
		$pinfo=pathinfo($fileName);
		$ftype=$pinfo['extension'];
		$destination =$this->destination_folder.$this->generateImgName(strtoupper($ftype));
		if($type){
			if(!copy($filetmpname,$destination)){
				return $this->errorType=5;//复制文件出错
			}
		}else{
			if (file_exists($destination)){
				return $this->errorType=3;//同名文件已经存在了
			}
			if(!move_uploaded_file($filetmpname,$destination)){
				return $this->errorType=4;//移动文件出错
			}
		}
		$this->imgname=$destination;
		if($this->addpreview){
			$this->makeThumb($this->imgname);
		}
		if($this->addwatermark){
			$this->makewatermark($this->imgname);
		}
		return ($this->imgname);
	}
	//选择生成水印类型(文字或者图片)
	function makewatermark($destination){
        switch($this->watertype){
            case 'txt':   //加水印字符串
		 	return $this->waterMarktxt($destination);
            break;
            case 'img':   //加水印图片
            return $this->waterMarkimg($destination);
		break;
        }
	}
	//==========================================
	// 函数: waterMarkimg($destination,$image_size,$destination_folder)
	// 功能: 给图片加水印
	// 参数: $destination 图片文件名
	// 参数: $image_size 大小数组(包含二个字符串)
	//destination_folder文件存放路径
	// 返回: 1 成功 成功时返回生成的图片路径
	//==========================================
	function waterMarkimg($destination)
	{
		$image_size = getimagesize($destination);
	    $iinfo=getimagesize($destination,$iinfo);//取得GIF、JPEG、PNG或SWF图形的大小
        $iinfo2=getimagesize($this->waterimg,$iinfo2);//取得GIF、JPEG、PNG或SWF水印图形的大小
        $nimage=imagecreatetruecolor($image_size[0],$image_size[1]);//建立一个新的图形
        $white=imagecolorallocate($nimage,255,255,255);//分配图形的颜色
        $black=imagecolorallocate($nimage,0,0,0);//分配图形的颜色
        $red=imagecolorallocate($nimage,255,0,0);//分配图形的颜色
        imagefill($nimage,0,0,$white);//将图形着色
        switch ($iinfo[2])
        {
            case 1:
            $simage =imagecreatefromgif($destination);//从文件或URL建立一个新的图形
            break;
            case 2:
            $simage =imagecreatefromjpeg($destination);
            break;
            case 3:
            $simage =imagecreatefrompng($destination);
            break;
            case 6:
            $simage =imagecreatefromwbmp($destination);
            break;
            default:$this->errorType=5;//不支持的文件类型
            return;
        }
        imagecopy($nimage,$simage,0,0,0,0,$image_size[0],$image_size[1]);
           switch ($iinfo2[2]){
            case 1:
            $simage1 =imagecreatefromgif($this->waterimg);
            break;
            case 2:
            $simage1 =imagecreatefromjpeg($this->waterimg);
            break;
            case 3:
            $simage1 =imagecreatefrompng($this->waterimg);
            break;
            case 6:
            $simage1 =imagecreatefromwbmp($this->waterimg);
            break;
            default:$this->errorType=6;//不支持的水印文件类型
            return;
        }
		$gifsize=getimagesize($this->waterimg);//取得GIF、JPEG、PNG或SWF图形的大小
       switch($this->position){//水印位置
		  case 1:// 上左
          imagecopy($nimage,$simage1,0,0,0,0,$gifsize[0],$gifsize[1]); // 左下
          break;
		  case 2:// 上中
          imagecopy($nimage,$simage1,($image_size[0]-$gifsize[0])/2,0,0,0,$gifsize[0],$gifsize[1]); // 左下
          break;
		  case 3:// 右上
		  imagecopy($nimage,$simage1,$image_size[0]-$gifsize[0],0,0,0,$gifsize[0],$gifsize[1]);
		  break;
		  case 4:// 中左
          imagecopy($nimage,$simage1,0,($image_size[1]-$gifsize[1])/2,0,0,$gifsize[0],$gifsize[1]); // 左下
          break;
		  case 5:// 居中
		  imagecopy($nimage,$simage1,($image_size[0]-$gifsize[0])/2, ($image_size[1]-$gifsize[1])/2,0,0,$gifsize[0],$gifsize[1]);
		  break;
		  case 6:// 中右
          imagecopy($nimage,$simage1,$image_size[0]-$gifsize[0],($image_size[1]-$gifsize[1])/2,0,0,$gifsize[0],$gifsize[1]); // 左下
          break;
          case 9:// 右下
          imagecopy($nimage,$simage1,$image_size[0]-$gifsize[0], $image_size[1]-$gifsize[1],0,0,$gifsize[0],$gifsize[1]);
          break;
		  case 7:// 左下
          imagecopy($nimage,$simage1,0,$image_size[1]-$gifsize[1],0,0,$gifsize[0],$gifsize[1]); // 左下
          break;
		  case 8:// 底中
          imagecopy($nimage,$simage1,($image_size[0]-$gifsize[0])/2,$image_size[1]-$gifsize[1],0,0,$gifsize[0],$gifsize[1]); // 左下
          break;
        }
       imagedestroy($simage1);// 结束图形
       switch ($iinfo[2]){
            case 1:
            imagejpeg($nimage, $destination);
            break;
            case 2:
            imagejpeg($nimage, $destination);
            break;
            case 3:
            imagepng($nimage, $destination);
            break;
            case 6:
            imagewbmp($nimage, $destination);
            break;
        }
        //覆盖原上传文件
        imagedestroy($nimage);// 结束图形
        imagedestroy($simage);// 结束图形
        return $destination;
	}
	//==========================================
	// 函数: addwatermark($sourFile, $text)
	// 功能: 给图片加水印
	// 参数: $sourFile 图片文件名
	// 参数: $text 文本数组(包含二个字符串)
	//displayPath文件存放路径
	// 返回: 1 成功 成功时返回生成的图片路径
	//==========================================
	function waterMarktxt($sourFile){
		 $maxWidth  = 300;			//图片最大宽度
		 $maxHeight = 300;			//图片最大高度
		 $toFile	= true;			//是否生成文件
		$imageInfo	= $this->getInfo($sourFile);
		switch ($imageInfo["type"])
		{
			case 1:	//gif
			$newName	= substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".GIF";
				break;
			case 2:	//jpg
			$newName	= substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".JPG";
				break;
			case 3:	//png
			$newName	= substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".PNG";
				break;
			default:
				return 0;
				break;
		}
		switch ($imageInfo["type"])
		{
			case 1:	//gif
				$img = imagecreatefromgif($sourFile);
				break;
			case 2:	//jpg
				$img = imagecreatefromjpeg($sourFile);
				break;
			case 3:	//png
				$img = imagecreatefrompng($sourFile);
				break;
			default:
				return 0;
				break;
		}
		if (!$img) {
			return 0;
		}
		$width  = ($maxWidth > $imageInfo["width"]) ? $imageInfo["width"] : $maxWidth;
		$height = ($maxHeight > $imageInfo["height"]) ? $imageInfo["height"] : $maxHeight;
		$srcW	= $imageInfo["width"];
		$srcH	= $imageInfo["height"];
		if ($srcW * $width > $srcH * $height)
			$height = round($srcH * $width / $srcW);
		else
			$width = round($srcW * $height / $srcH);
		//*
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			$new = imagecreatetruecolor($width, $height);
			imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		else
		{
			$new = imagecreate($width, $height);
			imagecopyresized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		$black = imagecolorallocate($new, 0, 0, 0);
		//$alpha = imagecolorallocatealpha($new, 230, 230, 230, 40);
		$alpha = imagecolorallocatealpha($new, 230, 230, 230,$this->alpha);
		//$rectW = max(strlen($text[0]),strlen($text[1]))*7;
		imagefilledrectangle($new, 0, $height-26, $width, $height, $alpha);
		$white = imagecolorallocate ($new, 0, 0, 0);
  		imagettftext ($new, 10, 0, 20, $height-8,$white, $this->ttf, $this->waterstring);
		//*/
        if ($toFile)
		{
			if (file_exists($this->destination_folder.$newName))
			 @unlink($this->destination_folder.$newName);
			imagejpeg($new, $this->destination_folder.$newName);
			imagedestroy($new);
			imagedestroy($img);
			return $this->destination_folder.$newName;
		}
		else
		{
			imagejpeg($new);
			imagedestroy($new);
			imagedestroy($img);
		}
	}
    //==========================================
	// 函数: getInfo($file)
	// 功能: 返回图像信息
	// 参数: $file 文件名称
	// 返回: 图片信息数组
	//==========================================
	function getInfo($file)
	{
		//$file=$file;
		$data=getimagesize($file);
		$imageInfo["width"]	= $data[0];
		$imageInfo["height"]= $data[1];
		$imageInfo["type"]	= $data[2];
		$imageInfo["name"]	= basename($file);
		$imageInfo["size"]  = filesize($file);
		return $imageInfo;
	}
	//==========================================
	// 函数: makeThumb($sourFile,$width=80,$height=60)
	// 功能: 生成缩略图(输出到浏览器)
	// 参数: $sourFile 图片源文件
	// 参数: $width 生成缩略图的宽度
	// 参数: $height 生成缩略图的高度
	// 返回: 0 失败 成功时返回生成的图片路径
	//==========================================
	function makeThumb($sourFile,$width=100,$height=100,$newNamePre='')
	{
		$imageInfo	= $this->getInfo($sourFile);
		switch ($imageInfo["type"])
		{
			case 1:	//gif
			$newName	='make'.$newNamePre.substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".GIF";
				break;
			case 2:	//jpg
			$newName	='make'.$newNamePre.substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".JPG";
				break;
			case 3:	//png
			$newName	='make'.$newNamePre.substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".PNG";
				break;
			default:
				return 0;
				break;
		}
		switch ($imageInfo["type"])
		{
			case 1:	//gif
				$img = imagecreatefromgif($sourFile);
				break;
			case 2:	//jpg
				$img = imagecreatefromjpeg($sourFile);
				break;
			case 3:	//png
				$img = imagecreatefrompng($sourFile);
				break;
			default:
				return 0;
				break;
		}
		if (!$img){
			return 0;
		}
		//原图片尺寸
		$srcW	= $imageInfo["width"];
		$srcH	= $imageInfo["height"];
		//等比缩放
		if(floor($srcW/$srcH) >= 1){
			$width  = ($width > $imageInfo["width"]) ? $imageInfo["width"] : $width;
			$height=round($srcH*$width/$srcW);
		}else{
			$height = ($height > $imageInfo["height"]) ? $imageInfo["height"] : $height;
			$width=round($srcW*$height/$srcH);
		}
		//生成图片
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			//$new = imagecreatetruecolor($width, $height);
			$new = imagecreatetruecolor($width, $height);
			ImageCopyResampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		else
		{
			$new = imagecreate($width, $height);
			ImageCopyResized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		//*/
		if (file_exists($this->destination_folder . $newName)){
			unlink($this->destination_folder . $newName);
		}
		ImageJPEG($new, $this->destination_folder . $newName,100);
		ImageDestroy($new);
		ImageDestroy($img);

		$this->previewname=$this->destination_folder . $newName;
		return ($this->previewname);
	}
	function news_makeThumb($sourFile,$width=100,$height=100,$newNamePre=''){
		$imageInfo	= $this->getInfo($sourFile);
		$this->destination_folder=str_replace($imageInfo["name"],"",$sourFile);
		switch ($imageInfo["type"]){
			case 1:	//gif
			$newName	='make'.$newNamePre.substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".GIF";
				break;
			case 2:	//jpg
			$newName	='make'.$newNamePre.substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".JPG";
				break;
			case 3:	//png
			$newName	='make'.$newNamePre.substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . ".PNG";
				break;
			default:
				return 0;
				break;
		}
		switch ($imageInfo["type"])
		{
			case 1:	//gif
				$img = imagecreatefromgif($sourFile);
				break;
			case 2:	//jpg
				$img = imagecreatefromjpeg($sourFile);
				break;
			case 3:	//png
				$img = imagecreatefrompng($sourFile);
				break;
			default:
				return 0;
				break;
		}
		if (!$img){
			return 0;
		}
		//原图片尺寸
		$srcW	= $imageInfo["width"];
		$srcH	= $imageInfo["height"];
		//等比缩放
		if(floor($srcW/$srcH) >= 1){
			$width  = ($width > $imageInfo["width"]) ? $imageInfo["width"] : $width;
			$height=round($srcH*$width/$srcW);
		}else{
			$height = ($height > $imageInfo["height"]) ? $imageInfo["height"] : $height;
			$width=round($srcW*$height/$srcH);
		}
		//生成图片
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			//$new = imagecreatetruecolor($width, $height);
			$new = imagecreatetruecolor($width, $height);
			ImageCopyResampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		else
		{
			$new = imagecreate($width, $height);
			ImageCopyResized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		//*/
		if (file_exists($this->destination_folder . $newName)){
			unlink($this->destination_folder . $newName);
		}
		ImageJPEG($new, $this->destination_folder . $newName,100);
		ImageDestroy($new);
		ImageDestroy($img);
		$this->previewname=$this->destination_folder . $newName;
		return ($this->previewname);
	}
    //判断图片是否需要先缩，后上传.
	function getimage($name,$maxwidth=800,$maxheight=600){
		list($width,$height,$type,$attr) =getimagesize($name);
        $imgname=$name;
		if($width>$maxwidth){
           $imgname=$this->makeThumb($name,$maxwidth,$maxheight);//生成缩略图
           unlink($name);//删除原图
        }
        return $imgname;
	}
	function generateImgName($ftype){
		$imgname='';
		$microtime=@explode(" ",microtime());
		$imgname=ceil($microtime[0]*10000000)+$microtime[1];
		$imgname.=rand(1,9);
		$imgname.='.'.$ftype;
		return ($imgname);
	}
	function toimgserver($url){
		$count=0;
		do{
			$urlcopyimg="";
			$status=@file_get_contents($urlcopyimg);
			$count++;
		}while($status && $count<3);
		return (true);
	}
}
?>