<?php
/*
 * 关键字调用
 * ----------------------------------------------------------------------------
 * 分别对 职位，公司，一句话招聘
 *
 * ============================================================================
*/
function smarty_function_keyword($paramer,&$smarty){
	global $db,$db_config,$config;

	if($paramer['keytype']=="once")
	{
		$type = "1";
	}elseif($paramer['keytype']=="firm")
	{
		$type = "4";
	}else{
		$type = "3";$paramer['keytype']="com";
	}
	$time = time();
	$kwywords = $db->select_all("$db_config[def]hot_key","`type`='$type' AND `check`='1' ORDER BY num DESC");
	if(is_array($kwywords))
	{
		foreach($kwywords as $key=>$value)
		{
			$kwywords[$key]["url"] = $config['sy_weburl']."/index.php?m=".$paramer['keytype']."&keyword=".$value[key_name];
		}
	}

	$smarty->assign("keywords",$kwywords);

}