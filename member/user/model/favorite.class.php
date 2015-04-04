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
class favorite_controller extends user{
	function index_action(){
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$this->CacheInclude($CacheArr);
		$this->public_action();
		$this->member_satic();
		$urlarr=array("c"=>"favorite","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("fav_job","`uid`='".$this->uid."' order by id desc",$pageurl,"20");
		$fnum=$this->obj->DB_select_num("fav_job","`uid`='".$this->uid."'","`id`");
		if($rows&&is_array($rows)){
			foreach($rows as $val){
				$jobids[]=$val['job_id'];
			}
			$company_job=$this->obj->DB_select_all("company_job","`id` in(".@implode(',',$jobids).")","`id`,`salary`,`provinceid`,`cityid`");
			foreach($rows as $key=>$val){
				foreach($company_job as $v){
					if($val['job_id']==$v['id']){
						$rows[$key]['salary']=$v['salary'];
						$rows[$key]['provinceid']=$v['provinceid'];
						$rows[$key]['cityid']=$v['cityid'];
					}
				}
			}
		}
		$num=$this->obj->DB_select_num("fav_job","`uid`='".$this->uid."'");
		$this->obj->DB_update_all("member_statis","fav_jobnum='".$num."'","`uid`='".$this->uid."'");
		$this->yunset("rows",$rows);
		$this->yunset("fnum",$fnum);
		$this->user_tpl('favorite');
	}
	function del_action(){
		if($_GET['id']){
			$del=(int)$_GET['id'];
			$nid=$this->obj->DB_delete_all("fav_job","`id`='".$del."' and `uid`='".$this->uid."'");
			if($nid){
				$fnum=$this->obj->DB_select_num("fav_job","`uid`='".$this->uid."'","`id`");
				$this->obj->update_once('member_statis',array('fav_jobnum'=>$fnum),array('uid'=>$this->uid));
				$this->obj->member_log("删除收藏的职位信息",5,3);
				$this->layer_msg('删除成功！',9,0,"index.php?c=favorite");
			}else{
				$this->layer_msg('删除失败！',8,0,"index.php?c=favorite");
			}
		}
	}
}
?>