<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2015 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class company extends common
{
	function public_action(){
		$now_url=@explode("/",$_SERVER['REQUEST_URI']);
		$now_url=$now_url[count($now_url)-1];
		$this->yunset("now_url",$now_url);
		$this->yunset("comstyle","../template/member/com");
		include(PLUS_PATH."menu.cache.php");
		$this->yunset("menu_name",$menu_name);
	}
	function company_satic()
	{
		$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'");
		$rating=$this->obj->DB_select_once("company_rating","`id`='".$statis['rating']."'");
		$statis['rating_type'] = $rating['type'];
		if($statis['vip_etime']<time()){
			if($rating['type']=='1'){
				$nums=$statis['job_num']+$statis['editjob_num']+$statis['breakjob_num']+$statis['down_resume'];
			}else{
				$nums=0;
			}
			if($nums<1){
				$data['rating_name']="�ǻ�Ա";
				$data['rating']="";
				$data['vip_etime']="";
				$where['uid']=$this->uid;
				$this->obj->update_once("company_statis",$data,$where);
			}
		}
		if($statis['autotime']>=time()){

			$statis['auto'] = 1;
		}
		$statis['pay_format']=number_format($statis['pay'],2);
		$this->yunset("statis",$statis);
		return $statis;
	}
	function get_com($type)
	{
		$statis=$this->company_satic();
		if($statis['vip_etime']>time() || $statis['vip_etime']=="0")
		{
			if($statis['rating_type']=="")
			{
				$rating=$this->obj->DB_select_once("company_rating","`id`='".$statis['rating']."'");
				$this->obj->DB_update_all("company_statis","`rating_type`='".$rating['type']."'","`uid`='".$this->uid."'");
				$statis['rating_type']=$rating['type'];
			}
			if($statis['rating_type']==1)
			{
				if($type==1){
					if($statis['job_num']>0){
						$value="`job_num`=`job_num`-1";
					}else{
						if($this->config['com_integral_online']=="1"){
							$this->intergal($type,$statis);
						}else{
							$this->obj->ACT_layer_msg("��Ա����ְλ����,�����Ա�������ݣ�",8,"index.php?c=job&w=1");
						}
					}
				}elseif($type==2){
					if($statis['editjob_num']>0){
						$value="`editjob_num`=`editjob_num`-1";
					}else{
						if($this->config['com_integral_online']=="1"){
							$this->intergal($type,$statis);
						}else{
							$this->obj->ACT_layer_msg("��Ա�޸�ְλ���꣡",8,"index.php?c=job&w=1");
						}
					}
				}elseif($type==3){
					if($statis['breakjob_num']>0){
						$value="`breakjob_num`=`breakjob_num`-1";
					}else{
						 if($this->config['com_integral_online']=='1'){
							$this->intergal($type,$statis);
						}else{
							$this->layer_msg("��Աˢ��ְλ���꣡",8,0,"index.php?c=pay");
						}
					}
				}
				if($value){
					$this->obj->DB_update_all("company_statis",$value,"`uid`='".$this->uid."'");
				}
			}
		}else{
			$this->intergal($type,$statis);
		}
	}
	function intergal($type,$statis)
	{
		if($type==1 && $this->config['integral_job']){
			if($this->config['integral_job_type']=="1")
			{
				$auto=true;
			}else{
				if($statis['integral']<$this->config['integral_job']){
					$this->obj->ACT_layer_msg("���".$this->config['integral_pricename']."��������ְλ��",8,"index.php?c=pay");
				}
				$auto=false;
			}
			$nid=$this->obj->company_invtal($this->uid,$this->config['integral_job'],$auto,"����ְλ",true,2,'integral',6);
		}elseif($type==2 && $this->config['integral_jobedit']){
			if($this->config['integral_jobedit_type']=="1")
			{
				$auto=true;
			}else{
				if($statis['integral']<$this->config['integral_jobedit'])
				{
					$this->obj->ACT_layer_msg("���".$this->config['integral_pricename']."�����޸�ְλ��",8,"index.php?c=pay");
				}
				$auto=false;
			}
			$nid=$this->obj->company_invtal($this->uid,$this->config['integral_jobedit'],$auto,"�޸�ְλ",true,2,'integral',7);
		}elseif($type==3 && $this->config['integral_jobefresh']){
			if($this->config['integral_jobefresh_type']=="1")
			{
				$auto=true;
			}else{
				if($statis['integral']<$this->config['integral_jobefresh']){
					if($_GET){
						$this->layer_msg("���".$this->config['integral_pricename']."����ˢ��ְλ��",8,0,"index.php?c=pay");
					}else{
						$this->obj->ACT_layer_msg("���".$this->config['integral_pricename']."����ˢ��ְλ��",8,"index.php?c=pay");
					}
				}
				$auto=false;
			}
			$nid=$this->obj->company_invtal($this->uid,$this->config['integral_jobefresh'],$auto,"ˢ��ְλ",true,2,'integral',8);
		}
	}
	function com_tpl($tpl){
		$this->yuntpl(array('member/com/'.$tpl));
	}

	function get_user()
	{
		$rows=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		if(!$rows['name'] || !$rows['address'] || !$rows['pr']){
			$this->obj->ACT_msg("index.php?c=info","�������ƹ�˾���ϣ�");
		}
		return $rows;
	}

	function job(){
		if($_GET['p_uid']){
			$data['p_uid']=(int)$_GET['p_uid'];
			$data['inputtime']=time();
			$data['c_uid']=$this->uid;
			$data['usertype']=(int)$_COOKIE['usertype'];
			$haves=$this->obj->DB_select_once("blacklist","`p_uid`=".$data['p_uid']."  and `c_uid`=".$data['c_uid']." and `usertype`=".$data['usertype']."");
			if(is_array($haves)){
				$this->obj->layer_msg("���û��������������У�",8,0,$_SERVER['HTTP_REFERER']);
			}else{
				$nid=$this->obj->insert_into("blacklist",$data);
				$num=$this->obj->DB_select_num("userid_job","`uid`=".$data['p_uid']."  and `com_id`=".$data['c_uid']."");
				$this->obj->DB_delete_all("userid_job","`uid`=".$data['p_uid']."  and `com_id`=".$data['c_uid'].""," ");
				$this->obj->DB_update_all("member_statis","`sq_jobnum`=`sq_jobnum`-$num","`uid`='".$data['p_uid']."'");
				if($nid)
				{
					$this->obj->member_log("�����˲�");
					$this->layer_msg('ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']);
				}else{
					$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
				}
			}
		}
		if($_GET['r_uid']){
			if($_GET['r_reason']=="")
			{
				$this->obj->ACT_layer_msg("�ٱ����ݲ���Ϊ�գ�",8,"index.php?c=down");
			}
			$data['p_uid']=(int)$_GET['r_uid'];
			$data['inputtime']=time();
			$data['c_uid']=$this->uid;
			$data['eid']=(int)$_GET['eid'];
			$data['r_name']=$_GET['r_name'];
			$data['usertype']=(int)$_COOKIE['usertype'];
			$data['username']=$this->username;
			$data['r_reason']=$_GET['r_reason'];
			$haves=$this->obj->DB_select_once("report","`p_uid`=".$data['p_uid']." and `c_uid`=".$data['c_uid']." and `usertype`=".$data['usertype']."","id");
			if(is_array($haves))
			{
				$this->obj->ACT_layer_msg("���Ѿ��ٱ������û���",8,"index.php?c=down");
			}else{
				$nid=$this->obj->insert_into("report",$data);
				if($nid)
				{
					$this->obj->member_log("�ٱ��˲š�".$_GET['r_name']."��");
					$this->obj->ACT_layer_msg("�����ɹ���",9,"index.php?c=down");
				}else{
					$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,"index.php?c=down");
				}
			}
		}
		if($_POST['recid'])
		{
			$id=(int)$_POST['recid'];
			$_POST['day']=intval($_POST['day']);
			if($_POST['day']<1){
				$this->obj->ACT_layer_msg("����ȷ��д�Ƽ�������",2,$_SERVER['HTTP_REFERER']);
			}
			$reow=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","integral");
			$job=$this->obj->DB_select_once("company_job","`id`='".$id."'","name,rec_time");
			if($job['rec_time']<time())
			{
				$time=time()+$_POST['day']*86400;
			}else{
				$time=$job['rec_time']+$_POST['day']*86400;
			}
			$integral=$this->config['com_recjob']*$_POST['day'];
			if($reow['integral']<$integral && $this->config['com_recjob_type']=="2")
			{
				$this->obj->ACT_layer_msg("����".$this->config['integral_pricename']."���㣬���ֵ��",8,"index.php?c=pay");
			}else{
				if($this->config['com_recjob_type']=="1")
				{
					$auto=true;
				}else{
					$auto=false;
				}
				$this->obj->company_invtal($this->uid,$integral,$auto,"�����Ƽ�ְλ",true,2,'integral',12);
			}
			$data['rec']=1;
			$data['rec_time']=$time;
			$where['id']=$id;
			$where['uid']=$this->uid;
			$this->obj->update_once("company_job",$data,$where);
			$this->obj->member_log("�����Ƽ�ְλ��".$job['name']."��",1,1);
 			$this->obj->ACT_layer_msg("�Ƽ��ɹ���",9,$_SERVER['HTTP_REFERER']);
		}
		if($_POST['urgentid'])
		{
			$id=(int)$_POST['urgentid'];
			if($_POST['day']<1)
			{
 				$this->obj->ACT_layer_msg("����ȷ��д����������",8,$_SERVER['HTTP_REFERER']);
			}
			$reow=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","integral");
			$integral=$this->config['com_urgent']*$_POST['day'];
			$job=$this->obj->DB_select_once("company_job","`id`='".$id."'","name,urgent_time");
			if($job['urgent_time']<time())
			{
				$time=time()+$_POST['day']*86400;
			}else{
				$time=$job['urgent_time']+$_POST['day']*86400;
			}
			if($reow['integral']<$integral && $this->config['com_urgent_type']=="2")
			{
 				$this->obj->ACT_layer_msg("����".$this->config['integral_pricename']."���㣬���ֵ��",8,"index.php?c=pay");
			}else{
				if($this->config['com_urgent_type']=="1")
				{
					$auto=true;
				}else{
					$auto=false;
				}
				$this->obj->company_invtal($this->uid,$integral,$auto,"��������ְλ",true,2,'integral',10);
				$data['urgent']=1;
				$data['urgent_time']=$time;
				$where['id']=$id;
				$where['uid']=$this->uid;
				$this->obj->update_once("company_job",$data,$where);
				$this->obj->member_log("��������ְλ��".$job['name']."��",1,1);
 				$this->obj->ACT_layer_msg("��������ְλ�ɹ���",9,$_SERVER['HTTP_REFERER']);
			}
		}
		if($_GET['up']){
			$this->get_com(3);
			$nid=$this->obj->DB_update_all("company_job","`lastupdate`='".time()."'","`uid`='".$this->uid."' and `id`='".(int)$_GET['up']."'");
			if($nid)
			{
				$this->obj->DB_update_all("company","`jobtime`='".time()."'","`uid`='".$this->uid."'");
				$job=$this->obj->DB_select_once("company_job","`id`='".(int)$_GET['up']."'","name");
				$this->obj->member_log("ˢ��ְλ��".$job['name']."��",1,4);
				$this->layer_msg('ˢ��ְλ�ɹ���',9,0,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('ˢ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
			}
		}
		if($_POST['gotimeid'])
		{
			if($_POST['day']<1)
			{
 				$this->obj->ACT_layer_msg("����ȷ��д����������",8);
			}else{
				$posttime=(int)$_POST['day']*86400;
				$ids=@explode(",",$_POST['gotimeid']);
				if(is_array($ids))
				{
					foreach($ids as $value)
					{
						$where=array();$data=array();
						$row=$this->obj->DB_select_once("company_job","`id`='".(int)$value."' and `uid`='".$this->uid."'","`state`,`edate`");
						$time=$row['edate']+$posttime;
						$where['id']=(int)$value;
						$where['uid']=$this->uid;
						if($row['state']==2 && $time>time())
						{
							$data['edate']=$time;
							$data['state']=1;
							$id=$this->obj->update_once("company_job",$data,$where);
							$this->obj->update_once("company_statis","`status2`=`status2`-1,`status1`=`status1`+1","uid='".$this->uid."'");
						}else{
							$id=$this->obj->update_once("company_job",array("edate"=>$time),$where);
						}
					}
				}
				if($id)
				{
					$this->obj->member_log("ְλ����");
					$this->obj->ACT_layer_msg("���ڳɹ���",9,$_SERVER['HTTP_REFERER']);
				}else{
					$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
				}
			}
		}
		if($_POST['status'] && $_POST['id'])
		{
			$where['id']=(int)$_POST['id'];
			$where['uid']=$this->uid;
			$nid=$this->obj->update_once("company_job",array("status"=>(int)$_POST['status']),$where);
			if($nid)
			{
				$this->obj->member_log("�޸�ְλ����״̬");
				echo 1;die;
			}else{
				echo 2;die;
			}
		}
		if($_GET['del'] || is_array($_POST['checkboxid']))
		{
			if(is_array($_POST['checkboxid']))
			{
				$layer_type=1;
				$delid=$this->pylode(",",$_POST['checkboxid']);
			}else if($_GET['del']){
				$layer_type=0;
				$delid=(int)$_GET['del'];
			}
			$nid=$this->obj->DB_delete_all("company_job","`uid`='".$this->uid."' and `id` in (".$delid.")"," ");
			$this->obj->DB_delete_all("company_job_link","`uid`='".$this->uid."' and `jobid` in (".$delid.")"," ");
			if($nid){
				$newest=$this->obj->DB_select_once("company_job","`uid`='".$this->uid."' order by lastupdate DESC","`lastupdate`");
				$this->obj->update_once("company",array("jobtime"=>$newest['lastupdate']),array("uid"=>$this->uid));
				$this->obj->member_log("ɾ��ְλ",1,3);
				$this->layer_msg('ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function add_invoice_record($post,$order_id,$oid){
		if($post['linkway']=='1'){
			$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`linkman`,`linktel`,`address`");
			$link=",`link_man`='".$company['linkman']."',`link_moblie`='".$company['linktel']."',`address`='".$company['address']."'";
		}else{
			$post=$this->post_trim($post);
			if($post['link_man']==''||$post['link_moblie']==''||$post['address']==''){
				$this->obj->ACT_layer_msg("��ϵ�ˡ���ϵ�绰�����͵�ַ������Ϊ�գ�",8,$_SERVER['HTTP_REFERER']);
			}else{
				$link=",`link_man`='".$post['link_man']."',`link_moblie`='".$post['link_moblie']."',`address`='".$post['address']."'";
			}
		}
		$record=$this->obj->DB_select_once("invoice_record","`order_id`='".$order_id."' and `uid`='".$this->uid."'","id");
		if($record['id']){
			$this->obj->DB_update_all("invoice_record","`title`='".trim($post['invoice_title'])."',`status`='0',`addtime`='".time()."'".$link,"`id`='".$record['id']."'");
		}else{
			$iid=$this->obj->DB_insert_once("invoice_record","`oid`='".$oid."',`order_id`='".$order_id."',`uid`='".$this->uid."',`title`='".trim($post['invoice_title'])."',`status`='0',`addtime`='".time()."'".$link);
			if($iid==false||$iid==''){$this->obj->ACT_layer_msg("��Ʊ��Ϣ���ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);}
		}
	}
	function wnameup($namekey,$wname,$type)
	{
		$wanmeinfo = $this->obj->DB_select_all("company_statis","`$namekey`='$wname' AND `uid`<>'".$this->uid."'");
		if(is_array($wanmeinfo)&&!empty($wanmeinfo))
		{
			$this->obj->ACT_layer_msg("���ʺ��Ѿ����󶨣������˶�����������Ա���ߣ�",8,"index.php?c=Web&type=".$type);
		}else{
			$this->obj->update_once("company_statis",array($namekey=>$wname),array("uid"=>$this->uid));
		}
	}
	function logout_action(){
		$this->logout();
	}
	function HandleError($message)
	{
		echo $message;
	}
	function CreateFirstName($file_extension )
	{
		$num=date('mdHis').rand(1,100);
		$fileName=$num.".".$file_extension;
		return $fileName;
	}
	function CreateNextName($file_extension,$file_dir)
	{
		$fileName_arr = scandir($file_dir,1);
		$fileName=$fileName_arr[0];
		$aa=floatval($fileName);
		$num=0;
		$num=(1+$aa);
		if(empty($aa)){
			$num = date('mdHis').rand(1,100);
		}
		return $num.".".$file_extension;
	}
	function createdatefilename($file_extension)
	{
		date_default_timezone_set('PRC');
		return date('mdHis').rand(1,100).".".$file_extension;
	}
	function create_folders($dir)
	{
       return is_dir($dir) or ($this->create_folders(dirname($dir)) and mkdir($dir, 0777));
     }
}
?>