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
class expectq_controller extends user{
	function index_action(){
		$this->get_user();
		$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
		if($num>=$this->config['user_number']&&$_GET['e']==''){
			$this->obj->ACT_msg("index.php?c=resume","��ļ������Ѿ�����ϵͳ���õļ�������");
		}
		$row=$this->obj->DB_select_alls("resume_expect","resume_doc","a.`uid`='".$this->uid."' and a.`id`='".(int)$_GET['e']."' and a.`id`=b.eid");
		$this->yunset("row",$row[0]);

		if($row[0]['job_classid']){
			include APP_PATH."/plus/job.cache.php";
			$job_classid=@explode(",",$row[0]['job_classid']);
			if(is_array($job_classid)){
				foreach($job_classid as $key){
					$job_classname[]=$job_name[$key];
				}
				$this->yunset("job_classname",$this->pylode('+',$job_classname));
			}
			$this->yunset("job_classid",$job_classid);
		}
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		$this->user_cache();
		$this->yunset("js_def",2);
		$this->user_tpl('expectq');
	}
	function save_action(){
		if($_POST['submit']){
			$eid=(int)$_POST['eid'];
			$data['doc']=str_replace("&amp;","&",html_entity_decode($_POST['doc'],ENT_QUOTES,"GB2312"));
			$_POST['lastupdate']=mktime();
			$_POST['integrity']=100;
			unset($_POST['eid']);
			unset($_POST['submit']);
			unset($_POST['doc']);
			if(!$eid){
				$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
				if($num>=$this->config['user_number']&&$_GET['e']==''){
					$this->obj->ACT_msg("index.php?c=resume","��ļ������Ѿ�����ϵͳ���õļ�������");
				}
				$_POST['doc']='1';
				$_POST['uid']=(int)$this->uid;
				$nid=$this->obj->insert_into("resume_expect",$_POST);
				$data['eid']=(int)$nid;
				$data['uid']=(int)$this->uid;
				$nid2=$this->obj->insert_into("resume_doc",$data);
				if($nid2){
					if($num==0){
						$this->obj->update_once('resume',array('def_job'=>$nid),array('uid'=>$this->uid));
 					}
					$nid2=$this->obj->DB_update_all("member_statis","`resume_num`=`resume_num`+1","uid='".$this->uid."'");
				}
				if($nid2)
				{
					$this->obj->member_log("���ճ������",2,1);
					$this->obj->ACT_layer_msg("��ӳɹ���",9,"index.php?c=resume");
				}else{
					$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,"index.php?c=resume");
				}
			}else{
			
				$this->obj->update_once("resume_expect",$_POST,array("id"=>$eid));
				$nid=$this->obj->update_once("resume_doc",$data,array("eid"=>$eid));
				if($nid)
				{
					$this->obj->member_log("����ճ������",2,2);
					$this->obj->ACT_layer_msg("���³ɹ���",9,"index.php?c=resume");
				}else{
					$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,"index.php?c=resume");
				}
 			}
		}
	}
}
?>