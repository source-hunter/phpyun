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
class hr_controller extends company
{
	function index_action(){
		if(!empty($_GET['keyword'])){
			$resume=$this->obj->DB_select_all("resume","`r_status`<>'2' and `name` like '%".$_GET['keyword']."%'","`name`,`sex`,`edu`,`uid`");
			if(is_array($resume) && !empty($resume)){
				foreach($resume as $v){
					$uid[]=$v['uid'];
				}
			}
			$urlarr['keyword']=$_GET['keyword'];
			$where=" uid in (".$this->pylode(',',$uid).") and ";
		}
		if($_GET['job_id']){
			$where ="job_id=".intval($_GET['job_id'])." and ";
			$urlarr['job_id']=$_GET['job_id'];
		}
		$this->public_action();
		$urlarr['c']="hr";
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("userid_job",$where."  `com_id`='".$this->uid."' order by id desc",$pageurl,"10");
		if(is_array($rows) && !empty($rows)){
			if(empty($resume)){
				foreach($rows as $v){
					$uid[]=$v['uid'];
				}
				$resume=$this->obj->DB_select_all("resume","`uid` in (".$this->pylode(",",$uid).") and `r_status`<>'2'","`name`,`sex`,`edu`,`uid`");
			}
			$userid_msg=$this->obj->DB_select_all("userid_msg","`fid`='".$this->uid."' and `uid` in (".@implode(",",$uid).")","uid");
			if(is_array($resume))
			{
				include(PLUS_PATH."user.cache.php");
				foreach($rows as $k=>$v)
				{
					foreach($resume as $val)
					{
						if($v['uid']==$val['uid'])
						{
							$rows[$k]['name']=$val['name'];
							$rows[$k]['sex']=$userclass_name[$val['sex']];
							$rows[$k]['edu']=$userclass_name[$val['edu']];
						}
					}
					foreach($userid_msg as $val)
					{
						if($v['uid']==$val['uid'])
						{
							$rows[$k]['userid_msg']=1;
						}
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->company_satic();
		$this->yunset("js_def",5);
		$this->com_tpl('hr');
	}
	function hrset_action(){
		if($_POST['ajax']==1 && $_POST['ids'])
		{
			$this->obj->DB_update_all("userid_job","`is_browse`='2'","`id` in (".$this->pylode(",",$_POST['ids']).") and `com_id`='".$this->uid."'");
			$this->unset_remind("userid_job",'2');
			$this->obj->member_log("�����Ķ�����ְλ���˲�");
			$this->layer_msg('�����ɹ���',9,0,"index.php?c=hr");
		}
		if($_POST['delid']||$_GET['delid']){
			if(is_array($_POST['delid'])){
				$id=$this->pylode(",",$_POST['delid']);
				$layer_type='1';
			}else{
				$id=(int)$_GET['delid'];
				$layer_type='0';
			}
			$sq_num = $this->obj->DB_select_all("userid_job","`id` in (".$id.") and `com_id`='".$this->uid."'","`uid`");
			if(is_array($sq_num))
			{
				foreach($sq_num as $v)
				{
					$this->obj->DB_update_all("member_statis","`sq_jobnum`=`sq_jobnum`-1","`uid`='".$v['uid']."'");
		    	}
			}
			$num=count($sq_num);
			$this->obj->DB_update_all("company_statis","`sq_job`=`sq_job`-$num","`uid`='".$this->uid."'");
			$nid=$this->obj->DB_delete_all("userid_job","`id` in (".$id.") and `com_id`='".$this->uid."'"," ");
			if($nid)
			{
				$this->unset_remind("userid_job",'2');
				$this->obj->member_log("ɾ������ְλ���˲�",6,3);
				$this->layer_msg('ɾ���ɹ���',9,$layer_type,"index.php?c=hr");
			}else{
				$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,"index.php?c=hr");
			}
		}
	}
}
?>