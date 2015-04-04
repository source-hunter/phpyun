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
class Upload {
	var $imgname='';
	var $previewname='';			// ����������������ڼ�¼��ͼƬȫ�����Լ�����ͼȫ��
	var $upfiledir='/upload';
	var $maxsize='10240';				//ͼƬ���СKB
	var $addpreview=true;			//�Ƿ���������ͼ
	var $addwatermark=false;		//$addwatermark=1��ˮӡ����������ˮӡ
	var $watertype='img';			//ˮӡ����(txtΪ����,imgΪͼƬ)
    var $waterimg='/images/logo.png';//ˮӡͼƬ
	var $waterstring='www.phpyun.com';	//ˮӡ�ַ�
	var $ttf='';		//Ĭ������
	var $alpha='50';				//͸����
    var $position=1;				//ˮӡλ�� 1����,2:����3:����4:����5����6����7����8����9����
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
		�ϴ�ͼƬ
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
		$this->destination_folder=$this->upfiledir.'/'.date('Ymd').'/'; //�ϴ��ļ�·��
		if(!file_exists($this->upfiledir)){
			@mkdir($this->upfiledir,0777,true);
		}
		if(!file_exists($this->destination_folder)){
			@mkdir($this->destination_folder,0777,true);
		}
		$imgpreviewsize=1/2;    //����ͼ����
		if(@filesize($filetmpname) > ($this->maxsize*1024))//����ļ���С
    	{
			return $this->errorType=1;//�ļ�̫��
   		 }
		 if(!in_array(strtolower($nameArr[$num]),$uptypes))
		//����ļ�����
		{
			return $this->errorType=2;//�ļ����Ͳ���

		}
		$image_size = getimagesize($filetmpname);
		$pinfo=pathinfo($fileName);
		$ftype=$pinfo['extension'];
		$destination =$this->destination_folder.$this->generateImgName(strtoupper($ftype));
		if($type){
			if(!copy($filetmpname,$destination)){
				return $this->errorType=5;//�����ļ�����
			}
		}else{
			if (file_exists($destination)){
				return $this->errorType=3;//ͬ���ļ��Ѿ�������
			}
			if(!move_uploaded_file($filetmpname,$destination)){
				return $this->errorType=4;//�ƶ��ļ�����
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
	//ѡ������ˮӡ����(���ֻ���ͼƬ)
	function makewatermark($destination){
        switch($this->watertype){
            case 'txt':   //��ˮӡ�ַ���
		 	return $this->waterMarktxt($destination);
            break;
            case 'img':   //��ˮӡͼƬ
            return $this->waterMarkimg($destination);
		break;
        }
	}
	//==========================================
	// ����: waterMarkimg($destination,$image_size,$destination_folder)
	// ����: ��ͼƬ��ˮӡ
	// ����: $destination ͼƬ�ļ���
	// ����: $image_size ��С����(���������ַ���)
	//destination_folder�ļ����·��
	// ����: 1 �ɹ� �ɹ�ʱ�������ɵ�ͼƬ·��
	//==========================================
	function waterMarkimg($destination)
	{
		$image_size = getimagesize($destination);
	    $iinfo=getimagesize($destination,$iinfo);//ȡ��GIF��JPEG��PNG��SWFͼ�εĴ�С
        $iinfo2=getimagesize($this->waterimg,$iinfo2);//ȡ��GIF��JPEG��PNG��SWFˮӡͼ�εĴ�С
        $nimage=imagecreatetruecolor($image_size[0],$image_size[1]);//����һ���µ�ͼ��
        $white=imagecolorallocate($nimage,255,255,255);//����ͼ�ε���ɫ
        $black=imagecolorallocate($nimage,0,0,0);//����ͼ�ε���ɫ
        $red=imagecolorallocate($nimage,255,0,0);//����ͼ�ε���ɫ
        imagefill($nimage,0,0,$white);//��ͼ����ɫ
        switch ($iinfo[2])
        {
            case 1:
            $simage =imagecreatefromgif($destination);//���ļ���URL����һ���µ�ͼ��
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
            default:$this->errorType=5;//��֧�ֵ��ļ�����
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
            default:$this->errorType=6;//��֧�ֵ�ˮӡ�ļ�����
            return;
        }
		$gifsize=getimagesize($this->waterimg);//ȡ��GIF��JPEG��PNG��SWFͼ�εĴ�С
       switch($this->position){//ˮӡλ��
		  case 1:// ����
          imagecopy($nimage,$simage1,0,0,0,0,$gifsize[0],$gifsize[1]); // ����
          break;
		  case 2:// ����
          imagecopy($nimage,$simage1,($image_size[0]-$gifsize[0])/2,0,0,0,$gifsize[0],$gifsize[1]); // ����
          break;
		  case 3:// ����
		  imagecopy($nimage,$simage1,$image_size[0]-$gifsize[0],0,0,0,$gifsize[0],$gifsize[1]);
		  break;
		  case 4:// ����
          imagecopy($nimage,$simage1,0,($image_size[1]-$gifsize[1])/2,0,0,$gifsize[0],$gifsize[1]); // ����
          break;
		  case 5:// ����
		  imagecopy($nimage,$simage1,($image_size[0]-$gifsize[0])/2, ($image_size[1]-$gifsize[1])/2,0,0,$gifsize[0],$gifsize[1]);
		  break;
		  case 6:// ����
          imagecopy($nimage,$simage1,$image_size[0]-$gifsize[0],($image_size[1]-$gifsize[1])/2,0,0,$gifsize[0],$gifsize[1]); // ����
          break;
          case 9:// ����
          imagecopy($nimage,$simage1,$image_size[0]-$gifsize[0], $image_size[1]-$gifsize[1],0,0,$gifsize[0],$gifsize[1]);
          break;
		  case 7:// ����
          imagecopy($nimage,$simage1,0,$image_size[1]-$gifsize[1],0,0,$gifsize[0],$gifsize[1]); // ����
          break;
		  case 8:// ����
          imagecopy($nimage,$simage1,($image_size[0]-$gifsize[0])/2,$image_size[1]-$gifsize[1],0,0,$gifsize[0],$gifsize[1]); // ����
          break;
        }
       imagedestroy($simage1);// ����ͼ��
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
        //����ԭ�ϴ��ļ�
        imagedestroy($nimage);// ����ͼ��
        imagedestroy($simage);// ����ͼ��
        return $destination;
	}
	//==========================================
	// ����: addwatermark($sourFile, $text)
	// ����: ��ͼƬ��ˮӡ
	// ����: $sourFile ͼƬ�ļ���
	// ����: $text �ı�����(���������ַ���)
	//displayPath�ļ����·��
	// ����: 1 �ɹ� �ɹ�ʱ�������ɵ�ͼƬ·��
	//==========================================
	function waterMarktxt($sourFile){
		 $maxWidth  = 300;			//ͼƬ�����
		 $maxHeight = 300;			//ͼƬ���߶�
		 $toFile	= true;			//�Ƿ������ļ�
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
	// ����: getInfo($file)
	// ����: ����ͼ����Ϣ
	// ����: $file �ļ�����
	// ����: ͼƬ��Ϣ����
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
	// ����: makeThumb($sourFile,$width=80,$height=60)
	// ����: ��������ͼ(����������)
	// ����: $sourFile ͼƬԴ�ļ�
	// ����: $width ��������ͼ�Ŀ��
	// ����: $height ��������ͼ�ĸ߶�
	// ����: 0 ʧ�� �ɹ�ʱ�������ɵ�ͼƬ·��
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
		//ԭͼƬ�ߴ�
		$srcW	= $imageInfo["width"];
		$srcH	= $imageInfo["height"];
		//�ȱ�����
		if(floor($srcW/$srcH) >= 1){
			$width  = ($width > $imageInfo["width"]) ? $imageInfo["width"] : $width;
			$height=round($srcH*$width/$srcW);
		}else{
			$height = ($height > $imageInfo["height"]) ? $imageInfo["height"] : $height;
			$width=round($srcW*$height/$srcH);
		}
		//����ͼƬ
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
		//ԭͼƬ�ߴ�
		$srcW	= $imageInfo["width"];
		$srcH	= $imageInfo["height"];
		//�ȱ�����
		if(floor($srcW/$srcH) >= 1){
			$width  = ($width > $imageInfo["width"]) ? $imageInfo["width"] : $width;
			$height=round($srcH*$width/$srcW);
		}else{
			$height = ($height > $imageInfo["height"]) ? $imageInfo["height"] : $height;
			$width=round($srcW*$height/$srcH);
		}
		//����ͼƬ
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
    //�ж�ͼƬ�Ƿ���Ҫ���������ϴ�.
	function getimage($name,$maxwidth=800,$maxheight=600){
		list($width,$height,$type,$attr) =getimagesize($name);
        $imgname=$name;
		if($width>$maxwidth){
           $imgname=$this->makeThumb($name,$maxwidth,$maxheight);//��������ͼ
           unlink($name);//ɾ��ԭͼ
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