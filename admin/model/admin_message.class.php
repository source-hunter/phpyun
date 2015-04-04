<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class admin_message_controller extends common
{
	
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'留言状态',"value"=>array("1"=>"已回复","2"=>"未回复"));
		$ad_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"end","name"=>'留言时间',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
		$where =1;
		if($_GET['status']){
			if($_GET['status']=="1"){
				$where.=" AND `status`='".$_GET['status']."'";
			}elseif($_GET['status']=="2"){
				$where.=" AND `status`='0'";
			}
			$urlarr['status']=$_GET['status'];
		}
		if(trim($_GET['keyword'])){
			if($_GET["type"]==1){
				$where.=" and `username` like '%".trim($_GET['keyword'])."%'";
			}else{
				$where.=" and `content` like '%".trim($_GET['keyword'])."%'";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$this->get_page("message",$where,$pageurl,$this->config['sy_listnum']);
		$this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_message'));
	}
   
   function show_action()
   {
		if($_POST['submit'])
		{
			if($_POST['content']=="")
			{
				$this->obj->ACT_layer_msg("请填写内容！",2,"index.php?m=admin_message");
			}
			$value="`reply`='".$_POST['content']."',`reply_time`='".time()."',`status`='1'";
			$nid=$this->obj->DB_update_all("message",$value,"`id`='".$_POST['id']."'");
 		    $nid?$this->obj->ACT_layer_msg("留言反馈回复(ID:$nid)成功！",9,"index.php?m=admin_message",2,1):$this->obj->ACT_layer_msg("回复(ID:$nid)失败！",8,$_SERVER['HTTP_REFERER']);
		}
		$this->yuntpl(array('admin/admin_message_show'));
	}
	function del_action()
	{
	    if($_GET['del'])
	    {
			$this->check_token();
	    	$del=$_GET['del'];
	    	if($del)
	    	{
	    		if(is_array($del))
	    		{
	    			$del=@implode(',',$del);
					$layer_msg=1;
		    	}else{
					$layer_msg=0;
		    	}
		    	$this->obj->DB_delete_all("message","`id` in (".$del.")","");
				$this->layer_msg("留言反馈(ID:".$del.")删除成功！",9,$layer_msg,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('非法操作！',3);
			}
	    }
	}
}