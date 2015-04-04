<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class expectq_controller extends user{
	function index_action(){
		$this->get_user();
		$num=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
		if($num>=$this->config['user_number']&&$_GET['e']==''){
			$this->obj->ACT_msg("index.php?c=resume","你的简历数已经超过系统设置的简历数了");
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
					$this->obj->ACT_msg("index.php?c=resume","你的简历数已经超过系统设置的简历数了");
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
					$this->obj->member_log("添加粘贴简历",2,1);
					$this->obj->ACT_layer_msg("添加成功！",9,"index.php?c=resume");
				}else{
					$this->obj->ACT_layer_msg("添加失败！",8,"index.php?c=resume");
				}
			}else{
			
				$this->obj->update_once("resume_expect",$_POST,array("id"=>$eid));
				$nid=$this->obj->update_once("resume_doc",$data,array("eid"=>$eid));
				if($nid)
				{
					$this->obj->member_log("更新粘贴简历",2,2);
					$this->obj->ACT_layer_msg("更新成功！",9,"index.php?c=resume");
				}else{
					$this->obj->ACT_layer_msg("更新失败！",8,"index.php?c=resume");
				}
 			}
		}
	}
}
?>