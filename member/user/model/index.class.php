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
class index_controller extends user{
	function index_action()
	{
		$this->public_action();
		$this->member_satic();
        $this->com_cache();
		$resume = $this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`def_job`,`name`,`status`,`email`,`telphone`");
		$expect=$this->obj->DB_select_once("resume_expect","`id`='".$resume['def_job']."'",'integrity,job_classid,cityid');
		$jobwhere="(`job_post` in (".$expect['job_classid'].") or `job1_son` in (".$expect['job_classid'].")) and `cityid`='".$expect['cityid']."' ";
		$this->yunset("jobwhere",$jobwhere);
		$newmsg=$this->obj->DB_select_num("userid_msg","`uid`='".$this->uid."' and type<>'1' and `is_browse`='1'");
		$msgnum=$this->obj->DB_select_num("userid_msg","`uid`='".$this->uid."' and type<>'1'");
		$msg_count=$this->obj->DB_select_num("message","`fa_uid`='".$this->uid."' and `username`='管理员'");
		$lookNum=$this->obj->DB_select_num("look_resume","`uid`='".$this->uid."' and `status`<>'1'");
		$downNum=$this->obj->DB_select_num("down_resume","`uid`='".$this->uid."'");
		if($expect['integrity']>0){
			$numresume=$expect['integrity'];
		}else{
			$numresume=100;
		}
		$rlist=$this->obj->DB_select_all("resume_expect","`uid`='".$this->uid."' order by `defaults` desc","id,name,defaults,full,integrity,lastupdate,doc,tmpid,topdate");
		if($rlist&&is_array($rlist)){
			foreach($rlist as $key=>$val){
				if($val['topdate']>1){
					$rlist[$key]['topdate']=date("Y-m-d",$val['topdate']);
				}else{
					$rlist[$key]['topdate']='- -';
				}
			}
		} 
		$this->yunset("rlist",$rlist);
		$finder=$this->finder();
		$this->config['user_finder']<count($finder)?$findernum=0:$findernum=$this->config['user_finder']-count($finder);
		$this->yunset("finder", $finder);
		$this->yunset("findernum", $findernum);
		$this->yunset("numresume",$numresume);
		$this->yunset("newmsg", $newmsg);
		$this->yunset("msgnum", $msgnum);
		$this->yunset("msg_count",$msg_count);

		$this->yunset("resume",$resume);
		$this->yunset("lookNum",$lookNum);
		$this->yunset("downNum",$downNum);
		$this->yunset("js_def",1);
		$this->user_tpl('index');
	}

}
?>