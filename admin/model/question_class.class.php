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
class question_class_controller extends common{
	function index_action(){
		if($_GET[pid]){
			$where="`pid`='".$_GET['pid']."' ";
			$urlarr['pid']=$_GET['pid'];
			$this->yunset("pid",$_GET['pid']);
		}else{
			$where="`pid`='0' ";
		}

		if($_GET[name]){
			$where.=" and `name` like '%".$_GET['name']."%' ";
			$urlarr['name']=$_GET['name'];
			$this->yunset("name",$_GET['name']);
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
		$q_class=$this->get_page("q_class",$where,$pageurl,$this->config['sy_listnum']);
		$this->yunset("q_class",$q_class);
		$this->yuntpl(array('admin/admin_q_class_list'));
	}
	function add_action(){
		if($_GET['pid']){
			$this->yunset("pid",$_GET['pid']);
		}
		if($_GET['id']){
			$q_class=$this->obj->DB_select_once("q_class","id='".$_GET['id']."'");
			$this->yunset("q_class",$q_class);
			$this->yunset("pid",$q_class['pid']);
		}
		$all_q_class = $this->obj->DB_select_all("q_class","`pid`='0'","`id`,`name`,`pid`");
		$this->yunset("class_list",$all_q_class);
		$this->yuntpl(array('admin/admin_q_class_add'));
	}

	function save_action(){
		if(is_uploaded_file($_FILES['pic']['tmp_name'])) {
			$upload=$this->upload_pic("../upload/question_class/");
			$pictures=$upload->picture($_FILES[pic]);
			$pic=str_replace("../","",$pictures);
			$value.="`pic`='".$pic."',";
		}
		$value.="`name`='".$_POST['name']."',";
		$value.="`pid`='".$_POST['pid']."',";
		$value.="`sort`='".$_POST['sort']."',";
		if($_GET['pid']){
			$url="index.php?m=question_class&pid=".$_GET['pid'];
		}else{
			$url="index.php?m=question_class";
		}
		$intro = str_replace("&amp;","&",html_entity_decode($_POST['intro'],ENT_QUOTES,"GB2312"));

		if($_POST['add']){
			$value.="`intro`='".$intro."',";
			$value.="`add_time`='".time()."'";
			$nbid=$this->obj->DB_insert_once("q_class",$value);
			isset($nbid)?$this->obj->ACT_layer_msg("�ʴ����(ID:".$nbid.")��ӳɹ���",9,$url,2,1):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,$url);
		}else{
			$value.="`intro`='".$intro."'";
			$nbid=$this->obj->DB_update_all("q_class",$value,"`id`='".$_POST['id']."'");
			isset($nbid)?$this->obj->ACT_layer_msg("�ʴ����(ID:".$_POST['id'].")���³ɹ���",9,$url,2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$url);
		}
	}

	function del_action(){
		$this->check_token();
		if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
				$this->obj->DB_delete_all("q_class","`id` in(".@implode(',',$del).") or `pid` in(".@implode(',',$del).")","");
				$this->del_question($del);
				$this->layer_msg('�ʴ����(ID:'.$del.')ɾ���ɹ���',9,1,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('��ѡ����Ҫɾ�������',8,1);
	    	}
	    }
	
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("q_class","`id`='".$_GET['id']."' or `pid`='".$_GET['id']."'","");
			$this->del_question(array($_GET['id']));
			$result?$this->layer_msg('�ʴ����(ID:'.$_GET['id'].')ɾ���ɹ���',9):$this->layer_msg('ɾ��ʧ�ܣ�',8);
		}else{
			$this->layer_msg('�Ƿ�������',3);
		}
	}
	function del_question($cid){
		$qid=$this->obj->DB_select_all("question","`cid` in(".$cid.")","`id`");
		foreach($qid as $q_v){
			$qids[]=$q_v['id'];
		}
		$qids=@implode(",",$qids);
		$this->obj->DB_delete_all("question","`id` in(".$qids.")","");
		$this->obj->DB_delete_all("answer","`qid` in(".$qids.")","");
		$this->obj->DB_delete_all("answer_review","`qid` in(".$qids.")","");
	}
}
?>