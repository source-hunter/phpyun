<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class my_question_controller extends user{
	function index_action()
	{
		$this->public_action();
		if($_GET['type']==0)
		{
			$table="question";
		}elseif($_GET['type']==1){
			$table="answer";
		}elseif($_GET['type']==2){
			$table="answer_review";
		}
		include(LIB_PATH."page3.class.php");
		$limit=10;
		$page=$_GET["page"]<1?1:$_GET["page"];
		$ststrsql=($page-1)*$limit;
		$page_url = "index.php?c=".$_GET['c']."&type=".intval($_GET['type'])."&page={{page}}";
		$num = $this->obj->DB_select_num($table,"`uid`='".$this->uid."'");

		if($num>$limit){
			$pages=ceil($num/$limit);
			$page = new page($page,$limit,$num,$page_url);
			$pagenav=$page->numPage();
		}
		if($_GET["type"]==0)
		{
			$list = $this->obj->DB_select_all($table,"`uid`='".$this->uid."'  ORDER BY `add_time` DESC LIMIT $ststrsql,$limit");
		}else{
			$list = $this->obj->DB_select_alls($table,"question","a.`uid`='".$this->uid."' and a.`qid`=b.`id`  ORDER BY a.`add_time` DESC LIMIT $ststrsql,$limit","a.`content`,a.`add_time`,b.`id`,b.`title`,a.`id` as `aid`");
		}
		if($list[0]!='')
		{
			$this->yunset("q_list",$list);
		}
		$this->yunset("gettype",$_GET["type"]);
		$this->yunset("pagenav",$pagenav);
		$this->user_tpl('my_question');
	}
	function del_action(){
		$del=(int)$_GET['id'];
		$is_del=$this->obj->DB_delete_all("question","`id`='".$del."' and uid='".$this->uid."'");
		if(!empty($is_del)){
			$this->obj->DB_delete_all("answer","`qid`='".$del."'","");
			$this->obj->DB_delete_all("answer_review","`qid`='".$del."'","");
			$this->obj->member_log("删除问答");
			$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>