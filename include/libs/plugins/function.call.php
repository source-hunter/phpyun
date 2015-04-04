<?php
function smarty_function_call($paramer,&$smarty){
		global $config,$views;
		include LIB_PATH."datacall.class.php";
		$obj=$views->obj;
		$call= new datacall("plus/data/",$obj);
		$row=$call->get_data($paramer[id]);//Éú³É»º´æ
		$row=str_replace("\n","",$row);
		$row=str_replace("\r","",$row);
		return $row;
	}
?>