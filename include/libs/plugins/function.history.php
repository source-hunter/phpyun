<?php
/*
 * 单个人才调用
 * ----------------------------------------------------------------------------
 * 分别对 职位，对应招聘公司，公司所属企业级别，认证等级等作操作
 *
 * ============================================================================
*/
function smarty_function_history($paramer,&$smarty){
	global $db,$db_config,$config;
	if($paramer[type]==1){
		$_SESSION[history][$paramer[jobid]] =$paramer;
    }
}