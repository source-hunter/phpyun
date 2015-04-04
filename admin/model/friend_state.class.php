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
class friend_state_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"end","name"=>'发布时间',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		include(PLUS_PATH."user.cache.php");
		include(PLUS_PATH."com.cache.php");
		$this->set_search();
		$where = "1";
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
		if($_GET['usertype']){
			$where.=" AND `usertype`='".$_GET['usertype']."'";
			$urlarr['usertype']=$_GET['usertype'];
		}
		if($_GET['status']=="1"){
			$where.=" AND `status`='".$_GET['status']."'";
		}elseif($_GET['status']=="2"){
			$where.=" AND `status`='0'";
		}

 		
		if($this->config['sy_web_site']=='1'){
			$wheres=1;
			$com=$this->obj->DB_select_all("company",$wheres,"`uid`");
			
			$uid=array();
			if(is_array($com)){
				foreach($com as $v){
					$uid[]=$v['uid'];
				}
			}
			
			$where.=" AND `uid` in (".@implode(",",$uid).")";
		}
		if($_GET['keyword']){
			if($_GET['type']==1){
				if($uid&&is_array($uid)){
					$infouid = $this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%' and `uid` in(".@implode(',',$uid).")","`uid`,`username`");
				}else{
					$infouid = $this->obj->DB_select_all("member","`username` like '%".$_GET['keyword']."%'","`uid`,`username`");
				}
				if(is_array($infouid)&&$infouid){
					foreach($infouid as $k=>$v){
						$info_uids[] = $v['uid'];
					}
					$uids = @implode(",",$info_uids);
				}
				$where.=" and `uid` in ($uids)";
			}else{
				$where.=" and `content` like '%".$_GET['keyword']."%'";
			}
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
		}
		
		if($_GET['order']){
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
        $urlarr['status']=$_GET['status'];
        $urlarr['order']=$_GET['order'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$mes_list=$this->get_page("friend_state",$where,$pageurl,$this->config['sy_listnum']);
		if(is_array($mes_list)){
			if($infouid==''){
				$uids=array();
				foreach($mes_list as $value){
					$uids[]=$value['uid'];
				}
				$infouid = $this->obj->DB_select_all("member","`uid` in (".@implode(',',$uids).")","`uid`,`username`");
			}
			foreach($mes_list as $key=>$value){
				foreach($infouid as $k=>$v){
					if($value['uid']==$v['uid']){
						$mes_list[$key]['username'] = $v['username'];
					}
				}
			}
		}
		$this->yunset("get_type", $_GET);
		$this->yunset("mes_list",$mes_list);
		$this->yuntpl(array('admin/friend_state'));
	}

	function del_action(){

		$this->check_token();
	
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($_GET['del']){
	    		if(is_array($_GET['del'])){
					$this->obj->DB_delete_all("friend_state","`id` in(".@implode(',',$_GET['del']).")","");
					$this->obj->DB_delete_all("friend_reply","`nid` in(".@implode(',',$_GET['del']).")","");
					$del=@implode(',',$_GET['del']);
					$layer_type='1';
		    	}else{
					$layer_type='0';
		    		$this->obj->DB_delete_all("friend_state","`id`='$del'");
		    		$this->obj->DB_delete_all("friend_reply","`nid`='$del'");
		    	}
	    		$this->layer_msg( "会员动态(ID:".$del.")删除成功！",9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
	    		$this->layer_msg( "请选择您要删除的信息！",8,1,$_SERVER['HTTP_REFERER']);
	    	}
	    }
		
		if($_GET['time']){
			$time=strtotime($_GET['time']." 23:59:59");
			$friend_state=$this->obj->DB_select_all("friend_state","`ctime`<'$time'","id");
			if($friend_state&&is_array($friend_state)){
				foreach($friend_state as $val){
					$ids[]=$val['id'];
				}
				$ids=@implode(',',$ids);
				$this->obj->DB_delete_all("friend_state","`id` in (".$ids.")","");
				$this->obj->DB_delete_all("friend_reply","`nid` in (".$ids.")","");
			}
			$this->layer_msg("会员动态(ID:".$ids.")删除成功！",9,0,$_SERVER['HTTP_REFERER']);
		}
	}
}