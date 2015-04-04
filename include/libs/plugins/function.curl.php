<?php

function smarty_function_curl($paramer,&$smarty){

		global $config,$seo;
		$url  =  get_url($paramer,$config,$seo,'company');
		return $url;
	}
?>