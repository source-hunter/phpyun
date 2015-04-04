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
class admin_user_controller extends common
{
	function index_action(){
		$adminuser=$this->obj->DB_select_all("admin_user","1 order by uid desc");
		$adminusergroup=$this->obj->DB_select_all("admin_user_group","1 order by id desc");
		if(is_array($adminusergroup)){
			foreach($adminusergroup as $v){
				$groupname[$v['id']]=$v['group_name'];
			}
		}
		$this->yunset("groupname",$groupname);
		$this->yunset("adminuser",$adminuser);
		$this->yunset("adminusergroup",$adminusergroup);
		$this->yuntpl(array('admin/admin_user_list'));
	}

	
	function add_action(){
		if(isset($_GET['uid'])){
			$where="`uid`='".$_GET['uid']."'";
			$adminuser=$this->obj->DB_select_once("admin_user",$where);
			$this->yunset("adminuser",$adminuser);
		}
		$user_group=$this->obj->DB_select_all("admin_user_group","1 order by `id` desc");
		$this->yunset("user_group",$user_group);
		$this->yuntpl(array('admin/admin_user_add'));
	}
	function group_action(){
		$adminusergroup=$this->obj->DB_select_all("admin_user_group","1 order by id desc");
		$this->yunset("adminusergroup",$adminusergroup);
		$this->yuntpl(array('admin/admin_group_list'));
	}
	function myuser_action(){
		$this->yunset("row",$row);
		$where="`uid`='".$_SESSION['auid']."'";
		$adminuser=$this->obj->DB_select_once("admin_user",$where);
		$this->yunset("adminuser",$adminuser);
		$user_group=$this->obj->DB_select_all("admin_user_group","1 order by `id` desc");
		$this->yunset("user_group",$user_group);
		$this->yuntpl(array('admin/admin_myuser'));
	}
	function pass_action(){
		if($_POST['oldpass']){
			$where="`uid`='".$_SESSION['auid']."'";
			$row=$this->obj->DB_select_once("admin_user",$where);
			if(md5($_POST['oldpass'])!=$row['password']){
				$this->obj->ACT_layer_msg("原始密码不正确！",8,$_SERVER['HTTP_REFERER']);
			}
			if($_POST['password']!=$_POST['okpassword']){
				$this->obj->ACT_layer_msg("新密码两次输入不一致！",8,$_SERVER['HTTP_REFERER']);
			}
			$value.="`password`='".md5($_POST['password'])."'";
			$nbid=$this->obj->DB_update_all("admin_user",$value,$where);
			unset($_SESSION['authcode']);
			unset($_SESSION['auid']);
			unset($_SESSION['ausername']);
			unset($_SESSION['ashell']);
			$this->obj->ACT_layer_msg("管理员(ID:".$row['uid']."帐号".$row['username'].")密码修改成功,请重新登录！",9,$_SERVER['HTTP_REFERER'],2,1);
		}
					
		$this->yuntpl(array('admin/admin_mypass'));
	}
	
	function addgroup_action(){
		
		if((int)$_GET['id']){
			$where="`id`='".$_GET['id']."'";
			$admingroup=$this->obj->DB_select_once("admin_user_group",$where);
			$this->yunset("admin_group",$admingroup);
			$this->yunset("power",unserialize($admingroup[2]));
		}
		
		$nav_user=$this->obj->DB_select_alls("admin_user","admin_user_group","a.`m_id`=b.`id` and a.`uid`='".$_SESSION['auid']."'");
		
		$menurows=$this->obj->DB_select_all("admin_navigation","`display`<>1 order by `sort` desc");
		$i=0;$j=0;$a=0;$b=0;
		if(is_array($menurows)){
			foreach($menurows as $key=>$v){
				if($v['keyid']==0){
					$navigation[$i]['id']=$v['id'];
					$navigation[$i]['name']=$v['name'];
					$i++;
				}
				if($v['menu']==2){
					$menu[$j]['id']=$v['id'];
					$menu[$j]['name']=$v['name'];
					$menu[$j]['url']=$v['url'];
					$j++;
				}
			}
		}
		
		if(is_array($navigation)){
			foreach($navigation as $va){
				if(is_array($menurows)){
					foreach($menurows as $key=>$v){
						if($v['keyid']==$va['id']){
							if(!is_array($one_menu[$va['id']]))$a=0;
							$one_menu[$va['id']][$a]['id']=$v['id'];
							$one_menu[$va['id']][$a]['name']=$v['name'];
							$a++;
							foreach($menurows as $key=>$vaa){
								if($vaa['keyid']==$v['id']){
									if(!is_array($two_menu[$v['id']]))$b=0;
									$two_menu[$v['id']][$b]['id']=$vaa['id'];
									$two_menu[$v['id']][$b]['name']=$vaa['name'];
									$two_menu[$v['id']][$b]['url']=$vaa['url'];
									$b++;
								}
							}
						}
					}
				}
			}
		}
		$power=unserialize($nav_user[0]['group_power']);
		$this->yunset("one_menu",$one_menu);
		$this->yunset("two_menu",$two_menu);
		$this->yunset("navigation",$navigation);
		$this->yuntpl(array('admin/admin_group'));
	}
	
