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
class finder_controller extends company{
	function index_action(){
		$finder=$this->obj->DB_select_all("finder","`uid`='".$this->uid."' order  by `id` desc");
		if($finder&&is_array($finder)){
			include APP_PATH."/plus/user.cache.php";
			include APP_PATH."/plus/job.cache.php";
			include APP_PATH."/plus/industry.cache.php";
			include APP_PATH."/plus/city.cache.php";

			$uptime=array('1'=>'今天',"3"=>'最近三天','7'=>'最近七天','30'=>'最近一个月',"90"=>'最近三个月');
			$adtime=array('1'=>'一天内',"3"=>'三天内','7'=>'七天内',"15"=>'十五天内','30'=>'一个月内',"60"=>'两个月内');


			foreach($finder as $key=>$val){
				$arr=$findername=array();
				$para=@explode('##',$val['para']);
				$arr['m']='user';
				$arr['c']='search';
				foreach($para as $val){
					$parav=@explode('=',$val);
					$arr[$parav[0]]=$parav[1];
				}
				if($arr['jobids']){
					$jobids=@explode(',',$arr['jobids']);
					foreach($jobids as $val){
						$jobname[]=$job_name[$val];
					}
					$findername[]=@implode('、',$jobname);
				}
				if($arr['hy']){$findername[]=$industry_name[$arr['hy']];}
				if($arr['adtime']){$findername[]=$adtime[$arr['adtime']];}
				if($arr['edu']){$findername[]=$userclass_name[$arr['edu']];}
				if($arr['word']){$findername[]=$userclass_name[$arr['word']];}
				if($arr['uptime']){$findername[]=$uptime[$arr['uptime']];}
				if($arr['salary']){$findername[]=$userclass_name[$arr['salary']];}
				if($arr['type']){$findername[]=$userclass_name[$arr['type']];}
				if($arr['cityid']){$findername[]=$city_name[$arr['cityid']];}
				if($arr['exp']){$findername[]=$userclass_name[$arr['exp']];}
				if($arr['sex']){$findername[]=$userclass_name[$arr['sex']];}
				$finder[$key]['findername']=@implode('+',$findername);
				$finder[$key]['url']=$this->url('index','index',$arr);
			}
		}

		$this->yunset("js_def",6);
		$this->public_action();
		$this->yunset("finder",$finder);
		$this->com_tpl('finder');
	}
	function edit_action(){
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$result=$this->CacheInclude($CacheArr);
	 
		if($_GET['id']){
			$info=$this->obj->DB_select_once("finder","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."'");
			if($info['para']){
				$para=@explode('##',$info['para']);
				foreach($para as $val){
					$arr=@explode('=',$val);
					$parav[$arr['0']]=$arr['1'];
				}
				if($parav['jobids']){
					$jobids=@explode(',',$parav['jobids']);
					foreach($jobids as $val){
						$jobname[]=$result['job_name'][$val];
					}
					$parav['jobname']=@implode(',',$jobname);
				}
				$this->yunset("parav",$parav);
			}
			$this->yunset("info",$info); 
			$this->yunset("js_def",1);
			$this->public_action();

		}
		$uptime=array('1'=>'今天',"3"=>'最近三天','7'=>'最近七天','30'=>'最近一个月',"90"=>'最近三个月');
		$adtime=array('1'=>'一天内',"3"=>'三天内','7'=>'七天内',"15"=>'十五天内','30'=>'一个月内',"60"=>'两个月内');
	 
		$this->yunset("adtime",$adtime);
		$this->yunset("uptime",$uptime);
		$this->com_tpl('finderinfo');
	}
	function save_action()
	{
		if($_POST['submitBtn'])
		{
			$num=$this->obj->DB_select_num('finder',"`uid`='".$this->uid."'");
			if($_POST['id']=="")
			{
				if($num>=$this->config['com_finder'])
				{
					$this->obj->ACT_layer_msg("已达到最大搜索器数量！",8,"index.php?c=finder");
				}
			}
			$post=$this->post_trim($_POST);
			$id=(int)$post['id'];
			$cycle=(int)$post['cycle'];
			$job_num=(int)$post['job_num'];
			$name=$post['name'];
 			unset($post['id']);
			unset($post['submitBtn']);
			unset($post['name']);
			foreach($post as $key=>$val){
				if(trim($val)){
					$para[]=$key."=".$val;
				}
			}
			$paras=@implode('##',$para);
			$result=$this->insertfinder($paras,$id,$name);
			$result?$this->obj->ACT_layer_msg("信息更新成功！",9,"index.php?c=finder"):$this->obj->ACT_layer_msg("信息更新失败！",8,"index.php?c=finder");
		}
	}
	function del_action(){
		if($_GET['id']){
			$this->obj->member_log("删除搜索器");
			$res=$this->obj->DB_delete_all("finder","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			$res?$this->layer_msg("删除成功！",9,0):$this->layer_msg("删除失败！",8,0);
		}
	}
}
?>