<?php
/*
 * 分城市调用
 * ----------------------------------------------------------------------------
 *
 *
 * ============================================================================
*/ 
/*function smarty_function_fz_list($paramer,&$smarty){
	global $db,$db_config,$city_ABC;
	include(PLUS_PATH."domain_cache.php");
	foreach($site_domain as $s_v){
		if($s_v['fz_type']=='1'){
			$city_id[]=$s_v['cityid'];
		} 
	} 
	$city_ids=implode(",",$city_id);
	$sitecity=$db->select_all("city_class","`id` in(".$city_ids.")","`id`,`name`,`letter`");
	foreach($city_ABC as $key=>$val){
		foreach($sitecity as $v){ 
			if($val==$v['letter']){
				$site[$val][]= $v;
			} 
		}  
	}   
	$smarty->assign("$paramer[fz_list]",$site);
}*/
function smarty_function_hy_list($paramer,&$smarty){ 
	global $db,$db_config;
	include(PLUS_PATH."domain_cache.php");
	foreach($site_domain as $h_v){
		if($h_v['fz_type']=='2'){
			$hy_site[]=$h_v;
		} 
	}  
	$smarty->assign("$paramer[hy_list]",$hy_site);
}
?>