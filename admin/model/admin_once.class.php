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
class admin_once_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'审核状态',"value"=>array("1"=>"已审核","3"=>"未审核","2"=>"已过期"));
		$lo_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"time","name"=>'发布时间',"value"=>$lo_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
        if($_GET['m']=='admin_once'){
        	$where=1;
		  if(trim($_GET['keyword'])){
				if($_GET['type']){
					if ($_GET['type']=='1'){
						$where.=" and `companyname` like '%".$_GET['keyword']."%'";
					}elseif ($_GET['type']=='2'){
						$where.=" and `title` like '%".$_GET['keyword']."%'";
					}elseif ($_GET['type']=='3'){
						$where.=" and `phone` like '%".$_GET['keyword']."%'";
					}elseif ($_GET['type']=='4'){
						$where.=" and `linkman` like '%".$_GET['keyword']."%'";
					}
					$urlarr['type']=$_GET['type'];
				}
				$urlarr['keyword']=$_GET['keyword'];
		  }elseif($_GET['status']){
				$time=mktime();
				if($_GET['status']=='1'){
					$where.=" and status='1' and `edate`>$time ";
					$urlarr['status']='1';
				}elseif($_GET['status']=='3'){
					$where.=" and status='0' and `edate`>$time ";
					$urlarr['status']='3';
				}elseif($_GET['status']=='2'){
					$where.=" and `edate`<$time ";
					$urlarr['status']='2';
				}
		    }
        }
		if($_GET['time']){
			if($_GET['time']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.(int)$_GET['time'].'day')."'";
			}
			$urlarr['time']=$_GET['time'];
		}
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("once_job",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($rows)&&$rows)
		{
			foreach($rows as $key=>$val)
			{
				if($val['edate']<mktime()){
					$rows[$key]['status']=2;
				}
			}
		}
		$this->yunset("rows", $rows);
		$this->yunset("get_type", $_GET);
		$this->yuntpl(array('admin/admin_once'));
	}

	function ctime_action(){
		extract($_POST);
		$id=@explode(",",$onceids);
		if(is_array($id)){
			$posttime=$endtime*86400;
			$id=$this->obj->DB_update_all("once_job","`edate`=`edate`+'".$posttime."'","`id` in (".$onceids.")");
			$id?$this->obj->ACT_layer_msg("职位延期(ID:".$jobid.")设置成功！",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("设置失败！",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",3,$_SERVER['HTTP_REFERER']);
		}
	}
	function show_action(){
		$show=$this->obj->DB_select_all("once_job","`id`='".$_GET['id']."'");
		$this->yunset("show",$show[0]);
		$this->yuntpl(array('admin/admin_once_show'));
	}
	function status_action(){
		$this->obj->DB_update_all("once_job","`status`='".$_POST['status']."'","`id` IN (".$_POST['allid'].")");
		$this->obj->admin_log("职位(ID:".$_POST['allid'].")审核成功");
		echo $_POST['status'];die;
	}
	function ajax_action()
	{
		include APP_PATH."/plus/user.cache.php";
		$row=$this->obj->DB_select_once("once_job","`id`='".$_GET['id']."'");
		$info['title']=iconv("gbk","utf-8",$row['title']);
		$info['mans']=$row['mans'];
		$info['require']=iconv("gbk","utf-8",$row['require']);
		$info['companyname']=iconv("gbk","utf-8",$row['companyname']);
		$info['phone']=$row['phone'];
		$info['linkman']=iconv("gbk","utf-8",$row['linkman']);
		$info['address']=iconv("gbk","utf-8",$row['address']);
		$info['time']=date("Y-m-d",$row['ctime']);
		$info['status']=$row['status'];
		$info['qq']=$row['qq'];
		$info['email']=$row['email'];
		$info['edate']=date("Y-m-d",$row['edate']);
		echo json_encode($info);
	}
	function del_action(){
		$this->check_token();
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if(is_array($del)){
				$this->obj->DB_delete_all("once_job","`id` in(".@implode(',',$del).")"," ");
				$this->layer_msg("职位(ID:".@implode(',',$del).")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg("请选择您要删除的招聘！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	    if(isset($_GET['id'])){
			$result=$this->obj->DB_delete_all("once_job","`id`='".$_GET['id']."'" );
			$result?$this->layer_msg("职位(ID:".$_GET['id'].")删除成功！",9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("非法操作！",8,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>