	function save_action()
	{
		if(isset($_POST['useradd'])){
			if(!empty($_POST['username'])&&!empty($_POST['name'])){
				 $value="`m_id`='".$_POST['m_id']."',`username`='".$_POST['username']."',`name`='".$_POST['name']."'";
				if($_POST['password']){
					$value.=",`password`='".md5($_POST['password'])."'";
				}
				if(!$_POST[uid]){
				 	$nbid=$this->obj->DB_insert_once("admin_user","$value");
					$name="管理员（ID:".$nbid."）添加";
				 }else{
				 	$nbid=$this->obj->DB_update_all("admin_user",$value,"`uid`='".$_POST['uid']."'");
				 	if($_POST['uid']==$_SESSION['auid']){
						unset($_SESSION['authcode']);
						unset($_SESSION['auid']);
						unset($_SESSION['ausername']);
						unset($_SESSION['ashell']);
						$this->obj->ACT_layer_msg( "管理员(ID:".$_POST['uid'].")修改成功,请重新登录！",9,$_SERVER['HTTP_REFERER'],2,1);
				 	}
				 	$name="管理员(ID:".$_POST['uid'].")更新";
				 }
				isset($nbid)?$this->obj->ACT_layer_msg($name."成功！",9,"index.php?m=admin_user",2,1):$this->obj->ACT_layer_msg($name."失败！",8,"index.php?m=admin_user");
			}else{
				$this->obj->ACT_layer_msg( "请填写完整！",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	function deluser_action()
	{
		$this->check_token();
		if(isset($_GET['uid'])){
			$where="`uid`='".$_GET['uid']."'";
			$result=$this->obj->DB_delete_all("admin_user", $where);
			isset($result)?$this->layer_msg('管理员（ID:'.$_GET['uid'].'）删除成功！',9):$this->layer_msg('删除失败！',8);
		}else{
			$this->layer_msg('非法操作！',3);
		}
	}
	
	function delgroup_action()
	{
		$this->check_token();
		if(isset($_GET['id'])){
			$where="`id`='".$_GET['id']."'";
			$result=$this->obj->DB_delete_all("admin_user_group",$where);
			isset($result)?$this->layer_msg('用户组（ID：'.$_GET['id'].'）删除成功！',9):$this->layer_msg('删除失败！',8);
		}else{
			$this->layer_msg('非法操作！',3);
		}
	}
	
	function savagroup_action()
	{
		extract($_POST);
		if(!$groupid){
			$value.="`group_name`='".$group_name."',";
			$value.="`group_power`='".serialize($power)."'";
			$id=$this->obj->DB_insert_once("admin_user_group",$value);
			isset($id)?$this->obj->ACT_layer_msg( "用户组(ID：".$id.")添加成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "用户组添加失败！",9,$_SERVER['HTTP_REFERER']);
		}else{
			$value.="`group_name`='".$group_name."',";
			$value.="`group_power`='".serialize($power)."'";
			$result=$this->obj->DB_update_all("admin_user_group", $value,"`id`='".$groupid."'");
			isset($result)?$this->obj->ACT_layer_msg( "用户组(ID：".$groupid.")修改成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "修改失败！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}

?>