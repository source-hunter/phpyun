<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class camphoto_controller extends user{
	function index_action(){
		if(function_exists("imagecreate")){
			$w = (int)$_POST['w'];
			$h = (int)$_POST['h'];
			$img = imagecreatetruecolor($w, $h);
			imagefill($img, 0, 0, 0xffffff);
			$rows = 0;
			$cols = 0;
			for($rows = 0; $rows < $h; $rows++){
				$c_row = explode(",", $_POST['PX' . $rows]);
				for($cols = 0; $cols < $w; $cols++){
					$value = $c_row[$cols];
					if($value != ""){
						$hex = $value;
						while(strlen($hex) < 6){
							$hex = "0" . $hex;
						}
						$r = hexdec(substr($hex, 0, 2));
						$g = hexdec(substr($hex, 2, 2));
						$b = hexdec(substr($hex, 4, 2));
						$test = imagecolorallocate($img, $r, $g, $b);
						imagesetpixel($img, $cols, $rows, $test);
					}
				}
			}
			$savePath = "../upload/user/".date('Ymd').'/';
			if(!file_exists($savePath)){
				@mkdir($savePath,0777,true);
			}
			$filename=$savePath.date('YmdHis')."_".rand(100,999).".jpg";


			header("Pragma:no-cache\r\n");
			header("Cache-Control:no-cache\r\n");
			header("Expires:0\r\n");

			if(function_exists("imagejpeg")){
				imagejpeg($img,  $filename, 100);
			}else{
				imagepng($img,  $filename, 100);
			}
			ImageDestroy($img);

			$filename=substr($filename,1);
			$this->obj->DB_update_all("resume","`photo`='$filename',`resume_photo`='$filename'","`uid`='".$this->uid."'");
		}
		header("Location: ../../member/index.php?c=uppic");
	}

}
?>