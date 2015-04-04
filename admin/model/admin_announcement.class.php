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
class admin_announcement_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'����ʱ��',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
	
		if($_GET['keyword']){
			$where="`title` like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}else{
			$where=1;
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `datetime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `datetime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
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
		$announcement=$this->get_page("admin_announcement",$where,$pageurl,$this->config['sy_listnum']);
		$this->yunset("announcement",$announcement);
		$this->yuntpl(array('admin/admin_announcement_list'));
	}

	function add_action()
	{
		$where=1;
		
		$shell=$this->obj->DB_select_once("admin_user","`uid`='".$_SESSION['auid']."'");
		$where="`id` in (".$shell['domain'].")";
		
		$domain = $this->obj->DB_select_all("domain",$where,"`id`,`title`");
		$this->yunset("domain",$domain);
		if($_GET['id'])
		{
			$announcement = $this->obj->DB_select_once("admin_announcement","`id`='".$_GET['id']."'");
			if($announcement['did']=="0")
			{
				$announcement['domain_name']="ȫվʹ��";
			}else{
				$domains=@explode(",",$announcement['did']);
				foreach($domains as $v)
				{
					foreach($domain as $val)
					{
						if($v==$val['id'])
						{
							$domain_name[]=$val['title'];
						}
					}
				}
				$announcement['domain_name']=@implode(",",$domain_name);
			}
			$announcement['content']=str_replace("&","&amp;",$announcement['content']);
			$this->yunset("announcement",$announcement);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
		}
        $this->yuntpl(array('admin/admin_announcement_add'));
	}

	
	function save_action(){
		
		if($_POST['update']){
			$time = time();
			if(empty($_POST['did']))
			{
				$_POST['did']=0;
			}
			$value.="`did`='".$_POST['did']."',";
			$value.="`title`='".$_POST['title']."',";
			$value.="`datetime`='$time',";
			$value.="`keyword`='".$_POST['keyword']."',";
			$value.="`description`='".$_POST['description']."',";
			$content = str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			$value.="`content`='".$content."'";
			$nbid=$this->obj->DB_update_all("admin_announcement",$value,"`id`='".$_POST['id']."'");
			$lasturl=str_replace("&amp;","&",$_POST['lasturl']);
			isset($nbid)?$this->obj->ACT_layer_msg("����(ID:".$_POST['id'].")���³ɹ���",9,$lasturl,2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$lasturl,2,1);
		}
	
	    if($_POST['add']){
			$time = time();
			if(empty($_POST['did']))
			{
				$_POST['did']=0;
			}
			$value.="`did`='".$_POST['did']."',";
			$value.="`title`='".$_POST['title']."',";
			$value.="`datetime`='$time',";;
			$value.="`keyword`='".$_POST['keyword']."',";
			$value.="`description`='".$_POST['description']."',";
			$content = str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			$value.="`content`='".$content."'";
			$nbid=$this->obj->DB_insert_once("admin_announcement",$value);
			isset($nbid)?$this->obj->ACT_layer_msg("����(ID:".$nbid.")��ӳɹ���",9,"index.php?m=admin_announcement",2,1):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,"index.php?m=admin_announcement",2,1);
		}
	}
	function del_action(){
			$this->check_token();
		
	    if($_GET['del']){

	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
					$this->obj->DB_delete_all("admin_announcement","`id` in(".@implode(',',$del).")","");
			    }else{
	    		 	$this->obj->DB_delete_all("admin_announcement","`id`='$del'");
	    		}
				$this->layer_msg('����(ID:'.@implode(',',$del).')ɾ���ɹ���',9,1,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('��ѡ����Ҫɾ���Ĺ��棡',8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
		
	    if(isset($_GET['id'])){
			$where="`id`='".$_GET['id']."'";
			$result=$this->obj->DB_delete_all("admin_announcement", $where);
			isset($result)?$this->layer_msg('����(ID:'.$_GET['id'].')ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('�Ƿ�������',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>