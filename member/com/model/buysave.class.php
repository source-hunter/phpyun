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
				$this->obj->ACT_msg("index.php?c=ad","�Ƿ�������");
			}
		}elseif($_POST['type']=='pl'){
			$integral=$this->config['integral_com_comments']*$_POST['time'];
		}
		if($statis['integral']<$integral){
			$this->obj->ACT_layer_msg("���".$this->config['integral_pricename']."���㣬���ȳ�ֵ��",8,"index.php?c=pay");
		}
		if($_POST['type']=='pl'){
			$this->obj->company_invtal($this->uid,$integral,false,"������ҵ���۹���",true,2,'integral',16);
			$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`pl_time`");
			if($company['pl_time']>time()){
				$pl_time=$company['pl_time']+86400*30*$_POST['time'];
			}else{
				$pl_time=time()+86400*30*$_POST['time'];
			}
			$oid=$this->obj->update_once("company",array("pl_time"=>$pl_time),array("uid"=>$this->uid));
			if($oid){
				$this->obj->member_log("�������۹���");
				$this->obj->ACT_layer_msg("���ѹ���ɹ���",9,"index.php");
			}else{
 				$this->obj->ACT_layer_msg("����ʧ�ܣ����Ժ����ԣ�",8,$_SERVER['HTTP_REFERER']);
			}
		}
		if($_POST['type']=='vip'){
			if($integral<0){
				$this->obj->ACT_layer_msg("��������",8,$_SERVER['HTTP_REFERER']);
			}else{
				$nid=$this->obj->company_invtal($this->uid,$integral,false,"����".$row['name'],true,2,'integral',1);
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
						$this->obj->member_log("����".$row['name']);
						$this->obj->ACT_layer_msg("���ѹ���ɹ���",9,"index.php");
					}else{
						$this->obj->ACT_layer_msg("����ʧ�ܣ����Ժ����ԣ�",8,$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->obj->ACT_layer_msg("ϵͳ��������ϵ����Ա��",8,"index.php");
				}
			}
		}
	}
}
?>