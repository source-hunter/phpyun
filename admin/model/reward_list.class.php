<?php
/** $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class reward_list_controller extends common
{
	function set_search(){
		$search_list[]=array("param"=>"status","name"=>'���״̬',"value"=>array("2"=>"δ���","1"=>"�����"));
		$search_list[]=array("param"=>"change","name"=>'�һ�ʱ��',"value"=>array("1"=>"����","3"=>"�������","7"=>"�������","15"=>"�������","30"=>"���һ����"));
		$this->yunset("search_list",$search_list);
	}
	function index_action()
	{
		$this->set_search();
		$where="1";
		if($_GET['status']){
            if($_GET['status']=='2'){
            	$where.=" and `status`='0'";
            }else{
 				$where.=" and `status`='".$_GET['status']."'";
            }
			$urlarr['status']=$_GET['status'];
		}
		if($_GET['change']){
			if($_GET['change']=='1'){
				$where.=" and `ctime` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `ctime` >= '".strtotime('-'.$_GET['change'].'day')."'";
			}
			$urlarr['change']=$_GET['change'];
		}
		if(trim($_GET['keyword']))
		{
			if($_GET['type']=='1'){
				$where.=" and `name` like '%".trim($_GET['keyword'])."%'";
			}elseif($_GET['type']=='2'){
				$where.=" and `username` like '%".trim($_GET['keyword'])."%'";
			}
			$urlarr['type']="".$_GET['type']."";
			$urlarr['keyword']="".trim($_GET['keyword'])."";
		}
		if($_GET['order']){
			if($_GET['order']=="desc"){
				$order=" order by `".$_GET['t']."` desc";
			}else{
				$order=" order by `".$_GET['t']."` asc";
			}

		}else{
			$order=" order by `id` desc";
		}
		if($_GET['order']=="asc"){
			$this->yunset("order","desc");
		}else{
			$this->yunset("order","asc");
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("change",$where.$order,$pageurl,$this->config['sy_listnum']);
		$this->yunset("rows",$rows);
		$changetime=array('1'=>'һ��','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
        $this->yunset("change",$changetime);
		$this->yuntpl(array('admin/reward_list'));
	}

	function statusbody_action(){
		$userinfo = $this->obj->DB_select_once("change","`id`=".$_GET['id'],"`statusbody`,`linktel`,`linkman`,`body`");
		echo $userinfo['statusbody'];die;
	}

	function status_action(){ 
		extract($_POST);
		if(intval($_POST['id'])){ 
			$change=$this->obj->DB_select_once("change","`id`='".intval($_POST['id'])."'","`gid`,`num`,`status`");
			if($status>0&&$change['status']=='0'){ 
			
				if($status=='1'){
					$this->obj->DB_update_all("reward","`num`=`num`+'".$change['num']."'","`id`='".$change['gid']."'");
				}else{
					$this->obj->DB_update_all("reward","`stock`=`stock`+'".$change['num']."'","`id`='".$change['gid']."'");
				}
			}
			
			$id=$this->obj->DB_update_all("change","`status`='".$status."',`linktel`='".$linktel."',`linkman`='".$linkman."',`body`='".$body."',`statusbody`='".$statusbody."'","`id`='".intval($_POST['id'])."'");
			
 			$id?$this->obj->ACT_layer_msg("�һ���¼���(ID:".$aid.")���óɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("�Ƿ�������",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function statuss_action(){
		$change=$this->obj->DB_select_all("change","`id` IN (".$_POST['allid'].")","`gid`,`num`,`status`");
		if($_POST['status']=='1'){
			foreach($change as $val){
				$this->obj->DB_update_all("reward","`num`=`num`+'".$val['num']."'","`id`='".$val['gid']."'");
			}
		}else{
			foreach($change as $val){
				$this->obj->DB_update_all("reward","`stock`=`stock`+'".$val['num']."'","`id`='".$val['gid']."'");
			}
		}
		$this->obj->DB_update_all("change","`status`='".$_POST['status']."'","`id` IN (".$_POST['allid'].")");
		$this->obj->admin_log("�������(ID:".$_POST['allid'].")��˳ɹ�");
		echo $_POST['status'];die;
	}


	function del_action(){
		if($_GET['del']){ 
			$this->check_token();
			$del=$_GET['del'];
			if(is_array($del)){
				$del=@implode(',',$del);
				$layer_type=1;
			}else{
				$layer_type=0;
			}

		    $rowss=$this->obj->DB_select_all("change","`id` in(".$del.")","`uid`,`gid`,`num`,`integral`,`usertype`");
				if($rowss&&is_array($rowss)){
					foreach($rowss as $val){
						if($val['usertype']=="1")
						{
							$table="member_statis";
						}elseif($val['usertype']=="2"){
							$table="company_statis";
						}
						$this->obj->DB_update_all($table,"`integral`=`integral`+".$val['integral']."","`uid`='".$val['uid']."'");
						$this->obj->DB_update_all("reward","`stock`=`stock`+".$val['num']."","`id`='".$val['gid']."'");
					}
				}








		$del=$this->obj->DB_delete_all("change","`id` in (".$del.")"," ");
		$del?$this->layer_msg('�һ���¼(ID:'.$del.')ɾ���ɹ���',9,$layer_type,$_SERVER['HTTP_REFERER']):$this->layer_msg('ɾ��ʧ�ܣ�',8,$layer_type,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('��ѡ��Ҫɾ�������ݣ�',8,0,$_SERVER['HTTP_REFERER']);
		}



	}
}

?>