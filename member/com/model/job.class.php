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
class job_controller extends company
{
	function index_action(){
		$this->public_action();
		$urlarr=array("c"=>"job","page"=>"{{page}}");
		if($_GET['w']>=0){
			$where=" and state='".(int)$_GET['w']."'";
			$urlarr['w'] = $_GET['w'];
		}
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("company_job","`uid`='".$this->uid."' ".$where,$pageurl,"10");
		if(is_array($rows) && !empty($rows)){
			foreach($rows as $k=>$v){
				$rows[$k]['usernum']=$this->obj->DB_select_num("resume_expect","FIND_IN_SET('".$v['job_post']."',job_classid) and `hy`='".$v['hy']."' and `cityid`='".$v['cityid']."'");
				$rows[$k]['jobnum']=$this->obj->DB_select_num("userid_job","`job_id`='".$v['id']."' and `com_id`='".$this->uid."'");
			}
		}
		$Field = "SUM(case when 1=1 then 1 else 0 end) as job,SUM(case when state='0' then 1 else 0 end) as status0,SUM(case when state='1' then 1 else 0 end) as status1,SUM(case when state='2' then 1 else 0 end) as status2,SUM(case when state='3' then 1 else 0 end) as status3";
		$status=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."'",$Field);
		$this->yunset("status",$status[0]);
		$max_fen=$this->obj->DB_select_once("company_job","`state`='1' and `sdate`<'".mktime()."' and `r_status`<>'2' and `edate`>'".mktime()."' order by `xuanshang` desc");
		$urgent=$this->config['com_urgent'];
		$this->yunset("rows",$rows);
		$this->yunset("urgent",$urgent);
		$this->yunset("max_fen",$max_fen);
		$this->company_satic();
		$this->yunset("js_def",3);
		if(intval($_GET['w'])==1){
			$this->com_tpl('joblist');
		}else{
			$this->com_tpl('job');
		}
	}
	function opera_action(){
		$this->job();
	}
	function buyautojob_action(){
		$autocount = intval($_POST['autocount']);
		if($autocount>0)
		{
			$buyprice = ceil($autocount*$this->config['job_auto']);

			$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`integral`,`autotime`");

			if($statis['integral']>=$buyprice || $this->config['job_auto_type']=="1")
			{
				if($statis['autotime']>=time())
				{
					$autotime = $statis['autotime']+$autocount*86400;
				}else{
					$autotime = time()+$autocount*86400;
				}
				$this->obj->update_once('company_statis',array('autotime'=>$autotime),array('uid'=>$this->uid));
				if($this->config['job_auto_type']=="1")
				{
					$auto=true;
				}else{
					$auto=false;
				}
				$this->obj->company_invtal($this->uid,$buyprice,$auto,"购买职位自动刷新",true,2,'integral',9);
				$this->obj->update_once('company_job',array('autotime'=>$autotime),"`uid`='".$this->uid."' AND `autotype`>0");
				$this->insert_company_pay($buyprice,2,$this->uid,"购买职位自动刷新",1,9);
				$this->obj->member_log("购买职位自动刷新功能");
				$this->obj->ACT_layer_msg("购买成功，有效期至".date('Y-m-d',$autotime),9,"index.php?c=job&w=1");
			}else{
				$this->obj->ACT_layer_msg("您的".$this->config['integral_pricename']."余额不足，请先兑换或者充值！",8,"index.php?c=job&w=1");
			}

		}else{

			$this->obj->ACT_layer_msg("请填写一个有效的购买期限！",9,"index.php?c=job&w=1");
		}
	}
	function autojob_action(){
		$jobid = (int)$_POST['jobid'];
		$alljob = (int)$_POST['alljob'];
		$autotype = (int)$_POST['autotype'];
		if($jobid>0)
		{
			$where['uid'] = $this->uid;
			if(!$alljob)
			{
				$where['id'] = $jobid;
			}
			if($autotype>0)
			{
				$statis=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`integral`,`autotime`");
				if($statis['autotime']<time()){
					$this->obj->ACT_layer_msg("您的刷新功能已到期，如有需要请继续购买！",8,"index.php?c=job&w=1");
				}
			}else{

				$autotype = 0;
				$statis['autotime'] = 0;
			}
			$this->obj->update_once('company_job',array('autotype'=>$autotype,'autotime'=>$statis['autotime']),$where);
			$this->obj->member_log("设置刷新职位功能");
			$this->obj->ACT_layer_msg("刷新设置成功！",9,"index.php?c=job&w=1");
		}else{
			$this->obj->ACT_layer_msg("请选择有效的职位信息！",9,"index.php?c=job&w=1");
		}
	}
}
?>