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
class sysnews_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'����ʱ��',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
		$where = "1";
		if($_GET['keyword'])
		{
			if($_GET['type']=="1")
			{
				$where.=" AND `username` like '%".$_GET['keyword']."%'";
			}else{
				$where.=" AND `content` like '%".$_GET['keyword']."%'";
			}
			$urlarr['keyword']=$_GET['keyword'];
			$urlarr['type']=$_GET['type'];
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
        $urlarr['status']=$_GET['status'];
        $urlarr['order']=$_GET['order'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("sysmsg",$where,$pageurl,$this->config['sy_listnum']);
		$this->yuntpl(array('admin/sysnews'));
	}
   function add_action()
   {
		if($_POST['submit'])
		{
			if($_POST['content']=="")
			{
				$this->obj->ACT_layer_msg("����д���ݣ�",2,"index.php?m=sysnews&c=add");
			}
			if($_POST['all']=="5")
			{
				$userarr=@explode(",",$_POST['userarr']);
				foreach($userarr as $v)
				{
					$where[]=" `username`='".$v."'";
				}
				$where=@implode(" or ",$where);
			}else{
				$where="`usertype`='".$_POST['all']."'";
			}
			$member=$this->obj->DB_select_all("member",$where,"`uid`,`username`");
			if(!empty($member))
			{
				$data['content']=$_POST['content'];
				$data['ctime']=time();
				foreach($member as $v)
				{
					$data['fa_uid']=$v['uid'];
					$data['username']=$v['username'];
					$this->obj->insert_into("sysmsg",$data);
				}
				$this->obj->ACT_layer_msg("ϵͳ��Ϣ����(ID:$nid)�ɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
			}else{
				$this->obj->ACT_layer_msg("�û������ڣ�",2,"index.php?m=sysnews&c=add");
			}
		}
		$this->yuntpl(array('admin/sysnews_add'));
	}
	function del_action()
	{
		$this->check_token();
	    if($_GET['del'])
	    {
	    	$del=$_GET['del'];
	    	if($_GET['del'])
	    	{
	    		if(is_array($_GET['del']))
	    		{
					$del=@implode(',',$_GET['del']);
					$layer_type='1';
		    	}else{
					$layer_type='0';
		    	}
		    	$this->obj->DB_delete_all("sysmsg","`id` in (".$del.")","");
	    		$this->layer_msg("ϵͳ��Ϣ(ID:".$del.")ɾ���ɹ���",9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
	    		$this->layer_msg("��ѡ����Ҫɾ������Ϣ��",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	}
}