<?php

function smarty_function_furl($paramer,&$smarty){
		global $config,$seo;
		$url  =  get_url($paramer,$config,$seo,'friend');
		return $url;
	}
?>