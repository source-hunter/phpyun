<?php

function smarty_function_im($paramer,&$smarty){
		global $config;
		include CONFIG_PATH."db.data.php";
		$content=$paramer['cont'];
		foreach($arr_data['imface'] as $kf=>$vaf){
			$content=str_replace("[".$kf."]",'<img src="'.$config['sy_weburl'].$arr_data['faceurl'].$vaf.'">',$content);
		}
		return $content;
	}
?>