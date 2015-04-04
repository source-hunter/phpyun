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
class admin_company_job_controller extends common
{
	function set_search(){
		include APP_PATH."/plus/com.cache.php";
        foreach($comdata['job_type'] as $k=>$v){
               $comarr[$v]=$comclass_name[$v];
        }
        foreach($comdata['job_salary'] as $k=>$v){
               $comar[$v]=$comclass_name[$v];
        }
		$search_list[]=array("param"=>"state","name"=>'���״̬',"value"=>array("1"=>"�����","4"=>"δ���","3"=>"δͨ��","2"=>"�ѹ���"));
		$search_list[]=array("param"=>"status","name"=>'��Ƹ״̬',"value"=>array("1"=>"����ͣ","2"=>"������"));
		$search_list[]=array("param"=>"jtype","name"=>'ְλ����',"value"=>array("urgent"=>"����ְλ","rec"=>"�Ƽ�ְλ"));
		$search_list[]=array("param"=>"job_type","name"=>'��������',"value"=>$comarr);
		$search_list[]=array("param"=>"adtime","name"=>'����ʱ��',"value"=>array("1"=>"����","3"=>"�������","7"=>"�������","15"=>"�������","30"=>"���һ����"));
		$search_list[]=array("param"=>"etime","name"=>'����ʱ��',"value"=>array("1"=>"�ѵ���","3"=>"�������","7"=>"�������","15"=>"�������","30"=>"���һ����"));
		$search_list[]=array("param"=>"salary","name"=>'���ʴ���',"value"=>$comar);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$this->set_search();
		$time = time();
        $wheres = "1 ";
		if($_GET['hy']){
			$wheres .= " AND `hy` = '".$_GET['hy']."' ";
			$urlarr['hy']=$_GET['hy'];
		}
		if($_GET['job1']){
			$wheres .= " AND `job1` = '".$_GET['job1']."' ";
			$urlarr['job1']=$_GET['job1'];
		}
		if($_GET['job1_son']){
			$wheres .= " AND `job1_son` = '".$_GET['job1_son']."' ";
			$urlarr['job1_son']=$_GET['job1_son'];
		}
		if($_GET['job_post']){
			$wheres .= " AND `job_post` = '".$_GET['job_post']."' ";
			$urlarr['job_post']=$_GET['job_post'];
		}
		if($_GET['provinceid']){
			$wheres .= " AND `provinceid` = '".$_GET['provinceid']."' ";
			$urlarr['provinceid']=$_GET['provinceid'];
		}
		if($_GET['cityid']){
			$wheres .= " AND `cityid` = '".$_GET['cityid']."' ";
			$urlarr['cityid']=$_GET['cityid'];
		}
		if($_GET['three_cityid']){
			$wheres .= " AND `three_cityid` = '".$_GET['three_cityid']."' ";
			$urlarr['three_cityid']=$_GET['three_cityid'];
		}
		if($_GET['salary']){
			$wheres .= " AND `salary` = '".$_GET['salary']."' ";
			$urlarr['salary']=$_GET['salary'];
		}
		if($_GET['type']){
			$wheres .= " AND `type` = '".$_GET['type']."' ";
			$urlarr['type']=$_GET['type'];
		}
		if($_GET['number']){
			$wheres .= " AND `number` = '".$_GET['number']."' ";
			$urlarr['number']=$_GET['number'];
		}
		if($_GET['exp']){
			$wheres .= " AND `exp` = '".$_GET['exp']."' ";
			$urlarr['exp']=$_GET['exp'];
		}
		if($_GET['report']){
			$wheres .= " AND `report` = '".$_GET['report']."' ";
			$urlarr['report']=$_GET['report'];
		}
		if($_GET['sex']){
			$wheres .= " AND `sex` = '".$_GET['sex']."' ";
			$urlarr['sex']=$_GET['sex'];
		}
		if($_GET['edu']){
			$wheres .= " AND `edu` = '".$_GET['edu']."' ";
			$urlarr['edu']=$_GET['edu'];
		}
		if($_GET['marriage']){
			$wheres .= " AND `marriage` = '".$_GET['marriage']."' ";
			$urlarr['marriage']=$_GET['marriage'];
		}
        if($_GET['keyword']){
			$wheres .= " AND `name` like '%".$_GET['keyword']."%' ";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$where=1;
        if ($_GET['news_search']){
			extract($_GET);
			if($keyword!=""){
				if($type=='1'){
					$where .=" and  `com_name` like '%".$keyword."%'";
				}else{
					$where .=" and `name` like '%".$keyword."%'";
				}
				$urlarr['type']=$type;
				$urlarr['keyword']=$_GET['keyword'];
			}
			$urlarr['news_search']=$_GET['news_search'];
		}
		if ($_GET['job_type']){
			$where .=" and `type`='".$_GET['job_type']."'";
			$urlarr['job_type']=$_GET['job_type'];
		}
		if($_GET['adtime']){
			if($_GET['adtime']=='1'){
				$where .=" and `sdate`>'".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where .=" and `sdate`>'".strtotime('-'.intval($_GET['adtime']).' day')."'";
			}
			$urlarr['adtime']=$_GET['adtime'];
		}
		if($_GET['etime']){
			if($_GET['adtime']=='1'){
				$where .=" and `edate`<'".time()."'";
			}else{
				$where .=" and `edate`>'".time()."' AND `edate`<'".strtotime('+'.intval($_GET['etime']).' day')."'";
			}
			$urlarr['etime']=$_GET['etime'];
		}
		if ($_GET['salary']){
			$where .=" and `salary`='".$_GET['salary']."'";
			$urlarr['salary']=$_GET['salary'];
		}
		if($_GET['id'])
		{
			$where.=" and `id`='".$_GET['id']."'";
			$urlarr['id']=$_GET['id'];
		}
		if($_GET['state']){
			if($_GET['state']=="1"){
				$where.= "  and `edate`>'".time()."' and `state`='1'";
			}elseif($_GET['state']=="2"){
				$where.= "  and `edate`<'".time()."'";
			}elseif($_GET['state']=="3"){
				$where.= " and `state`='".$_GET['state']."'";
			}elseif($_GET['state']=="4"){
				$where.= "  and `state`='0'";
			}
			$urlarr['state']=$_GET['state'];
		}
		if($_GET['status'])
		{
			if($_GET['status']=="1")
			{
				$where.=" and `status`='1'";
			}else{
				$where.=" and `status`!='1'";
			}
			$urlarr['status']=$_GET['status'];
		}
		if($_GET['jtype']){
			if($_GET['jtype']=='rec'){
				$where.= "  and `rec_time`>".time();
			}else if($_GET['jtype']=='urgent'){
				$where.= "  and `urgent_time`>".time();
			}
			$urlarr['jtype']=$_GET['jtype'];
		}
		
		if($_GET['order'])
		{
			$where.=" order by ".$_GET['t']." ".$_GET['order'];
			$urlarr['order']=$_GET['order'];
			$urlarr['t']=$_GET['t'];
		}else{
			$where.=" order by id desc";
		}
		if($_GET['advanced']){
			$where= $wheres;
			$urlarr['advanced']=$_GET['advanced'];
		}

		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("company_job",$where,$pageurl,$this->config['sy_listnum']);
		include APP_PATH."/plus/job.cache.php";
		include APP_PATH."/plus/industry.cache.php";
		include APP_PATH."/plus/com.cache.php";
		if(is_array($rows)){
			foreach($rows as $k=>$v){
				$rows[$k]['edu']=$comclass_name[$v['edu']];
				$rows[$k]['exp']=$comclass_name[$v['exp']];
				if($v['job_post']){
					$rows[$k]['job']=$job_name[$v['job_post']];
				}else{
					$rows[$k]['job']=$job_name[$v['job1_son']];
				}

				$rows[$k]['salary']=$comclass_name[$v['salary']];
				$rows[$k]['type']=$comclass_name[$v['type']];
				if($v['edate']<time())
				{
					$rows[$k]['edatetxt'] = "<font color='red'>�ѵ���</font>";
				}elseif($v['edate']<(time()+3*86400)){
				
					$rows[$k]['edatetxt'] = "<font color='blue'>3�����</font>";

				}elseif($v['edate']<(time()+7*86400)){
				
					$rows[$k]['edatetxt'] = "<font color='blue'>7�����</font>";
				}else{
					$rows[$k]['edatetxt'] = date("Y-m-d",$v['edate']);
				}	
				if($v['urgent_time']>$time){
					$rows[$k]['urgent_day'] = ceil(($v['urgent_time']-$time)/86400);
				}else{
					$rows[$k]['urgent_day'] = "0";
				}
				if($v['rec_time']>$time){
					$rows[$k]['rec_day'] = ceil(($v['rec_time']-$time)/86400);
				}else{
					$rows[$k]['rec_day'] = "0";
				}

			}
		}
		$adtime=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$adtime=array('1'=>'����','3'=>'�������','7'=>'�������','15'=>'�������','30'=>'���һ����');
		$this->yunset("adtime",$adtime);
		$where=str_replace(array("(",")"),array("[","]"),$where);
		$this->yunset("where",$where);
		$this->yunset("get_type", $_GET);
		$this->job_cache();
		$this->com_cache();
		$this->industry_cache();
		$this->yunset("rows",$rows);
		$this->yunset("time",$time);
		$this->yuntpl(array('admin/admin_company_job'));
	}
    
	function show_action(){
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
		$this->CacheInclude($CacheArr);
		if($_GET['id']){
			$show=$this->obj->DB_select_once("company_job","id='".$_GET['id']."'");
			$show['lang']=@explode(',',$show['lang']);
			$show['welfare']=@explode(',',$show['welfare']);
			$this->yunset("show",$show);
		}

		if($_POST['update']){
			$_POST['lang']=@implode(',',$_POST['lang']);
			$_POST['welfare']=@implode(',',$_POST['welfare']);

			$_POST['edate']=strtotime($_POST['edate']);
			$_POST['description'] = str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
			$_POST['lastupdate'] = time();
			unset($_POST['update']);unset($_POST['content']);
			if($_POST['edate']>time()){
				$_POST['state']="1";
			}else{
				$this->obj->ACT_layer_msg("����ʱ�䲻��С�ڵ�ǰʱ��",8,"index.php?m=admin_company_job",2,1);
			}

			if($_POST['id']&&$_POST['uid']==''){
				$job=$this->obj->DB_select_once("company_job","`id`='".$_POST['id']."'","`uid`");
				$where['id']=$_POST['id'];
				unset($_POST['id']);
				$this->obj->update_once("company_job",$_POST,$where);
				$this->obj->ACT_layer_msg("ְλ(ID:".$where['id'].")�޸ĳɹ���",9,"index.php?m=admin_company_job",2,1);
			}else if($_POST['uid']){
				$company=$this->obj->DB_select_once("company","`uid`='".$_POST['uid']."'","name");
				$statis=$this->obj->DB_select_once("company_statis","`uid`='".$_POST['uid']."'","`vip_etime`,`job_num`,`integral`");

				if($statis['vip_etime']>time() || $statis['vip_etime']=="0")
				{
					if($statis['rating_type']==1)
					{
						if($statis['job_num']<1)
						{
							if($this->config['com_integral_online']=="1")
							{
								if($statis['integral']<$this->config['integral_job'])
								{
									$this->obj->ACT_layer_msg($this->config['integral_pricename']."��������ְλ��",8,"index.php?m=admin_company_job");
								}
							}else{
								$this->obj->ACT_layer_msg("�û�Ա����ְλ���꣡",8,"index.php?m=admin_company_job");
							}
						}else{
							$this->obj->DB_update_all("company_statis","`job_num`=`job_num`-1","`uid`='".$_POST['uid']."'");
						}
					}
				}else{
					if($this->config['com_integral_online']=="1")
					{
						if($statis['integral']<$this->config['integral_job'])
						{
							$this->obj->ACT_layer_msg($this->config['integral_pricename']."��������ְλ��",8,"index.php?m=admin_company_job");
						}
					}else{
						$this->obj->ACT_layer_msg("�û�Ա����ְλ���꣡",8,"index.php?m=admin_company_job");
					}
				}
				$_POST['com_name']=$company['name'];
				$_POST['sdate']=time();
				$id=$this->obj->insert_into("company_job",$_POST);
				if($id){
					$this->obj->ACT_layer_msg( "ְλ(ID:".$id.")�����ɹ���",9,'index.php?m=admin_company_job&c=show&uid='.$_POST['uid'],2,1);
				}else{
					$this->obj->ACT_layer_msg( "ְλ����ʧ�ܣ�",8,'index.php?m=admin_company_job&c=show&uid='.$_POST['uid'],2,1);
				}
			}
		}
		$this->yunset("uid",$_GET['uid']);
		$this->yuntpl(array('admin/admin_company_job_show'));
	}
	function lockinfo_action(){
		$userinfo = $this->obj->DB_select_once("company_job","`id`=".$_POST['id'],"`statusbody`");
		echo $userinfo['statusbody'];die;
	}
	function status_action(){
		 extract($_POST);
		 $id = @explode(",",$pid);
		 if(is_array($id)){
			foreach($id as $value){
				if($value)
				{
					$idlist[] = $value;
					$data[] = $this->shjobmsg($value,$status,$statusbody);
				}

			}

			if($data!=""){
				$smtp = $this->email_set();
				foreach($data as $key=>$value){
					$this->send_msg_email($value['email'],$smtp);
				}
			}
			$aid = @implode(",",$idlist);
			$id=$this->obj->DB_update_all("company_job","`state`='$status',`statusbody`='".$statusbody."'","`id` IN ($aid)");
			$id?$this->obj->ACT_layer_msg("ְλ���(ID:".$aid.")���óɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("�Ƿ�������",3,$_SERVER['HTTP_REFERER']);
		}
	}
	function saveclass_action(){
		extract($_POST);
		if($hy==""){
			$this->obj->ACT_layer_msg("��ѡ����ҵ���",8,$_SERVER['HTTP_REFERER']);
		}
		if($job1==""){
			$this->obj->ACT_layer_msg("��ѡ��ְλ���",8,$_SERVER['HTTP_REFERER']);
		}
		$id=$this->obj->DB_update_all("company_job","`hy`='$hy',`job1`='$job1',`job1_son`='$job1_son',`job_post`='$job_post'","`id` IN ($jobid)");
		$id?$this->obj->ACT_layer_msg("ְλ���(ID:".$jobid.")�޸ĳɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("�޸�ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
	}
	function jobclass_action(){
		include(PLUS_PATH."industry.cache.php");
		include(PLUS_PATH."job.cache.php");
		if(is_array($job_type[$_POST['val']])&&$job_type[$_POST['val']]){
			foreach($job_type[$_POST['val']] as $val){
				$html.="<option value='".$val."'>".$job_name[$val]."</option>";
			}
		}else{$html.="<option value=''>���޷���</option>";}
		echo $html;
	}
	function shjobmsg($jobid,$yesid,$statusbody)
	{
		$data=array();
		$comarr=$this->obj->DB_select_once("company_job","`id`='".$jobid."'","uid,name");
		if($yesid==1){
			$data['type']="zzshtg";
			$this->send_dingyue($jobid,2);
		}elseif($yesid==3){
			$data['type']="zzshwtg";
		}
		if($data['type']!=""){
			$uid=$this->obj->DB_select_alls("member","company","a.`uid`='".$comarr['uid']."' and a.`uid`=b.`uid`","a.email,a.moblie,a.uid,b.name");
			$data['uid']=$uid[0]['uid'];
			$data['name']=$uid[0]['name'];
			$data['email']=$uid[0]['email'];
			$data['moblie']=$uid[0]['moblie'];
			$data['jobname']=$comarr['name'];
			$data['date']=date("Y-m-d H:i:s");
			$data['status_info']=$statusbody;
			return $data;
		}
	}
	function ctime_action(){
		extract($_POST);
		$id=@explode(",",$jobid);
		if(is_array($id)){
			$posttime=$endtime*86400;
			foreach($id as $value){
				$row=$this->obj->DB_select_once("company_job","`id`='".$value."'");
				if($row['state']==2 || $row['edate']<time()){
					$time=time()+$posttime;
					$id=$this->obj->DB_update_all("company_job","`edate`='".$time."',`state`='1'","`id`='".$value."'");
				}else{
					$time=$row['edate']+$posttime;
					$id=$this->obj->DB_update_all("company_job","`edate`='".$time."'","`id`='".$value."'");
				}
			}
			$id?$this->obj->ACT_layer_msg("ְλ����(ID:".$jobid.")���óɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg("����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}else{
			$this->obj->ACT_layer_msg("�Ƿ�������",3,$_SERVER['HTTP_REFERER']);
		}
	}
	function recommend_action(){
		extract($_POST);
		if($addday<1&&$s==''){$this->obj->ACT_layer_msg("�Ƽ���������Ϊ�գ�",8);}
		$addtime = 86400*$addday;
		if($pid){
			if($s==1){
				$this->obj->DB_update_all("company_job","`rec_time`='0',`rec`='0'","`id`='$pid'");
			}elseif($eid>time()){
				$this->obj->DB_update_all("company_job","`rec_time`=`rec_time`+$addtime,`rec`='1'","`id`='$pid'");
			}else{
				$this->obj->DB_update_all("company_job","`rec_time`=".time()."+$addtime,`rec`='1'","`id`='$pid'");
			}
			$this->obj->ACT_layer_msg("ְλ�Ƽ�(ID:".$pid.")���óɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
		}
		if(!empty($codearr)){
			if($s==1){
				$this->obj->DB_update_all("company_job","`rec_time`='0',`rec`='0'","`id` in (".$codearr.")");
				$this->obj->ACT_layer_msg("ȡ��ְλ�Ƽ����óɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
			}else{
				$code_com=@explode(",",$codearr);
				if(is_array($code_com)){
					foreach($code_com as $k=>$v){
						$r_time[$v]=$this->obj->DB_select_once("company_job","`id`='".$v."'","`rec_time`");
					}
				}
                if(is_array($r_time)){
                	$ti=time();
                	foreach($r_time as $ke=>$va){
                       if($va['rec_time']<$ti){
                       	    $g_id[]=$ke;  
                       }else{
                       	    $m_id[]=$ke; 
                       }
                	}
                	$guoqi=@implode(",",$g_id);
                	$meiguo=@implode(",",$m_id);
                	if(is_array($g_id)){
				            $this->obj->DB_update_all("company_job","`rec_time`=".time()."+$addtime,`rec`='1'","`id` in (".$guoqi.")");
                	}elseif($m_id){
				            $this->obj->DB_update_all("company_job","`rec_time`=`rec_time`+$addtime,`rec`='1'","`id` in (".$meiguo.")");
                	}
                	$this->obj->ACT_layer_msg("ְλ�Ƽ����óɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
                }
			}
		}

	}
	function urgent_action(){
		extract($_POST);
		if($addday<1&&$s==''){$this->obj->ACT_layer_msg("������������Ϊ�գ�",8);}
		$addtime = 86400*$addday;
		if($pid){
			if($s==1){
				$this->obj->DB_update_all("company_job","`urgent_time`='0',`urgent`='0'","`id`='$pid'");
			}elseif($eid>time()){
				$this->obj->DB_update_all("company_job","`urgent_time`=`urgent_time`+$addtime,`urgent`='1'","`id`='$pid'");
			}else{
				$this->obj->DB_update_all("company_job","`urgent_time`=".time()."+$addtime,`urgent`='1'","`id`='$pid'");
			}
			$this->obj->ACT_layer_msg("������Ƹ(ID:".$pid.")���óɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
		}
		if(!empty($codeugent)){
			if($s==1){
				$this->obj->DB_update_all("company_job","`urgent_time`='0',`urgent`='0'","`id` in (".$codeugent.")");
				$this->obj->ACT_layer_msg("ȡ��ְλ�������óɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
			}else{
				$code_ugent=@explode(",",$codeugent);
				if(is_array($code_ugent)){
					foreach($code_ugent as $k=>$v){
						$r_time[$v]=$this->obj->DB_select_once("company_job","`id`='".$v."'","`urgent_time`");
					}
				}
                if(is_array($r_time)){
                	$ti=time();
                	foreach($r_time as $ke=>$va){
                       if($va['urgent_time']<$ti){
                       	    $g_id[]=$ke;  
                       }else{
                       	    $m_id[]=$ke; 
                       }
                	}
                	$guoqi=@implode(",",$g_id);
                	$meiguo=@implode(",",$m_id);
                	if($g_id){
				            $this->obj->DB_update_all("company_job","`urgent_time`=".time()."+$addtime,`urgent`='1'","`id` in (".$guoqi.")");
                	}elseif($m_id){
				            $this->obj->DB_update_all("company_job","`urgent_time`=`urgent_time`+$addtime,`urgent`='1'","`id` in (".$meiguo.")");
                	}
                	$this->obj->ACT_layer_msg("ְλ�������óɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
                }
			}
		}

	}
	function del_action()
	{
		$this->check_token();
	    if($_GET['del']||$_GET['id']){
    		if(is_array($_GET['del'])){
    			$layer_type=1;
				$del=@implode(',',$_GET['del']);
	    	}else{
	    		$layer_type=0;
	    		$del=$_GET['id'];
	    	}
			$this->obj->DB_delete_all("company_job","`id` in (".$del.")","");
			$this->obj->DB_delete_all("company_job_link","`jobid` in (".$del.")","");
			$this->layer_msg("ְλ(ID:".$del.")ɾ���ɹ���",9,$layer_type,$_SERVER['HTTP_REFERER']);
    	}else{
			$this->layer_msg("��ѡ����Ҫɾ������Ϣ��",8,1);
    	}
	}
	function refresh_action()
	{
		$list=$this->obj->DB_select_all("company_job","`id` in (".$_POST['ids'].")","`uid`");
		if(is_array($list))
		{
			foreach($list as $v)
			{
				$uid[]=$v['uid'];
			}
			$this->obj->DB_update_all("company","`jobtime`='".time()."'","`uid` in (".@implode(",",$uid).")");
		}
		$this->obj->DB_update_all("company_job","`lastupdate`='".time()."'","`id` in (".$_POST['ids'].")");
		$this->obj->admin_log("ְλ(ID".$_POST['ids']."ˢ�³ɹ�");
	}

}
?>