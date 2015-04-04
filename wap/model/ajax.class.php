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
class ajax_controller extends common{

	function index_ajaxresume_action()
	{
		if($_POST['show_job'])
		{
			$company_job=$this->obj->DB_select_all("company_job","`uid`='".$this->uid."' and `state`='1'","`name`,`id`");
			if($company_job&&is_array($company_job))
			{
				foreach($company_job as $val)
				{
					$html.="<option value='".iconv("gbk","utf-8",$val['name'])."+".$val['id']."'>".iconv("gbk","utf-8",$val['name'])."</option>";
				}
				$arr['html']=$html;
			}else{
				$arr['status']=5;
			}
		}
		if($arr['status']=='')
		{
			$company_yq=$this->obj->DB_select_once("userid_msg","`fid`='".$this->uid."'  order by `id` desc");
			if(!$company_yq['linkman'] || !$company_yq['linktel'] || !$company_yq['address'])
			{
				$company = $this->obj->DB_select_once("company","`uid`='".$this->uid."'","`linkman`,`linktel`,`linkphone`,`address`");
				if(!$company_yq['linkman'])
				{
					$company_yq['linkman'] = $company['linkman'];
				}
				if(!$company_yq['address'])
				{
					$company_yq['address'] = $company['address'];
				}
				if(!$company_yq['linktel'])
				{
					if($company['linktel'])
					{
						$company_yq['linktel'] = $company['linktel'];
					}else{
						$company_yq['linktel'] = $company['linkphone'];
					}
				}
			}
			$arr['linkman']=iconv("gbk","utf-8",$company_yq['linkman']);
			$arr['linktel']=iconv("gbk","utf-8",$company_yq['linktel']);
			$arr['content']=iconv("gbk","utf-8",$company_yq['content']);
			$arr['address']=iconv("gbk","utf-8",$company_yq['address']);
			$arr['intertime']=iconv("gbk","utf-8",$company_yq['intertime']);



			$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`rating`,`vip_etime`,`invite_resume`,`rating_type`");
			if($row['rating']==0)
			{
				$arr['status']=1;
				$arr['integral']=$this->config['integral_interview'];
			}else{
				if($row['vip_etime']>time() || $row['vip_etime']=="0")
				{
					if($row['rating_type']=="1")
					{
						if($row['invite_resume']==0)
						{
							if($this->config['com_integral_online']=="1")
							{
								$arr['status']=2;
								$arr['integral']=$this->config['integral_interview'];
							}else{
								$arr['status']=4;
							}
						}else{
							$arr['status']=3;
						}
					}else{
						$arr['status']=3;
					}
				}else{
					if($this->config['com_integral_online']=="1")
					{
						$arr['status']=2;
						$arr['integral']=$this->config['integral_interview'];
					}else{
						$arr['status']=4;
					}
				}
			}

		}
		echo json_encode($arr);die;
	}

