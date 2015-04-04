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
class friend_message_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'����ʱ��',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		include(PLUS_PATH."user.cache.php");
		include(PLUS_PATH."com.cache.php");
		$this->set_search();
		$where = "1";

		if($_GET['usertype']){
			$where.=" AND `usertype`='".$_GET['usertype']."'";
			$urlarr['usertype']=$_GET['usertype'];
		}
		if($_GET['status']=="1"){
			$where.=" AND `status`='".$_GET['status']."'";
			$urlarr['status']=$_GET['status'];
		}elseif($_GET['status']=="2"){
			$where.=" AND `status`='0'";
			$urlarr['status']=$_GET['status'];
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
 		
 		if($this->config['sy_web_site']=='1'){
			$wheres=1;
			$com=$this->obj->DB_select_all("company",$wheres,"`uid`");
			
			$uid=array();
			if(is_array($com))
			{
				foreach($com as $v)
				{
					$uid[]=$v['uid'];
				}
			}
			
			$where.=" AND (`uid` in (".@implode(",",$uid).") and `fid` in (".@implode(",",$uid)."))";
 		}
		
		if(trim($_GET['keyword'])){
			if($_GET['type']==1){
				if($uid&&is_array($uid)){
					$infouid = $this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%' and `uid` in(".@implode(',',$uid).")","`uid`,`username`");
				}else{
					$infouid = $this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%'","`uid`,`username`");
				}
				if(is_array($infouid)){
					foreach($infouid as $k=>$v){
						$info_uids[] = $v['uid'];
					}
					$uids = @implode(",",$info_uids);
				}
				$where.=" and `uid` in ($uids)";
			}else{
				$where.=" and `content` like '%".$_GET['keyword']."%'";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
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
		$mes_list=$this->get_page("friend_message",$where,$pageurl,$this->config['sy_listnum']);

		if(is_array($mes_list)){
			if($infouid==''){
				$uids=array();
				foreach($mes_list as $value){
					$uids[]=$value['uid'];
				}
				$infouid = $this->obj->DB_select_all("member","`uid` in (".@implode(',',$uids).")","`uid`,`username`");
			}
			foreach($mes_list as $key=>$value){
				foreach($infouid as $k=>$v)
				{
					if($value['uid']==$v['uid'])
					{
						 $mes_list[$key]['username'] = $v['username'];
					}

				}
			}
		}
        $this->yunset("get_type", $_GET);
		$this->yunset("mes_list",$mes_list);
		$this->yuntpl(array('admin/friend_message'));
	}

	function del_action(){
		$this->check_token();
		
	    if($_GET['del']){
	    	if($_GET['del']){
	    		if(is_array($_GET['del'])){
					$this->obj->DB_delete_all("friend_message","`id` in(".@implode(',',$_GET['del']).")","");
					$del=@implode(',',$_GET['del']);
					$layer_type='1';
		    	}else{
		    		$this->obj->DB_delete_all("friend_message","`id`='$del'");
					$layer_type='0';
		    	}
	    		$this->layer_msg( "����(ID:".$del.")ɾ���ɹ���",9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "��ѡ����Ҫɾ������Ϣ��",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
		
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("friend_message","`id`='".$_GET['id']."'" );
			isset($result)?$this->layer_msg('����(ID:'.$_GET['id'].')ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('�Ƿ�������',3,0,$_SERVER['HTTP_REFERER']);
		}
	}
}