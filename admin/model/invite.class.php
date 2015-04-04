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
class invite_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"browse","name"=>'�Ƿ�鿴',"value"=>array("1"=>"δ�鿴","2"=>"�Ѳ鿴"));
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'����ʱ��',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$where = "1";
		$this->set_search();
		if($_GET['keyword'])
		{
			if($_GET['type']=="1")
			{
				$info=$this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%'","`uid`");
				if(is_array($info)){
					foreach ($info as $v){
						$comid[]=$v['uid'];
					}
				}
				$where.=" and `uid` in (".@implode(",",$comid).")";
			}elseif ($_GET['type']=="2"){
				$where.=" and `fname` like '%".$_GET['keyword']."%'";
			}elseif ($_GET['type']=="3"){
				$where.=" and `title` like '%".$_GET['keyword']."%'";
			}elseif ($_GET['type']=="4"){
				$where.=" and `content` like '%".$_GET['keyword']."%'";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `datetime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `datetime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
		if($_GET['browse']){
			$where.=" and `is_browse`= '".$_GET['browse']."'";
			$urlarr['browse']=$_GET['browse'];
		}
		if($_GET['sdate'])
		{
			$sdate=strtotime($_GET['sdate']);
			$where.=" and `datetime`>'$sdate'";
		}
		if($_GET['edate'])
		{
			$edate=strtotime($_GET['edate']);
			$where.=" and `datetime`<'$edate'";
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$list=$this->get_page("userid_msg",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($list))
		{
			foreach($list as $v)
			{
				$uid[]=$v['uid'];
				$fid[]=$v['fid'];
			}
			$member=$this->obj->DB_select_all("member","`uid` in (".@implode(",",$uid).")","username,uid");
			$statis=$this->obj->DB_select_all("company_statis","`uid` in (".@implode(",",$fid).")","rating,uid");
			foreach($list as $k=>$v)
			{
				foreach($member as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$list[$k]['username']=$val['username'];
					}
				}
				foreach($statis as $val)
				{
					if($v['fid']==$val['uid'])
					{
						$list[$k]['rating']=$val['rating'];
					}
				}
			}
		}
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/invite'));
	}
	function del_action()
	{
		$this->check_token();
	    if($_GET['del']){
	    	if(is_array($_GET['del']))
	    	{
	    		$del=@implode(",",$_GET['del']);
	    		$layer_status=1;
	    	}else{
	    		$del=$_GET['del'];
	    		$layer_status=0;
	    	}
			$this->obj->DB_delete_all("userid_msg","`id` in (".$del.")","");
			$this->layer_msg( "�������Լ�¼(ID:".$del.")ɾ���ɹ���",9,$layer_status,$_SERVER['HTTP_REFERER']);
	   }else{
			$this->layer_msg( "��ѡ����Ҫɾ������Ϣ��",8,1,$_SERVER['HTTP_REFERER']);
    	}
	}
}
?>