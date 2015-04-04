<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
*/
class finder_controller extends user{
	function index_action(){
		$finder=$this->finder();
		$syfinder=$this->config['user_finder']-count($finder);
		$syfinder<0?0:$syfinder;
		$this->yunset("syfinder",$syfinder);
		$this->yunset("finder",$finder);
		$this->user_tpl('finder');
	}
	function save_action(){
		$para=array();
		if($_POST['submitBtn']){
			$num=$this->obj->DB_select_num('finder',"`uid`='".$this->uid."'");
			if($num>=$this->config['user_finder']){
				$this->obj->ACT_layer_msg("�Ѵﵽ���������������",8,"index.php?c=finder");
			}
			$post=$this->post_trim($_POST);
			$id=(int)$post['id'];
			$name=$post['name'];
  			unset($post['id']);
			unset($post['submitBtn']);
			unset($post['cycle']);
			unset($post['job_num']);
			unset($post['email']);
			unset($post['name']);
			foreach($post as $key=>$val){
				if(trim($val)){
					$para[]=$key."=".$val;
				}
			}
			$paras=@implode('##',$para);
			$result=$this->insertfinder($paras,$id,$name);
			$result?$this->obj->ACT_layer_msg("��Ϣ���³ɹ���",9,"index.php?c=finder"):$this->obj->ACT_layer_msg("��Ϣ����ʧ�ܣ�",8,"index.php?c=finder");
		}
	}
	function del_action(){
		if($_GET['id']){
			$this->obj->member_log("ɾ��������");
			$res=$this->obj->DB_delete_all("finder","`id`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			$res?$this->layer_msg("ɾ���ɹ���",9,0):$this->layer_msg("ɾ��ʧ�ܣ�",8,0);
		}
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
		}
		$sdate=array('1'=>'һ����',"3"=>'������','7'=>'������',"15"=>'ʮ������','30'=>'һ������',"60"=>'��������');
		$this->yunset("sdate",$sdate); 
		$this->user_tpl('finderinfo');
	}
}
?>