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
class tiny_controller extends common
{
	function index_action()
	{
		if($this->config['sy_wjl_web']=="2"){
			$data['msg']='�ܱ�Ǹ����ģ���ѹرգ�';
			$data['url']='index.php';
			$this->yunset("layer",$data);
		}
		$this->get_moblie();
		$this->yunset("title","΢����");
		$this->yuntpl(array('wap/tiny'));
	}
	function add_action(){
		if($this->config['sy_wjl_web']=="2"){
			$data['msg']='�ܱ�Ǹ����ģ���ѹرգ�';
			$data['url']='index.php';
			$this->yunset("layer",$data);
		}
		$this->get_moblie();
		if($_GET['id']){
			$row=$this->obj->DB_select_once("resume_tiny","`id`='".$_GET[id]."'");
			$this->yunset("row",$row);
		}
		if($_POST['submit']){
			$_POST['status']=$this->config['user_wjl'];
			$_POST['time']=time();
			$_POST['username']=trim($_POST['username']);
			$_POST['production']=trim($_POST['production']);
			$_POST['job']=trim($_POST['job']);
			$password=md5(trim($_POST['password']));
			$type=trim($_POST['type']);
			unset($_POST['submit']);
			unset($_POST['type']);
			$id=intval($_POST['id']);
			if(!isset($_POST['id'])){
				$_POST['password']=$password;
				$nid=$this->obj->insert_into("resume_tiny",$_POST);
				$nid?$data['msg']='�����ɹ���':$data['msg']='����ʧ�ܣ�';
				$data['url']='index.php?m=tiny';
			}else{
				$arr=$this->obj->DB_select_once("resume_tiny","`id`='".$id."' and `password`='".$password."'");
				if($arr['id']){
					if($_POST['id']&&$type){
						if($type=='3'){
							$nid=$this->obj->DB_delete_all("resume_tiny","`id`='".$arr['id']."'");
						}else{
							unset($_POST['id']);
							$nid=$this->obj->update_once("resume_tiny",$_POST,array("id"=>$arr['id']));
						}
					}else{
						$_POST['password']=$password;
						$nid=$this->obj->insert_into("resume_tiny",$_POST);
					}
					$nid?$data['msg']='�����ɹ���':$data['msg']='�����ɹ���';
					$data['url']='index.php?m=tiny';
				}else{
					$data['msg']='�������';
					$data['url']='1';
				}
			}
		}
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$this->yunset("layer",$data);		
		$this->yunset("title","΢����");
		$this->yuntpl(array('wap/tiny_add'));
	}
	function show_action()
	{
		if($this->config['sy_wjl_web']=="2"){
			$data['msg']='�ܱ�Ǹ����ģ���ѹرգ�';
			$data['url']='index.php';
			$this->yunset("layer",$data);
		}
		$this->get_moblie();
		$this->yunset("title","΢����");
		$this->user_cache();
		$row=$this->obj->DB_select_once("resume_tiny","`id`='".$_GET[id]."'");
		$this->yunset("row",$row);
		$this->yuntpl(array('wap/tiny_show'));
	}
}
?>