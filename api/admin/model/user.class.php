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
class user_controller extends appadmin
{
	function resumelist_action()
	{
		$where="height_status='0'";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		
		if($keyword)
		{
			include APP_PATH."/plus/job.cache.php";
			$where1[]="`name` LIKE '%".$keyword."%'";
			foreach($job_name as $k=>$v){
				if(strpos($v,$keyword)!==false){
					$jobid[]=$k;
				}
			}
			if(is_array($jobid))
			{
				foreach($jobid as $value)
				{
					$class[]="FIND_IN_SET('".$value."',job_classid)";
				}
				$where1[]=@implode(" or ",$class);
			}
			$where.=" AND (".@implode(" or ",$where1).")";
		}
		if($order){
			$where.=" order by ".$order;
		}else{
			$where.=" order by id desc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_all("resume_expect",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['hy']=$v['hy'];
				$list[$k]['provinceid']=$v['provinceid'];
				$list[$k]['cityid']=$v['cityid'];
				$list[$k]['three_cityid']=$v['three_cityid'];
				$list[$k]['cityname']=$city_name[$v['three_cityid']]."-".$city_name[$v['cityid']];
				$list[$k]['salary']=$v['salary'];
				$list[$k]['job_classid']=$v['job_classid'];
				$list[$k]['report']=$v['report'];
				$list[$k]['type']=$v['type'];
				$list[$k]['lastupdate']=$v['lastupdate'];
				$list[$k]['ctime']=$v['ctime'];
			}
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}

