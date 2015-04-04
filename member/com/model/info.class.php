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
class info_controller extends company
{
	function index_action()
	{
		$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		$this->yunset("row",$row);
		$this->public_action();
		$this->city_cache();
		$this->job_cache();
		$this->com_cache();
		$this->industry_cache();
		$this->yunset("js_def",2);
		$this->com_tpl('info');
	}
	function save_action(){
		if($_POST['submitbtn'])
		{
			$_POST=$this->post_trim($_POST);
			if($_POST['name']=="")
			{
				$this->obj->ACT_layer_msg("企业全称不能为空！",8,"index.php?c=info");
			}
			if($_POST['hy']=="")
			{
				$this->obj->ACT_layer_msg("从事行业不能为空！",8,"index.php?c=info");
			}
			if($_POST['pr']=="")
			{
				$this->obj->ACT_layer_msg("企业性质不能为空！",8,"index.php?c=info");
			}
			if($_POST['provinceid']=="")
			{
				$this->obj->ACT_layer_msg("企业地址不能为空！",8,"index.php?c=info");
			}
			if($_POST['mun']=="")
			{
				$this->obj->ACT_layer_msg("企业规模不能为空！",8,"index.php?c=info");
			}
			if($_POST['address']=="")
			{
				$this->obj->ACT_layer_msg("公司地址不能为空！",8,"index.php?c=info");
			}
			if($_POST['linkmail']=="")
			{
				$this->obj->ACT_layer_msg("联系邮件不能为空！",8,"index.php?c=info");
			}
			if($_POST['content']=="")
			{
				$this->obj->ACT_layer_msg("企业简介不能为空！",8,"index.php?c=info");
			}
			$this->obj->delfiledir("../upload/tel/".$this->uid);
			unset($_POST['submitbtn']);
			if($_FILES['uplocadpic']['tmp_name'])
			{
				$upload=$this->upload_pic("../upload/company/",false,$this->config['com_pickb']);
				$pictures=$upload->picture($_FILES['uplocadpic']);
				$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
				$s_thumb=$upload->makeThumb($pictures,185,75,'_S_');
				$this->obj->unlink_pic($pictures);
				$_POST['logo']=str_replace("../upload/company","./upload/company",$s_thumb);
				$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."' and `logo`<>''");
				if(is_array($row))
				{
					$this->obj->unlink_pic(".".$row['logo']);
				}
			}
			if($_FILES['firmpic']['tmp_name'])
			{
				$upload=$this->upload_pic("../upload/company/",false,$this->config['com_uppic']);
				$firmpic=$upload->picture($_FILES['firmpic']);
				$this->picmsg($firmpic,$_SERVER['HTTP_REFERER']);
				$_POST['firmpic'] = str_replace("../upload/company","./upload/company",$firmpic);
				$rows=$this->obj->DB_select_once("company","`uid`='".$this->uid."' and `firmpic`<>''");
				if(is_array($rows))
				{
					$this->obj->unlink_pic(".".$rows['firmpic']);
				}
			}
			$cert_email = $this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and `type`='1'");
			if(is_array($cert_email))
			{
				if($cert_email['check'] != $_POST['linkmail'])
				{
					$_POST['email_status'] = "0";
					$this->obj->DB_delete_all("company_cert","`id`='".$cert_email['id']."'");
				}
			}
			$cert_tel = $this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and `type`='2'");
			if(is_array($cert_tel))
			{
				if($cert_tel['check'] != $_POST['linktel'])
				{
					$_POST['moblie_status'] = "0";
					$this->obj->DB_delete_all("company_cert","`id`='".$cert_tel['id']."'");
				}
			}
			$where['uid']=$this->uid;
			$_POST['content'] = str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'background-color:','background-color:','white-space:'),html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			$_POST['lastupdate']=mktime();
			$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","yyzz_status");
			if($company['yyzz_status']=='1'){
				unset($_POST['name']);
			}
			$nid=$this->obj->update_once("company",$_POST,$where);
			$data['com_name']=$_POST['name'];
			$data['pr']=$_POST['pr'];
			$data['mun']=$_POST['mun'];
			$data['com_provinceid']=$_POST['provinceid'];
			$this->obj->update_once("company_job",$data,array("uid"=>$this->uid));
			$this->obj->update_once("member",array("email"=>$_POST['linkmail'],"moblie"=>$_POST['linktel']),array("uid"=>$this->uid));
			$this->obj->update_once("userid_job",array("com_name"=>$_POST['name']),array("com_id"=>$this->uid));
			$this->obj->update_once("fav_job",array("com_name"=>$_POST['name']),array("com_id"=>$this->uid));
			$this->obj->update_once("report",array("r_name"=>$_POST['name']),array("c_uid"=>$this->uid));
			$this->obj->update_once("blacklist",array("com_name"=>$_POST['name']),array("c_uid"=>$this->uid));
			$this->obj->update_once("msg",array("com_name"=>$_POST['name']),array("job_uid"=>$this->uid));
			if($nid)
			{
				$this->obj->member_log("修改企业信息",7);
				if($row['name']=="")
				{
					$this->obj->company_invtal($this->uid,$this->config['integral_userinfo'],true,"首次填写基本资料",true,2,'integral',25);
				}
				$this->obj->ACT_layer_msg("更新成功！",9,"index.php?c=info");
			}else{
				$this->obj->ACT_layer_msg("更新失败！",8,"index.php?c=info");
			}
		}
	}
	function verify_action(){
		$info=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","`linktel`,`linkmail`");
		if($_POST['email']){
			if($info['linkmail']!="" && $info['linkmail']!=$_POST['email']){
				echo 1;die;
			}
		}
		if($_POST['moblie']){
			if($info['linktel']!="" && $info['linktel']!=$_POST['moblie']){
				echo 1;die;
			}
		}
	}
}
?>