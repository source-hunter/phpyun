<?php 
function smarty_function_totime($paramer,&$smarty){
	if($paramer['type']==""){
		$paramer['type']="Y-m-d";
	}
	return date($paramer['type'],$paramer['time']);
}
?>