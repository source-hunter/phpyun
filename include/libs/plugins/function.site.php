<?php
/*
 * 分城市调用
 * ----------------------------------------------------------------------------
 *
 *
 * ============================================================================
*/
function smarty_function_site($paramer,&$smarty){
	global $db,$db_config,$city_ABC;
	$time = time();
	$sitecity = $db->select_all("city_class","`display`='1' AND `sitetype`='1'");

	if(is_array($sitecity))
	{
		foreach($city_ABC as $key=>$value)
		{
			foreach($sitecity as $k=>$v)
			{
				if($v[letter]==$value)
				{
					$site[$value][] = $v;
				}
			}
		}
	}
	$smarty->assign("$paramer[assign_name]",$site);
}
