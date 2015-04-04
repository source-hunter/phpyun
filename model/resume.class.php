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
class resume_controller extends common
{
    function index_action()
    {
		if($_GET['uid'])
		{
		
			$def_job=$this->obj->DB_select_once("resume","`r_status`<>'2' and `uid`='".(int)$_GET['uid']."'","def_job");
			if(!is_array($def_job))
			{
				$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"没有找到该人才！");
			}else{
				if($def_job['def_job']=="0")
				{
					$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"还没有创建简历！");
				}else{
					$_GET['id']=$def_job['def_job'];
				}
			}
			$this->resume_select($def_job['def_job']);
			
		}
		if($_GET['id']){
			$id=(int)$_GET['id'];
			$this->obj->DB_update_all("resume_expect","`hits`=`hits`+1","`id`='".$id."'");
			$resume_expect=$this->obj->DB_select_once("resume_expect","`id`='".$id."'","`uid`,`tmpid`");
	
			if($_COOKIE['usertype']=="2"){
				$look_resume=$this->obj->DB_select_once("look_resume","`com_id`='".$this->uid."' and `resume_id`='".$id."'");
				if(!empty($look_resume))
				{
					$this->obj->DB_update_all("look_resume","`datetime`='".time()."'","`com_id`='".$this->uid."' and `resume_id`='".$id."'");
				}else{
					$value.="`uid`='".$resume_expect['uid']."',";
					$value.="`resume_id`='".$id."',";
					$value.="`com_id`='".$this->uid."',";
					$value.="`datetime`='".time()."'";
					$this->obj->DB_insert_once("look_resume",$value);
				}
			}
		}
		if($_COOKIE['usertype']=="2" || $_COOKIE['usertype']=="3")
		{
			$this->yunset("uid",$_COOKIE['uid']);
			$this->obj->DB_update_all("userid_job","`is_browse`=2","`com_id`='".$this->uid."' and `eid`='".(int)$_GET['id']."'");
			if($_COOKIE['usertype']=="2"){
				$this->unset_remind("userid_job",'2');
			}else{
				$this->unset_remind("userid_job3",'3');
			}

		}

		if($_GET['type']=="word")
		{
			$resume=$this->obj->DB_select_once("down_resume","`eid`='".(int)$_GET['id']."' and `downtime`='".$_GET['downtime']."'");
			if(is_array($resume) && !empty($resume))
			{
				$this->yun_tpl(array('wordresume'));
				die;
			}
		}
		$this->seo("resume");
		if($_GET['type']==2){
		
			$this->yun_tpl(array('gresume'));
		}else{
			
			if($_GET['tmp']=='d')
			{
				$this->yun_tpl(array('index'));
			}elseif(intval($_GET['tmp'])>0)
			{
				$this->yun_tpl(array('jianli'.intval($_GET['tmp']).'/index'));
			}else{
				if($resume_expect['tmpid']){
					
					$this->yun_tpl(array('jianli'.intval($resume_expect['tmpid']).'/index'));
				}else{
					$this->yun_tpl(array('index'));
				}
			}
	
			
		} 
    }
    function sendresume_action(){
		$this->yunset("type",$_GET['type']);
		$this->yunset("job_link",$_GET['job_link']);
		$this->yun_tpl(array('sendresume'));
    }
    function resumeshare_action()
    {
		if($_POST)
		{
			if($this->config["sy_smtpserver"]=="" || $this->config["sy_smtpemail"]=="" ||	$this->config["sy_smtpuser"]==""){
				echo "网站邮件服务器暂不可用！";die; 
			}
			if($_POST['femail']=="" || $_POST['myemail']=="" || $_POST['authcode']=="")
			{
				echo "请完整填写信息！";die;
			}
			if(md5($_POST['authcode'])!=$_SESSION['authcode'])
			{
				unset($_SESSION['authcode']);echo "验证码不正确！";die;
			}
			if($_COOKIE["sendresume"]==$_POST['id'])
			{
				echo "请不要频繁发送邮件！同一简历发送间隔为两分钟！";die;
			}
			if($this->CheckRegEmail(trim($_POST['femail']))==false){echo "邮箱格式错误！";die;}
			$contents=file_get_contents($this->url("index","resume",array("c"=>"sendresume","id"=>(int)$_POST['id']),"1"));
 			$smtp = $this->email_set();
			$smtpusermail =$this->config['sy_smtpemail'];
			$myemail = $this->stringfilter($_POST['myemail']);

			$sendid = $smtp->sendmail($_POST['femail'],$smtpusermail,"您的好友".$myemail."向您推荐了简历！",$contents,"HTML","","","",$myemail);
			if($sendid)
			{
				echo 1;
			}else{
				echo "邮件发送错误 原因：1邮箱不可用 2网站关闭邮件服务";die;
			}
			SetCookie("sendresume",$_POST['id'],time() + 120, "/");
			die;
		}
		$this->seo("resume_share");
		$this->yunset("id",$_GET['id']);
		$this->yun_tpl(array('resume_share'));
    }
    function user_resume($uid="",$eid="")
    {
    	$status=0;
		if($this->uid==$uid && $this->username && $_COOKIE['usertype']==1)
		{
			$status=1;
			$this->yunset("user_status",$status);
		}
		if($this->uid && $this->username && $_COOKIE['usertype']==2){
			$row=$this->obj->DB_select_once("down_resume","`eid`='".$eid."' and comid='".$this->uid."'");
			if(is_array($row))
			{
				$status=1;
				$this->yunset("com_status",$status);
			}
		}
    }
    function send_email_resume_action()
    {
    	$this->yun_tpl(array('send_email_resume'));
    }

	function saveresumebackground_action()
    {
		$user_expect=$this->obj->DB_select_once("resume_expect","`id`='".$_POST['eid']."'");
		if($user_expect['uid']==$this->uid&&($this->uid!='')){
    		$update=$this->obj->DB_update_all("resume_expect","`resume_diy`='".$_POST['background']."'","`id`='".$_POST['eid']."'");
			echo empty($update)?1:2;
		}else{
			echo 0;
		}
    }
    function down_action()
    {
    	if($_COOKIE['usertype']!="2" || !$this->uid)
    	{
    		echo 2;die;
    	}
		$user=$this->obj->DB_select_once("resume_expect","`id`='".(int)$_GET['id']."'");
		if(empty($user['works_upload'])){
            echo 3;
            exit;
		}
		if(file_exists(APP_PATH.$user['works_upload'])) {
			header("Content-type:text/html;charset=utf-8");
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=".basename($user['works_upload'])."");
            readfile(APP_PATH.$user['works_upload']);
            exit;
        }else{
            echo "文件不存在！";exit;
        }
	}
}
?>