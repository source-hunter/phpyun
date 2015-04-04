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
class admin_job_controller extends common
{
	function index_action(){

		$position=$this->obj->DB_select_all("job_class","`keyid`='0' order by sort asc");
		$this->yunset("position",$position);
		$this->yuntpl(array('admin/admin_job'));
	}
	function classadd_action()
	{
		$position=$this->obj->DB_select_all("job_class","`keyid`='0' order by sort asc");
		$this->yunset("position",$position);
		if($_GET['id'])
		{
			$info=$this->obj->DB_select_once("job_class","`id`='".$_GET['id']."'");
			$this->yunset("info",$info);
			$job=$this->obj->DB_select_once("job_class","`id`='".$info['keyid']."'");
			$class2=$this->obj->DB_select_all("job_class","`keyid`='".$job['keyid']."' order by sort asc");
			$this->yunset("class2",$class2);
			$this->yunset("job",$job);
		}
		$this->yuntpl(array('admin/admin_job_classadd'));
	}

	function save_action()
	{
		if($_POST['submit'])
		{
			if($_POST['id'])
			{
				$where=" and `id`<>'".$_POST['id']."'";
			}
			$info=$this->obj->DB_select_once("job_class","`name`='".trim($_POST['position'])."'".$where);
			if(!empty($info))
			{
				$this->obj->ACT_layer_msg("������Ѵ��ڣ�",8,$_SERVER['HTTP_REFERER']);
			}else{
				if($_POST['keyid']!="")
				{
					$value.="`keyid`='".$_POST['keyid']."',";
				}elseif($_POST['nid']!=""){
					$value.="`keyid`='".$_POST['nid']."',";
				}
				$value.="`name`='".$_POST['position']."',";
				$value.="`sort`='".$_POST['sort']."',";
				$content=str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'','',''),html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
				$value.="`content`='".$content."'";
				if($_POST['id'])
				{
					$nid=$this->obj->DB_update_all("job_class",$value,"`id`='".$_POST['id']."'");
					$msg="����";
				}else{
					$nid=$this->obj->DB_insert_once("job_class",$value);
					$msg="���";
				}
				$this->cache_action();
				$nid?$this->obj->ACT_layer_msg($msg."�ɹ���",9,"index.php?m=admin_job",2,1):$this->obj->ACT_layer_msg($msg."ʧ�ܣ�",8,"index.php?m=admin_job");
			}
		}
	}
	function ajaxjob_action(){
		extract($_GET);
		if($id!=""){
		    $jobs=$this->obj->DB_select_all("job_class","`keyid`=$id");
			if(is_array($jobs)){
				$html .= "<select name='keyid'>";
				if($ajax=="1"){
					$html .= "<option value=\"\">==��ѡ��==</option>";
				}
				foreach($jobs as $key=>$v){
					$html .= "<option value='".$v['id']."'>".$v['name']."</option>";
				}
				$html .= "<select>";
				echo $html;
			 	die;
			}die;
		}
	}

	function up_action(){

		if((int)$_GET['id']){
			$id=(int)$_GET['id'];
			$onejob=$this->obj->DB_select_once("job_class","`id`='".$_GET['id']."'");
			$twojob=$this->obj->DB_select_all("job_class","`keyid`='".$_GET['id']."' order by sort asc","id,sort");
			if(is_array($twojob)){
				foreach($twojob as $key=>$v){
					$val[]=$v['id'];
					$root_arr = @implode(",",$val);
				}
			}
			$jobs=$this->obj->DB_select_all("job_class","`keyid`='".$_GET['id']."' or `keyid` in ($root_arr) order by sort asc");
			$a=0;
			if(is_array($jobs)){
				foreach($jobs as $key=>$v){
					if($v['keyid']==$id){
						$twojob[$a]['id']=$v['id'];
						$twojob[$a]['sort']=$v['sort'];
						$twojob[$a]['name']=$v['name'];
						$a++;
					}else{
						$threejob[$v['keyid']][]=$v;
					}
				}
			}
			$this->yunset("id",$id);
			$this->yunset("onejob",$onejob);
			$this->yunset("twojob",$twojob);
			$this->yunset("threejob",$threejob);
		}
		$position=$this->obj->DB_select_all("job_class","`keyid`='0'");
		$this->yunset("position",$position);
		$this->yuntpl(array('admin/admin_job'));
	}

	function upp_action(){

		if($_POST['update']){
			if(!empty($_POST['position'])){
				$value="`name`='".$_POST['position']."',`sort`='".$_POST['sort']."'";
				$where="`id`='".$_POST['id']."'";
				$up=$this->obj->DB_update_all("job_class",$value,$where);
				$this->cache_action();
 		        $up?$this->obj->ACT_layer_msg("ְλ���(ID:".$_POST['id'].")���³ɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("����ȷ��д��Ҫ���µ�ְλ��",8,$_SERVER['HTTP_REFERER']);
			}
		}
		$this->yuntpl(array('admin/admin_job'));
	}

	function del_action(){
		if((int)$_GET['delid']){
			$this->check_token();
			$ids=$this->sonclass(array(intval($_GET['delid'])));
			$layer_type='0';
		}else if($_POST['del']){
			$ids=$this->sonclass($_POST['del']);
			$layer_type='1';
		}
		$id=$this->obj->DB_delete_all("job_class","`id` in (".@implode(',',$ids).")","");
		$this->cache_action();
		isset($id)?$this->layer_msg('ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
	function sonclass($id){
		$class=$this->obj->DB_select_all("job_class","`keyid` in(".@implode(',',$id).")","id");
		if($class&&is_array($class)){
			foreach($class as $val){
				$ids[]=$val['id'];
			}
		}
		$cl=$this->obj->DB_select_all("job_class","`keyid` in(".@implode(',',$ids).")","id");
		if($cl){
			foreach($cl as $v){
				$ids[]=$v['id'];
			}
		}
		if($ids&&is_array($ids)){
			$ids=array_merge($id,$ids);
		}else{
			$ids=$id;
		}

		return $ids;
	}
	function sclass_action(){
		include(PLUS_PATH."job.cache.php");
		if(is_array($job_type[$_GET['pid']])){
			$html="<option value=\"\">--��ѡ��--</option>";
			foreach($job_type[$_GET['pid']] as $val){
				$html.="<option value=\"".$val."\">".$job_name[$val]."</option>";
			}
		}else{
			$html="<option value=\"\">--��������--</option>";
		}
		echo $html;die;
	}
	function cache_action(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->job_cache("job.cache.php");
	}
	function move_action(){
		extract($_GET);
		if($keyid==""){
			$move = $this->obj->DB_update_all("job_class","`keyid`=$nid","`id`=$pid");
		}else{
			$move = $this->obj->DB_update_all("job_class","`keyid`=$keyid","`id`=$pid");
		}
 		$move?$this->obj->ACT_layer_msg("ְλ���(ID:".$pid.")�ƶ��ɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("�ƶ�ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
	}
	function ajax_action()
	{
		if($_POST['sort'])
		{
			$this->obj->DB_update_all("job_class","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("�޸�ְλ���(ID:".$_POST['id'].")������");
		}
		if($_POST['name'])
		{
			$_POST['name']=$this->stringfilter($_POST['name']);
			$this->obj->DB_update_all("job_class","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("�޸�ְλ���(ID:".$_POST['id'].")������");
		}
		$this->cache_action();echo '1';die;
	}
}

?>