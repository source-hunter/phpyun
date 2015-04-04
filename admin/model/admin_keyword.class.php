<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class admin_keyword_controller extends common
{
	
	function set_search(){
		$search_list[]=array("param"=>"rec","name"=>'�Ƽ�',"value"=>array("1"=>"���Ƽ�","2"=>"δ�Ƽ�"));
		$search_list[]=array("param"=>"bold","name"=>'�Ƿ�Ӵ�',"value"=>array("1"=>"��","2"=>"��"));
		$keywordarr=array("1"=>"΢��Ƹ","3"=>"ְλ","4"=>"��˾","5"=>"����","8"=>"΢������");
		$this->yunset("keywordarr",$keywordarr);
		$search_list[]=array("param"=>"check","name"=>'���״̬',"value"=>array("1"=>"�����","2"=>"δ���"));
		$search_list[]=array("param"=>"type","name"=>'��������',"value"=>$keywordarr);
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
			$this->obj->ACT_layer_msg("�ؼ������Ʋ���Ϊ�գ�",8,$_SERVER['HTTP_REFERER']);
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
			$oid?$this->obj->ACT_layer_msg("�ؼ���(ID:".$id.")����ɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ����������ԣ�",8,$_SERVER['HTTP_REFERER']);
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
			$this->layer_msg('��ѡ��Ҫɾ�������ݣ�',8);
		}
		$del=$this->obj->DB_delete_all("hot_key","`id` in (".$delid.")"," ");
		$this->get_cache();
		$del?$this->layer_msg('�ؼ���(ID:'.$delid.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
	
	function ajax_action(){
		$keywordarr=array("1"=>"΢��Ƹ","3"=>"ְλ","4"=>"��˾","5"=>"����","8"=>"΢������");
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
			$this->obj->admin_log("�Թؼ��� ".$row['name']." �Ƿ�Ӵֽ�������");
		}elseif($type=="tuijian"){
			$this->obj->admin_log("�Թؼ��� ".$row['name']." �Ƿ��Ƽ���������");
		}elseif($type=="check"){
			$this->obj->admin_log("�Թؼ��� ".$row['name']." �Ƿ���˽�������");
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