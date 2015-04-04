<?php

function smarty_function_url($paramer,&$smarty){
		global $config,$seo;

		$url  =  get_url($paramer,$config,$seo);
		return $url;
	}
?>