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
class blacklist_controller extends company
{
	function index_action()
	{
		$where="`c_uid`='".$this->uid."' and `usertype`='2'";
		if($_GET['keyword'])
		{
			$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and a.`r_status`<>'2' and a.`name` like '%".trim($_GET['keyword'])."%'","a.`name`,a.`uid`,a.`sex`,a.`edu`,b.`job_classid`");
			if(is_array($resume))
			{
				foreach($resume as $v)
				{
					$uidarr[]=$v['uid'];
				}
			}
			$where.=" and p_uid in (".@implode(',',$uidarr).")";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$this->public_action();
		$urlarr["c"]="blacklist";
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("blacklist",$where." order by id desc",$pageurl,"10");
		if(is_array($rows))
		{
			if(!$_GET['keyword'])
			{
				if(empty($resume))
				{
					foreach($rows as $v)
					{
						$uid[]=$v['p_uid'];
					}
					$resume=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and a.`r_status`<>'2' and a.uid in (".@implode(',',$uid).")","a.`name`,a.`uid`,a.`sex`,a.`edu`,b.`job_classid`");
				}
			}
			if(is_array($resume))
			{
				include(PLUS_PATH."user.cache.php");
				include(PLUS_PATH."job.cache.php");
				foreach($rows as $key=>$val)
				{
					foreach($resume as $va)
					{
						if($val['p_uid']==$va['uid'])
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
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->company_satic();
		$this->yunset("js_def",5);
		$this->com_tpl('blacklist');
	}
	function del_action()
	{
		if($_POST['delid']||$_GET['id'])
		{
			if(is_array($_POST['delid'])){
				$id=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}else{
				$id=(int)$_GET['id'];
				$layer_type='0';
			}
			$nid=$this->obj->DB_delete_all("blacklist","`id` in (".$id.") and `c_uid`='".$this->uid."'"," ");
			if($nid)
			{
				$this->obj->member_log("ɾ��������");
				$this->layer_msg('ɾ���ɹ���',9,$layer_type,"index.php?c=blacklist");
			}else{
				$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,"index.php?c=blacklist");
			}
		}
	}
}
?>