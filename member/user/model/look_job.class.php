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
class look_job_controller extends user{
	function index_action(){

		$urlarr=array("c"=>"look_job","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$look=$this->get_page("look_job","`uid`='".$this->uid."' and `status`='0' order by `datetime` desc",$pageurl,"10");
		if(is_array($look))
		{
			include APP_PATH."/plus/city.cache.php";
			include APP_PATH."/plus/com.cache.php";
			foreach($look as $v)
			{
				$jobid[]=$v['jobid'];
			}
			$job=$this->obj->DB_select_all("company_job","`id` in (".@implode(",",$jobid).")","`id`,`name`,`com_name`,`salary`,`provinceid`,`cityid`");
			foreach($look as $k=>$v)
			{
				foreach($job as $val)
				{
					if($v['jobid']==$val['id'])
					{
						$look[$k]['jobname']=$val['name'];
						$look[$k]['comname']=$val['com_name'];
						$look[$k]['salary']=$comclass_name[$val['salary']];
						$look[$k]['provinceid']=$city_name[$val['provinceid']];
						$look[$k]['cityid']=$city_name[$val['cityid']];
					}
				}
			}
		}
		$this->yunset("js_def",2);
		$this->yunset("look",$look);
		$this->public_action();
		$this->user_tpl('look_job');
	}
	function del_action(){
		if($_GET['del']||$_GET['id']){
			if(is_array($_GET['del'])){
				$del=$this->pylode(",",$_GET['del']);
				$layer_type=1;
			}else{
				$del=(int)$_GET['id'];
				$layer_type=0;
			}
			$this->obj->DB_update_all("look_job","`status`='1'","`id` in (".$del.") and `uid`='".$this->uid."'");
			$this->obj->member_log("ɾ��ְλ�����¼(ID:".$del.")");
 			$this->layer_msg('ɾ���ɹ���',9,$layer_type,"index.php?c=look_job");
		}
	}
}
?>