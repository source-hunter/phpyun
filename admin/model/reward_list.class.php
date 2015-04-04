<?php
/** $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class reward_list_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'审核状态',"value"=>array("2"=>"未审核","1"=>"已审核"));
		$search_list[]=array("param"=>"change","name"=>'兑换时间',"value"=>array("1"=>"今天","3"=>"最近三天","7"=>"最近七天","15"=>"最近半月","30"=>"最近一个月"));
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
		$where="1";
		if($_GET['status']){
            if($_GET['status']=='2'){
            	$where.=" and `status`='0'";
            }else{
 				$where.=" and `status`='".$_GET['status']."'";
            }
			$urlarr['status']=$_GET['status'];
		}
		if($_GET['change']){
			if($_GET['change']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.$_GET['change'].'day')."'";
			}
			$urlarr['change']=$_GET['change'];
		}
		if(trim($_GET['keyword']))
		{
			if($_GET['type']=='1'){
				$where.=" and `name` like '%".trim($_GET['keyword'])."%'";
			}elseif($_GET['type']=='2'){
				$where.=" and `username` like '%".trim($_GET['keyword'])."%'";
			}
			$urlarr['type']="".$_GET['type']."";
			$urlarr['keyword']="".trim($_GET['keyword'])."";
		}
		if($_GET['order']){
			if($_GET['order']=="desc"){
				$order=" order by `".$_GET['t']."` desc";
			}else{
				$order=" order by `".$_GET['t']."` asc";
			}

		}else{
			$order=" order by `id` desc";
		}
		if($_GET['order']=="asc"){
			$this->yunset("order","desc");
		}else{
			$this->yunset("order","asc");
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("change",$where.$order,$pageurl,$this->config['sy_listnum']);
		$this->yunset("rows",$rows);
		$changetime=array('1'=>'一天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
        $this->yunset("change",$changetime);
		$this->yuntpl(array('admin/reward_list'));
	}

	function statusbody_action(){
		$userinfo = $this->obj->DB_select_once("change","`id`=".$_GET['id'],"`statusbody`,`linktel`,`linkman`,`body`");
		echo $userinfo['statusbody'];die;
	}

	function status_action(){ 
		extract($_POST);
		if(intval($_POST['id'])){ 
			$change=$this->obj->DB_select_once("change","`id`='".intval($_POST['id'])."'","`gid`,`num`,`status`");
			if($status>0&&$change['status']=='0'){ 
			
				if($status=='1'){
					$this->obj->DB_update_all("reward","`num`=`num`+'".$change['num']."'","`id`='".$change['gid']."'");
				}else{
					$this->obj->DB_update_all("reward","`stock`=`stock`+'".$change['num']."'","`id`='".$change['gid']."'");
				}
			}
			
			$id=$this->obj->DB_update_all("change","`status`='".$status."',`linktel`='".$linktel."',`linkman`='".$linkman."',`body`='".$body."',`statusbody`='".$statusbody."'","`id`='".intval($_POST['id'])."'");
			
 			$id?$this->obj->ACT_layer_msg("兑换记录审核(ID:".$aid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function statuss_action(){
		$change=$this->obj->DB_select_all("change","`id` IN (".$_POST['allid'].")","`gid`,`num`,`status`");
		if($_POST['status']=='1'){
			foreach($change as $val){
				$this->obj->DB_update_all("reward","`num`=`num`+'".$val['num']."'","`id`='".$val['gid']."'");
			}
		}else{
			foreach($change as $val){
				$this->obj->DB_update_all("reward","`stock`=`stock`+'".$val['num']."'","`id`='".$val['gid']."'");
			}
		}
		$this->obj->DB_update_all("change","`status`='".$_POST['status']."'","`id` IN (".$_POST['allid'].")");
		$this->obj->admin_log("批量审核(ID:".$_POST['allid'].")审核成功");
		echo $_POST['status'];die;
	}


	function del_action(){
		if($_GET['del']){ 
			$this->check_token();
			$del=$_GET['del'];
			if(is_array($del)){
				$del=@implode(',',$del);
				$layer_type=1;
			}else{
				$layer_type=0;
			}

		    $rowss=$this->obj->DB_select_all("change","`id` in(".$del.")","`uid`,`gid`,`num`,`integral`,`usertype`");
				if($rowss&&is_array($rowss)){
					foreach($rowss as $val){
						if($val['usertype']=="1")
						{
							$table="member_statis";
						}elseif($val['usertype']=="2"){
							$table="company_statis";
						}
						$this->obj->DB_update_all($table,"`integral`=`integral`+".$val['integral']."","`uid`='".$val['uid']."'");
						$this->obj->DB_update_all("reward","`stock`=`stock`+".$val['num']."","`id`='".$val['gid']."'");
					}
				}








		$del=$this->obj->DB_delete_all("change","`id` in (".$del.")"," ");
		$del?$this->layer_msg('兑换记录(ID:'.$del.')删除成功！',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('请选择要删除的内容！',8,0,$_SERVER['HTTP_REFERER']);
		}



	}
}

?>