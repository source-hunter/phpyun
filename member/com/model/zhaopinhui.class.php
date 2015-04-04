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
class zhaopinhui_controller extends company{ 
	function index_action(){
		$this->public_action();
		$urlarr["c"]="zhaopinhui";
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$where="`uid`='".$this->uid."'";
		$rows=$this->get_page("zhaopinhui_com",$where,$pageurl,"10");
		if(is_array($rows))
		{
			foreach($rows as $key=>$v)
			{
				$zphid[]=$v['zid'];
			}
			$zphrows=$this->obj->DB_select_all("zhaopinhui","`id` in (".$this->pylode(',',$zphid).")","`id`,`title`,`address`");

			foreach($rows as $k=>$v)
			{
				foreach($zphrows as $val)
				{
					if($v['zid']==$val['id'])
					{
						$rows[$k]['title']=$val['title'];
						$rows[$k]['address']=$val['address'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->yunset("js_def",2);
		$this->com_tpl("zhaopinhui");
	}
	function del_action(){
		$delid=$this->obj->DB_delete_all("zhaopinhui_com","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'"," ");
		if($delid){
			$this->obj->member_log("退出招聘会");
			$this->layer_msg('退出成功！',9,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('退出失败！',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>