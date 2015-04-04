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
class index_controller extends company
{
	function index_action(){
		include(CONFIG_PATH."db.data.php");
		$this->yunset("arr_data",$arr_data);
		$statis=$this->company_satic();
		$invite_resume=$this->obj->DB_select_num("userid_msg","`fid`='".$this->uid."'");
		$down_resume=$this->obj->DB_select_num("down_resume","`comid`='".$this->uid."'");
		$de_resume=$this->obj->DB_select_num("userid_job","`com_id`='".$this->uid."' and `is_browse`='1'");
        $this->yunset("de_resume",$de_resume);
		$des_resume=$this->obj->DB_select_num("userid_job","`com_id`='".$this->uid."'");
        $this->yunset("des_resume",$des_resume);
		$this->yunset("invite_resume",$invite_resume);
		$this->yunset("down_resume",$down_resume);
		$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","`login_date`");

		if($statis['rating']>0){
			$company_rating=$this->obj->DB_select_once("company_rating","`id`='".$statis['rating']."'");
			if($statis['vip_etime']>time()){
				$days=round(($statis['vip_etime']-mktime())/3600/24) ;
				$this->yunset("days",$days);
			}
			$this->yunset("company_rating",$company_rating);
		}

		$_GET['cityid']=$company['cityid'];
		$_GET['hy']=$company['hy'];
		$where_uid="-1";
		$atn=$this->obj->DB_select_all("atn","`sc_uid`='".$this->uid."' and `usertype`='1' order by `id` desc");
		if($atn&&is_array($atn))
		{
			foreach($atn as $val)
			{
				$uids[]=$val['uid'];
			}
			$where_uid=@implode(",",$uids);
		}
		$normal_job_num=$this->obj->DB_select_num("company_job","`uid`='".$this->uid."' and `state`='1' and `edate`>'".time()."'");
		$this->yunset("where_uid",$where_uid);
		$this->yunset("member",$member);
		$this->yunset("uid",$this->uid);
		$this->yunset("normal_job_num",$normal_job_num);
		$this->yunset("company",$company);
		$this->public_action();
		$this->yunset("js_def",1);
		$this->com_tpl('index');
	}
}
?>