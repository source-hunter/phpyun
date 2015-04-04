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
class comcert_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'审核状态',"value"=>array("3"=>"未审核","1"=>"已审核","2"=>"未通过"));
		$ad_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"end","name"=>'申请时间',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
        if($_GET['status']){
			if($_GET['status']=='3'){
				$where=" and a.`status`='0'";
				$urlarr['status']='0';
			}else{
				$where=" and a.`status`='".$_GET['status']."'";
				$urlarr['status']=$_GET['status'];
			}
        }
		if($_GET['keyword']){
			$where.=" and b.`name` like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['status']){
			if($_GET['status']=='3'){
				$where.=" and a.`status` ='0'";
			}else{
				$where.=" and a.`status` ='".$_GET['status']."'";
			}
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
	
		if($_GET['order'])
		{
			$where.=" order by a.`".$_GET['t']."` ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by a.`id` desc";
		}
		include(LIB_PATH."page3.class.php");
		$limit=$this->config["sy_listnum"];
		$page=$_GET["page"]<1?1:$_GET["page"];
		$ststrsql=($page-1)*$limit;
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET["m"],$urlarr);
		$count=$this->obj->DB_select_alls("company_cert","company","a.`uid`=b.`uid` and a.`type`='3' $where","a.uid");
 		$num = count($count);
 		$page = new page($page,$limit,$num,$pageurl);
		$pagenav=$page->numPage();
		$rows=$this->obj->DB_select_alls("company_cert","company","a.`uid`=b.`uid` and a.`type`='3' $where $order limit $ststrsql,$limit","b.name,a.*");
		$this->yunset("pagenav",$pagenav);
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/admin_com_cert'));
	}
	function sbody_action(){
		$userinfo = $this->obj->DB_select_once("company_cert","`type`='3' and `uid`='".$_POST['uid']."'","`statusbody`");
		echo $userinfo['statusbody'];die;
	}
	function status_action()
	{
		if($_POST['uid'])
		{
			$uid=$_POST['uid'];
			if($_POST['status']!="1"){
				$yyzz_status=0;
			}else{
				$yyzz_status=1;
			}
			$this->obj->DB_update_all("company","`yyzz_status`='".$yyzz_status."'","`uid` in (".$uid.")");
			$this->obj->DB_update_all("friend_info","`iscert`='".$_POST['status']."'","`uid` in (".$uid.")");
			$id=$this->obj->DB_update_all("company_cert","`status`='".$_POST['status']."',`statusbody`='".$_POST['statusbody']."'","`uid` in (".$uid.") and `type`='3'");
			$company=$this->obj->DB_select_all("company","`uid` in (".$uid.")","uid,name,linkmail");
			if(is_array($company))
			{
				foreach($company as $v)
				{
					if($this->config['sy_email_comcert']=='1'){
						$this->send_msg_email(array("email"=>$v['linkmail'],"certinfo"=>$_POST['statusbody'],"comname"=>$v['name'],'uid'=>$v['uid'],'name'=>$v['name'],"type"=>"comcert"));
						if($_POST['status']==1)
						{
							$this->get_integral_action($v['uid'],"integral_comcert","认证营业执照");
						}
					}
				}
			}
			if($id)
			{
				$this->obj->ACT_layer_msg("企业认证审核(UID:".$uid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1);
			}else{
				$this->obj->ACT_layer_msg("设置失败！",8,$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function del_action()
	{
		if(is_array($_POST['del'])){
			$linkid=@implode(',',$_POST['del']);
			$type=1;
		}else{
			$this->check_token();
			$linkid=$_GET['uid'];
			$type=0;
		}
		if($linkid==""){
			$this->layer_msg('请选择您要删除的数据！',8,1,$_SERVER['HTTP_REFERER']);
		}
		$this->obj->DB_update_all("company","`yyzz_status`='0'","`uid` in (".$linkid.")");
		$this->obj->DB_update_all("friend_info","`iscert`='0'","`uid` in (".$linkid.")");
	    $cert=$this->obj->DB_select_all("company_cert","`uid` in (".$linkid.") and `type`='3'","`check`");
	    if(is_array($cert))
	    {
	     	foreach($cert as $v)
	     	{
	     		$this->obj->unlink_pic("../".$v['check']);
	     	}
	    }
		$delid=$this->obj->DB_delete_all("company_cert","`uid` in (".$linkid.")  and `type`='3'","");
		$delid?$this->layer_msg('企业认证(UID:'.$linkid.')删除成功！',9,$type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$type,$_SERVER['HTTP_REFERER']);
	}
}

?>