<?php

function smarty_function_aurl($paramer,&$smarty){

		global $config,$seo;
		$url  =  get_url($paramer,$config,$seo,'ask');
		return $url;
	}
?>