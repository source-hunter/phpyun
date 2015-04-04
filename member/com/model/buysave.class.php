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
class buysave_controller extends company
{
	function index_action()
	{
		$statis=$this->company_satic();
		if($_POST['type']=='vip')
		{
			$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_POST['vipid']."'");
			$integral=$row['integral_buy'];
		}elseif($_POST['type']=='ad'){
			$row=$this->obj->DB_select_once("ad_class","`id`='".(int)$_POST['aid']."' and `type`='1'");
			if($row['id'])
			{
				$integral=$row['integral_buy']*$_POST['buy_time'];
			}else{
				$this->obj->ACT_msg("index.php?c=ad","非法操作！");
			}
		}elseif($_POST['type']=='pl'){
			$integral=$this->config['integral_com_comments']*$_POST['time'];
		}
		if($statis['integral']<$integral){
			$this->obj->ACT_layer_msg("你的".$this->config['integral_pricename']."不足，请先充值！",8,"index.php?c=pay");
		}
		if($_POST['type']=='pl'){
			$this->obj->company_invtal($this->uid,$integral,false,"购买企业评论管理",true,2,'integral',16);
			$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`pl_time`");
			if($company['pl_time']>time()){
				$pl_time=$company['pl_time']+86400*30*$_POST['time'];
			}else{
				$pl_time=time()+86400*30*$_POST['time'];
			}
			$oid=$this->obj->update_once("company",array("pl_time"=>$pl_time),array("uid"=>$this->uid));
			if($oid){
				$this->obj->member_log("购买评论管理");
				$this->obj->ACT_layer_msg("您已购买成功！",9,"index.php");
			}else{
 				$this->obj->ACT_layer_msg("购买失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
			}
		}
		if($_POST['type']=='vip'){
			if($integral<0){
				$this->obj->ACT_layer_msg("数量错误！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$nid=$this->obj->company_invtal($this->uid,$integral,false,"购买".$row['name'],true,2,'integral',1);
				if($nid){
					$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_POST['vipid']."'");
					$value="`rating`='".(int)$_POST['vipid']."',";
					$value.="`rating_name`='".$row['name']."',";
					$value.="`job_num`=`job_num`+".$row['job_num'].",";
					$value.="`down_resume`=`down_resume`+".$row['resume'].",";
					$value.="`invite_resume`=`invite_resume`+".$row['interview'].",";
					$value.="`editjob_num`=`editjob_num`+".$row['editjob_num'].",";
					$value.="`breakjob_num`=`breakjob_num`+".$row['breakjob_num'].",";
					$value.="`rating_type`='".$row['type']."',";
					$vip_etime=$row['service_time']*86400;
					$value.="`vip_etime`='".$vip_etime."'";
					$oid=$this->obj->DB_update_all("company_statis",$value,"`uid`='".$this->uid."'");
					$this->obj->DB_update_all("company_job","`rating`='".(int)$_POST['vipid']."'","`uid`='".$this->uid."'");
					if($oid){
						$this->obj->member_log("购买".$row['name']);
						$this->obj->ACT_layer_msg("您已购买成功！",9,"index.php");
					}else{
						$this->obj->ACT_layer_msg("购买失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->obj->ACT_layer_msg("系统出错，请联系管理员！",8,"index.php");
				}
			}
		}
	}
}
?>