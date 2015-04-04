<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class once_controller extends common
{
	function index_action()
	{
		if($this->config['sy_wzp_web']=="2")
		{
			header("location:".$this->url("index","error"));
		}
		$ip = $this->obj->fun_ip_get();
		$start_time=strtotime(date('Y-m-d 00:00:00')); 
		if($_POST['submit'])
		{
			$authcode=md5($_POST['authcode']);
			$password=md5($_POST['password']);
			$id=(int)$_POST['id'];
			$submit=$_POST['submit'];
			unset($_POST['authcode']);
			unset($_POST['password']);
			unset($_POST['submit']);
			unset($_POST['id']);
			$_POST['status']=$this->config['com_fast_status'];
			$_POST['login_ip']=$ip;
			$_POST['ctime']=time();
			$_POST['edate']=strtotime("+".(int)$_POST['edate']." days");
			if($id!=""){
				$arr=$this->obj->DB_select_once("once_job","`id`='".$id."' and `password`='".$password."'");
				if(empty($arr)){
					$this->obj->ACT_layer_msg("���벻��ȷ",8,"index.php?m=once");
				}
				$this->obj->update_once("once_job",$_POST,array("id"=>$id));
				if($this->config['com_fast_status']=="0"){$msg="�޸ĳɹ����ȴ���ˣ�";}else{$msg="�޸ĳɹ�!";}
			}else{
				if(strstr($this->config['code_web'],'һ�仰��Ƹ'))
				{
					if($authcode!=$_SESSION['authcode'])
					{
						unset($_SESSION['authcode']);
						$this->obj->ACT_layer_msg("��֤�����",8);
					}
				}
				$_POST['password']=$password;
				$mess=$this->obj->DB_select_num("once_job","`login_ip`='".$ip."' and `ctime`>'".$start_time."'","`id`");
				if($this->config['sy_once']=="0"){
						$this->obj->insert_into("once_job",$_POST);
				        if($this->config['com_fast_status']=="0"){$msg="�����ɹ����ȴ���ˣ�";}else{$msg="�����ɹ�!";}
				}else{
					if($this->config['sy_once']>$mess){
							$this->obj->insert_into("once_job",$_POST);
					        if($this->config['com_fast_status']=="0"){$msg="�����ɹ����ȴ���ˣ�";}else{$msg="�����ɹ�!";}
					}else{
                           $this->obj->ACT_layer_msg("һ����ֻ�ܷ���".$this->config['sy_once']."�Σ�",8,"index.php?m=once");
					}
				}
			}
			$this->obj->ACT_layer_msg($msg,9,"index.php?m=once");
		}
		if($_GET['id']){
			$onceinfo=$this->obj->DB_select_once("once_job","`id`='".(int)$_GET['id']."'");
			if(!empty($onceinfo)){
	            $onceinfo["title"]=iconv("gbk","utf-8",$onceinfo["title"]);
				$onceinfo["companyname"]=iconv("gbk","utf-8",$onceinfo["companyname"]);
				$onceinfo["linkman"]=iconv("gbk","utf-8",$onceinfo["linkman"]);
				$onceinfo["address"]=iconv("gbk","utf-8",$onceinfo["address"]);
				$onceinfo["require"]=iconv("gbk","utf-8",$onceinfo["require"]);
				$onceinfo['edate']=ceil(($onceinfo['edate']-mktime())/86400);
				echo json_encode($onceinfo);die;
			}
			$this->yunset("once_id",$_GET['id']);
		}
		$this->seo("once");
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
		$jobinfo=$this->obj->DB_select_once("once_job","`id`='".(int)$_POST['tid']."' and `password`='".md5($_POST['pw'])."'");
		if(!is_array($jobinfo) || empty($jobinfo))
		{
			echo 2;die;
		}
		if($_POST['type']==1)
		{
			$this->obj->DB_update_all("once_job","`ctime`='".time()."'","`id`='".(int)$jobinfo['id']."'");
			echo 3;die;
		}elseif($_POST['type']==3){
			$this->obj->DB_delete_all("once_job","`id`='".(int)$jobinfo['id']."'");
			echo 4;die;
		}else{
			if($jobinfo['edate']>mktime()){
				$jobinfo['edate']=ceil(($jobinfo['edate']-mktime())/86400);
			}else{
				$jobinfo['edate']=iconv("gbk","utf-8","�ѹ���");
			}
			$jobinfo["title"]=iconv("gbk","utf-8",$jobinfo["title"]);
			$jobinfo["companyname"]=iconv("gbk","utf-8",$jobinfo["companyname"]);
			$jobinfo["linkman"]=iconv("gbk","utf-8",$jobinfo["linkman"]);
			$jobinfo["address"]=iconv("gbk","utf-8",$jobinfo["address"]);
			$jobinfo["require"]=iconv("gbk","utf-8",$jobinfo["require"]);
			echo json_encode($jobinfo);die;
		}
	}
	function show_action(){
		if(isset($_GET['id'])){
		  $id=(int)$_GET['id'];
           $o_info=$this->obj->DB_select_once("once_job","`id`='".$id."'");
		   $this->obj->DB_update_all("once_job","`hits`=`hits`+1","`id`='".$id."'");
		}
		$this->seo("once");
		$this->yunset("o_info",$o_info);
		$this->yun_tpl(array('show'));
	}
}
?>