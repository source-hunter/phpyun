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
class look_controller extends user{
	function index_action(){
		$urlarr=array("c"=>"look","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$look=$this->get_page("look_resume","`uid`='".$this->uid."' and `status`='0' order by datetime desc",$pageurl,"15");
		if(is_array($look))
		{
			foreach($look as $k=>$v)
			{
				$com_uid[] = $v['com_id'];
				$res_uid[] = $v['resume_id'];
			}
			$resume=$this->obj->DB_select_all("resume_expect","`id` IN (".$this->pylode(",",$res_uid).")","id,name");
			$com=$this->obj->DB_select_all("company","`uid` IN (".$this->pylode(",",$com_uid).")","uid,name");
			foreach($look as $k=>$v)
			{
				foreach($resume as $key=>$val)
				{
					if($v['resume_id']==$val['id'])
					{
						$look[$k]['resume']=$val['name'];
					}
				}
				foreach($com as $value)
				{
					if($v['com_id']==$value['uid'])
					{
						$look[$k]['com']=$value['name'];
					}
				}
			}
		}
		$this->yunset("js_def",2);
		$this->yunset("look",$look);
		$this->public_action();
		$this->user_tpl('look');
	}
	function del_action(){
		if($_GET['id']||$_GET['del']){
			if(is_array($_GET['del'])){
				$del=$this->pylode(",",$_GET['del']);
				$layer_type=1;
			}else{
				$del=(int)$_GET['id'];
				$layer_type=0;
			}

			$this->obj->DB_update_all("look_resume","`status`='1'","`id` in (".$del.") and `uid`='".$this->uid."'");
			$this->obj->member_log("删除简历浏览记录(ID:".$del.")");
 			$this->layer_msg('删除成功！',9,$layer_type,"index.php?c=look");
		}
	}
}
?>