	function resumeshow_action()
	{
		if(!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$select="a.uid,a.name,a.sex,a.birthday,a.marriage,a.height,a.living,a.domicile,a.nationality,a.weight,a.edu,a.exp,b.provinceid,b.cityid,b.three_cityid,b.salary,b.type,b.hits,b.lastupdate,b.doc,a.idcard_pic,a.photo,a.resume_photo,a.description,a.address,a.homepage,a.email,a.telphone,a.idcard,b.hy,b.job_classid,b.report,b.id,b.integrity";
		$rows=$this->obj->DB_select_alls("resume","resume_expect","a.uid=b.uid and b.id='".(int)$_POST['id']."'",$select);
		$row=$rows[0];
		if(!empty($row))
		{
			$data['info']['id']		=$row['id'];
			$data['info']['homepage']	=iconv("gbk","UTF-8",$row['homepage']);
			$data['info']['email']		=$row['email'];
			$data['info']['telphone']	=$row['telphone'];
			$data['info']['idcard']	=$row['idcard']?$row['idcard']:"";
			$data['info']['uid']		=$row['uid'];
			$data['info']['name']		=iconv("gbk","UTF-8",$row['name']);
			$data['info']['sex']		=$row['sex'];
			$data['info']['edu']		=$row['edu'];
			$data['info']['exp']		=$row['exp'];
			$data['info']['provinceid']	=$row['provinceid'];
			$data['info']['cityid']		=$row['cityid'];
			$data['info']['three_cityid']=$row['three_cityid'];
			$data['info']['salary']		=$row['salary'];
			$data['info']['type']		=$row['type'];
			$data['info']['lastupdate']	=$row['lastupdate'];
			$data['info']['hits']		=$row['hits'];
			$data['info']['hy']		=$row['hy'];
			$data['info']['report']		=$row['report'];
			$data['info']['job_classid']		=$row['job_classid'];
			$data['info']['birthday']	=$row['birthday'];
			$data['info']['marriage']	=iconv("gbk","UTF-8",$row['marriage']);
			$data['info']['height']		=$row['height'];
			$data['info']['nationality']=iconv("gbk","UTF-8",$row['nationality']);
			$data['info']['weight']		=$row['weight'];
			$data['info']['living']	=iconv("gbk","UTF-8",$row['living']);
			$data['info']['domicile']		=iconv("gbk","UTF-8",$row['domicile']);
			$data['info']['doc']	=$row['doc'];
			$data['info']['idcard_pic']	=$row['idcard_pic'];
			$data['info']['photo']	=$row['photo'];
			$data['info']['resume_photo']	=$row['resume_photo'];
			$data['info']['description']	=iconv("gbk","UTF-8",$row['description']);
			$data['info']['address']	=iconv("gbk","UTF-8",$row['address']);
			$data['info']['integrity']	=$row['integrity'];
			$ewhere="`eid`='".$row['id']."'";
			if($row['doc']==1){
				$doc = $this->obj->DB_select_once("resume_doc",$ewhere);
				$data['docbody']['id']=iconv("gbk","UTF-8",$doc['id']);
				$data['docbody']['body']=iconv("gbk","UTF-8",$doc['doc']);
			}else{
				$skill = $this->obj->DB_select_all("resume_skill",$ewhere);
				if(is_array($skill)){
					foreach($skill as $key=>$k){
						$data['skill'][$key]['id']=iconv("gbk","UTF-8",$k['id']);
						$data['skill'][$key]['name']=iconv("gbk","UTF-8",$k['name']);
						$data['skill'][$key]['skill']=iconv("gbk","UTF-8",$k['skill']);
						$data['skill'][$key]['ing']=iconv("gbk","UTF-8",$k['ing']);
						$data['skill'][$key]['longtime']=iconv("gbk","UTF-8",$k['longtime']);
					}
				}
				$work = $this->obj->DB_select_all("resume_work",$ewhere);
				if(is_array($work)){
					foreach($work as $key=>$k){
						$data['work'][$key]['id']=iconv("gbk","UTF-8",$k['id']);
						$data['work'][$key]['name']=iconv("gbk","UTF-8",$k['name']);
						$data['work'][$key]['sdate']=iconv("gbk","UTF-8",$k['sdate']);
						$data['work'][$key]['edate']=iconv("gbk","UTF-8",$k['edate']);
						$data['work'][$key]['department']=iconv("gbk","UTF-8",$k['department']);
						$data['work'][$key]['title']=iconv("gbk","UTF-8",$k['title']);
						$data['work'][$key]['content']=iconv("gbk","UTF-8",$k['content']);
					}
				}
				$project = $this->obj->DB_select_all("resume_project",$ewhere);
				if(is_array($project)){
					foreach($project as $key=>$k){
						$data['project'][$key]['id']=iconv("gbk","UTF-8",$k['id']);
						$data['project'][$key]['name']=iconv("gbk","UTF-8",$k['name']);
						$data['project'][$key]['sdate']=iconv("gbk","UTF-8",$k['sdate']);
						$data['project'][$key]['edate']=iconv("gbk","UTF-8",$k['edate']);
						$data['project'][$key]['sys']=iconv("gbk","UTF-8",$k['sys']);
						$data['project'][$key]['title']=iconv("gbk","UTF-8",$k['title']);
						$data['project'][$key]['content']=iconv("gbk","UTF-8",$k['content']);
					}
				}
				$edu = $this->obj->DB_select_all("resume_edu",$ewhere);
				if(is_array($edu)){
					foreach($edu as $key=>$k){
						$data['edu'][$key]['id']=iconv("gbk","UTF-8",$k['id']);
						$data['edu'][$key]['name']=iconv("gbk","UTF-8",$k['name']);
						$data['edu'][$key]['sdate']=iconv("gbk","UTF-8",$k['sdate']);
						$data['edu'][$key]['edate']=iconv("gbk","UTF-8",$k['edate']);
						$data['edu'][$key]['specialty']=iconv("gbk","UTF-8",$k['specialty']);
						$data['edu'][$key]['title']=iconv("gbk","UTF-8",$k['title']);
						$data['edu'][$key]['content']=iconv("gbk","UTF-8",$k['content']);
					}
				}
				$training = $this->obj->DB_select_all("resume_training",$ewhere);
				if(is_array($training)){
					foreach($training as $key=>$k){
						$data['training'][$key]['id']=iconv("gbk","UTF-8",$k['id']);
						$data['training'][$key]['name']=iconv("gbk","UTF-8",$k['name']);
						$data['training'][$key]['sdate']=iconv("gbk","UTF-8",$k['sdate']);
						$data['training'][$key]['edate']=iconv("gbk","UTF-8",$k['edate']);
						$data['training'][$key]['title']=iconv("gbk","UTF-8",$k['title']);
						$data['training'][$key]['content']=iconv("gbk","UTF-8",$k['content']);
					}
				}
				$cert = $this->obj->DB_select_all("resume_cert",$ewhere);
				if(is_array($cert)){
					foreach($cert as $key=>$k){
						$data['cert'][$key]['id']=iconv("gbk","UTF-8",$k['id']);
						$data['cert'][$key]['name']=iconv("gbk","UTF-8",$k['name']);
						$data['cert'][$key]['sdate']=iconv("gbk","UTF-8",$k['sdate']);
						$data['cert'][$key]['edate']=iconv("gbk","UTF-8",$k['edate']);
						$data['cert'][$key]['title']=iconv("gbk","UTF-8",$k['title']);
						$data['cert'][$key]['content']=iconv("gbk","UTF-8",$k['content']);
					}
				}
				$other = $this->obj->DB_select_all("resume_other",$ewhere);
				if(is_array($other)){
					foreach($other as $key=>$k){
						$data['other'][$key]['id']=iconv("gbk","UTF-8",$k['id']);
						$data['other'][$key]['title']=iconv("gbk","UTF-8",$k['title']);
						$data['other'][$key]['content']=iconv("gbk","UTF-8",$k['content']);
					}
				}
			}
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}

	function resumedel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$list=$this->obj->DB_select_all("resume_expect","`id` in (".$_POST['ids'].")","`uid`");
		$result=$this->obj->DB_delete_all("resume_expect","`id` in (".$_POST['ids'].")","");
		$del_array=array("resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume");
		foreach($del_array as $v)
		{
			$this->obj->DB_delete_all($v,"`eid` in (".$_POST['ids'].")","");
		}
		foreach($list as $v)
		{
			$this->obj->DB_update_all("member_statis","`resume_num`=`resume_num`-1","`uid`='".$v['uid']."'");
		}
		$this->write_appadmin_log("删除简历(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}

	function certlist_action()
	{
		$where="`idcard_pic`<>''";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (`name` like '%".$keyword."%')";
		}
		if($_POST['status'])
		{
			if($_POST['status']==1)
			{
				$where.=" and `idcard_status`='1'";
			}else if($_POST['status']==2){
				$where.=" and `idcard_status`='2'";
			}else{
				$where.=" and `idcard_status`='0'";
			}
		}
		if($order){
			$where.=" order by ".$order;
		}else{
			$where.=" order by `idcard_status` asc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$select="uid,name,idcard_pic,idcard,idcard_status,cert_time";
		$rows=$this->obj->DB_select_all("resume",$where,$select);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['uid']=$v['uid'];
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['idcard_pic']=$v['idcard_pic'];
				$list[$k]['idcard']=$v['idcard'];
				if($v['idcard_status']=='2'){
					$list[$k]['idcard_status']='0';
				}else{
					$list[$k]['idcard_status']=$v['idcard_status'];
				}
				$list[$k]['cert_time']=$v['cert_time'];
			}
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function certstatus_action()
	{
		if(!$_POST['uid']||!$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		if($_POST['status']==1)
		{
			$value.="`idcard_status`='1'";
		}else if($_POST['status']==2){
			$value.="`idcard_status`='2'";
		}else{
			$value.="`idcard_status`='0'";
		}
		$value.=" , `statusbody`='".$_POST['statusbody']."'";
		$this->obj->DB_update_all("resume",$value,"`uid`='".(int)$_POST['uid']."'");
		$this->write_appadmin_log("删除个人认证(UID:".$_POST['uid'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function certdel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$cert=$this->obj->DB_select_all("resume","`uid` in (".$_POST['ids'].")","`idcard_pic`");
	    if(is_array($cert)){
	     	foreach($cert as $v){
	     		$this->obj->unlink_pic($v['idcard_pic']);
	     	}
	    }
		$del=$this->obj->DB_update_all("resume","`idcard_pic`='',`idcard_status`='0',`cert_time`='',`statusbody`=''","`uid` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("删除个人认证(UID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function tinylist_action()
	{
		$where="1";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter(iconv('utf-8','gbk',$_POST['keyword']));
		if($keyword)
		{
			$where.=" and (`username` like '%".$keyword."%' or `job` like '%".$keyword."%' or `production` like '%".$keyword."%')";
		}
		if($_POST['status'])
		{
			if($_POST['status']==1)
			{
				$where.=" and `status`='1'";
			}else{
				$where.=" and `status`='0'";
			}
		}
		if($order){
			$where.=" order by ".$order;
		}else{
			$where.=" order by `status` asc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_all("resume_tiny",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['username']=iconv("gbk","UTF-8",$v['username']);
				$list[$k]['sex']=$v['sex'];
				$list[$k]['exp']=$v['exp'];
				$list[$k]['job']=iconv("gbk","UTF-8",$v['job']);
				$list[$k]['mobile']=$v['mobile'];
				$list[$k]['qq']=$v['qq'];
				$list[$k]['time']=$v['time'];
				$list[$k]['status']=$v['status'];
				$list[$k]['login_ip']=$v['login_ip'];
			}
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function tinyshow_action()
	{
		if(!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$row=$this->obj->DB_select_once("resume_tiny","`id`='".(int)$_POST['id']."'");
		if(!empty($row))
		{
			$list['id']=$row['id'];
			$list['username']=iconv("gbk","UTF-8",$row['username']);
			$list['production']=iconv("gbk","UTF-8",$row['production']);
			$list['sex']=$row['sex'];
			$list['exp']=$row['exp'];
			$list['job']=iconv("gbk","UTF-8",$row['job']);
			$list['mobile']=$row['mobile'];
			$list['qq']=$row['qq'];
			$list['time']=$row['time'];
			$list['status']=$row['status'];
			$list['login_ip']=$row['login_ip'];
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function tinydel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$delid=$this->obj->DB_delete_all("resume_tiny","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("删除微简历(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function tinystatus_action()
	{
		if(!$_POST['ids']||!$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		if($_POST['status'])
		{
			if($_POST['status']=="2")
			{
				$status=0;
			}else{
				$status=(int)$_POST['status'];
			}
		}
		$this->obj->DB_update_all("resume_tiny","`status`='".$status."'","`id` in (".$_POST['ids'].")");
		$this->write_appadmin_log("审核微简历(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function trustlist_action()
	{
		$where="a.eid=b.id";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (a.`username` like '%".$keyword."%' or b.`name` like '%".$keyword."%')";
		}
		if($_POST['status'])
		{
			if($_POST['status']==3)
			{
				$where.=" and a.`status`='0'";
			}else{
				$where.=" and a.`status`='".(int)$_POST['status']."'";
			}
		}
		if($order){
			$where.=" order by a.".$order;
		}else{
			$where.=" order by `status` asc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_alls("user_entrust","resume_expect",$where,"a.`id`,a.`eid`,b.`uid`,a.`username`,b.`name`,a.`price`,a.`status`,b.`is_entrust`,a.`add_time`");
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['eid']=$v['eid'];
				$list[$k]['uid']=$v['uid'];
				$list[$k]['username']=iconv("gbk","UTF-8",$v['username']);
				$list[$k]['resumename']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['price']=$v['price'];
				$list[$k]['status']=$v['status']=='0'?'3':$v['status'];
				$list[$k]['is_entrust']=$v['is_entrust'];
				$list[$k]['add_time']=$v['add_time'];
			}
			$data['error']=1;
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function trustdel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$delid=$this->obj->DB_delete_all("user_entrust","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("删除委托简历(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function truststatus_action()
	{
		if(!$_POST['id']||!$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$pid=(int)$_POST['id'];
		if($_POST['status']=="3")
		{
			$status="0";
		}else{
			$status=(int)$_POST['status'];
		}
		$user_entrust = $this->obj->DB_select_once("user_entrust","`id`='".$pid."'");
		if($status=='2'){
			$this->obj->DB_update_all("resume_expect","`is_entrust`='0'","`uid`='".$user_entrust['uid']."' and `id`='".$user_entrust['eid']."'");
			if($user_entrust['0']){
				$this->obj->DB_update_all("member_statis","`pay`=`pay`+'".$user_entrust['price']."'","`uid`='".$user_entrust['uid']."'");
			}
		}else{
			$this->obj->DB_update_all("resume_expect","`is_entrust`=`is_entrust`+1","`uid`='".$user_entrust['uid']."' and `id`='".$user_entrust['eid']."'");
		}
		$id=$this->obj->DB_update_all("user_entrust","`status`='$status'","`uid`='".$user_entrust['uid']."' and `id`='".$pid."'");
		$this->write_appadmin_log("审核委托简历(ID:".$_POST['id'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function userlist_action()
	{
		$where="a.uid=b.uid";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (a.`username` like '%".$keyword."%' or b.`name` like '%".$keyword."%')";
		}
		if($order){
			$where.=" order by a.".$order;
		}else{
			$where.=" order by a.uid desc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_alls("member","resume",$where,"a.uid,a.username,b.email,b.telphone,a.reg_date,b.name,a.status");
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['uid']=$v['uid'];
				$list[$k]['username']=iconv("gbk","UTF-8",$v['username']);
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['email']=$v['email'];
				$list[$k]['moblie']=$v['telphone'];
				$list[$k]['reg_date']=$v['reg_date'];
				$list[$k]['status']=$v['status'];
			}
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
			}
			$data['list']=$list;
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"没有获得信息");
		}
	}
	function userdel_action()
	{
		if(!$_POST['uids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$uids=$_POST['uids'];
		$del=@explode(",",$_POST['uids']);
		foreach($del as $k=>$v)
		{
			$this->obj->delfiledir("../upload/tel/".intval($v));
		}
		$uids = $this->pylode(",",$del);
		$resume=$this->obj->DB_select_all("resume","`uid` in ($uids) and `photo`<>''","`photo`,`resume_photo`");
		if(is_array($resume)){
	    	foreach($resume as $val){
	    		$this->obj->unlink_pic(".".$val['photo']);
	    		$this->obj->unlink_pic(".".$val['resume_photo']);
	    	}
	    }
		$friend_pic = $this->obj->DB_select_all("friend_info","`uid` in ($uids) and `pic`<>''","`pic`,`pic_big`");
		if(is_array($friend_pic)){
	    	foreach($friend_pic as $val){
	    		$this->obj->unlink_pic($val['pic']);
	    		$this->obj->unlink_pic($val['pic_big']);
	    	}
		}
		$del_array=array("member","resume","member_statis","look_resume","userid_msg","userid_job","resume_expect","resume_cert","resume_edu","resume_other","resume_project","resume_skill","resume_training","resume_work","resume_doc","user_resume","friend_info","friend_message","friend_state","question","msg","attention","rebates","company_msg");
		foreach($del_array as $value){
			$this->obj->DB_delete_all($value,"`uid` in ($uids)","");
		}
		$this->obj->DB_delete_all("atn","`uid` in ($uids) or `scid` in ($uids)","");
	    $this->obj->DB_delete_all("message","`fa_uid` in ($uids)","");
	    $this->obj->DB_delete_all("friend_reply","`fid` in ($uids)","");
	    $this->obj->DB_delete_all("friend_foot","`uid` in ($uids) or `fid` in ($uids)","");
	    $this->obj->DB_delete_all("blacklist","`p_uid` in ($uids)","");
	    $this->obj->DB_delete_all("friend","`uid` in ($uids) or `nid` in ($uids)","");
	    $this->obj->DB_delete_all("report","`p_uid` in ($uids) or `c_uid` in ($uids)","");
		$this->write_appadmin_log("删除个人用户(UID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function lock_action()
	{
		if(!$_POST['uid'] || !$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$uid=(int)$_POST['uid'];
		$_POST['status']=(int)$_POST['status'];
 		$id=$this->obj->DB_update_all("member","`status`='".$_POST['status']."',`lock_info`='".iconv("utf-8","gbk",$_POST['statusbody'])."'","`uid`='".$uid."'");
 		$this->obj->DB_update_all("resume","`r_status`='".$_POST['status']."'","`uid`='".$uid."'");
 		if($this->config['sy_email_lock']=='1'){
			$userinfo = $this->obj->DB_select_once("member","`uid`=".$uid,"`email`,`uid`,`name`,`usertype`");
			$data=$this->forsend($userinfo);
			$this->send_msg_email(array("email"=>$userinfo['email'],'uid'=>$data['uid'],'name'=>$data['name'],"certinfo"=>$_POST['statusbody'],"username"=>$userinfo['name'],"type"=>"lock"));
		}
		if($id)
		{
			$this->write_appadmin_log("个人会员锁定(UID:".$_POST['uid'].")");
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"锁定设置失败");
		}
	}
}
?>