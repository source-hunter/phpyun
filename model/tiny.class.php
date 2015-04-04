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
class tiny_controller extends common
{
	function index_action()
	{
		if($this->config['sy_wjl_web']=="2")
		{
			header("location:".$this->url("index","error"));
		}
		$this->user_cache();
		$ip = $this->obj->fun_ip_get();
		$s_time=strtotime(date('Y-m-d 00:00:00'));
		if($_POST['submit'])
		{
			$id=(int)$_POST['id'];
			$authcode=md5($_POST['authcode']);
			$password=md5($_POST['password']);
			unset($_POST['authcode']);
			unset($_POST['password']);
			unset($_POST['submit']);
			unset($_POST['id']);
			$_POST['status']=$this->config['user_wjl'];
			$_POST['login_ip']=$ip;
			$_POST['time']=time();
			$_POST['qq']=$_POST['qq'];

			if($id!="")
			{
				$arr=$this->obj->DB_select_once("resume_tiny","`id`='".$id."' and `password`='".$password."'");
				if(empty($arr)){
					$this->obj->ACT_layer_msg("密码不正确",8,"index.php?m=tiny");
				}
				$this->obj->update_once("resume_tiny",$_POST,array("id"=>$id));
				if($this->config['user_wjl']=="0"){$msg="修改成功，等待审核！";}else{$msg="修改成功!";}
			}else{
				if($authcode!=$_SESSION['authcode'])
				{
					unset($_SESSION['authcode']);
					$this->obj->ACT_layer_msg($_POST['authcode']."验证码错误！".$_SESSION['authcode'],8,$_SERVER['HTTP_REFERER']);
				}
				$_POST['password']=$password;

				$m_tiny=$this->obj->DB_select_num("resume_tiny","`login_ip`='".$ip."' and `time`>'".$s_time."'","`id`");
				if($this->config['sy_tiny']=="0"){
					$this->obj->insert_into("resume_tiny",$_POST);
					if($this->config['user_wjl']=="0"){$msg="发布成功，等待审核！";}else{$msg="发布成功!";}
				}else{
					if($this->config['sy_tiny']>$m_tiny){
						$this->obj->insert_into("resume_tiny",$_POST);
						if($this->config['user_wjl']=="0"){$msg="发布成功，等待审核！";}else{$msg="发布成功!";}
					}else{
	                    $this->obj->ACT_layer_msg("一天内只能发布".$this->config['sy_tiny']."次！",8,"index.php?m=tiny");
					}
				}
			}
			$this->obj->ACT_layer_msg($msg,9,"index.php?m=tiny");
		}
		$this->seo("tiny");
		$this->yunset("ip",$ip);
		$this->yun_tpl(array('index'));
	}
	function ajax_action()
	{
		if(md5($_POST['code'])!=$_SESSION['authcode'])
		{
			unset($_SESSION['authcode']);
			echo 1;die;
		}
		$jobinfo=$this->obj->DB_select_once("resume_tiny","`id`='".(int)$_POST['tid']."' and `password`='".md5($_POST['pw'])."'");
		if(!is_array($jobinfo) || empty($jobinfo))
		{
			echo 2;die;
		}
		if($_POST['type']==1)
		{
			$this->obj->DB_update_all("resume_tiny","`time`='".time()."'","`id`='".(int)$jobinfo['id']."'");
			echo 3;die;
		}elseif($_POST['type']==3){
			$this->obj->DB_delete_all("resume_tiny","`id`='".(int)$jobinfo['id']."'");
			echo 4;die;
		}else{
			$this->user_cache();
			$jobinfo['username']=iconv("gbk","utf-8",$jobinfo['username']);
			$jobinfo['job']=iconv("gbk","utf-8",$jobinfo['job']);
			$jobinfo['production']=iconv("gbk","utf-8",$jobinfo['production']);
			echo json_encode($jobinfo);die;
		}
	}
	function show_action(){
		$CacheArr['user'] =array('userdata','userclass_name');
		$Array = $this->CacheInclude($CacheArr);
		if(isset($_GET['id'])){
			$id=(int)$_GET['id'];
           $t_info=$this->obj->DB_select_once("resume_tiny","`id`='".$id."'");
		   $this->obj->DB_update_all("resume_tiny","`hits`=`hits`+1","`id`='".$id."'");

		}
		$data['tiny_username']=$t_info['username'];
		$data['tiny_job']=$t_info['job'];
		$data['tiny_desc']=$t_info['production'];
		$this->data=$data;
		$this->seo("tiny_cont");
		$this->yunset("t_info",$t_info);
		$this->yun_tpl(array('show'));
	}
}
?>

