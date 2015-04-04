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
class reward_controller extends common
{
			//���ø߼���������
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'���״̬',"value"=>array("1"=>"�ϼ�","2"=>"�¼�"));
		$search_list[]=array("param"=>"rec","name"=>'�����Ƽ�',"value"=>array("1"=>"��","2"=>"��"));
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
		$where="1";
		if(trim($_GET['keyword']))
		{
			$where.=" and `name` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=trim($_GET['keyword']);
		}
		if($_GET['status'])
		{
			if($_GET['status']=='2'){
				$where.=" and `status`='0'";
			}else{
				$where.=" and `status`='".$_GET['status']."'";
			}
			$urlarr['status']=$_GET['status'];
		}
		if($_GET['rec'])
		{
			if($_GET['rec']=='2'){
				$where.=" and `rec`='0'";
			}else{
				$where.=" and `rec`='".$_GET['rec']."'";
			}
			$urlarr['rec']=$_GET['rec'];
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
		$rows=$this->get_page("reward",$where,$pageurl,$this->config['sy_listnum']);
        if(is_array($rows))
        {
        	$class=$this->obj->DB_select_all("reward_class");
        	foreach($rows as $k=>$v)
        	{
        		foreach($class as $val)
        		{
        			if($v['nid']==$val['id'])
        			{
        				$rows[$k]['classname']=$val['name'];
        			}
        		}
        	}
        	$this->yunset("rows",$rows);
        }
        $this->yunset("get_type",$_GET);
		$this->yuntpl(array('admin/admin_reward'));
	}
	function add_action()
	{
		if($_POST['submit'])
		{
			if(is_uploaded_file($_FILES['pic']['tmp_name']))
			{
				$upload=$this->upload_pic("../upload/reward/");
				$pictures=$upload->picture($_FILES['pic']);
				$pic=str_replace("../","",$pictures);
				$value.="`pic`='".$pic."',";
				if($_POST['id']){
					$row=$this->obj->DB_select_once("reward","`id`='".$_POST['id']."'");
					@unlink("../".$row['pic']);
				}
			}
			$value.="`name`='".$_POST['name']."',";
			$value.="`nid`='".$_POST['nid']."',";
			$value.="`integral`='".$_POST['integral']."',";
			$value.="`restriction`='".$_POST['restriction']."',";
			$value.="`stock`='".$_POST['stock']."',";
			$value.="`sort`='".$_POST['sort']."',";
			$content= str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			$value.="`content`='".$content."',";
			$value.="`status`='".$_POST['status']."',";
			$value.="`sdate`='".mktime()."'";
			if($_POST['id']){
				$nbid=$this->obj->DB_update_all("reward",$value,"`id`='".$_POST['id']."'");
				isset($nbid)?$this->obj->ACT_layer_msg("��Ʒ(ID:".$_POST['id'].")���³ɹ���",9,"index.php?m=reward",2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,"index.php?m=reward");
			}else{
				$nbid=$this->obj->DB_insert_once("reward",$value);
				isset($nbid)?$this->obj->ACT_layer_msg("��Ʒ(ID:".$nbid.")��ӳɹ���",9,"index.php?m=reward",2,1):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,"index.php?m=reward");
			}
		}
		if($_GET['id'])
		{
			$info=$this->obj->DB_select_once("reward","`id`='".$_GET['id']."'");
			$this->yunset("info",$info);
		}
		$class=$this->obj->DB_select_all("reward_class");
		$this->yunset("class",$class);
		$this->yuntpl(array('admin/admin_reward_add'));
	}
	function status_action()
	{
		$id=$this->obj->DB_update_all("reward","`status`='".$_GET['rec']."'","`id`='".$_GET['id']."'");
		$this->obj->admin_log("��Ʒ(ID:".$_GET['id'].")״̬���óɹ���");
		echo $id?1:0;die;
	}

	function rec_action()
	{
		$id=$this->obj->DB_update_all("reward","`rec`='".$_GET['rec']."'","`id`='".$_GET['id']."'");
		$this->obj->admin_log("��Ʒ(ID:".$_GET['id'].")״̬���óɹ���");
		echo $id?1:0;die;
	}

	function del_action()
	{
		if($_GET['del']){
			$this->check_token();
			$del=$_GET['del'];
			if(is_array($del)){
				$del=@implode(',',$del);
				$layer_type=1;
			}else{
				$layer_type=0;
			}
			$id=$this->obj->DB_delete_all("reward","`id` in (".$del.")"," ");
			$del?$this->layer_msg('��Ʒ(ID:'.$del.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('��ѡ��Ҫɾ�������ݣ�',8);
		}
	}
	function group_action()
	{
		if($_POST['submit'])
		{
			if($_POST['id'])
			{
				$id=$this->obj->DB_update_all("reward_class","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
				isset($id)?$this->obj->ACT_layer_msg("��Ʒ���(ID:".$_POST['id'].")���³ɹ���",9,"index.php?m=reward&c=group",2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,"index.php?m=reward&c=group");
			}else{
				$id=$this->obj->DB_insert_once("reward_class","`name`='".$_POST['name']."'");
				isset($id)?$this->obj->ACT_layer_msg("��Ʒ���(ID:".$id.")��ӳɹ���",9,"index.php?m=reward&c=group",2,1):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,"index.php?m=reward&c=group");
			}
		}
		if($_GET['id'])
		{
			$info=$this->obj->DB_select_once("reward_class","`id`='".$_GET['id']."'");
			$this->yunset("info",$info);
		}
		$list=$this->obj->DB_select_all("reward_class");
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_reward_group'));
	}

	function delgroup_action()
	{
		if($_GET['del']){
			$this->check_token();
			$del=$_GET['del'];
			if(is_array($del)){
				$del=@implode(',',$del);
				$layer_type=1;
			}else{
				$layer_type=0;
			}
			$id=$this->obj->DB_delete_all("reward_class","`id` in (".$del.")"," ");
			$del?$this->layer_msg('��Ʒ���(ID:'.$del.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('��ѡ��Ҫɾ�������ݣ�',8);
		}
	}
}
?>