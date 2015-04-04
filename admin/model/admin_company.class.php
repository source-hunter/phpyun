<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class admin_company_controller extends common
{
	
	function set_search(){

		
		$rating=$this->obj->DB_select_all("company_rating","`category`='1' order by `sort` asc","`id`,`name`");
		if(!empty($rating)){
			foreach($rating as $k=>$v){
                 $ratingarr[$v['id']]=$v['name'];
			}
		}
		$nrating=array();
		foreach($rating as $val){
			$nrating[$val['id']]=$val['name'];
		}
		$this->yunset("rating", $nrating);
		$adtime=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$lotime=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$status=array('1'=>'已审核','2'=>'已锁定','3'=>'未审核');
		$edtime=array('1'=>'7天内','2'=>'一个月内','3'=>'半年内','4'=>'一年内');
		$this->yunset("adtime",$adtime);
		$this->yunset("lotime",$lotime);
		$this->yunset("status",$status);
		$this->yunset("edtime",$edtime);
		
		$search_list[]=array("param"=>"rec","name"=>'知名企业',"value"=>array("1"=>"是","2"=>"否"));
		$search_list[]=array("param"=>"status","name"=>'审核状态',"value"=>$status);
		$search_list[]=array("param"=>"rating","name"=>'会员等级',"value"=>$ratingarr);
		$search_list[]=array("param"=>"time","name"=>'到期时间',"value"=>$edtime);
		$search_list[]=array("param"=>"lotime","name"=>'最近登录',"value"=>$lotime);
		$search_list[]=array("param"=>"adtime","name"=>'最近注册',"value"=>$lotime);
		$this->yunset("ratingarr",$ratingarr);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
		$where=$mwhere="1";
		$uids=array();
		if($_GET['status']){
			if($_GET['status']=='3'){
				$mwhere.=" and `usertype`='2' and `status`='0'";
			}else if($_GET['status']){
				$mwhere.=" and `usertype`='2' and `status`='".intval($_GET['status'])."'";
			}
			$urlarr['status']=intval($_GET['status']);
		}
		if($_GET['rating']){
			$swhere="`rating`='".$_GET['rating']."'";
			$urlarr['rating']=$_GET['rating'];
		}
		if($_GET['time']){
            if($_GET['time']=='1'){
            	$num="+7 day"; 
            }elseif($_GET['time']=='2'){
				$num="+1 month"; 
            }elseif($_GET['time']=='3'){
				$num="+6 month"; 
            }elseif($_GET['time']=='4'){
                $num="+1 year"; 
            }
			if($swhere){
				$swhere.=" and `vip_etime`>'".time()."' and `vip_etime`<'".strtotime($num)."'";
			}else{
				$swhere.=" `vip_etime`>'".time()."' and `vip_etime`<'".strtotime($num)."'";
			}
			$urlarr['time']=$_GET['time'];
		}
		if($swhere){
			$list=$this->obj->DB_select_all("company_statis",$swhere,"`uid`,`pay`,`rating`,`rating_name`,`vip_etime`");
			foreach($list as $val){
				$uids[]=$val['uid'];
			}
			$where.=" and `uid` in (".@implode(',',$uids).")";
		}
		if($_GET['rec']){
       	   if($_GET['rec']=='1'){
 				$where.= "  and `rec`=1 ";
       	   }else{
 				$where.= "  and `rec`=0 ";
       	   }
			$urlarr['rec']=$_GET['rec'];
       }


	   if($_GET['hy']){
			$where .= " and `hy` = '".$_GET['hy']."' ";
			$urlarr['hy']=$_GET['hy'];
		}
	   if($_GET['provinceid']){
			$where .= " and `provinceid` = '".$_GET['provinceid']."' ";
			$urlarr['provinceid']=$_GET['provinceid'];
		}
		if($_GET['cityid']){
			$where .= " and `cityid` = '".$_GET['cityid']."' ";
			$urlarr['cityid']=$_GET['cityid'];
		}
		 if($_GET['pr']){
			$where .= " and `pr` = '".$_GET['pr']."' ";
			$urlarr['pr']=$_GET['pr'];
		}
		 if($_GET['mun']){
			$where .= " and `mun` = '".$_GET['mun']."' ";
			$urlarr['mun']=$_GET['mun'];
		}
	    if($_GET['keywords']){
			$where .= " and `name` like '%".$_GET['keywords']."%' ";
			$urlarr['keywords']=$_GET['keywords'];
		}
	   if(trim($_GET['keyword'])){
            if($_GET['com_type']=='1'){
				$where.= "  AND `name` like '%".$_GET['keyword']."%' ";
            }elseif($_GET['com_type']=='2'){
				$mwhere.=" and `username` like '%".$_GET['keyword']."%'";
            }elseif($_GET['com_type']=='3'){
				$where.= "  AND `linkman` like '%".$_GET['keyword']."%' ";
            }elseif($_GET['com_type']=='4'){
				$where.= "  AND `linktel` like '%".$_GET['keyword']."%' ";
            }elseif($_GET['com_type']=='5'){
				$where.= "  AND `linkmail` like '%".$_GET['keyword']."%' ";
            }
			$urlarr['com_type']=$_GET['com_type'];
			$urlarr['keyword']=$_GET['keyword'];
		}

		if($_GET['adtime']){
			if($_GET['adtime']=='1'){
				$mwhere .=" and `reg_date`>'".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$mwhere .=" and `reg_date`>'".strtotime('-'.intval($_GET['adtime']).' day')."'";
			}
			$urlarr['adtime']=$_GET['adtime'];
		}
		if($_GET['lotime']){
			if($_GET['lotime']=='1'){
				$mwhere .=" and `login_date`>'".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$mwhere .=" and `login_date`>'".strtotime('-'.intval($_GET['lotime']).' day')."'";
			}
			$urlarr['lotime']=$_GET['lotime'];
		}
		if($mwhere!='1'){
			$username=$this->obj->DB_select_all("member",$mwhere." and `usertype`='2'","`username`,`uid`,`reg_date`,`login_date`,`status`");
			$uids=array();
			foreach($username as $val){
				$uids[]=$val['uid'];
			}
			$where.=" and `uid` in (".@implode(',',$uids).")";
		}
		
		if($_GET['order'])
		{
			if($_GET['t']=="time")
			{
				$where.=" order by `lastupdate` ".$_GET['order'];
			}else{
				$where.=" order by ".$_GET['t']." ".$_GET['order'];
			}
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by `uid` desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("company",$where,$pageurl,$this->config['sy_listnum']);

 		if(is_array($rows)&&$rows){
			if($_GET['type']!='1'&&empty($username)){
				foreach($rows as $v){$uids[]=$v['uid'];}
				$username=$this->obj->DB_select_all("member","`uid` in (".@implode(",",$uids).")","`username`,`uid`,`reg_date`,`login_date`,`status`");
			}
			if(empty($list)){
				$list=$this->obj->DB_select_all("company_statis","`uid` in (".@implode(",",$uids).")","`uid`,`pay`,`integral`,`rating`,`rating_name`,`vip_etime`");
			}
 			foreach($rows as $k=>$v){
				foreach($username as $val){
					if($v['uid']==$val['uid']){
						$rows[$k]['username']=$val['username'];
						$rows[$k]['reg_date']=$val['reg_date'];
						$rows[$k]['login_date']=$val['login_date'];
						$rows[$k]['status']=$val['status'];
					}
				}
				foreach($list as $val){
					if($v['uid']==$val['uid']){
						$rows[$k]['rating']=$val['rating'];
						$rows[$k]['pay']=$val['pay'];
						$rows[$k]['rating_name']=$val['rating_name'];
						$rows[$k]['vip_etime']=$val['vip_etime'];
						$rows[$k]['integral']=$val['integral'];
					}
				}
			}
		}

		$nav_user=$this->obj->DB_select_alls("admin_user","admin_user_group","a.`m_id`=b.`id` and a.`uid`='".$_SESSION["auid"]."'");
		$power=unserialize($nav_user[0]["group_power"]);
		if(in_array('141',$power)){
			$this->yunset("email_promiss", '1');
		}
		if(in_array('163',$power)){
			$this->yunset("moblie_promiss", '1');
		}

		$where=str_replace(array("(",")"),array("[","]"),$where);
		$this->yunset("where", $where);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_company'));
	}

	function edit_action()
	{
		if((int)$_GET['id'])
		{
			$com_info = $this->obj->DB_select_once("member","`uid`='".$_GET['id']."'");
			$row = $this->obj->DB_select_once("company","`uid`='".$_GET['id']."'");
			$statis = $this->obj->DB_select_once("company_statis","`uid`='".$_GET['id']."'");
			$rating_list = $this->obj->DB_select_all("company_rating","`category`=1");
			$this->yunset("statis",$statis);
			$this->yunset("row",$row);
			$this->yunset("rating_list",$rating_list);
			$this->yunset("rating",$_GET['rating']);
			$this->yunset("lasturl",$_SERVER['HTTP_REFERER']);
			$this->yunset("com_info",$com_info);
			$CacheArr['com'] =array('comdata','comclass_name');
			$CacheArr['city'] =array('city_index','city_type','city_name');
			$CacheArr['industry'] =array('industry_index','industry_name');
			$this->CacheInclude($CacheArr);

		}
		if($_POST['com_update'])
		{
			$email=$_POST['email'];
			$uid=$_POST['uid'];
			$user = $this->obj->DB_select_once("member","`email`='$email' and `uid`<>'$uid'",'name');
			if(is_array($user)){
				$msg = "邮箱已存在！";
				$this->obj->ACT_layer_msg( $msg,8,$_SERVER['HTTP_REFERER'],2,1);
			}else{
				$this->obj->DB_update_all("company","`r_status`='".$_POST['status']."'","`uid`=".$_POST['uid']." ");

				if($_POST['status']=='2'){
					$mem = $this->obj->DB_select_once("member","`uid`=".$_POST['uid'],"`email`,`status`,`usertype`,`uid`");
					$smtp = $this->email_set();
					if($mem['status']!='2'){
						$data=$this->forsend($mem);
						$this->send_msg_email(array("email"=>$mem['email'],"lock_info"=>$_POST['lock_info'],"uid"=>$data['uid'],"name"=>$data['name'],"type"=>"lock"));
						$this->obj->DB_update_all("member","`lock_info`='".$_POST['lock_info']."'","`uid`='".$_POST['uid']."'");
					}

				}
				unset($_POST['com_update']);
				$ratingid = (int)$_POST['ratingid'];
				unset($_POST['ratingid']);
				$post['uid']=$_POST['uid'];
				$post['password']=$_POST['password'];
				$post['email']=$_POST['email'];
				$post['moblie']=$_POST['moblie'];
				$post['status']=$_POST['status'];
				$post['address']=$_POST['address'];
				$nid = $this->uc_edit_pw($post,1,"index.php?m=com_member");
				$value.="`name`='".$_POST['name']."',";
				$value.="`hy`='".$_POST['hy']."',";
				$value.="`pr`='".$_POST['pr']."',";
				$value.="`provinceid`='".$_POST['provinceid']."',";
				$value.="`cityid`='".$_POST['cityid']."',";
				$value.="`mun`='".$_POST['mun']."',";
				$value.="`linkphone`='".$_POST['linkphone']."',";
				$value.="`linktel`='".$_POST['moblie']."',";
				$value.="`money`='".$_POST['money']."',";
				$value.="`zip`='".$_POST['zip']."',";
				$value.="`linkman`='".$_POST['linkman']."',";
				$value.="`linkjob`='".$_POST['linkjob']."',";
				$value.="`linkqq`='".$_POST['linkqq']."',";
				$value.="`website`='".$_POST['website']."',";
				$content=str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'','',''),html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
				$value.="`content`='".$content."',";
				$value.="`admin_remark`='".$_POST['admin_remark']."',";
				$value.="`linkmail`='".$_POST['email']."'";

				$this->obj->DB_update_all("company",$value,"`uid`='".$_POST['uid']."'");
				$rat_arr = @explode("+",$rating_name);
				$statis = $this->obj->DB_select_once("company_statis","`uid`='".$_POST['uid']."'");
				if($ratingid != $statis['rating'])
				{
					$rat_value=$this->rating_info($ratingid);
					$this->obj->DB_update_all("company_statis",$rat_value,"`uid`='".$_POST['uid']."'");
				}else{
					if($_POST['vip_etime']){
						$value3.="`vip_etime`='".strtotime($_POST['vip_etime'])."',";
					}else{
						$value3.="`vip_etime`='0',";
					}
					$value3.="`job_num`='".$_POST['job_num']."',";
					$value3.="`down_resume`='".$_POST['down_resume']."',";
					$value3.="`editjob_num`='".$_POST['editjob_num']."',";
					$value3.="`invite_resume`='".$_POST['invite_resume']."',";
					$value3.="`breakjob_num`='".$_POST['breakjob_num']."'";
					$this->obj->DB_update_all("company_statis",$value3,"`uid`='".$_POST['uid']."'");
				}
				$value2.="`com_name`='".$_POST['name']."',";
				$value2.="`pr`='".$_POST['pr']."',";
				$value2.="`mun`='".$_POST['mun']."',";
				$value2.="`com_provinceid`='".$_POST['provinceid']."',";
				$value2.="`rating`='".$rat_arr[0]."',";
				$value2.="`r_status`='".$_POST['status']."'";
				$this->obj->DB_update_all("company_job",$value2,"`uid`='".$_POST['uid']."' ");
				$lasturl=str_replace("&amp;","&",$_POST['lasturl']);
				$this->obj->ACT_layer_msg( "企业会员(ID:".$_POST['uid'].")修改成功！",9,$lasturl,2,1);
			}
		}
		$this->yuntpl(array('admin/admin_member_comedit'));
	}
	function rating_action(){
		$ratingid = (int)$_POST['ratingid'];
		$statis = $this->obj->DB_select_all("company_statis","`uid`='".$_POST['uid']."'");
		if(is_array($statis))
		{
			$value=$this->rating_info($ratingid);
			$this->obj->DB_update_all("company_statis",$value,"`uid`='".$_POST['uid']."'");
			$this->obj->admin_log("企业会员(ID".$_POST['uid'].")更新会员等级");
		}else{
			$value="`uid`='".$_POST['uid']."',";
			$value.=$this->rating_info($ratingid);
			$this->obj->DB_insert_once("company_statis",$value);
			$this->obj->admin_log("企业会员(ID".$_POST['uid'].")添加会员等级");
		}
		echo "1";die;
	}
	function add_action(){
		$rating_list = $this->obj->DB_select_all("company_rating","`category`=1");
		if($_POST['submit'])
		{
			extract($_POST);
			if($username==""||strlen($username)<2||strlen($username)>15)
			{
				$msg = "会员名不能为空或不符合要求！";
			}elseif($password==""||strlen($username)<2||strlen($username)>15){
				$msg = "密码不能为空或不符合要求！";
			}elseif($email==""){
				$msg = "email不能为空！";
			}else{
				if($this->config['sy_uc_type']=="uc_center"){
					$this->obj->uc_open();
					$user = uc_get_user($username);
				}else{
					$user = $this->obj->DB_select_once("member","`username`='$username' OR `email`='$email'");
				}
				if(is_array($user))
				{
					$msg = "用户名或邮箱已存在！";
				}else{
					$ip = $this->obj->fun_ip_get();
					$time = time();
					if($this->config['sy_uc_type']=="uc_center")
					{
						$uid=uc_user_register($_POST['username'],$_POST['password'],$_POST['email']);
						if($uid<0)
						{
							$this->obj->get_admin_msg("index.php?m=com_member&c=add","该邮箱已存在！");
						}else{
							list($uid,$username,$email,$password,$salt)=uc_get_user($username);
							$value = "`username`='$username',`password`='$password',`email`='$email',`usertype`='2',`address`='$address',`status`='$status',`salt`='$salt',`moblie`='$moblie',`reg_date`='$time',`reg_ip`='$ip'";
						}
					}else{
						$salt = substr(uniqid(rand()), -6);
						$pass = md5(md5($password).$salt);
						$value = "`username`='$username',`password`='$pass',`email`='$email',`usertype`='2',`address`='$address',`status`='$status',`salt`='$salt',`moblie`='$moblie',`reg_date`='$time',`reg_ip`='$ip'";
					}
					$nid = $this->obj->DB_insert_once("member",$value);
					$new_info = $this->obj->DB_select_once("member","`username`='$username'");
					$uid = $new_info['uid'];
					if($uid>0)
					{
						$this->obj->DB_insert_once("company","`uid`='$uid',`name`='$name',`linktel`='$moblie',`linkmail`='$email',`address`='$address'");
						$rat_arr = @explode("+",$rating_name);
						$value = "`uid`='$uid',";
						$value.=$this->rating_info($rat_arr[0]);
						$this->obj->DB_insert_once("company_statis",$value);
						$this->obj->DB_insert_once("friend_info","`uid`='$uid',`nickname`='$name',`usertype`='2'");
						$msg="会员(ID:".$uid.")添加成功";
					}
				}
			}
			if($_POST['type']){
				echo "<script type='text/javascript'>window.location.href='index.php?m=admin_company_job&c=show&uid=".$nid."'</script>";die;
			}else{
				$this->obj->ACT_layer_msg($msg,9,"index.php?m=admin_company",2,1);
			}

		}
		$this->yunset("get_info",$_GET);
		$this->yunset("rating_list",$rating_list);
		$this->yuntpl(array('admin/admin_member_comadd'));
	}
	function rating_info($id)
	{

		$row = $this->obj->DB_select_once("company_rating","`id`='".$id."'");
		$value="`rating`='$id',";
		$value.="`integral`='".$this->config['integral_reg']."',";
		$value.="`rating_name`='".$row['name']."',";
		$value.="`job_num`='".$row['job_num']."',";
		$value.="`down_resume`='".$row['resume']."',";
		$value.="`invite_resume`='".$row['interview']."',";
		$value.="`editjob_num`='".$row['editjob_num']."',";
		$value.="`breakjob_num`='".$row['breakjob_num']."',";
		$value.="`rating_type`='".$row['type']."',";
		if($row['service_time']>0)
		{
			$time=time()+86400*$row['service_time'];
		}else{
			$time=0;
		}
		$value.="`vip_etime`='".$time."'";
		return $value;
	}
	function getstatis_action(){

		if($_POST['uid'])
		{
			$rating	= $this->obj->DB_select_once("company_statis","`uid`='".intval($_POST['uid'])."'","`rating`,`job_num`,`down_resume`,`editjob_num`,`invite_resume`,`breakjob_num`,`vip_etime`,`pay`,`integral`");
			if($rating['vip_etime']>0)
			{
				$rating['vipetime'] = date("Y-m-d",$rating['vip_etime']);
			}else{
				$rating['vipetime'] = iconv('gbk','utf-8','不限');
			}

			echo json_encode($rating);
		}
	}
	function getrating_action(){

		if($_POST['id'])
		{
			$rating	= $this->obj->DB_select_once("company_rating","`id`='".intval($_POST['id'])."'","`resume`,`job_num`,`interview`,`editjob_num`,`breakjob_num`,`service_time`,`coupon`,`category`,`type`");
			$rating['oldetime'] = time()+$rating['service_time']*86400;
			$rating['vipetime'] = date("Y-m-d",(time()+$rating['service_time']*86400));

			echo json_encode($rating);
		}
	}
	function uprating_action(){

		 if($_POST['ratuid']){

			$uid = intval($_POST['ratuid']);
			$statis = $this->obj->DB_select_once("company_statis","`uid`='".$uid."'");

			unset($_POST['ratuid']);unset($_POST['pytoken']);
			if((int)$_POST['addday']>0)
			{
				if((int)$_POST['oldetime']>0)
				{
					$_POST['vip_etime'] = intval($_POST['oldetime'])+intval($_POST['addday'])*86400;
				}else{
					$_POST['vip_etime'] = time()+intval($_POST['addday'])*86400;
				}
			}else{
				$_POST['vip_etime'] = intval($_POST['oldetime']);
			}
			unset($_POST['addday']);
			unset($_POST['oldetime']);

			foreach($_POST as $key=>$value){

				$statisValue[] = "`$key`='$value'";
			}
			$statisSqlValue = @implode(',',$statisValue);
			if($statis['rating'] != $_POST['rating'])
			{
				$statisSqlValue.=",`vip_stime`='".time()."'";
			}
			$id = $this->obj->DB_update_all("company_statis",$statisSqlValue,"`uid`='".$uid."'");
			if($statis['rating'] != $_POST['rating'])
			{
				$this->obj->DB_update_all("company_job","`rating`='".$_POST['rating']."'","`uid`='".$uid."'");
			}
			$id?$this->obj->ACT_layer_msg("企业会员等级(ID:".$aid.")修改成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("修改失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg( "非法操作！",8,$_SERVER['HTTP_REFERER']);
		}

	}
	function recommend_action(){
		$nid=$this->obj->DB_update_all("company","`".$_GET['type']."`='".$_GET['rec']."'","`uid`='".$_GET['id']."'");
		$this->obj->admin_log("知名企业(ID:".$_GET['id'].")设置成功");
		echo $nid?1:0;die;
	}
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
	    			$layer_type=1;
	    			$uids = @implode(",",$del);
	    			foreach($del as $k=>$v){
	    				$this->obj->delfiledir("../upload/tel/".intval($v));
	    			}
				    $company=$this->obj->DB_select_all("company","`uid` in (".$uids.") and `logo`!=''","logo,firmpic");
				    if(is_array($company)){
				    	foreach($company as $v){
				    		$this->obj->unlink_pic(".".$v['logo']);
				    		$this->obj->unlink_pic(".".$v['firmpic']);
				    	}
				    }
		    	    $cert=$this->obj->DB_select_all("company_cert","`uid` in (".$uids.") and `type`='3'","check");
		    	    if(is_array($cert)){
				    	foreach($cert as $v){
				    		$this->obj->unlink_pic("../".$v['check']);
				    	}
				    }
		    	    $product=$this->obj->DB_select_all("company_product","`uid` in (".$uids.")","pic");
		    	    if(is_array($product)){
		    	    	foreach($product as $val){
		    	    		$this->obj->unlink_pic("../".$val['pic']);
		    	    	}
		    	    }
		    	    $show=$this->obj->DB_select_all("company_show","`uid` in (".$uids.")","picurl");
		    	    if(is_array($show)){
		    	    	foreach($show as $val){
		    	    		$this->obj->unlink_pic("../".$val['picurl']);
		    	    	}
		    	    }
		    	    $uhotjob=$this->obj->DB_select_all("hotjob","`uid` in (".$uids.")","hot_pic");
		    	    if(is_array($uhotjob)){
		    	    	foreach($uhotjob as $val){
		    	    		$this->obj->unlink_pic("../".$val['hot_pic']);
		    	    	}
		    	    }
		    	  	$banner=$this->obj->DB_select_all("banner","`uid` in (".$uids.")","pic");
		    	    if(is_array($banner)){
		    	    	foreach($banner as $val)
		    	    	{
		    	    		$this->obj->unlink_pic($val['pic']);
		    	    	}
		    	    }
		    	    $friend_pic = $this->obj->DB_select_all("friend_info","`uid` in (".$uids.") and `pic`!=''","pic,pic_big");
		    		if(is_array($friend_pic))
		    		{
		    	    	foreach($friend_pic as $val)
		    	    	{
		    	    		$this->obj->unlink_pic($val['pic']);
		    	    		$this->obj->unlink_pic($val['pic_big']);
		    	    	}
		    		}
					$del_array=array("member","company","company_job","company_cert","company_news","company_order","company_product","company_show","banner","company_statis","friend_info","friend_state","question","attention","hotjob");
					foreach($del_array as $value)
					{
						$this->obj->DB_delete_all($value,"`uid` in (".$uids.")","");
					}
		    	    $this->obj->DB_delete_all("company_pay","`com_id` in (".$uids.")"," ");
					$this->obj->DB_delete_all("atn","`uid` in (".$uids.") or `scid` in (".$uids.")","");
					$this->obj->DB_delete_all("look_resume","`com_id` in (".$uids.")","");
					$this->obj->DB_delete_all("fav_job","`com_id` in (".$uids.")","");
					$this->obj->DB_delete_all("userid_msg","`fid` in (".$uids.")","");
					$this->obj->DB_delete_all("userid_job","`com_id` in (".$uids.")","");
					$this->obj->DB_delete_all("look_job","`com_id` in (".$uids.")","");
					$this->obj->DB_delete_all("message","`fa_uid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("friend_reply","`fid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("friend","`uid` in (".$uids.") or `nid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("friend_foot","`uid` in (".$uids.") or `fid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("friend_message","`uid`='".$del."' or `fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("msg","`job_uid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("blacklist","`c_uid` in (".$uids.")","");
		    	    $this->obj->DB_delete_all("report","`p_uid` in ($uids) or `c_uid` in ($uids)","");
		    	}else{
		    		$layer_type=0;
					$uids=$del = intval($del);
					$uids=$del;
		    		$friend_pic = $this->obj->DB_select_once("friend_info","`uid`='".$del."' and `pic`!=''","pic,pic_big");
		    		if(is_array($friend_pic)){
		    			$this->obj->unlink_pic($friend_pic['pic']);
		    			$this->obj->unlink_pic($friend_pic['pic_big']);
		    		}
		    		$this->obj->delfiledir("../upload/tel/".$del);
				    $company=$this->obj->DB_select_once("company","`uid`='".$del."' and `logo`!=''","logo,firmpic");
				    $this->obj->unlink_pic(".".$company['logo']);
				    $this->obj->unlink_pic(".".$company['firmpic']);
		    	    $cert=$this->obj->DB_select_once("company_cert","`uid`='".$del."' and `type`='3'","check");
		    	    $this->obj->unlink_pic("../".$cert['check']);
		    	    $product=$this->obj->DB_select_all("company_product","`uid`='".$del."'","pic");
		    	    if(is_array($product))
		    	    {
		    	    	foreach($product as $v)
		    	    	{
		    	    		$this->obj->unlink_pic("../".$v['pic']);
		    	    	}
		    	    }
		    	    $show=$this->obj->DB_select_all("company_show","`uid`='".$del."'","picurl");
		    	    if(is_array($show))
		    	    {
		    	    	foreach($show as $v)
		    	    	{
		    	    		$this->obj->unlink_pic("../".$v['picurl']);
		    	    	}
		    	    }
			    	$uhotjob=$this->obj->DB_select_all("hotjob","`uid`='".$del."'","hot_pic");
		    	    if(is_array($uhotjob))
		    	    {
		    	    	foreach($uhotjob as $val)
		    	    	{
		    	    		$this->obj->unlink_pic("../".$val['hot_pic']);
		    	    	}
		    	    }
		    	    $banner=$this->obj->DB_select_once("banner","`uid`='".$del."'","pic");
					$this->obj->unlink_pic($banner['pic']);
					$del_array=array("member","company","company_job","company_cert","company_news","company_order","company_product","company_show","banner","company_statis","friend_info","friend_state","question","attention","hotjob");
					foreach($del_array as $value)
					{
						$this->obj->DB_delete_all($value,"`uid`='".$del."'","");
					}
					$this->obj->DB_delete_all("company_pay","`com_id`='".$del."'"," ");
		    	    $this->obj->DB_delete_all("atn","`uid`='".$del."' or `scid`='".$del."'","");
		    	    $this->obj->DB_delete_all("look_resume","`com_id`='".$del."'","");
		    	    $this->obj->DB_delete_all("look_job","`com_id`='".$del."'","");
		    	    $this->obj->DB_delete_all("fav_job","`com_id`='".$del."'","");
		    	    $this->obj->DB_delete_all("userid_msg","`fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("userid_job","`com_id`='".$del."'","");
		    	    $this->obj->DB_delete_all("message","`fa_uid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend","`uid`='".$del."' or `nid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend_foot","`uid`='".$del."' or `fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend_message","`uid`='".$del."' or `fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("friend_reply","`fid`='".$del."'","");
		    	    $this->obj->DB_delete_all("msg","`job_uid`='".$del."'","");
		    	    $this->obj->DB_delete_all("blacklist","`c_uid`='".$del."'","");
		    	    $this->obj->DB_delete_all("report","`p_uid`='".$del."' or `c_uid`='".$del."'");
		    	}
	    		$this->layer_msg( "公司(ID:".$uids.")删除成功！",9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg( "请选择您要删除的公司！",8,1);
	    	}
	    }
	}
	function lockinfo_action(){
		$userinfo = $this->obj->DB_select_once("member","`uid`=".$_POST['uid'],"`lock_info`");
		echo $userinfo['lock_info'];die;
	}
	function lock_action(){
		$_POST['uid']=intval($_POST['uid']);
		$email=$this->obj->DB_select_alls("member","company","a.`uid`='".$_POST['uid']."' and a.`uid`=b.`uid`","a.`email`,b.`name`");
		$this->obj->DB_update_all("company_job","`r_status`='".$_POST['status']."'","`uid`='".$_POST['uid']."'");
		$this->obj->DB_update_all("company","`r_status`='".$_POST['status']."'","`uid`='".$_POST['uid']."'");
		$id=$this->obj->DB_update_all("member","`status`='".$_POST['status']."',`lock_info`='".$_POST['lock_info']."'","`uid`='".$_POST['uid']."'");
		$this->send_msg_email(array("email"=>$email[0]['email'],"uid"=>$_POST['uid'],"name"=>$email[0]['name'],"lock_info"=>$_POST['lock_info'],"type"=>"lock"));
		$id?$this->obj->ACT_layer_msg("企业会员(ID:".$_POST['uid'].")锁定设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "设置失败！",8,$_SERVER['HTTP_REFERER']);
	}
	function status_action(){
		 extract($_POST);
		 $id = @explode(",",$uid);
		 $member=$this->obj->DB_select_all("member","`uid` in (".$uid.")","`email`,`uid`");
		 $smtp = $this->email_set();
		 if(is_array($member)&&$member){
			 $company=$this->obj->DB_select_all("company","`uid` in (".$uid.")","`name`,`uid`");
			 $info=array();
			foreach($company as $val){
				$info[$val['uid']]=$val['name'];
			}
			foreach($member as $v){
				$idlist[] =$v['uid'];
				$this->send_msg_email(array("uid"=>$v['uid'],"name"=>$info[$v['uid']],"email"=>$v['email'],"status_info"=>$statusbody,"date"=>date("Y-m-d H:i:s"),"type"=>"userstatus"));
			}
			if(trim($statusbody)){
				$lock_info=$statusbody;
			}
			$aid = @implode(",",$idlist);
			$id=$this->obj->DB_update_all("member","`status`='".$status."',`lock_info`='".$lock_info."'","`uid` IN (".$aid.")");
			$id?$this->obj->ACT_layer_msg("企业会员审核(ID:".$aid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("审核设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg( "非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}

	function hotjobinfo_action(){
		if($_GET['id']){
			$hotjob=$this->obj->DB_select_once("hotjob","`uid`='".(int)$_GET['id']."'");
		}else if($_GET['uid']){
			$row = $this->obj->DB_select_alls("company","company_statis","a.`uid`='".(int)$_GET['uid']."' and b.`uid`='".(int)$_GET['uid']."'","a.`content`,a.`name` as username,b.`rating_name` as rating,a.`uid`,a.`logo` as hot_pic");
			$row=$row[0];
			$row['content']=@explode(' ',trim(strip_tags($row['content'])));
			if(is_array($row['content'])&&$row['content']){
				foreach($row['content'] as $val){
					$row['beizhu'].=trim($val);
				}
			}else{
				$row['beizhu']=$row['content'];
			}
			$hotjob=$row;
			$hotjob['time_start']=time();
		}
		$this->yunset("hotjob",$hotjob);
		$this->yuntpl(array('admin/admin_hotjob_info'));
	}

	function save_action(){
		extract($_POST);
		if(is_uploaded_file($_FILES['hot_pic']['tmp_name'])){
			$upload=$this->upload_pic("../upload/hotpic/");
			$pictures=$upload->picture($_FILES['hot_pic']);
			$pic = str_replace("../upload","upload",$pictures);
		}else{
			if($_POST['hotad']){
				$defpic=".".$defpic;
				$url=@explode("/",$defpic);
				$url2=str_replace($url[4],time().".jpg",$defpic);
				copy($defpic,$url2);
				$pic = str_replace("../upload","upload",$url2);
			}
		}
		if($_POST['hotad']){
			$start = strtotime($time_start);
			$end = strtotime($time_end);
			$nid=$this->obj->DB_insert_once("hotjob","`uid`='$uid',`username`='$username',`sort`='$sort',`rating`='$rating',`hot_pic`='$pic',`service_price`='$service_price',`beizhu`='$beizhu',`time_start`='$start',`time_end`='$end'");
			$this->obj->DB_update_all("company","`hottime`='".$end."',`rec`='1'","`uid`='".$uid."'");
			$this->obj->ACT_layer_msg("名企招聘(ID:".$nid.")设定成功！",9,"index.php?m=admin_company",2,1);
		}elseif($_POST['hotup']){
			$start = strtotime($time_start);
			$end = strtotime($time_end);
			$value = "`service_price`='$service_price',`time_start`='$start',`time_end`='$end',`beizhu`='$beizhu',`sort`='$sort'";
			if($pic!=""){
				$hot=$this->obj->DB_select_once("hotjob","`id`='$id'");
				$this->obj->unlink_pic("../".$hot['hot_pic']);
				$value.=",`hot_pic`='$pic'";
			}
			$this->obj->DB_update_all("hotjob",$value,"`id`='$id'");
			$this->obj->DB_update_all("company","`hottime`='".$end."'","`uid`='".$uid."'");
			$this->obj->ACT_layer_msg("名企招聘(ID:".$id.")修改成功！",9,"index.php?m=admin_company",2,1);
		}
		$this->yuntpl(array('admin/admin_hotjob_add'));
	}
	function delhot_action(){
		$this->check_token();
	    if(isset($_GET['id'])){
	    	$hot=$this->obj->DB_select_once("hotjob","`uid`='".$_GET['id']."'");
			$this->obj->unlink_pic("../".$hot['hot_pic']);
			$result=$this->obj->DB_delete_all("hotjob","`uid`='".$_GET['id']."'" );
			if($result){
				$this->obj->DB_update_all("company","`hottime`='',`rec`='0'","`uid`='".$hot['uid']."'");
				$this->layer_msg('名企招聘(ID:'.$_GET['id'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function changeorder_action(){
		if($_POST['uid']){
			if(!$_POST['order']){
				$_POST['order']='0';
			}
			$this->obj->DB_update_all("company","`order`='".$_POST['order']."'","`uid`='".$_POST['uid']."'");
			$this->obj->admin_log("公司(ID:".$_POST['uid'].")排序设置成功");
		}
		die;
	}
	
	function Imitate_action(){
		extract($_GET);
		$user_info = $this->obj->DB_select_once("member","`uid`='".$uid."'");
		$this->unset_cookie();
		$this->add_cookie($user_info['uid'],$user_info['username'],$user_info['salt'],$user_info['email'],$user_info['password'],$user_info['usertype']);

		header('Location: '.$this->config['sy_weburl'].'/member');
	}

}
?>