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
class resume_controller extends user{
	function index_action(){
		$this->public_action();
		$num=$this->member_satic();
		$maxnum=$this->config['user_number']-$num['resume_num'];
		if($maxnum<0){$maxnum='0';}
		$this->yunset("maxnum",$maxnum);
		$this->yunset("confignum",$this->config['user_number']);
		$rows=$this->obj->DB_select_all("resume_expect","`uid`='".$this->uid."'","id,name,lastupdate,doc,tmpid,integrity,hits,statusbody,`topdate`");
		if($rows&&is_array($rows)){
			foreach($rows as $key=>$val){
				if($val['topdate']>1){
					$rows[$key]['topdate']=date("Y-m-d",$val['topdate']);
				}else{
					$rows[$key]['topdate']='- -';
				}
			}
		}
		$row=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`def_job`,`idcard_status`,`idcard_pic`");
		$isallow_addresume="0";
		if($this->config['user_enforce_identitycert']=="1"){
			if($row['idcard_status']=="1"&&$row['idcard_pic']){
				$isallow_addresume="1";
			}else{
				$isallow_addresume="0";
			}
		}else{
			$isallow_addresume="1";
		}
		$this->yunset("isallow_addresume",$isallow_addresume);
		$this->yunset("rows",$rows);
		$this->yunset("def_job",$row['def_job']);
		$this->yunset("js_def",2);
		$this->user_tpl('resume');
	}
	function del_action(){
		$del=(int)$_GET['id'];
		$show=$this->obj->DB_select_all("resume_show","`eid`='".$del."' and `picurl`<>''","`picurl`");
		if(is_array($show))
		{
			foreach($show as $v)
			{
				$this->obj->unlink_pic(".".$show['picurl']);
			}
		}
		$del_array=array("resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume","resume_show","down_resume","userid_job");
		if($this->obj->DB_delete_all("resume_expect","`id`='".$del."' and `uid`='".$this->uid."'")){
			foreach($del_array as $v){
				$this->obj->DB_delete_all($v,"`eid`='".$del."'' and `uid`='".$this->uid."'","");
			}
			$def_id=$this->obj->DB_select_once("resume","`uid`='".$this->uid."' and `def_job`='".$del."'");
			if(is_array($def_id)){
				$row=$this->obj->DB_select_once("resume_expect","`uid`='".$this->uid."'");
				$this->obj->update_once('resume',array('def_job'=>$row['id']),array('uid'=>$this->uid));
			}
			$this->obj->DB_update_all('member_statis',"`resume_num`=`resume_num`-1","`uid`='".$this->uid."'");
			$this->layer_msg('删除成功！',9,0,"index.php?c=resume");
		}else{
			$this->layer_msg('删除失败！',8,0,"index.php?c=resume");
		}
	}
	function publicdel_action(){
		if($_POST['id']&&$_POST['table'])
		{
			$tables=array("skill","work","project","edu","training","cert","other");
			if(in_array($_POST['table'],$tables))
			{
				$table = "resume_".$_POST['table'];
				$eid=(int)$_POST['eid'];
				$id=(int)$_POST['id'];
				$url = $_POST['table'];
				$nid=$this->obj->DB_delete_all($table,"`id`='".$id."' and `uid`='".$this->uid."'");
				$this->obj->DB_update_all("user_resume","`".$url."`=`".$url."`-1","`eid`='".$eid."' and  `uid`='".$this->uid."'");
				$resume=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
				$resume[$url];
				if($nid)
				{
					$resume_row=$this->obj->DB_select_once("user_resume","`eid`='".$eid."'");
					$numresume=$this->obj->complete($resume_row);

					$data[integrity]=$numresume;
					$data[num]=$resume[$url];
					echo json_encode($data);die;
				}else{
					echo 0;die;
				}
			}else{
				echo 0;die;
			}
		}
	}
	function rtop_action(){
		$days=intval($_POST['days']);
		if($days<1){$this->obj->ACT_layer_msg("请输入正确的置顶天数！",8);}
		if(intval($_POST['eid'])<1){$this->obj->ACT_layer_msg("非法操作！",8);}
		$statis=$this->obj->DB_select_once("member_statis","`uid`='".$this->uid."'","`integral`");
		$num=$days*$this->config['integral_resume_top'];
		if($num>$statis['integral']){
			$this->obj->ACT_layer_msg($this->config['integral_pricename']."不足，请减少置顶天数！",8);
		}else{ 
			$result=$this->obj->company_invtal($this->uid,$num,false,'简历置顶');
			if($result){
				$time=86400*$days;
				$topdate=$this->obj->DB_select_once("resume_expect","`id`='".intval($_POST['eid'])."' and `uid`='".$this->uid."'","topdate");
				if($topdate['topdate']>=time()){$time=$topdate['topdate']+$time;}else{$time=time()+$time;}
				$this->obj->DB_update_all("resume_expect","`top`='1',`topdate`='".$time."'","`id`='".intval($_POST['eid'])."' and `uid`='".$this->uid."'");
				$this->obj->member_log("简历置顶");
				$this->obj->ACT_layer_msg("操作成功！",9,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("操作失败！",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function resume_ajax_action(){
		$this->select_resume('resume_'.$_POST['type'],$_POST['id']);
	}
	
	function refresh_action(){
		$id=(int)$_GET['id'];
		$nid=$this->obj->update_once('resume_expect',array('lastupdate'=>time()),array('id'=>$id,'uid'=>$this->uid));
		$nid?$this->layer_msg('刷新成功！',9,0):$this->layer_msg('刷新失败！',8,0);
 	}

}
?>