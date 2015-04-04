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
class friend_letter_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'ʱ��',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		include(PLUS_PATH."user.cache.php");
		include(PLUS_PATH."com.cache.php");
		$this->set_search();
		$where = "1";
		if(trim($_GET['keyword'])){
            if ($_GET['type']=='3'){
				$where.=" and `content` like '%".$_GET['keyword']."%'";
			}else{
				$friendinfo=$this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%'","`uid`");
				if (is_array($friendinfo)){
					foreach ($friendinfo as $key=>$val){
						$friuids[]=$val['uid'];
					}
					$listuids=@implode(",",$friuids);
				}
				if ($_GET['type']=='2'){
					$where.=" and `fid` in (".$listuids.")";
				}else{
					$where.=" and `uid` in (".$listuids.")";
				}

			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
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
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("friend_message",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uids[]=$v['uid'];
				$uids[]=$v['fid'];
			}
			$statis =$this->obj->DB_select_all("friend_info","`uid` in (".$this->pylode(",",$uids).")","uid,nickname");
			foreach($rows as $key=>$value)
			{
				$rows[$key]['ctime'] = date("Y-m-d H:i",$value['ctime']);
				foreach($statis as $k=>$v)
				{
					if($value['uid']==$v['uid'])
					{
						  $rows[$key]['nickname'] = $v['nickname'];
					}
					if($value['fid']==$v['uid'])
					{
						  $rows[$key]['name'] = $v['nickname'];
					}
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/friend_letter'));
	}

	function del_action(){
		$this->check_token();

	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($_GET['del']){
	    		if(is_array($_GET['del'])){
					$this->obj->DB_delete_all("friend_message","`id` in(".@implode(',',$_GET['del']).")","");
					$del=@implode(',',$_GET['del']);
		    	}else{
		    		$this->obj->DB_delete_all("friend_message","`id`='$del'");
		    	}
	    		$this->layer_msg( "վ���ż�¼(ID:".$del.")ɾ���ɹ���",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "��ѡ����Ҫɾ������Ϣ��",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("friend_message","`id`='".$_GET['id']."'" );
			isset($result)?$this->layer_msg('վ���ż�¼(ID:'.$_GET['id'].')ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('�Ƿ�������',3,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>