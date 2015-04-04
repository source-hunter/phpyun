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
class usercert_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'审核状态',"value"=>array("1"=>"已审核","2"=>"未审核"));
		$lo_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"time","name"=>'发布时间',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
		$where="`idcard_pic`<>''";
		if($_GET['status']){
			if($_GET['status']==1){
				$where.=" and `idcard_status`='1'";
				$urlarr['status']='1';
			}else if($_GET['status']==2){
				$where.=" and `idcard_status`='0'";
				$urlarr['status']='2';
			}
		}
		if(trim($_GET['keyword'])){
			$where.=" and `name` like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['time']){
			if($_GET['time']=='1'){
				$where.=" and `cert_time` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `cert_time` >= '".strtotime('-'.(int)$_GET['time'].'day')."'";
			}
			$urlarr['time']=$_GET['time'];
		}
		if($_GET['order']){
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.="order by `cert_time` desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("resume",$where,$pageurl,$this->config['sy_listnum']);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_user_cert'));
	}
	function status_action(){
		extract($_POST);
		$id=$this->obj->DB_update_all("resume","`idcard_status`='$status',`statusbody`='".$statusbody."'","`uid`=$uid");
		$this->obj->DB_update_all("friend_info","`iscert`='$status'","`uid`=$uid");
		if($this->config['sy_email_usercert']=='1'){
			$userinfo = $this->obj->DB_select_once("member","`uid`=".$uid,"`email`,`name`,`uid`,`usertype`");
			$data=$this->forsend($userinfo);
			$this->send_msg_email(array("email"=>$userinfo['email'],'uid'=>$data['uid'],'name'=>$data['name'],"certinfo"=>$statusbody,"username"=>$userinfo['username'],"type"=>"usercert"));
		}
		if($id)
		{
			$this->get_integral_action($this->uid,"integral_identity","上传身份验证");
			$this->obj->ACT_layer_msg( "个人认证审核(ID:".$uid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1);
		}else{
			$this->obj->ACT_layer_msg( "设置失败！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function sbody_action(){
		$userinfo = $this->obj->DB_select_once("resume","`uid`=".$_POST['uid'],"`statusbody`");
		echo $userinfo['statusbody'];die;
	}


	function idcard_status_action(){
		$this->obj->DB_update_all("resume","`idcard_status`='".$_POST['idcard_status']."'","`uid` IN (".$_POST['allid'].")");
		$this->obj->admin_log("个人认证(ID:".$_POST['allid'].")审核成功");
		echo $_POST['idcard_status'];die;
	}


	function del_action(){
		$this->check_token();
		if(is_array($_GET['del'])){
			$linkid=@implode(',',$_GET['del']);
			$layer_type=1;
		}else{
			$linkid=$_GET['id'];
			$layer_type=0;
		}
		if($linkid==""){
			$this->layer_msg('请选择您要删除的数据！',8,1,$_SERVER['HTTP_REFERER']);
		}
	    $cert=$this->obj->DB_select_all("resume","`uid` in ($linkid)","`idcard_pic`");
	    if(is_array($cert)){
	     	foreach($cert as $v){
	     		$this->obj->unlink_pic($v['idcard_pic']);
	     	}
	    }
		$del=$this->obj->DB_update_all("resume","`idcard_pic`='',`idcard_status`='0',`cert_time`='',`statusbody`=''","`uid` in ($linkid)","");
		$del?$this->layer_msg('个人认证审核(ID:'.$linkid.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
	}

}

?>