	function sava_ajaxresume_action()
	{
		$_POST['uid'] = intval($_POST['uid']);
		$data['uid']=$_POST['uid'];
		$data['title']='面试邀请';
		$data['content']=iconv("utf-8","gbk",$_POST['content']);
		$data['fid']=$this->uid;
		$data['datetime']=time();
		$data['address']=iconv("utf-8","gbk",$_POST['address']);
		$data['intertime']=iconv("utf-8","gbk",$_POST['intertime']);
		$data['linkman']=iconv("utf-8","gbk",$_POST['linkman']);
		$data['linktel']=iconv("utf-8","gbk",$_POST['linktel']);
		$data['jobname']=iconv("utf-8","gbk",$_POST['jobname']);
		$data['jobid']	=iconv("utf-8","gbk",$_POST['jobid']);

		$info['jobname']=iconv("utf-8","gbk",$_POST['jobname']);
		$info['username']=iconv("utf-8","gbk",$_POST['username']);

		$info['content']=$data['content'];
        $p_uid=$_POST['uid'];
		$black=$this->obj->DB_select_once("blacklist","`p_uid`='".$p_uid."' and `c_uid`='".$this->uid."'");
		if(!empty($black))
		{
			$arr['status']=8;
			echo json_encode($arr);die;
		}
		$black=$this->obj->DB_select_once("blacklist","`c_uid`='".$p_uid."' and `p_uid`='".$this->uid."'");
		if(!empty($black))
		{
			$arr['status']=9;
			echo json_encode($arr);die;
		}
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=2)
		{
			$arr['status']=0;
			echo json_encode($arr);die;
		}else{
			$umessage = $this->obj->DB_select_once("userid_msg","`uid`='".$p_uid."' AND `fid`='".$this->uid."'");
			if(is_array($umessage))
			{
			 	$arr['status']=7;
			}else{
				$com=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","name");
				$resume=$this->obj->DB_select_once("resume","`uid`='".$p_uid."'","name");
				$data['fname']=$com['name'];
				if($_POST['update_yq']=='1')
				{
					$data['default']=1;
				}else{
					$this->obj->DB_update_all("userid_msg","`default`='0'","`fid`='".$this->uid."'");
				}
				$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`rating`,`vip_etime`,`integral`,`invite_resume`,`rating_type`");
				if($row['rating']==0)
				{
			
					if($row['integral']<$this->config['integral_interview'] && $this->config['integral_interview_type']=="2")
					{
						$arr['status']=5;
						$arr['integral']=$row["integral"];
					}else{
						$this->obj->insert_into("userid_msg",$data);
						if($this->config['integral_interview_type']=="1")
						{
							$auto=true;
						}else{
							$auto=false;
						}
						$this->obj->company_invtal($this->uid,$this->config['integral_interview'],$auto,"邀请会员面试",true,2,'integral',14);
						$this->msg_post($_POST['uid'],$this->uid,$info);
						$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=".$_POST[eid]."\" target=\"_blank\">".$resume['name']."</a> 面试。";
						$this->addstate($state_content);
						$arr['status']=3;
						$this->obj->member_log("邀请了人才：".$resume['name'],4);
					}
				}else{
					if($row['vip_etime']>time() || $row['vip_etime']=="0")
					{
						if($row['rating_type']=="1")
						{
							if($row['invite_resume']==0){
								if($row['integral']<$this->config['integral_interview'] && $this->config['integral_interview_type']=="2")
								{
									$arr['status']=5;
									$arr['integral']=$row['integral'];
								}else{
									$this->obj->insert_into("userid_msg",$data);
									if($this->config['integral_interview_type']=="1")
									{
										$auto=true;
									}else{
										$auto=false;
									}
									$this->obj->company_invtal($this->uid,$this->config['integral_interview'],$auto,"邀请会员面试",true,2,'integral',14);
									$this->msg_post($_POST['uid'],$this->uid,$info);
									$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".$resume['name']."</a> 面试。";
									$this->addstate($state_content);
									$arr['status']=3;
									$this->obj->member_log("邀请了人才：".$resume['name'],4);
								}
							}else{
								
								$this->obj->insert_into("userid_msg",$data);
								$this->obj->DB_update_all("company_statis","`invite_resume`=`invite_resume`-1","uid='".$this->uid."'");
								$this->msg_post($_POST['uid'],$this->uid,$info);
								$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".$resume['name']."</a> 面试。";
								$this->addstate($state_content);
								$arr['status']=3;
								$this->obj->member_log("邀请了人才：".$resume['name'],4);
							}
						}else{
							$this->obj->insert_into("userid_msg",$data);
							$this->msg_post($_POST['uid'],$this->uid,$info);
							$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".$resume['name']."</a> 面试。";
							$this->addstate($state_content);
							$arr['status']=3;
							$this->obj->member_log("邀请了人才：".$resume['name'],4);
						}

					}else{
						if($row['integral']<$this->config['integral_interview'] && $this->config['integral_interview_type']=="2")
						{
							$arr['status']=5;
							$arr['integral']=$row['integral'];
						}else{
							$this->obj->insert_into("userid_msg",$data);
							if($this->config['integral_interview_type']=="1")
							{
								$auto=true;
							}else{
								$auto=false;
							}
							$this->obj->company_invtal($this->uid,$this->config['integral_interview'],$auto,"邀请会员面试",true,2,'integral',14);
							$this->msg_post($_POST['uid'],$this->uid,$info);
							$state_content = "我刚邀请了人才 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".$resume['name']."</a> 面试。";
							$this->addstate($state_content);
							$arr['status']=3;
							$this->obj->member_log("邀请了人才：".$resume['name'],4);
						}
					}
				}
				if($arr['status']=='3')
				{
					$title = '新面试邀请-'.$this->config['sy_webname'];
					$description = '面试职位：'.$data['jobname'].'\n面试公司：'.$data['fname'].'\n面试时间：'.$data['intertime'].'\n联系人	：'.$data['linkman'].'\n联系电话：'.$data['linktel'].'\n面试地址：'.$data['address'].'\n'.$data['content'].'\n';
					$url   = $this->config['sy_weburl'].'/wap';
					$picurl=$this->config['sy_weburl'].'/'.$this->config['sy_wxlogo'];
					$this->wx_message($_POST['uid'],$title,$description,$url,$picurl);
				}
			}
		}
		echo json_encode($arr);
	}
	function msg_post($uid,$comid,$row=''){
		$com=$this->obj->DB_select_once("company","`uid`='".$comid."'","`uid`,`name`,`linkman`,`linktel`,`linkmail`");
		$info=$this->obj->DB_select_alls("member","resume","a.`uid`='".$uid."' and a.`uid`=b.`uid`","a.`email`,a.`moblie`,b.`name`");
		$info=$info[0];
		$data['uid']=$uid;
		$data['name']=$info['name'];
		$data['cuid']=$com['uid'];
		$data['cname']=$com['name'];
		$data['type']="yqms";
		$data['company']=$com['name'];
		$data['linkman']=$com['linkman'];
		$data['comtel']=$com['linktel'];
		$data['comemail']=$com['linkmail'];
		$data['content']=@str_replace("\n","<br/>",$row['content']);
		$data['jobname']=$row['jobname'];
		$data['username']=$row['username'];
		if($this->config['sy_msg_type']=="0"){
			$row=$this->obj->DB_select_once("company_statis","`uid`='".$comid."'","msg_num");
			if($row['msg_num']>0){
				$data['moblie']=$info['moblie'];
				$msg=$this->send_msg_email($data);
				unset($data['moblie']);
				if($msg=="发送成功!"){
					$this->obj->DB_update_all("company_statis","`msg_num`=`msg_num`-1","`uid`='".$comid."'");
				}
			}
			$data['email']=$info['email'];
			$this->send_msg_email($data);
		}else{
			$data['email']=$info['email'];
			$data['moblie']=$info['moblie'];
			$this->send_msg_email($data);
		}
	}
	function for_link_action()
	{
		$eid=(int)$_POST['eid'];
		if(!$this->uid || !$this->username || $_COOKIE['usertype']==1){
			if(!$this->uid || !$this->username){
				$arr['status']=1;
				$arr['msg']="请先登录！";
			}else if($this->usertype=='1'){
				$arr['status']=1;
				$arr['msg']="您不是企业账户，无法下载简历！";
			}
		}else{
			$resume=$this->obj->DB_select_once("down_resume","`eid`='".$eid."' and `comid`='".$this->uid."'");
			$user=$this->obj->DB_select_alls("resume","resume_expect","a.`r_status`<>'2' and a.`uid`=b.`uid` and b.`id`='".$eid."'","a.name,a.telphone,a.telhome,a.email,a.uid,b.id,b.`height_status`");
			$user=$user[0];
			$black=$this->obj->DB_select_once("blacklist","`p_uid`='".$user['uid']."' and `c_uid`='".$this->uid."'");
			if(!empty($black))
			{
				$arr['status']=1;
				$arr['msg']="该用户已被您列入黑名单！";
			}
			$black=$this->obj->DB_select_once("blacklist","`c_uid`='".$user['uid']."' and `p_uid`='".$this->uid."'");
			if(!empty($black))
			{
				$arr['status']=1;
				$arr['msg']="您已被该用户列入黑名单！";
			}
			$userid_job=$this->obj->DB_select_once("userid_job","`com_id`='".$this->uid."' and `eid`='".$eid."'");
			if((!empty($resume)||!empty($userid_job))&&$arr['status']==''){
				$arr['status']=3;
			}else if($arr['status']==''){
				$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","vip_etime,down_resume,rating_type");
				$data['eid']=$user['id'];
				$data['uid']=$user['uid'];
				$data['comid']=$this->uid;
				$data['downtime']=time();
				if($row['vip_etime']>time() || $row['vip_etime']=="0")
				{
					if($row['rating_type']==1)
					{
						if($row['down_resume']==0){
							if($this->config['com_integral_online']=="1")
							{
								$arr['status']=2;
								$arr['uid']=$user['uid'];
								$arr['msg']="你的等级特权已经用完,将扣除".$this->config['integral_down_resume'].$this->config['integral_pricename']."，是否下载？";
							}else{
								$arr['status']=1;
								$arr['msg']="会员下载简历已用完！";
							}
						}else{
							
							$this->obj->insert_into("down_resume",$data);
							$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
							$this->obj->DB_update_all("company_statis","`down_resume`=`down_resume`-1","uid='".$this->uid."'");
							$state_content = "新下载了简历 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$user[id]\" target=\"_blank\">".$user['name']."</a> 。";
							$this->addstate($state_content);
							$arr['status']=3;
							$this->obj->member_log("下载了简历：".$user['name'],3);
							$this->warning("2");
						}
					}else{
						$this->obj->insert_into("down_resume",$data);
						$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
						$state_content = "新下载了简历 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$user[id]\" target=\"_blank\">".$user['name']."</a> 。";
						$this->addstate($state_content);
						$arr['status']=3;
						$this->obj->member_log("下载了简历：".$user['name'],3);
						$this->warning("2");
					}
				}else{
					if($this->config['com_integral_online']=="1")
					{
						$arr['status']=2;
						$arr['uid']=$user['uid'];
						$arr['msg']="您的等级特权已到期,将扣除".$this->config['integral_down_resume'].$this->config['integral_pricename']."，是否下载？";
					}else{
						$arr['status']=1;
						$arr['msg']="您的等级特权已到期！";
					}
				}
			}
			if($arr['status']==3)
			{
				$html="<table>";
				$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","手机：")."</td><td>".$user['telphone']."</td></tr>";
				if($user['basic_info']=='1')
				{
					$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","座机：")."</td><td>".$user['telhome']."</td></tr>";
				}
				$html.="<tr><td align='right' width='90'>".iconv("gbk", "utf-8","邮箱：")."</td><td>".$user['email']."</td></tr>";
				$html.="</table>";
				$arr['html']=$html;
			}
		}
		$arr['msg']=iconv("gbk", "utf-8",$arr['msg']);
		echo json_encode($arr);die;
	}

	function down_resume_action()
	{
		$eid=(int)$_POST['eid'];
		$uid=(int)$_POST['uid'];
		$type=$_POST['type'];
		$data['eid']=$eid;
		$data['uid']=$uid;
		$data['comid']=$this->uid;
		$data['downtime']=time();
		if(!$this->uid || !$this->username || $_COOKIE['usertype']!=2)
		{
			$arr['status']=0;
			echo json_encode($arr);
			die;
		}else{
			$black=$this->obj->DB_select_once("blacklist","`p_uid`='".$uid."' and `c_uid`='".$this->uid."'");
			if(!empty($black))
			{
				$arr['status']=7;
				echo json_encode($arr);die;
			}
			$black=$this->obj->DB_select_once("blacklist","`c_uid`='".$uid."' and `p_uid`='".$this->uid."'");
			if(!empty($black))
			{
				$arr['status']=8;
				echo json_encode($arr);die;
			}
			$resume=$this->obj->DB_select_once("down_resume","`eid`='$eid' and `comid`='".$this->uid."'");
			if(is_array($resume))
			{
				$arr['status']=6;
				echo json_encode($arr);die;
			}
			$row=$this->obj->DB_select_once("company_statis","`uid`='".$this->uid."'","`down_resume`,`integral`,`vip_etime`,`rating`,`rating_type`");
			if($type=="integral")
			{
				if($row['integral']<$this->config['integral_down_resume'] && $this->config['integral_down_resume_type']=="2")
				{
					$arr['status']=5;
					$arr['integral']=$row['integral'];
				}else{
					$this->obj->insert_into("down_resume",$data);
					if($this->config['integral_down_resume_type']=="1")
					{
						$auto=true;
					}else{
						$auto=false;
					}
					$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
					$this->obj->company_invtal($this->uid,$this->config['integral_down_resume'],$auto,"下载简历",true,2,'integral',13);
					$state_content = "新下载了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".iconv("utf-8", "gbk",$_POST['username'])."</a> 的简历。";
					$this->addstate($state_content);
					$arr['status']=3;
					$this->obj->member_log("下载了 ".iconv("utf-8", "gbk",$_POST['username'])." 的简历。",3);
					$this->warning("2");
				}
			}else{
				$arr['integral']=$this->config['integral_down_resume'];
				if($row['rating']==0)
				{
					$arr['status']=1;
				}else{
					if($row['vip_etime']>time() || $row['vip_etime']=="0")
					{
						if($row['rating_type']=="1")
						{
							if($row['down_resume']==0){
								if($this->config['com_integral_online']=="1")
								{
									$arr['status']=2;
								}else{
									$arr['status']=4;
								}
							}else{
								
								$this->obj->insert_into("down_resume",$data);
								$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
								$this->obj->DB_update_all("company_statis","`down_resume`=`down_resume`-1","uid='".$this->uid."'");
								$state_content = "新下载了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".iconv("utf-8", "gbk",$_POST['username'])."</a> 的简历。";
								$this->addstate($state_content);
								$arr['status']=3;
								$this->obj->member_log("下载了 ".iconv("utf-8", "gbk",$_POST['username'])." 的简历。",3);
								$this->warning("2");
							}
						}else{
							$this->obj->insert_into("down_resume",$data);
							$this->obj->DB_update_all("resume_expect","`dnum`=`dnum`+'1'","`id`='".$eid."'");
							$state_content = "新下载了 <a href=\"".$this->config['sy_weburl']."/index.php?m=resume&id=$_POST[eid]\" target=\"_blank\">".iconv("utf-8", "gbk",$_POST['username'])."</a> 的简历。";
							$this->addstate($state_content);
							$arr['status']=3;
							$this->obj->member_log("下载了 ".iconv("utf-8", "gbk",$_POST['username'])." 的简历。",3);
							$this->warning("2");
						}
					}else{
						if($this->config['com_integral_online']=="1")
						{
							$arr['status']=2;
						}else{
							$arr['status']=4;
						}
					}
				}
			}
		}
		echo json_encode($arr);
	}
	function wap_job_action()
	{
		include(PLUS_PATH."job.cache.php");
		if($_POST['type']==1)
		{
			$data="<option value=''>--请选择--</option>";
		}
		if(is_array($job_type[$_POST['id']])){
			foreach($job_type[$_POST['id']] as $v){
				$data.="<option value='$v'>".$job_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function wap_city_action()
	{
		include(PLUS_PATH."city.cache.php");
		if($_POST['type']==1)
		{
			$data="<option value=''>--请选择--</option>";
		}
		if(is_array($city_type[$_POST['id']])){
			foreach($city_type[$_POST['id']] as $v){
				$data.="<option value='$v'>".$city_name[$v]."</option>";
			}
		}
		echo $data;
	}
	function talent_pool_action()
	{
		if($_COOKIE['usertype']!="2")
		{
			echo 0;die;
		}
		$row=$this->obj->DB_select_once("talent_pool","`eid`='".(int)$_POST['eid']."' and `cuid`='".$this->uid."'");
		if(empty($row))
		{
			$value.="`eid`='".(int)$_POST['eid']."',";
			$value.="`uid`='".(int)$_POST['uid']."',";
			$value.="`cuid`='".$this->uid."',";
			$value.="`ctime`='".time()."'";
			$this->obj->DB_insert_once("talent_pool",$value);
			echo 1;die;
		}else{
			echo 2;die;
		}
	}
	function checkOncePassword_action(){
		$_POST=$this->post_trim($_POST);
		$password=md5(trim($_POST['password']));
		$id=intval($_POST['id']);
		if(isset($_POST['id'])){
			$arr=$this->obj->DB_select_once("once_job","`id`='".$id."' and `password`='".$password."'");
			if($arr['id']){
				echo 1;die;
			}else{
				echo 2;die;
			}
		}
		
	}
	function checkTinyPassword_action(){
		$_POST=$this->post_trim($_POST);
		$password=md5(trim($_POST['password']));
		$id=intval($_POST['id']);
		if(isset($_POST['id'])){
			$arr=$this->obj->DB_select_once("resume_tiny","`id`='".$id."' and `password`='".$password."'");
			if($arr['id']){
				echo 1;die;
			}else{
				echo 2;die;
			}
		}
		
	}
}
?>