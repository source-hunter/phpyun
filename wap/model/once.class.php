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
class once_controller extends common
{
	function index_action()
	{
		if($this->config['sy_wzp_web']=="2"){
			$data['msg']='很抱歉！该模块已关闭！';
			$data['url']='index.php';
			$this->yunset("layer",$data);
		}
		$this->get_moblie();
		$this->yunset("title","微招聘");
		$this->yuntpl(array('wap/once'));
	}
	function add_action(){
		if($this->config['sy_wzp_web']=="2"){
			$data['msg']='很抱歉！该模块已关闭！';
			$data['url']='index.php';
			$this->yunset("layer",$data);
		}
		$this->get_moblie();
		if($_GET['id']){
			$row=$this->obj->DB_select_once("once_job","`id`='".intval($_GET[id])."'");
			$row['edate']=round(($row['edate']-$row['ctime'])/3600/24) ;
			$this->yunset("row",$row);
		}
		if($_POST['submit']){
			$_POST=$this->post_trim($_POST);
			$_POST['status']=$this->config['com_fast_status'];
			$_POST['ctime']=time();
			$_POST['require']=trim($_POST['require']);
			$_POST['edate']=strtotime("+".(int)$_POST['edate']." days");
			$password=md5(trim($_POST['password']));
			$type=trim($_POST['type']);
			unset($_POST['submit']);
			unset($_POST['type']);
			$id=intval($_POST['id']);
			if(!isset($_POST['id'])){
				$_POST['password']=$password;
				$nid=$this->obj->insert_into("once_job",$_POST);
				$nid?$data['msg']='操作成功！':$data['msg']='操作失败！';
				$data['url']='index.php?m=once';
			}else{
				$arr=$this->obj->DB_select_once("once_job","`id`='".$id."' and `password`='".$password."'");
				if($arr['id']){
					if($_POST['id']&&$type){
						$arr=$this->obj->DB_select_once("once_job","`id`='".$id."' and `password`='".$password."'");
						if($type=='3'){
							$nid=$this->obj->DB_delete_all("once_job","`id`='".$arr['id']."'");
						}else{
							unset($_POST['id']);
							unset($_POST['password']);
							$nid=$this->obj->update_once("once_job",$_POST,array("id"=>$arr['id']));
						}
					}else{
						$_POST['password']=$password;
						$nid=$this->obj->insert_into("once_job",$_POST);
					}
					$nid?$data['msg']='操作成功！':$data['msg']='操作成功！';
					$data['url']='index.php?m=once';
				}else{
					$data['msg']='密码错误！';
					$data['url']='1';
				}
			}
		}
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr=$this->CacheInclude($CacheArr);
		$this->yunset("layer",$data);
		$this->yunset("title","微招聘");
		$this->yuntpl(array('wap/once_add'));
	}
	function show_action(){
		if($this->config['sy_wzp_web']=="2"){
			$data['msg']='很抱歉！该模块已关闭！';
			$data['url']='index.php';
			$this->yunset("layer",$data);
		}
		$this->get_moblie();
		$this->yunset("title","微招聘");
		$row=$this->obj->DB_select_once("once_job","`id`='".intval($_GET[id])."'");
		$row['ctime']=date("Y-m-d",$row[ctime]);
		$this->yunset("row",$row);
		$this->yuntpl(array('wap/once_show'));
	}
}
?>