<?php
/*
 * �����˲ŵ���
 * ----------------------------------------------------------------------------
 * �ֱ�� ְλ����Ӧ��Ƹ��˾����˾������ҵ������֤�ȼ���������
 *
 * ============================================================================
*/
function smarty_function_history($paramer,&$smarty){
	global $db,$db_config,$config;
	if($paramer[type]==1){
		$_SESSION[history][$paramer[jobid]] =$paramer;
    }
}