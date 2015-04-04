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
class recharge_controller extends appadmin
{
	function getmember_action()
	{
		if(!$_POST['usertype'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$where="a.uid=b.uid and a.usertype='".(int)$_POST['usertype']."'";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			if($_POST['usertype']=='2'){
				$where.=" and (a.`username` LIKE '%".$keyword."%' or b.`name` LIKE '%".$keyword."%')";
			}else{
				$where.=" and (a.`username` LIKE '%".$keyword."%' or b.`realname` LIKE '%".$keyword."%')";
			}
		}
		if($order){
			$where.=" order by a.".$order;
		}else{
			$where.=" order by a.`uid` desc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		if($_POST['usertype']=='2'){				
			$rows=$this->obj->DB_select_alls("member",'company',$where,"a.uid,a.username,a.usertype,b.name");
		}else{				
			$rows=$this->obj->DB_select_alls("member",'lt_info',$where,"a.uid,a.username,a.usertype,b.realname as name");
		}
		if(!empty($rows))
		{	
			$uids=array();
			foreach($rows as $k=>$v)
			{
				$list[$k]['uid']=$v['uid'];
				$uids[]=$v['uid'];
				$list[$k]['usertype']=$v['usertype'];
				$list[$k]['username']=iconv("gbk","UTF-8",$v['username']);
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
			}
			if($_POST['usertype']=='2'){				
				$rating_rows=$this->obj->DB_select_all("company_statis","`uid` in(".$this->pylode(',',$uids).")",'`rating_name`,`rating`,`uid`');
			}else{				
				$rating_rows=$this->obj->DB_select_all("lt_statis","`uid` in(".$this->pylode(',',$uids).")",'`rating_name`,`rating`,`uid`');
			}
			foreach($rows as $k=>$v)
			{
				foreach($rating_rows as $key=>$val)
				{
					if($v['uid']==$val['uid']){		
						$list[$k]['rating_name']=iconv("gbk","UTF-8",$val['rating_name']);				
						$list[$k]['rating']=$val['rating'];break;
					}
				}
			}
			$data['error']=1;
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function recharge_action()
	{
		if(!$_POST['uids']||!$_POST['type']||!$_POST['fs']||!$_POST['order_price']||!$_POST['usertype'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$value="`order_price`='".$_POST['order_price']."',";
		$value.="`order_time`='".time()."',";
		$value.="`order_state`='2',";
		$value.="`remark`='".$_POST['remark']."',";
		if($_POST['type']=="price")
		{
			$value.="`type`='4',";
		}else{
			$value.="`type`='2',";
		}
		$num=$_POST['order_price'];
		if($_POST['fs']=="2")
		{
			$uvalue="`pay`=`pay`-$num";
			if($_POST['type']=="price")
			{
				$svalue="`pay`=`pay`-$num,`all_pay`=`all_pay`-$num";
			}else{
				$svalue="`integral`=`integral`-$num";
			}
			$value.="`order_type`='admincut',";
		}else{
			$uvalue="`pay`=`pay`+$num";
			if($_POST['type']=="price")
			{
				$svalue="`pay`=`pay`+$num,`all_pay`=`all_pay`+$num";
			}else{
				$svalue="`integral`=`integral`+$num";
			}
			$value.="`order_type`='adminpay',";
		}
		$uids=@explode(",",$_POST['uids']);
		foreach($uids as $v)
		{
			$value.="`order_id`='".mktime().rand(10000,99999)."',";
			$value.="`uid`='".$v."'";
			$this->obj->DB_insert_once("company_order",$value);
		}
		if($_POST['usertype']=="1")
		{
			$this->obj->DB_update_all("member_statis",$uvalue,"`uid` in (".$_POST['uids'].")");
		}elseif($_POST['usertype']=="2"){
			$this->obj->DB_update_all("company_statis",$svalue,"`uid` in (".$_POST['uids'].")");
		}elseif($_POST['usertype']=="3"){
			$this->obj->DB_update_all("lt_statis",$svalue,"`uid` in (".$_POST['uids'].")");
		}
		$this->write_appadmin_log("会员充值成功！");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function getrating_action()
	{
		if(!$_POST['usertype'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		if($_POST['usertype']==2)
		{
			$where="`category`='1'";
		}else if($_POST['usertype']==3){
			$where="`category`='2'";
		}else{
			$where="`category` in (".$_POST['usertype'].")";
		}
		$rows=$this->obj->DB_select_all("company_rating",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$usertype=($v['category']==1)?'2':'3';
				$list[$usertype][$k]['id']=$v['id'];
				$list[$usertype][$k]['money']=$v['service_price'];
				$list[$usertype][$k]['integral']=$v['integral_buy'];
				$list[$usertype][$k]['service_time']=$v['service_time'];
				$list[$usertype][$k]['name']=iconv("gbk","UTF-8",$v['name']);
			}
			$data['error']=1;
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function rating_action()
	{
		if(!$_POST['uids']||!$_POST['rating']||!$_POST['usertype'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		if($_POST['usertype']=="2")
		{
			$row=$this->obj->DB_select_once("company_rating","`category`='1' and `id`='".$_POST['rating']."'");
		}else{
			$row=$this->obj->DB_select_once("company_rating","`category`='2' and `id`='".$_POST['rating']."'");
		}
		if(empty($row))
		{
			$this->return_appadmin_msg(2,"该会员等级不存在");
		}
		if($_POST['usertype']=="2")
		{
			$value="`rating`='".$_POST['rating']."',";
			$value.="`rating_name`='".$row['name']."',";
			$value.="`job_num`='".$row['job_num']."',";
			$value.="`down_resume`='".$row['resume']."',";
			$value.="`invite_resume`='".$row['interview']."',";
			$value.="`editjob_num`='".$row['editjob_num']."',";
			$value.="`breakjob_num`='".$row['breakjob_num']."',";
			$value.="`lt_job_num`='".$row['lt_job_num']."',";
			$value.="`lt_down_resume`='".$row['lt_down_resume']."',";
			$value.="`lt_editjob_num`='".$row['lt_editjob_num']."',";
			$value.="`lt_breakjob_num`='".$row['lt_breakjob_num']."',";
			$value.="`msg_num`='".$row['msg_num']."',";
			$value.="`rating_type`='".$row['type']."',";
			if($row['service_time']>0)
			{
				$time=time()+86400*$row['service_time'];
			}else{
				$time=0;
			}
			$row_com=$this->obj->DB_select_once("company_statis","`uid` in (".$_POST['uids'].")");
			if($_POST['present_days']){
				if(is_numeric($_POST['present_days'])){
					if(empty($row_com['vip_etime'])){
						if($time==0){
							$time=time();
						}
					}else{
						if($time==0){
							if((intval($row_com['vip_etime'])>time())){
								$time+=intval($row_com['vip_etime']);
							}else{
								$time=time();
							}
						}else{
							if((intval($row_com['vip_etime'])>time())){
								$time=intval($row_com['vip_etime']);
							}
						}
					}

					$time=$time+86400*intval($_POST['present_days']);
				}
			}
			$value.="`vip_etime`='".$time."'";
			$this->obj->DB_update_all("company_statis",$value,"`uid` in (".$_POST['uids'].")");
		}else{
			$value="`rating`='".$_POST['rating']."',";
			$value.="`rating_name`='".$row['name']."',";
			$value.="`rating_type`='".$row['type']."',";
			$value.="`lt_job_num`='".$row['lt_job_num']."',";
			$value.="`lt_down_resume`='".$row['lt_resume']."',";
			$value.="`lt_editjob_num`='".$row['lt_editjob_num']."',";
			$value.="`lt_breakjob_num`='".$row['lt_breakjob_num']."',";
			if($row['service_time']>0)
			{
				$time=time()+86400*$row['service_time'];
			}else{
				$time=0;
			}
			$value.="`vip_etime`='$time'";
			$this->obj->DB_update_all("lt_statis",$value,"`uid` in (".$_POST['uids'].")");
		}
		$this->write_appadmin_log("会员等级设置成功！");
		$data['error']='1';
		echo json_encode($data);die;
	}
}
?>