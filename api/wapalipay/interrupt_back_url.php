<?php
error_reporting(0);

require_once(dirname(dirname(dirname(__FILE__)))."/plus/config.php");
if(!($config['sy_wapdomain'])){
	$wapdomain=$config['sy_weburl'].'/'.$config['sy_wapdir'];
}else{
	$wapdomain=$config['sy_wapdomain'];
}
$Loaction=$wapdomain."/member/index.php?c=pay";
header("Location: $Loaction\n");exit;
?>