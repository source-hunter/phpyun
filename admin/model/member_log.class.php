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
class member_log_controller extends common
{
	function index_action(){
		$where=1;
		if($_GET['type']){
			$where.=" and `usertype`='".$_GET['type']."'";
			$urlarr['type']=$_GET['type'];
		}else{
			$where.=" and `usertype`='1'";
		}
		if($_GET['operas']){
			$where.=" and `opera`='".$_GET['operas']."'";
			$urlarr['operas']=$_GET['operas'];
		}
		if($_GET['parr']){
			$where.=" and `type`='".$_GET['parrs']."'";
			$urlarr['parrs']=$_GET['parrs'];
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
		if($_GET['type']==''||$_GET['type']=='1'){
			$opera=array('2'=>'��������','5'=>"�ղ�ְλ",'6'=>'����ְλ','7'=>'�޸Ļ�����Ϣ','8'=>'�޸�����');
		}else if($_GET['type']=='2'||$_GET['type']=='3'){
			$opera=array('1'=>'ְλ����','3'=>'���ؼ���','4'=>'��������','7'=>'�޸Ļ�����Ϣ','8'=>'�޸�����');
		}

		$search_list[]=array("param"=>"operas","name"=>'��������',"value"=>$opera);
		if($_GET['operas']=='1'||$_GET['operas']=='2'){
			$parr=array('1'=>'����','2'=>'�޸�','3'=>'ɾ��','4'=>'ˢ��');
			$search_list[]=array("param"=>"parrs","name"=>'��������',"value"=>$parr);
		}
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'����ʱ��',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
		if($_GET['keyword']){
			$member=$this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%'","`uid`,`username`");
			foreach($member as $v)
			{
				$uid[]=$v['uid'];
			}
			$where.=" and `uid` in (".@implode(",",$uid).")";
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['order']){
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.="order by `id` desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("member_log",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($rows))
		{
			if($_GET['keyword']=="")
			{
				foreach($rows as $v)
				{
					$uid[]=$v['uid'];
				}
				$member=$this->obj->DB_select_all("member","`uid` in (".@implode(",",$uid).")","`uid`,`username`");
			}
			foreach($rows as $k=>$v)
			{
				foreach($member as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$rows[$k]['username']=$val['username'];
					}
				}
			}
		}
		$this->yunset("type",$_GET['type']);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/member_log'));
	}

	function del_action()
	{
		$this->check_token();
		
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
					$layer_type=1;
					$this->obj->DB_delete_all("member_log","`id` in (".@implode(',',$del).")","");
					$del=@implode(',',$del);
		    	}else{
					$this->obj->DB_delete_all("member_log","`id`='".$del."'");
					$layer_type=0;
		    	}
				$this->layer_msg('��Ա��־(ID:'.$del.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg('��ѡ����Ҫɾ������Ϣ��',8,0,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	
		if($_GET['time']){
			$time=strtotime($_GET['time']." 23:59:59");
			$this->obj->DB_delete_all("member_log","`ctime`<'".$time."' and `usertype`='".(int)$_GET['type']."'","");
			$this->layer_msg('��Ա��־ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']);
		}
	}
}

?>