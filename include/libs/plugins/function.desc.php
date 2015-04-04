<?php

function smarty_function_desc($paramer,&$smarty){
	global $db,$db_config,$config;
	$class=$db->select_all("desc_class");
	$desc=$db->select_all("description");
	if(is_array($class))
	{
		foreach($class as $k=>$v)
		{
			foreach($desc as $val)
			{
				if($v['id']==$val['nid'])
				{
					$class[$k]['list'][]=$val;
				}
			}
		}
	}
	$smarty->assign("$paramer[assign_name]",$class);
}