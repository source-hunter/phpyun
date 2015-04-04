<?php
/*
 * 分城市调用
 * ----------------------------------------------------------------------------
 *
 *
 * ============================================================================
*/
function smarty_function_fz_list($paramer,&$smarty){
	global $db,$db_config,$city_ABC;
	include(PLUS_PATH."domain_cache.php");
	foreach($site_domain as $k=>$v){
		if($v['fz_type']=='1'){
			if($v['three_cityid']>0){
				$city_id[]=$v['three_cityid'];
			}else{
				$city_id[]=$v['cityid'];
			}
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
}
?>