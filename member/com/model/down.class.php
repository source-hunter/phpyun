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
class down_controller extends company
{
	function index_action()
	{
		$where="`comid`='".$this->uid."'";
		if($_GET['keyword'])
		{
			$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and a.`r_status`<>'2' and a.`name` like '%".$_GET['keyword']."%'","a.`name`,a.`uid`,a.`sex`,a.`edu`,b.`job_classid`");
			if(is_array($resume))
			{
				foreach($resume as $v)
				{
					$uid[]=$v['uid'];
				}
			}
			$where.=" and uid in (".@implode(',',$uid).")";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$this->public_action();
		$urlarr['c']='down';
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("down_resume","$where order by id desc",$pageurl,"10");
		if(is_array($rows)&&$rows)
		{
			if(!$_GET['keyword'])
			{
				if(empty($resume))
				{
					foreach($rows as $v)
					{
						$uid[]=$v['uid'];
					}
					$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and a.`r_status`<>'2' and a.uid in (".@implode(",",$uid).")","a.`name`,a.`uid`,a.`sex`,a.`edu`,b.`job_classid`");
				}
			}
			$userid_msg=$this->obj->DB_select_all("userid_msg","`fid`='".$this->uid."' and `uid` in (".@implode(",",$uid).")","uid");
			if(is_array($resume))
			{
				include(PLUS_PATH."user.cache.php");
				include(PLUS_PATH."job.cache.php");
				foreach($rows as $key=>$val)
				{
					foreach($resume as $va)
					{
						if($val['uid']==$va['uid'])
						{
							$rows[$key]['name']=$va['name'];
							$rows[$key]['sex']=$userclass_name[$va['sex']];
							$rows[$key]['edu']=$userclass_name[$va['edu']];
							if($va['job_classid']!="")
							{
								$job_classid=@explode(",",$va['job_classid']);
								$rows[$key]['jobname']=$job_name[$job_classid[0]];
							}
						}
					}
					foreach($userid_msg as $va)
					{
						if($val['uid']==$va['uid'])
						{
							$rows[$key]['userid_msg']=1;
						}
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$report=$this->config['com_report'];
		$this->yunset("report",$report);
		$this->company_satic();
		$this->yunset("js_def",5);
		$this->com_tpl('down');
	}
	function del_action()
	{
		if($_POST['delid'] || $_GET['id'])
		{
			if($_GET['id']){
				$id=(int)$_GET['id'];
				$layer_type='0';
			}else{
				$id=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}
			$nid=$this->obj->DB_delete_all("down_resume","`id` in (".$id.") and `comid`='".$this->uid."'"," ");
			if($nid){
				$this->obj->member_log("ɾ�������ؼ����˲�",3,3);
				$this->layer_msg('ɾ���ɹ���',9,$layer_type,"index.php?c=down");
			}else{
				$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,"index.php?c=down");
			}
		}
	}
	function report_action()
	{
		if($_POST['submit'])
		{
			if($_POST['r_reason']=="")
			{
				$this->obj->ACT_layer_msg("�ٱ����ݲ���Ϊ�գ�",8,$_SERVER['HTTP_REFERER']);
			}
			$data['c_uid']=(int)$_POST['r_uid'];
			$data['inputtime']=mktime();
			$data['p_uid']=$this->uid;
			$data['usertype']=(int)$_COOKIE['usertype'];
			$data['eid']=(int)$_POST['eid'];
			$data['r_name']=$_POST['r_name'];
			$data['username']=$this->username;
			$data['r_reason']=$_POST['r_reason'];
			$haves=$this->obj->DB_select_once("report","`p_uid`='".$data['p_uid']."' and `c_uid`='".$data['c_uid']."' and `usertype`='".$data['usertype']."'");
			if(is_array($haves))
			{
				$this->obj->ACT_layer_msg("���Ѿ��ٱ������û���",8,$_SERVER['HTTP_REFERER']);
			}else{
				$nid=$this->obj->insert_into("report",$data);
				if($nid)
				{
					$this->obj->member_log("�ٱ��û�".$_POST['r_name']);
					$this->obj->ACT_layer_msg("�ٱ��ɹ���",9,$_SERVER['HTTP_REFERER']);
				}else{
					$this->obj->ACT_layer_msg("�ٱ�ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
				}
			}
		}
	}
}
?>