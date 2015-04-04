<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2015 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class wx_controller extends common
{
	function index_action(){

		$this->yuntpl(array('admin/admin_wx'));
	}

	function save_action(){
 		if($_POST["msgconfig"]){
			unset($_POST["msgconfig"]);
			unset($_POST["pytoken"]);
			
			if (is_uploaded_file($_FILES['sy_wx_logo']['tmp_name'])) {
				
				$upload=$this->upload_pic("../data/logo/");
				$pictures=$upload->picture($_FILES['sy_wx_logo']);
				$pic = str_replace("../data/logo","data/logo",$pictures);

				$logo = $this->obj->DB_select_once("admin_config","`name`='sy_wx_logo'");
				if(is_array($logo)){
					$this->obj->unlink_pic("../".$logo['config']);
					$this->obj->DB_update_all("admin_config","`config`='".$pic."'","`name`='sy_wx_logo'");
				}else{
					$this->obj->DB_insert_once("admin_config","`config`='".$pic."',`name`='sy_wx_logo'");
				}
			}

			foreach($_POST as $key=>$v){
		    	$config=$this->obj->DB_select_num("admin_config","`name`='$key'");
			   if($config==false){
				$this->obj->DB_insert_once("admin_config","`name`='$key',`config`='".$v."'");
			   }else{
				$this->obj->DB_update_all("admin_config","`config`='".$v."'","`name`='$key'");
			   }
		 	}
			$this->web_config();
			$this->obj->ACT_layer_msg("微信配置更新成功！",9,$_SERVER['HTTP_REFERER'],2,1);
		}
	}

	function binduser_action(){

 		$where = "`wxid`!=''";
		if($_GET['keyword']){
			$where.=" and `username` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$order = " ORDER BY `wxbindtime` DESC";
		$urlarr['c']=$_GET['c'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$userList=$this->get_page("member",$where.$order,$pageurl,$this->config['sy_listnum'],"`uid`,`username`,`wxid`,`wxbindtime`");

		$this->yunset("userList",$userList);
		$this->yuntpl(array('admin/admin_wxbind'));
	}

	function keyword_action(){

 		$where = "`type`='8'";
		if(trim($_GET['keyword'])){
			$where.=" and `key_name` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=trim($_GET['keyword']);
		}
		$order = " ORDER BY `num` DESC";
		$urlarr['c']=$_GET['c'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$keyList=$this->get_page("hot_key",$where.$order,$pageurl,$this->config['sy_listnum']);

		$this->yunset("keyList",$keyList);
		$this->yuntpl(array('admin/admin_wxkey'));
	}

	function wxnav_action()
	{
  		$list = $this->obj->DB_select_all("wxnav","1 ORDER BY `sort` ASC");
		if(is_array($list)){
			foreach($list as $value){
				if($value['keyid']=='0'){
					$navlist[$value['id']] = $value;
				}
			}
			foreach($list as $val){
				foreach($navlist as $key=>$v){
					if($v['id']==$val['keyid']){
						$navlist[$key]['list'][] = $val;
					}
				}
			}
		}
		$this->yunset('navlist',$navlist);
		$this->yuntpl(array('admin/admin_wxnav'));
	}

	function wxlog_action()
	{
 		$where = '1';
		if(trim($_GET['keyword'])){
			$where.=" and `wxname` like '%".trim($_GET['keyword'])."%'";
			$urlarr['keyword']=trim($_GET['keyword']);
		}
		$urlarr['c']="wxlog";
		$urlarr['page']="{{page}}";
		$order = " ORDER BY `time` DESC";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$logList=$this->get_page("wxlog",$where.$order,$pageurl,$this->config['sy_listnum']);
		$this->yunset("logList",$logList);
		$this->yuntpl(array('admin/admin_wxlog'));
	}
	function dellog_action(){
		$this->check_token();
		if($_GET['del']){
			$this->obj->DB_delete_all("wxlog","`id` in(".@implode(',',$_GET['del']).")","");
			$this->layer_msg('操作日志(ID:'.@implode(',',$_GET['del']).')删除成功！',9,1,$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function edit_action()
	{
		if($_POST['name'] && $_POST['keyid']!=='')
		{
			$_POST['name'] = $this->stringfilter($_POST['name']);
			$_POST['key'] = $this->stringfilter($_POST['key']);
			$where = "`name`='".$_POST['name']."'";
			if($_POST['keyid']>0)
			{
				if(!$_POST['key'] && $_POST['type']!='view')
				{
					echo 1;
					exit();
				}elseif($_POST['key']!=""){

					$where = "(`name`='".$_POST['name']."' OR `key`='".$_POST['key']."')";
				}
			}
			if($_POST['navid']>0)
			{
				$where .= " AND  `id`!='".$_POST['navid']."'";
			}

 			$nav = $this->obj->DB_select_num("wxnav",$where);
			if($nav>0)
			{
				echo 2;
				exit();
			}
			if($_POST['keyid']=='0')
			{
				$_POST['type']= 'click';
				unset($_POST['url']);unset($_POST['key']);
			}
			unset($_POST['pytoken']);
			if($_POST['navid']>0)
			{
				$navid = $_POST['navid'];
				unset($_POST['navid']);

				$this->obj->update_once("wxnav",$_POST,array('id'=>$navid));
				$this->obj->admin_log("微信菜单(ID:".$navid.")修改成功");
			}else{
				$navid=$this->obj->insert_into("wxnav",$_POST);
				$this->obj->admin_log("微信菜单(ID:".$navid.")添加成功");
			}

			echo 3;
			exit();
		}else{
			echo 1;
			exit();
		}

	}
 	public function creat_action()
	{
 		$list = $this->obj->DB_select_all("wxnav","1 ORDER BY `keyid` ASC,`sort` ASC");

		if(is_array($list))
		{
			foreach($list as $value){
				if($value['keyid']=='0'){
					$navlist[$value['id']] = $value;
				}
			}
			foreach($list as $val){
				foreach($navlist as $key=>$v){
					if($v['id']==$val['keyid']){
						$navlist[$key]['list'][] = $val;
					}
				}
			} 
			$CreatNav = '{"button":[';
			$i=0;
			foreach($navlist as $key=>$value)
			{
				if($i<1)
				{
					$CreatNav.='{"name":"'.iconv('gbk','utf-8',$value['name']).'","sub_button":[';
				}else{
					$CreatNav.=',{"name":"'.iconv('gbk','utf-8',$value['name']).'","sub_button":[';
				}
				$i++;
				$NavInfo = array();

				if(is_array($value['list']) && !empty($value['list'])){

					foreach($value['list'] as $k=>$v)
					{
						if($k>0)
						{
							$CreatNav.=',';
						}
						if($v['type']=='click')
						{
							$CreatNav.='{"type":"click","name":"'.iconv('gbk','utf-8',$v['name']).'","key":"'.iconv('gbk','utf-8',$v['key']).'"}';

						}elseif($v['type']=='view'){

							$CreatNav.='{"type":"view","name":"'.iconv('gbk','utf-8',$v['name']).'","url":"'.$v['url'].'"}';
						}
					}
				}
				$CreatNav.=']}';
			}
			$CreatNav.=']}';
 			$Token = $this->getToken();

 			$DelUrl = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$Token;
			$this->CurlPost($DelUrl);

			$Url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$Token;
			$result = $this->CurlPost($Url,$CreatNav);
			$Info = json_decode($result);

			if($Info->errcode=='0' || $Info->errmsg=='ok'){
 				echo 1;die;
			}else{
 				echo 2;die;
			}
		}
	}
 	function delnav_action(){
		if($_POST['del']){
			$this->obj->DB_delete_all("wxnav","`id` in(".@implode(',',$_POST['del']).")",'');
			$this->layer_msg('微信菜单(ID:'.@implode(',',$_POST['del']).')删除成功！',9,1,$_SERVER['HTTP_REFERER']);
		}
		if((int)$_GET['delid']){
			$this->check_token();
			$id=$this->obj->DB_delete_all("wxnav","`id`='".$_GET['delid']."'");
			$id?$this->layer_msg('微信菜单(ID:'.$_GET['delid'].')删除成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('删除失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
 	function deluser_action(){
		if($_GET['del']){
			$this->check_token();
			$this->obj->DB_update_all("member","`wxid`=''","`uid` in(".@implode(',',$_GET['del']).")");
			$this->layer_msg('微信用户(ID:'.@implode(',',$_GET['del']).')取消绑定成功！',9,1,$_SERVER['HTTP_REFERER']);
		}
	}
	function ajax_action()
	{
		if($_POST['sort'])
		{
			$this->obj->DB_update_all("wxnav","`sort`='".$_POST['sort']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("微信菜单(ID:".$_POST['id'].")排序修改成功");
		}
		if($_POST['name'])
		{
			$_POST['name']=$this->stringfilter($_POST['name']);
			$this->obj->DB_update_all("wxnav","`name`='".$_POST['name']."'","`id`='".$_POST['id']."'");
			$this->obj->admin_log("微信菜单(ID:".$_POST['id'].")名称修改成功");
		}
		echo '1';die;
	} 
}

?>