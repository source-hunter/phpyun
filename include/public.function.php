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
/*
* 把数组生成字符串
*/
function ArrayToString($obj,$withKey=true,$two=false){
	if(empty($obj))	return array();
	$objType=gettype($obj);
	if ($objType=='array') {
		$objstring = "array(";
	    foreach ($obj as $objkey=>$objv) {
			if($withKey)$objstring .="\"$objkey\"=>";
			$vtype =gettype($objv) ;
			if ($vtype=='integer') {
			  $objstring .="$objv,";
			}else if ($vtype=='double'){
			  $objstring .="$objv,";
			}else if ($vtype=='string'){
			  $objv= str_replace('"',"\\\"",$objv);
			  $objstring .="\"".$objv."\",";
			}else if ($vtype=='array'){
			  $objstring .="".ArrayToString($objv,false).",";
			}else if ($vtype=='object'){
			  $objstring .="".ArrayToString($objv,false).",";
			}else {
			  $objstring .="\"".$objv."\",";
			}
	    }
		$objstring = substr($objstring,0,-1)."";
		return $objstring.")\n";
	}
}
function ArrayToString2($obj,$withKey=true){
	if(empty($obj))	return "array()";
	$objType=gettype($obj);
	if ($objType=='array') {
		$objstring = "array(";
	    foreach ($obj as $objkey=>$objv) {
			if($withKey)$objstring .="\"$objkey\"=>";
			$vtype =gettype($objv) ;
			if ($vtype=='integer') {
			  $objstring .="$objv,";
			}else if ($vtype=='double'){
			  $objstring .="$objv,";
			}else if ($vtype=='string'){
			  $objv= str_replace('"',"\\\"",$objv);
			  $objstring .="\"".$objv."\",";
			}else if ($vtype=='array'){
			  $objstring .="".ArrayToString2($objv,$withKey).",";
			}else if ($vtype=='object'){
			  $objstring .="".ArrayToString2($objv,$withKey).",";
			}else {
			  $objstring .="\"".$objv."\",";
			}
	    }
		$objstring = substr($objstring,0,-1)."";
		return $objstring.")\n";
	}
}

function fun_ip_get() {
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
		$ip = getenv("HTTP_CLIENT_IP");
	} else
		if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		} else
			if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
				$ip = getenv("REMOTE_ADDR");
			} else
				if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
					$ip = $_SERVER['REMOTE_ADDR'];
				} else {
					$ip = "unknown";
				}
	 $preg="/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
	 if(preg_match($preg,$ip)){
	 
		return ($ip);
	 }
}
function get_ip_city($ip){
	$url='http://www.ip138.com/ips138.asp?ip='.$ip.'&action=2';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$content = curl_exec($ch);
	curl_close($ch);
	preg_match('/本站主数据：(?<mess>(.*))市(.*)<\/li><li>/',$content,$arr);
	if(strripos($arr['mess'],"省")>0)
	$city=substr($arr['mess'],strripos($arr['mess'],"省")+2,100);
	else
	$city=$arr['mess'];
	if(!$city)$city="无法获取";	
	return $city;
}
function getUploadPic($content,$count=0)
{
	$content=str_replace('"','',$content);
	$content=str_replace('\'','',$content);
	$content=str_replace('>',' width="">',$content);
	$pattern=preg_match_all('/<img[^>]+src=(.*?)\s[^>]+>/im' ,$content,$match);
	if($match[1])
	{
		if($count>0)
		{
			$i=0;
			foreach($match[1] as $v)
			{
				if(!empty($v))
				{
					$pic[]=$v;
					$i++;
					if($i>=$count)
					{
						break;
					}
				}
			}
			return $pic;
		}
		return $match[1];
	}
	return array();

}
	function dreferer($default = '') {
		$referer=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		if(strpos('a'.$referer,url('user','login'))) {
			$referer = $default;
		}
		else
		{
			$referer = substr($referer, -1) == '?' ? substr($referer, 0, -1) : $referer;
		}
		return $referer;
	}

	function file_mode_info($file_path){
	   
	    if (!file_exists($file_path)){
	        return false;
	    }
	    $mark = 0;
	    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
	     
	        $test_file = $file_path . '/cf_test.txt';
	      
	        if (is_dir($file_path)){
	          
	            $dir = @opendir($file_path);
	            if ($dir === false){
	                return $mark; 
	            }
	            if (@readdir($dir) !== false){
	                $mark ^= 1; 
	            }
	            @closedir($dir);
	        }
	    }
	    return $mark;
	}
	function getAround($lat,$lon,$raidus){
		$PI = 3.14159265;
		$latitude = $lat;
		$longitude = $lon;
		$degree = (24901*1609)/360.0;
		$raidusMile = $raidus;
		$dpmLat = 1/$degree;
		$radiusLat = $dpmLat*$raidusMile;
		$minLat = $latitude - $radiusLat;
		$maxLat = $latitude + $radiusLat;
		$mpdLng = $degree*cos($latitude*($PI/180));
		$dpmLng = 1/$mpdLng;
		$radiusLng = $dpmLng*$raidusMile;
		$minLng = $longitude - $radiusLng;
		$maxLng = $longitude + $radiusLng;
		return array($minLat,$maxLat,$minLng,$maxLng);
	}
function checkMobile($mobilephone){ 
	$exp = "/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[012356789]{1}[0-9]{8}$|14[57]{1}[0-9]$/"; 
	if(preg_match($exp,$mobilephone)){ 
		return true; 
	}else{ 
		return false; 
	} 
} 
function checkEmail($email){
	$pregEmail = "/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i";
	if(preg_match($pregEmail,$email)){ 
		return true; 
	}else{ 
		return false; 
	}  
}
function UserAgent(){    
    $user_agent = ( !isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];    
	if ((preg_match("/(iphone|ipod|android)/i", strtolower($user_agent))) AND strstr(strtolower($user_agent), 'webkit')){    
    	return true;    
	}else if(trim($user_agent) == '' OR preg_match("/(nokia|sony|ericsson|mot|htc|samsung|sgh|lg|philips|lenovo|ucweb|opera mobi|windows mobile|blackberry)/i", strtolower($user_agent))){   
		return true;   
	}else{//PC   
		return true;  
	}    
}
function get_domain($host) {
 $host=strtolower($host);
 if(strpos($host,'/')!==false){
  $parse = @parse_url($host);
  $host = $parse['host']; }
  $topleveldomaindb=array('com','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','mobi','cc','me'); $str='';
  foreach($topleveldomaindb as $v){
   $str.=($str ? '|' : '').$v;
  }
   $matchstr="[^\.]+\.(?:(".$str.")|\w{2}|((".$str.")\.\w{2}))$";
  if(preg_match("/".$matchstr."/ies",$host,$matchs)){
   $domain=$matchs['0'];
  } else{
   $domain=$host;
 }
 return $domain;
}

?>