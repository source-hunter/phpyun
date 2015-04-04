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
class right_controller extends company
{
	function index_action()
	{
		$this->public_action();
		$member[]=array("有效时间","service_time","天","不限");
		$member[]=array("<font color=red>服务价格</font>","service_price","元","0元");
		$member[]=array("发布职位数","job_num","份","不限");
		$member[]=array("下载简历数","resume","份","不限");
		$member[]=array("邀请人才面试数","interview","份","不限");
		$member[]=array("修改职位数","editjob_num","次","不限");
		$member[]=array("刷新职位数","breakjob_num","次","不限");
		$member[]=array("短信数","msg_num","条","0条");
		$member[]=array("说明","explains","","无");
		$member[]=array("<font color=red>赠送</font>","coupon","","无");
		$member[]=array("<font color=red>".$this->config['integral_pricename']."购买</font>","integral_buy",$this->config['integral_priceunit'],"0个");
		$this->yunset("member",$member);
		if($this->config['com_vip_type']==1)
		{
			$where = " and `type`='2'";
		}elseif($this->config['com_vip_type']==2){
			$where = " and `type`='1'";
		}
		$rows=$this->obj->DB_select_all("company_rating","`display`='1' and `id`<>'".$this->config['com_rating']."' and `category`=1 ".$where." order by `sort` asc");
		if(is_array($rows))
		{
			$coupon=$this->obj->DB_select_all("coupon","1","`id`,`name`");
			foreach($rows as $k=>$v)
			{
				foreach($coupon as $val)
				{
					if($v['coupon']==$val['id'])
					{
						$rows[$k]['coupon']=$val['name'];
					}
				}
			}
		}
		$num=count($rows);
		if($num>8)
		{
			foreach($rows as $k=>$v)
			{
				if($k<$num/2){
					$rows1[]=$v;
				}else{
					$rows2[]=$v;
				}
			}
			$rows=$rows1;
		}
		$statis=$this->company_satic();
		$this->yunset("statis",$statis);
		$this->yunset("rows",$rows);
		$this->yunset("rows2",$rows2);
		$this->yunset("js_def",4);
		$this->com_tpl('member_right');
	}
	function buyvip_action(){
		$this->public_action();
		$this->company_satic();
		$this->yunset("js_def",4);
		if($_GET['vipid']==0)
		{
			$this->com_tpl('buypl');
		}else{
			$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_GET['vipid']."' and display='1'");
			$this->yunset("row",$row);
			$this->com_tpl('buyvip');
		}
	}
	function buysave_action(){
		$statis=$this->company_satic();
		if($_POST['type']=='vip'){
			$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_POST['vipid']."'");
			$integral=$row['integral_buy'];
			$price=$row['service_price'];
			if($integral<0){
				$this->obj->ACT_layer_msg("数量错误！",8,$_SERVER['HTTP_REFERER']);
			}else{


				if($_POST['buytype']==2){
					if($statis['integral']<$integral){
 						$this->obj->ACT_layer_msg("你的".$this->config['integral_pricename']."不足，请先充值！",8,"index.php?c=pay");
					}
					$nid=$this->obj->company_invtal($this->uid,$integral,false,"购买".$row['name'],true,2,'integral',1);

				}else{
					if($statis['pay']<$price){
						$this->obj->ACT_layer_msg("你的余额不足，请先充值！",8,"index.php?c=pay");
					}

					$nid=$this->obj->company_invtal($this->uid,$price,false,"购买".$row['name'],true,2,"pay",1);
					if($_POST['coupon'])
					{
						$this->obj->DB_update_all("coupon_list","`status`='2',`xf_time`='".time()."'",$cwhere);
					}
				}
				if($nid){
					$row=$this->obj->DB_select_once("company_rating","`id`='".(int)$_POST['vipid']."'");

					if($row['coupon']>0)
					{
						$coupon=$this->obj->DB_select_once("coupon","`id`='".$row['coupon']."'");
						$data.="`uid`='".$this->uid."',";
						$data.="`number`='".time()."',";
						$data.="`ctime`='".time()."',";
						$data.="`coupon_id`='".$coupon['id']."',";
						$data.="`coupon_name`='".$coupon['name']."',";
						$validity=time()+$coupon['time']*86400;
						$data.="`validity`='".$validity."',";
						$data.="`coupon_amount`='".$coupon['amount']."',";
						$data.="`coupon_scope`='".$coupon['scope']."'";
						$this->obj->DB_insert_once("coupon_list",$data);
					}
					$value="`rating`='".(int)$_POST['vipid']."',";
					$value.="`rating_name`='".$row['name']."',";
					$value.="`job_num`='".$row['job_num']."',";
					$value.="`down_resume`='".$row['resume']."',";
					$value.="`invite_resume`='".$row['interview']."',";
					$value.="`editjob_num`='".$row['editjob_num']."',";
					$value.="`breakjob_num`='".$row['breakjob_num']."',";
					$value.="`msg_num`='".$row['msg_num']."',";
					$value.="`rating_type`='".$row['type']."',";
					if($row['service_time']>0)
					{
						$vip_etime=time()+86400*$row['service_time'];
					}else{
						$vip_etime=0;
					}
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
		}elseif($_POST['type']=='ad'){
			$row=$this->obj->DB_select_once("ad_class","`id`='".(int)$_POST['aid']."' and `type`='1'");
			if($row['id'])
			{
				$integral=$row['integral_buy']*$_POST['buy_time'];
			}else{
				$this->obj->ACT_msg("index.php?c=ad","非法操作！");
			}
			$pay_integral = $integral;
			$nid=$this->obj->company_invtal($this->uid,$pay_integral,false,"购买广告位",true,2,'integral',4);
			if($nid){
				$data['comid']=$this->uid;
				$data['order_id']=mktime().rand(10000,99999);
		 		$upload = $this->upload_pic("../upload/adpic/");
		 		$pictures=$upload->picture($_FILES['pic_url']);
		 		$data['ad_name']=$_POST['ad_name'];
		 		$data['pic_url']=$pictures;
				$data['pic_src']=$_POST['pic_src'];
				$data['buy_time']=$_POST['buy_time'];
				$data['integral']=$pay_integral;
				$data['aid']=(int)$_POST['aid'];
				$data['adname']=$_POST['adname'];
				$data['order_state']=2;
				$data['datetime']=mktime();
				$oid=$this->obj->insert_into("ad_order",$data);
				if($oid)
				{
					$content="购买了广告位 ".$_POST['adname'];
					$this->addstate($content,2);
					$this->obj->member_log($content);
 					$this->obj->ACT_layer_msg("您的订单已提交，请等待管理员审核！",9,"index.php?c=ad_order");
				}else{
 					$this->obj->ACT_layer_msg("提交失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
				}
			}else{
 				$this->obj->ACT_layer_msg("系统出错，请联系管理员！",8,"index.php");
			}
		}elseif($_POST['type']=='pl'){
			$integral=$this->config['integral_com_comments']*$_POST['time'];
			if($this->config['integral_com_comments_type']=="1")
			{
				$auto=true;
			}else{
				$auto=false;
			}
			$this->obj->company_invtal($this->uid,$integral,$auto,"购买企业评论管理",true,2,'integral',16);
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
		if($_POST['buytype']==1){
			if($statis['pay']<$price){
				$this->obj->ACT_layer_msg("你的余额不足，请先充值！",8,"index.php?c=pay");
			}
		}else{
			if($_POST['type']!='pl' || ($_POST['type']=='pl' && $this->config['integral_com_comments_type']=="2"))
			{
				if($statis['integral']<$integral){
	 				$this->obj->ACT_layer_msg("你的".$this->config['integral_pricename']."不足，请先充值！",8,"index.php?c=pay");
				}
			}
		}
	}
}
?>