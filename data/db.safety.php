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

function quotesGPC() {

	if(!get_magic_quotes_gpc()){
	 	$_POST = array_map("addSlash", $_POST);
		$_GET = array_map("addSlash", $_GET);
		$_COOKIE = array_map("addSlash", $_COOKIE);
	}

}
function addSlash($el) {
	if (is_array($el))
		return array_map("addSlash", $el);
	else
		return addslashes($el);
}
function gpc2sql($str,$str2) {

	if(preg_match("/select|insert|update|delete|union|load_file|outfile/is", $str))
	{
		exit(safe_pape());
	}

	if(preg_match("/select|insert|update|delete|union|load_file|outfile/is", $str2))
	{
		exit(safe_pape());
	}


	$arr=array("sleep"=>"Ｓleep"," and "=>" an d "," or "=>" Ｏr ","%20"=>" ","select"=>"Ｓelect","update"=>"Ｕpdate","count"=>"Ｃount","chr"=>"Ｃhr","truncate"=>"Ｔruncate","union"=>"Ｕnion","delete"=>"Ｄelete","insert"=>"Ｉnsert","<"=>"&lt;",">"=>"&gt;","\""=>"&quot;","'"=>"&acute;","--"=>"- -","\("=>"（","\)"=>"）");
	
	foreach($arr as $key=>$v){
    	$str = preg_replace('/'.$key.'/isU',$v,$str);
	}
	return $str;
}
function safeid($v){
	if(strstr($v,",")){
		$arr=explode(',',$v);
		foreach($arr as $val){
			$value[]=(int)$val;
		}
		$v=implode(',',$value);
	}elseif(is_array($v)){
		foreach($v as $val){
			$value[]=(int)$val;
		}
		$v=$value;
	}else{
		$v=int($v);	
	}
	return $v;
}
function safesql($StrFiltKey,$StrFiltValue,$type){
	
	 $getfilter = "\\<.+javascript:window\\[.{1}\\\\x|<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\\(\d+?|sleep\s*?\(.*\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|\\/\\*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT(\\(.+\\)|\\s+?.+?)|UPDATE(\\(.+\\)|\\s+?.+?)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?)FROM(\\(.+\\)|\\s+?.+?)|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

	$postfilter = "<.*=(&#\\d+?;?)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\\(\d+?|sleep\s*?\(.*\)|load_file\s*?\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|\\/\\*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT(\\(.+\\)|\\s+?.+?)|UPDATE(\\(.+\\)|\\s+?.+?)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?)FROM(\\(.+\\)|\\s+?.+?)|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

	$cookiefilter = "benchmark\s*?\\(\d+?|sleep\s*?\(.*\)|load_file\s*?\\(|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|\\/\\*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT(\\(.+\\)|\\s+?.+?)|UPDATE(\\(.+\\)|\\s+?.+?)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?)FROM(\\(.+\\)|\\s+?.+?)|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
	
	if($type=="GET")
	{
		$ArrFiltReq = $getfilter;

	}elseif($type=="POST"){
		
		$ArrFiltReq = $postfilter;
	
	}elseif($type=="COOKIE"){

		$ArrFiltReq = $cookiefilter;
	}
	if(is_array($StrFiltValue))
	{
		foreach($StrFiltValue as $key=>$value)
		{
			safesql($key,$value,$type);
		}
	}else{

		if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue)==1)
		{
			 exit(safe_pape());
		}
	}
	if (preg_match("/".$ArrFiltReq."/is",$StrFiltKey)==1)
	{
		 exit(safe_pape());
	}
}
function common_htmlspecialchars($key,$str,$str2,$config){
	if(is_array($str))
	{
		foreach($str as $str_k=>$str_v)
		{
			$str[$str_k] = common_htmlspecialchars($str_k,$str_v);
		}
	}else{
		if(!in_array($key,array('content','config','group_power','description','body','job_desc','eligible','other','code','intro','doc','traffic','media','packages','booth','participate')))
		{
			$str = strip_tags($str);			
		}else{
			
			if($_SESSION['xsstooken'] != sha1($config['sy_safekey']))
			{
				$str = RemoveXSS($str);
			}
		}
		$str = gpc2sql($str,$str2);
	}
	return $str;
}
function RemoveXSS($val) {
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);    
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';   
   $search .= '~`";:?+/={}[]-_|\'\\';   
   for ($i = 0; $i < strlen($search); $i++) {   
	  $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;   
	  $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;   
   }   
   $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');   
   $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);   
   
   $found = true; 
   while ($found == true) {   
	  $val_before = $val;   
	  for ($i = 0; $i < sizeof($ra); $i++) {   
		 $pattern = '/';   
		 for ($j = 0; $j < strlen($ra[$i]); $j++) {
			if ($j > 0) {   
			   $pattern .= '(';
			   $pattern .= '(&#[xX]0{0,8}([9ab]);)';   
			   $pattern .= '|';
			   $pattern .= '|(&#0{0,8}([9|10|13]);)';   
			   $pattern .= ')*';
			}   
			$pattern .= $ra[$i][$j];   
		 }   
		 $pattern .= '/i';    
		 $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
		 $val = preg_replace($pattern, $replacement, $val);
		 if ($val_before == $val) {    
			$found = false;    
		 }
	  }    
   }    
   return $val;    
}  
function sfkeyword($v,$config){
	
	 if($config['sy_fkeyword'])
	 {

		$fkey = @explode(",",$config['sy_fkeyword']);
		
		$safe_keyword = $config['sy_fkeyword_all'];
		
		return str_replace($fkey, $safe_keyword, $v);
	 }
}
quotesGPC();

if($config['sy_istemplate']!='1' || md5(md5($config['sy_safekey']).$_GET['m'])!=$_POST['safekey'])
{
	foreach($_POST  as $id=>$v){
		
		$str = html_entity_decode($v,ENT_QUOTES,"GB2312");
		
		$v = common_htmlspecialchars($id,$v,$str,$config);
		
		safesql($id,$v,"POST",$config);
		
		$id = sfkeyword($id,$config);
		$v = sfkeyword($v,$config);
		$_POST[$id] = $v;
	}
}

foreach($_GET  as $id=>$v){
	
	$str = html_entity_decode($v,ENT_QUOTES,"GB2312");
	
	$v = common_htmlspecialchars($id,$v,$str,$config);
	safesql($id,$v,"GET",$config);
	$id = sfkeyword($id,$config);
	$v = sfkeyword($v,$config);
	if(!is_array($v))
	$v=substr(strip_tags($v),0,80);
	$_GET[$id]=$v;
}

foreach($_COOKIE  as $id=>$v){
	
	$str = html_entity_decode($v,ENT_QUOTES,"GB2312");
	
	$v = common_htmlspecialchars($id,$v,$str,$config);
	safesql($id,$v,"COOKIE",$config);
	$id = sfkeyword($id,$config);
	$v = sfkeyword($v,$config);
	$v=substr(strip_tags($v),0,52);
	$_COOKIE[$id]=$v;
}

function safe_pape(){
  $pape=<<<HTML
  <html>
  <body style="margin:0; padding:0">
  <center><iframe width="100%" align="center" height="870" frameborder="0" scrolling="no" src="http://safe.webscan.360.cn/stopattack.html"></iframe></center>
  </body>
  </html>
HTML;
  echo $pape;
}
?>