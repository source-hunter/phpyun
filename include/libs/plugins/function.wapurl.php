<?php

function smarty_function_wapurl($paramer,&$smarty){
		global $config,$seo;
		if($_GET['wxid'])
		{
			$paramer['wxid'] = $_GET['wxid'];
		}
		$url  = get_url($paramer,$config,$seo,'wap');
		return $url;
	}
?>