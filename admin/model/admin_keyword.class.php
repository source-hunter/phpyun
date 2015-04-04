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
class admin_keyword_controller extends common
{
	
	function set_search(){
		$search_list[]=array("param"=>"rec","name"=>'推荐',"value"=>array("1"=>"已推荐","2"=>"未推荐"));
		$search_list[]=array("param"=>"bold","name"=>'是否加粗',"value"=>array("1"=>"是","2"=>"否"));
		$keywordarr=array("1"=>"微招聘","3"=>"职位","4"=>"公司","5"=>"简历","8"=>"微信搜索");
		$this->yunset("keywordarr",$keywordarr);
		$search_list[]=array("param"=>"check","name"=>'审核状态',"value"=>array("1"=>"已审核","2"=>"未审核"));
		$search_list[]=array("param"=>"type","name"=>'数据类型',"value"=>$keywordarr);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
		$type=(int)$_GET['type'];
		$where="1";
		if($type)
		{
			$where.=" and `type`='$type'";
			$urlarr['type']=$_GET['type'];
		}
		if ($_GET['rec']=='1'){
			$where.=" and `tuijian`='1'";
			$urlarr['rec']=$_GET['rec'];
		}elseif ($_GET['rec']=='2'){
			$where.=" and `tuijian`='0'";
			$urlarr['rec']='2';
		}
		if ($_GET['bold']=='1'){
			$where.=" and `bold`='1'";
			$urlarr['bold']=$_GET['bold'];
		}elseif ($_GET['bold']=='2'){
			$where.=" and `bold`='0'";
			$urlarr['bold']='2';
		}
		if($_GET['keyword'])
		{
			$where.=" and `key_name` like '%".$_GET['keyword']."%'";
			if ($_GET['cate']!=''){
				$where.=" and `type`='". $_GET['cate'] ."'";
			}
			$urlarr['cate']=$_GET['cate'];
			$urlarr['keyword']=$_GET['keyword'];
			$urlarr['news_search']=$_GET['news_search'];
		}
		if($_GET["check"]==1)
		{
			$where.=" and `check`='".$_GET['check']."'";
			$urlarr['check']=$_GET['check'];
		}elseif($_GET['check']=="2"){
			$where.=" and `check`!='1'";
			$urlarr['check']=$_GET['check'];
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by `id` desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("hot_key",$where,$pageurl,$this->config['sy_listnum']);
        $this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_keyword'));
	}
	function info_action(){
		$hot_key=$this->obj->DB_select_once("hot_key","`id`='".$_POST['id']."'");
		echo  json_encode($hot_key);die;
	}
	function save_action(){
		if(trim($_POST['key_name'])==''){
			$this->obj->ACT_layer_msg("关键字名称不能为空！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$value="`key_name`='".trim($_POST['key_name'])."',";
			$value.="`num`='".trim($_POST['num'])."',";
			$value.="`type`='".trim($_POST['type'])."',";
			$value.="`size`='".trim($_POST['size'])."',";
			$value.="`bold`='".trim($_POST['bold'])."',";
			$value.="`color`='".trim($_POST['color'])."',";
			$value.="`tuijian`='".trim($_POST['tuijian'])."',";
			$value.="`check`='1'";
			if($_POST['id']){
				$id=$_POST['id'];
				$oid=$this->obj->DB_update_all("hot_key",$value,"`id`='".$_POST['id']."'");
			}else{
				$oid=$this->obj->DB_insert_once("hot_key",$value);
				$id=$oid;
			}
			$this->get_cache();
			$oid?$this->obj->ACT_layer_msg("关键字(ID:".$id.")保存成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("保存失败，请销后再试！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function del_action(){
		extract($_GET);
		extract($_POST);
		if(is_array($del)){
			$delid=@implode(',',$del);
			$layer_type=1;
		}else{
			$this->check_token();
			$delid=$id;
			$layer_type=0;
		}
		if(!$delid){
			$this->layer_msg('请选择要删除的内容！',8);
		}
		$del=$this->obj->DB_delete_all("hot_key","`id` in (".$delid.")"," ");
		$this->get_cache();
		$del?$this->layer_msg('关键字(ID:'.$delid.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
	
	function ajax_action(){
		$keywordarr=array("1"=>"微招聘","3"=>"职位","4"=>"公司","5"=>"简历","8"=>"微信搜索");
		foreach($keywordarr as $k=>$v){
			if($_POST['type']==$k){
				$html.="<option value=".$k." selected >".$v."</option>";
			}else{
				$html.="<option value=".$k." >".$v."</option>";
			}
		}
		echo $html;die;
	}
	function recup_action(){
		extract($_GET);
		$this->check_token();
		if($id){
			$nid=$this->obj->DB_update_all("hot_key","`$type`='".$rec."'","`id`='$id'");
			$this->get_cache();
		}
		$row=$this->obj->DB_select_once("hot_key","`id`='$id'");
		if($type=="bold")
		{
			$this->obj->admin_log("对关键字 ".$row['name']." 是否加粗进行设置");
		}elseif($type=="tuijian"){
			$this->obj->admin_log("对关键字 ".$row['name']." 是否推荐进行设置");
		}elseif($type=="check"){
			$this->obj->admin_log("对关键字 ".$row['name']." 是否审核进行设置");
		}
		echo $nid?1:0;die;
	}
	function get_cache(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->keyword_cache("keyword.cache.php");
	}
}
?>