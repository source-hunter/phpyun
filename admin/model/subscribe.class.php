<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class subscribe_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"state","name"=>'״̬',"value"=>array("1"=>"����֤","2"=>"δ��֤"));
		$ad_time=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$search_list[]=array("param"=>"end","name"=>'����ʱ��',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
	    $where="1";
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `ctime` >='".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where .=" and `ctime`>'".strtotime('-'.intval($_GET['time']).' day')."'";
			}
		}
		if($_GET['state']){
			if($_GET['state']=='2'){
				$where.=" and `status`='0'";
			}else{
				$where.=" and `status`='".$_GET['state']."'";
			}
		}
		if($_GET['keyword'])
		{
		$where.=" and `email` like '%".$_GET['keyword']."%'";
		$urlarr['keyword']="".$_GET['keyword']."";
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("subscribe",$where,$pageurl,$this->config['sy_listnum']);
		include APP_PATH."/plus/com.cache.php";
		include APP_PATH."/plus/job.cache.php";
		include APP_PATH."/plus/city.cache.php";
		if(is_array($rows)){
			foreach($rows as $k=>$v){
                $rows[$k]['job1']=$job_name[$v['job1']];
				$rows[$k]['job1_son']=$job_name[$v['job1_son']];
				$rows[$k]['job_post']=$job_name[$v['job_post']];
				$rows[$k]['salary']=$comclass_name[$v['salary']];
				$rows[$k]['provinceid']=$city_name[$v['provinceid']];
				$rows[$k]['cityid']=$city_name[$v['cityid']];
				$rows[$k]['three_cityid']=$city_name[$v['three_cityid']];
			}
		}
		$this->yunset("rows",$rows);
		$this->yuntpl(array('admin/subscribe_list'));
	}

	function del_action()
	{
		$this->check_token();
		
	    if($_GET['del']){
	    	$del=$_GET['del'];
	    	if($del){
	    		if(is_array($del)){
					$layer_type=1;
					$this->obj->DB_delete_all("subscribe","`id` in(".@implode(',',$del).")","");
					$del=@implode(',',$del);
		    	}else{
					$this->obj->DB_delete_all("subscribe","`id`='$del'");
					$layer_type=0;
		    	}
				$this->layer_msg('����(ID:'.$del.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']);
	    	}else{
				$this->layer_msg('��ѡ����Ҫɾ������Ϣ��',8,0,$_SERVER['HTTP_REFERER']);
	    	}
	    }
	}
}

?>