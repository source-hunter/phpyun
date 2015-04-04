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
error_reporting(0);
session_start();
function random_language($len) {
	$srcstr = "qwertyuiopasdfghjklzxcvbnm";
	mt_srand();
	$strs = "";
	for ($i = 0; $i < $len; $i++) {
		$strs .= $srcstr[mt_rand(0, 10)];
	}
	return $strs;
}
function random_shuzi($len) {
	$srcstr = "0123456789";
	mt_srand();
	$strs = "";
	for ($i = 0; $i < $len; $i++) {
		$strs .= $srcstr[mt_rand(0, 9)]; 
	}
	return $strs;
}
function random($len) {
	$srcstr = "1a2s3d4f5g6hj8k9l0qwertyuiopzxcvbnm";
	mt_srand();
	$strs = "";
	for ($i = 0; $i < $len; $i++) {
		$strs .= $srcstr[mt_rand(0, 10)];
	}
	return $strs;
}
function authcode($len=4,$width="50",$height=25,$codetype="png",$code_type="3"){
	if($code_type==3){
		$strs = random($len);
	}else if($code_type==2){
		$strs = random_language($len);
	}else{
		$strs = random_shuzi($len);
	}
	$width = $width;
	$height = $height;
	if($codetype=="png"){
		@header("Content-Type:image/png");
	}elseif($codetype=="jpg"){
		@header("Content-Type:image/jpeg");
	}else{
		@header("Content-Type:image/gif");
	}
	$im = imagecreate($width, $height);
	
	$back = imagecolorallocate($im,  255, 255, 255);
	
    imagefill($im,0,0,$back);

	$pix = imagecolorallocate($im, 222, 222, 222);
	
	$border_color = imagecolorallocate($im, 234, 234, 234);
	$black = imagecolorallocate($im, 0, 0, 0); 
    $gray = imagecolorallocate($im, 200, 200, 200); 
 

	mt_srand();
	for ($i = 0; $i < 100; $i++) {
		imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pix);
	}
	
    $font_size=20;

	$x=rand(2,5);
    for($i=0;$i<$len;$i++){
		if($i==0){
			$xx=$x;
		}else{
			$xx= $x+$font_size-1+rand(0,1);
		}
     
        $text_color = imagecolorallocate($im,rand(50,180),rand(0,180),rand(100,180));
   
        imagechar($im,5,$xx,rand(1,15),$strs[$i],$text_color);
		$x=$xx;
    }
	imagerectangle($im, 0, 0, $width -1, $height -1, $border_color);
	imagepng($im);
	imagedestroy($im);
	$strs = md5(strtolower($strs));
	$_SESSION["authcode"] = $strs;

}
include(dirname(dirname(__FILE__))."/plus/config.php");
authcode($config['code_strlength'],$config['code_width'],$config['code_height'],$config['code_filetype'],$config['code_type']);
?>