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
class searchcom_controller extends user{
	function index_action(){
		$where="`name` like '%".$_GET['keyword']."%'";
		$urlarr=array("c"=>"searchcom","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("company",$where,$pageurl,"10");
		if(is_array($rows))
		{
			include APP_PATH."/plus/city.cache.php";
			foreach($rows as $v)
			{
				$c_uid[]=$v['uid'];
			}
			$black=$this->obj->DB_select_all("blacklist","`p_uid`='".$this->uid."' and `c_uid` in (".@implode(",",$c_uid).")");
			foreach($rows as $k=>$v)
			{
				$rows[$k]['provinceid']=$city_name[$v['provinceid']];
				$rows[$k]['cityid']=$city_name[$v['cityid']];
				foreach($black as $val)
				{
					if($v['uid']==$val['c_uid'])
					{
						$rows[$k]['status']="1";
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->public_action();
		$this->user_tpl('searchcom');
	}
	function shielda_action(){
		if($_POST['ids']){
			$ids=@explode(",",$_POST['ids']);
			foreach($ids as $v)
			{
				$row=$this->obj->DB_select_once("blacklist","`c_uid`='".$v."' and `p_uid`='".$this->uid."'");
				if(empty($row))
				{
					$com=$this->obj->DB_select_once("company","`uid`='".$v."'","`name`");
					$data=array();
					$data['c_uid']=$v;
					$data['p_uid']=$this->uid;
					$data['inputtime']=time();
					$data['usertype']="1";
					$data['com_name']=$com['name'];
					$this->obj->insert_into("blacklist",$data);
					$this->obj->member_log("屏蔽公司 《".$com['name']."》");
				}
			}
			$this->obj->ACT_layer_msg("操作成功！",9,$_SERVER['HTTP_REFERER']);
		}
	}
	function shield_action(){
		if($_GET['uid']){
			$uid=(int)$_GET['uid'];
			$row=$this->obj->DB_select_once("blacklist","`c_uid`='".$uid."' and `p_uid`='".$this->uid."'");
			if(!empty($row))
			{
				$this->layer_msg('您已屏蔽够该用户！',8,0,"index.php?c=searchcom");
			}
			$com=$this->obj->DB_select_once("company","`uid`='".$uid."'","`name`");
			$data['c_uid']=$uid;
			$data['p_uid']=$this->uid;
			$data['inputtime']=time();
			$data['usertype']="1";
			$data['com_name']=$com['name'];
			$nid=$this->obj->insert_into("blacklist",$data);
			if($nid)
			{
				$this->obj->member_log("屏蔽公司 《".$com['name']."》");
				$this->layer_msg('屏蔽成功！',9,0,"index.php?c=blacklist");
			}else{
				$this->layer_msg('屏蔽失败！',8,0,"index.php?c=searchcom");
			}
		}
	}
}
?>