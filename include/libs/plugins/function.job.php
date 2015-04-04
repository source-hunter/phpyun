<?php

function smarty_function_job($paramer,&$smarty){
	global $db,$db_config,$config;
	if($paramer[id])
	{
		$id = $paramer[id];
		$Info = $db->select_alls("company_job","company","a.`state`='1' and a.`r_status`!='2' and a.`id`='$id' and b.`uid`=a.`uid`","a.*,b.*,a.name as comname,a.provinceid as prov,a.cityid as city,a.three_cityid as three_city");
		if(is_array($Info))
		{
			//处理类别字段
			$cache_array = $db->cacheget();
			$Job = $db->array_action($Info[0],$cache_array);
		}
		$Job[cert_n] = @explode(",",$Job[cert]);
		$uid=$Info[0][uid];
		$joblist=$db->select_all("company_job","`uid`='$uid' and `status`!='1'");
	}
	$smarty->assign("$paramer[assign_name]",array("job_info"=>$Job,"joblist"=>$joblist));
}