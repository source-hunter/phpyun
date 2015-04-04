<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class hr_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'ǰ̨��ʾ',"value"=>array("1"=>"��ʾ","0"=>"����ʾ"));
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'�ϴ�����',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$where="1";
		$this->set_search();
		$t_class=$this->obj->DB_select_all("toolbox_class");
		if($_GET["type"]!=""){
			if($_GET["type"]=="2"){
				$class=$this->obj->DB_select_all("toolbox_class","`name` like '%".trim($_GET['keyword'])."%'","`id`");
				foreach($class as $val){
					$cids[]=$val['id'];
				}
				$where.=" and `cid` in(".@implode(',',$cids).")";
			}elseif($_GET["type"]=="1"){
				$where.=" and  `name` LIKE '%".trim($_GET['keyword'])."%'";
			}
			$urlarr["keyword"]=$_GET["keyword"];
			$urlarr["type"]=$_GET["type"];
		}
		if($_GET['status']!=''){
			$where.=" and `is_show` = '".$_GET['status']."'";
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `add_time` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `add_time` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
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
		$rows=$this->get_page("toolbox_doc",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($rows)){
			foreach($rows as $key=>$val){
				foreach($t_class as $value){
					if($val['cid']==$value['id']){
						$rows[$key]['cname']=$value['name'];
					}
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_hr_toolbox'));
	}
	function add_action(){
		if($_GET['id']){
			$row=$this->obj->DB_select_once("toolbox_doc","`id`='".$_GET['id']."'");
			$this->yunset("row", $row);
		}
		$t_class=$this->obj->DB_select_all("toolbox_class");
		$this->yunset("t_class", $t_class);
		$this->yuntpl(array('admin/admin_hr_adddoc'));
	}
	function save_action()
	{
		if($_POST['submit'])
		{
			$value.="`name`='".$_POST['name']."',";
			$value.="`cid`='".$_POST['cid']."',";
			$value.="`is_show`='".$_POST['is_show']."',";
			$value.="`add_time`='".time()."',";
			$_POST['url']=str_replace($this->config['sy_weburl'],"",$_POST['url']);
			$value.="`url`='".$_POST['url']."'";
			if($_POST['id']=='')
			{
				if($_POST['yurl']!=$_POST['url'])
				{
					@unlink('..'.$_POST['yurl']);
				}
				$nbid=$this->obj->DB_insert_once("toolbox_doc",$value);
				isset($nbid)?$this->obj->ACT_layer_msg("�ĵ�(".$nbid.")��ӳɹ���",9,"index.php?m=hr",2,1):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,"index.php?m=hr");
			}else{
				$nbid=$this->obj->DB_update_all("toolbox_doc",$value,"`id`='".$_POST['id']."'");
				isset($nbid)?$this->obj->ACT_layer_msg("�ĵ�(".$_POST['id'].")���³ɹ���",9,"index.php?m=hr",2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,"index.php?m=hr");
			}
		}
	}
	function del_action()
	{
		if($_GET['del'])
		{
			$this->check_token();
			if(is_array($_GET['del']))
			{
				$del=@implode(",",$_GET['del']);
				$layer_type=1;
			}else{
				$del=$_GET['del'];
				$layer_type=0;
			}
		}
		$row=$this->obj->DB_select_all("toolbox_doc","`id` in (".$del.")");
		if(is_array($row))
		{
			foreach($row as $v)
			{
				@unlink("..".$v['url']);
			}
		}
		$delid=$this->obj->DB_delete_all("toolbox_doc","`id` in ($del)","");
		$delid?$this->layer_msg('�ĵ�(ID:'.$del.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}
}

?>