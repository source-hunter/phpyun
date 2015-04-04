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
class com_controller extends appadmin
{
	function joblist_action()
	{
		$where=1;
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (`name` like '%".$keyword."%' or `com_name` like '%".$keyword."%')";
		}
		if($_POST['state'])
		{
			if($_POST['state']==2)
			{
				$where.=" and `edate`<'".time()."'";
			}elseif($_POST['state']==4){
				$where.=" and `state`='0'";
			}else{
				$where.=" and `state`='".$_POST['state']."'";
			}
		}
		if($order){
			$where.=" order by ".$order;
		}else{
			$where.=" order by `state` asc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_all("company_job",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id']?$v['id']:'';
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['com_name']=iconv("gbk","UTF-8",$v['com_name']);
				$list[$k]['state']=$v['state'];
				$list[$k]['status']=$v['state'];
				if($v['edate']<time())
				{
					$list[$k]['state']="2";
					$list[$k]['status']="2";
				}else if($v['state']=='0'){
					$list[$k]['state']="4";
				}
				$list[$k]['job_post']=$v['job_post'];
				$list[$k]['lastupdate']=$v['lastupdate'];
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

	function jobstatus_action()
	{
		if(!$_POST['ids'] || !$_POST['state'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		if($_POST['state']=="4")
		{
			$state="0";
		}else{
			$state=(int)$_POST['state'];
		}
		$statusbody=$this->stringfilter($_POST['statusbody']);
		$ids=@explode(",",$_POST['ids']);
		if(is_array($ids))
		{
			foreach($ids as $value)
			{
				if($value)
				{
					$data[] = $this->shjobmsg($value,$state,$statusbody);
				}
			}
			if($data!=""){
				$smtp = $this->email_set();
				foreach($data as $key=>$value){
					$this->send_msg_email($value['email'],$smtp);
				}
			}
			$id=$this->obj->DB_update_all("company_job","`state`='".$state."',`statusbody`='".$statusbody."'","`id` IN (".$_POST['ids'].")");
			if($id)
			{
				$data = array();
				$this->write_appadmin_log("审核职位");
				$data['error']=1;
				echo json_encode($data);die;
			}else{
				$this->return_appadmin_msg(2,"审核设置成功");
			}
		}else{
			$this->return_appadmin_msg(2,"参数出错");
		}
	}

	function jobshow_action()
	{
		if(!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$id=(int)$_POST['id'];
		$row=$this->obj->DB_select_once("company_job","`id`='".$id."'");
		if(!empty($row))
		{
			$list['id']		=$row['id'];
			$list['name']	=iconv("gbk","UTF-8",$row['name']);
			$list['comid']	=$row['uid'];
			$list['comname']=iconv("gbk","UTF-8",$row['com_name']);
			$list['hy']		=$row['hy'];
			$list['job1']	=$row['job1'];
			$list['job1_son']=$row['job1_son'];
			$list['job_post']=$row['job_post'];
			$list['provinceid']=$row['provinceid'];
			$list['cityid']	=$row['cityid'];
			$list['three_cityid']=$row['three_cityid'];
			$list['salary']	=$row['salary'];
			$list['type']	=$row['type'];
			$list['number']	=$row['number'];
			$list['exp']	=$row['exp'];
			$list['edu']	=$row['edu'];
			$list['state']	=$row['state'];
			$list['status']	=$row['state'];
			$list['report']	=$row['report'];
			$list['sex']	=$row['sex'];
			$list['marriage']=$row['marriage'];
			$list['description']=iconv("gbk","UTF-8",$row['description']);
			$list['xuanshang']=$row['xuanshang'];
			$list['sdate']	=$row['sdate'];
			$list['edate']	=$row['edate'];
			$list['jobhits']=$row['jobhits'];
			$list['lastupdate']=$row['lastupdate'];
			$list['rec']	=iconv("gbk","UTF-8",$row['rec']);
			$list['cloudtype']=iconv("gbk","UTF-8",$row['cloudtype']);
			$list['statusbody']=iconv("gbk","UTF-8",$row['statusbody']);
			$list['age']	=iconv("gbk","UTF-8",$row['age']);
			if($row['is_link']==1)
			{
				if($row['link_type']==1){
					$link=$this->obj->DB_select_once("company","`uid`='".$row['uid']."'");
					$list['linktel']	=iconv("gbk","UTF-8",$link['linktel']);
					$list['linkman']	=iconv("gbk","UTF-8",$link['linkman']);
				}else{
					$link=$this->obj->DB_select_once("company_job_link","`jobid`='".$row['id']."'");
					$list['linktel']	=iconv("gbk","UTF-8",$link['link_moblie']);
					$list['linkman']	=iconv("gbk","UTF-8",$link['link_man']);
				}
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
		if($data['type']!="")
		{
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

	function jobdel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$this->obj->DB_delete_all("company_job","`id` in (".$_POST['ids'].")","");
		$this->obj->DB_delete_all("company_job_link","`jobid` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("删除职位(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}

	function certlist_action()
	{
		$where="a.`type`='3' and a.uid=b.uid";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
        if($_POST['status'])
        {
			if($_POST['status']=='3'){
				$where.=" and a.`status`='0'";
			}else{
				$where.=" and a.`status`='".$_POST['status']."'";
			}
        }
		if($keyword)
		{
			$where.=" and b.`name` like '%".$keyword."%'";
		}
		if($order){
			$where.=" order by a.".$order;
		}else{
			$where.=" order by a.`status` asc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_alls("company_cert","company",$where,"a.*,b.name");
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['uid']=$v['uid'];
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['check']=$this->config['sy_weburl'].$v['check'];
				if($v['status']=='0'){
					$list[$k]['status']='3';
				}else{
					$list[$k]['status']=$v['status'];
				}
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

	function certstatus_action()
	{
		if(!$_POST['uid'] || !$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$uid=(int)$_POST['uid'];
		if($_POST['status']=="3")
		{
			$status="0";
		}else{
			$status=(int)$_POST['status'];
		}
		$statusbody=$this->stringfilter($_POST['statusbody']);
		$company=$this->obj->DB_select_once("company","`uid`='".$uid."'","`cert`,`linkmail`,`name`");
		$this->obj->DB_update_all("company","`yyzz_status`='".$status."'","`uid`='".$uid."'");
		$this->obj->DB_update_all("friend_info","`iscert`='".$status."'","`uid`='".$uid."'");
		$id=$this->obj->DB_update_all("company_cert","`status`='".$status."',`statusbody`='".$statusbody."'","`uid`='".$uid."'");
		if($this->config['sy_email_comcert']=='1'){
			$this->send_msg_email(array("email"=>$company['linkmail'],"certinfo"=>$statusbody,"comname"=>$company['name'],'uid'=>$uid,'name'=>$company['name'],"type"=>"comcert"));
		}
		if($id)
		{
			if($status==1)
			{
				$this->get_integral_action($uid,"integral_comcert","认证营业执照");
			}
			$this->write_appadmin_log("认证营业执照");
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"审核失败");
		}
	}

	function certdel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$this->obj->DB_update_all("company","`yyzz_status`='0'","`uid` in (".$_POST['ids'].")");
		$this->obj->DB_update_all("friend_info","`iscert`='0'","`uid` in (".$_POST['ids'].")");
	    $cert=$this->obj->DB_select_all("company_cert","`uid` in (".$_POST['ids'].") and `type`='3'","`check`");
	    if(is_array($cert))
	    {
	     	foreach($cert as $v)
	     	{
	     		$this->obj->unlink_pic("../".$v['check']);
	     	}
	    }
		$delid=$this->obj->DB_delete_all("company_cert","`uid` in (".$_POST['ids'].")  and `type`='3'","");
		$this->write_appadmin_log("删除企业认证(UID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}

	function newslist_action()
	{
		$where="a.uid=b.uid";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
        if($_POST['status'])
        {
			if($_POST['status']=='3'){
				$where.=" and a.`status`='0'";
			}else{
				$where.=" and a.`status`='".$_POST['status']."'";
			}
        }
		if($keyword)
		{
			$where.=" and b.`name` like '%".$keyword."%'";
		}
		if($order){
			$where.=" order by a.".$order;
		}else{
			$where.=" order by a.`status` asc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_alls("company_news","company",$where,"a.*,b.name");
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['title']=iconv("gbk","UTF-8",$v['title']);
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['status']=$v['status'];
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
	function newsshow_action()
	{
		if(!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$id=(int)$_POST['id'];
		$row=$this->obj->DB_select_once("company_news","`id`='".$id."'");
		if(!empty($row))
		{
			$list['id']		=$row['id'];
			$list['uid']	=$row['uid'];
			$list['title']	=iconv("gbk","UTF-8",$row['title']);
			$list['body']	=iconv("gbk","UTF-8",$row['body']);
			$list['ctime']	=$row['ctime'];
			$list['status']=$row['status'];
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
	function newsdel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$delid=$this->obj->DB_delete_all("company_news","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("删除企业新闻(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function newsstatus_action()
	{
		if(!$_POST['ids']||!$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		if($_POST['status'])
		{
			if($_POST['status']=="3")
			{
				$status=0;
			}else{
				$status=(int)$_POST['status'];
			}
		}
		$this->obj->DB_update_all("company_news","`status`='".$status."',`statusbody`='".$_POST['statusbody']."'","`id` in (".$_POST['ids'].")");
		$this->write_appadmin_log("审核企业新闻(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function productlist_action()
	{
		$where="a.uid=b.uid";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		$keyword=$keyword?$keyword:$_POST['keyword'];
        if($_POST['status'])
        {
			if($_POST['status']=='3'){
				$where.=" and a.`status`='0'";
			}else{
				$where.=" and a.`status`='".$_POST['status']."'";
			}
        }
		if($keyword)
		{
			$where.=" and a.`title` like '%".$keyword."%'";
		}
		if($order){
			$where.=" order by a.".$order;
		}else{
			$where.=" order by a.`status` asc";
		}
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$rows=$this->obj->DB_select_alls("company_product","company",$where,"a.*,b.name");
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['title']=iconv("gbk","UTF-8",$v['title']);
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['pic']=$this->config['sy_weburl']."/".$v['pic'];
				$list[$k]['status']=$v['status'];
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
	function productshow_action()
	{
		if(!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$id=(int)$_POST['id'];
		$row=$this->obj->DB_select_once("company_product","`id`='".$id."'");
		if(!empty($row))
		{
			$list['id']		=$row['id'];
			$list['uid']	=$row['uid'];
			$list['title']	=iconv("gbk","UTF-8",$row['title']);
			$list['body']	=iconv("gbk","UTF-8",$row['body']);
			$list['ctime']	=$row['ctime'];
			$list['status']=$row['status'];
			$list['pic']=$this->config['sy_weburl'].$row['pic'];
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
	function productdel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$delid=$this->obj->DB_delete_all("company_product","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("删除企业产品(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function productstatus_action()
	{
		if(!$_POST['ids']||!$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		if($_POST['status'])
		{
			if($_POST['status']=="3")
			{
				$status=0;
			}else{
				$status=(int)$_POST['status'];
			}
		}
		$this->obj->DB_update_all("company_product","`status`='".$status."',`statusbody`='".$_POST['statusbody']."'","`id` in (".$_POST['ids'].")");
		$this->write_appadmin_log("审核企业产品(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function oncelist_action()
	{
		$where=1;
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$order=$_POST['order'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (`title` like '%".$keyword."%' or `companyname` like '%".$keyword."%')";
		}
		if($_POST['status'])
		{
			if($_POST['status']==1)
			{
				$where.=" and `status`='1'";
			}elseif($_POST['status']==2){
				$where.=" and `edate`<'".time()."'";
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
		$rows=$this->obj->DB_select_all("once_job",$where);
		if(!empty($rows))
		{
			foreach($rows as $k=>$v)
			{
				$list[$k]['id']=$v['id'];
				$list[$k]['title']=iconv("gbk","UTF-8",$v['title']);
				$list[$k]['companyname']=iconv("gbk","UTF-8",$v['companyname']);
				$list[$k]['linkman']=iconv("gbk","UTF-8",$v['linkman']);
				$list[$k]['mans']=iconv("gbk","UTF-8",$v['mans']);
				$list[$k]['phone']=iconv("gbk","UTF-8",$v['phone']);
				$list[$k]['qq']=iconv("gbk","UTF-8",$v['qq']);
				$list[$k]['email']=iconv("gbk","UTF-8",$v['email']);
				$list[$k]['edate']=$v['edate'];
				$list[$k]['status']=$v['status'];
				$list[$k]['ctime']=$v['ctime'];
				if($v['edate']<mktime()){
					$list[$k]['status']='2';
				}
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
	function oncedel_action()
	{
		if(!$_POST['ids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$delid=$this->obj->DB_delete_all("once_job","`id` in (".$_POST['ids'].")","");
		$this->write_appadmin_log("删除微招聘(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function onceshow_action()
	{
		if(!$_POST['id'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$id=(int)$_POST['id'];
		$row=$this->obj->DB_select_once("once_job","`id`='".$id."'");
		if(!empty($row))
		{
			$list['id']		=$row['id'];
			$list['title']	=iconv("gbk","UTF-8",$row['title']);
			$list['require']	=iconv("gbk","UTF-8",$row['require']);
			$list['companyname']	=iconv("gbk","UTF-8",$row['companyname']);
			$list['linkman']	=iconv("gbk","UTF-8",$row['linkman']);
			$list['address']	=iconv("gbk","UTF-8",$row['address']);
			$list['email']	=iconv("gbk","UTF-8",$row['email']);
			$list['mans']	=$row['mans'];
			$list['phone']	=$row['phone'];
			$list['qq']	=$row['qq'];
			$list['edate']	=$row['edate'];
			$list['login_ip']	=$row['login_ip'];
			$list['ctime']	=$row['ctime'];
			$list['status']=$row['status'];
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
	function oncestatus_action()
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
		$this->obj->DB_update_all("once_job","`status`='".$status."'","`id` in (".$_POST['ids'].")");
		$this->write_appadmin_log("审核微招聘(ID:".$_POST['ids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}
	function companylist_action()
	{
		$where="a.uid=b.uid";
		$page=$_POST['page'];
		$limit=$_POST['limit'];
		$keyword=$this->stringfilter($_POST['keyword']);
		if($keyword)
		{
			$where.=" and (a.`username` like '%".$keyword."%' or b.`name` like '%".$keyword."%')";
		}
		if($_POST['status'])
		{
			if($_POST['status']==4)
			{
				$where.=" and a.`status`='0'";
			}else{
				$where.=" and a.`status`='".(int)$_POST['status']."'";
			}
		}
		$where.=" order by a.`status` asc,a.uid desc";
		$limit=!$limit?10:$limit;
		if($page){
			$pagenav=($page-1)*$limit;
			$where.=" limit $pagenav,$limit";
		}else{
			$where.=" limit $limit";
		}
		$select="a.uid,a.username,a.status,a.reg_date,b.name,b.linktel,b.linkman,b.linkmail,b.r_status";
		$rows=$this->obj->DB_select_alls("member","company",$where,$select);
		if(!empty($rows))
		{
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
			}
			$statis=$this->obj->DB_select_all("company_statis","`uid` in (".$this->pylode(",",$uid).")","`uid`,`rating_name`");
			foreach($rows as $k=>$v)
			{
				foreach($statis as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$list[$k]['rating_name']=iconv("gbk","UTF-8",$v['rating_name']);
					}
				}
				$list[$k]['uid']=$v['uid'];
				$list[$k]['username']=iconv("gbk","UTF-8",$v['username']);
				$list[$k]['name']=iconv("gbk","UTF-8",$v['name']);
				$list[$k]['linktel']=iconv("gbk","UTF-8",$v['linktel']);
				$list[$k]['linkman']=iconv("gbk","UTF-8",$v['linkman']);
				$list[$k]['linkmail']=iconv("gbk","UTF-8",$v['linkmail']);
				if($v['status']==0){
					$list[$k]['status']='4';
				}else{
					$list[$k]['status']=$v['status'];
				}
				$list[$k]['reg_date']=$v['reg_date'];
				$list[$k]['r_status']=$v['r_status'];
				$list[$k]['rec']=$v['rec'];
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
	function companyshow_action()
	{
		if(!$_POST['uid'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$uid=(int)$_POST['uid'];
		$row=$this->obj->DB_select_once("company","`uid`='".$uid."'");
		$member=$this->obj->DB_select_once("member","`uid`='".$uid."'","status");
		if(!empty($row))
		{
			$list['uid']		=$row['uid'];
			$list['name']		=iconv("gbk","UTF-8",$row['name']);
			$list['content']	=iconv("gbk","UTF-8",$row['content']);
			$list['address']	=iconv("gbk","UTF-8",$row['address']);
			$list['linkman']	=iconv("gbk","UTF-8",$row['linkman']);
			$list['linkjob']	=iconv("gbk","UTF-8",$row['linkjob']);
			$list['linkmail']	=iconv("gbk","UTF-8",$row['linkmail']);
			$list['hy']			=$row['hy'];
			$list['pr']			=$row['pr'];
			$list['provinceid']=$row['provinceid'];
			$list['cityid']	=$row['cityid'];
			$list['mun']		=$row['mun'];
			$list['sdate']		=$row['sdate']?$row['sdate']:'';
			$list['money']		=$row['money']?$row['money']:'';
			$list['zip']		=$row['zip'];
			$list['linkqq']	=iconv("gbk","UTF-8",$row['linkqq']);
			$list['linkphone']=iconv("gbk","UTF-8",$row['linkphone']);
			$list['linktel']	=iconv("gbk","UTF-8",$row['linktel']);
			$list['website']	=$row['website'];
			$list['logo']		=str_replace("./",$this->config['sy_weburl']."/",$row['logo']);
			$list['lastupdate']=$row['lastupdate'];
			$list['firmpic']	=str_replace("./",$this->config['sy_weburl']."/",$row['firmpic']);
			$list['rec']		=$row['rec'];
			$list['status']	=$member['status'];
			$list['r_status']=$row['r_status'];
			foreach($list as $k=>$v){
				if(is_array($v)){
					foreach($v as $key=>$val){
						$list[$k][$key]=isset($val)?$val:'';
					}
				}else{
					$list[$k]=isset($v)?$v:'';
				}
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
	function companydel_action()
	{
		if(!$_POST['uids'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$uids=$_POST['uids'];
		$del=@explode(",",$_POST['uids']);
		foreach($del as $k=>$v){
			$this->obj->delfiledir("../upload/tel/".intval($v));
		}
	    $company=$this->obj->DB_select_all("company","`uid` in (".$uids.") and `logo`<>''","logo,firmpic");
	    if(is_array($company)){
	    	foreach($company as $v){
	    		$this->obj->unlink_pic(".".$v['logo']);
	    		$this->obj->unlink_pic(".".$v['firmpic']);
	    	}
	    }
	    $cert=$this->obj->DB_select_all("company_cert","`uid` in (".$uids.") and `type`='3'","check");
	    if(is_array($cert)){
	    	foreach($cert as $v){
	    		$this->obj->unlink_pic("../".$v['check']);
	    	}
	    }
	    $product=$this->obj->DB_select_all("company_product","`uid` in (".$uids.")","pic");
	    if(is_array($product)){
	    	foreach($product as $val){
	    		$this->obj->unlink_pic("../".$val['pic']);
	    	}
	    }
	    $show=$this->obj->DB_select_all("company_show","`uid` in (".$uids.")","picurl");
	    if(is_array($show)){
	    	foreach($show as $val){
	    		$this->obj->unlink_pic("../".$val['picurl']);
	    	}
	    }
	    $uhotjob=$this->obj->DB_select_all("hotjob","`uid` in (".$uids.")","hot_pic");
	    if(is_array($uhotjob)){
	    	foreach($uhotjob as $val){
	    		$this->obj->unlink_pic("../".$val['hot_pic']);
	    	}
	    }
	  	$banner=$this->obj->DB_select_all("banner","`uid` in (".$uids.")","pic");
	    if(is_array($banner)){
	    	foreach($banner as $val)
	    	{
	    		$this->obj->unlink_pic($val['pic']);
	    	}
	    }
	    $friend_pic = $this->obj->DB_select_all("friend_info","`uid` in (".$uids.") and `pic`!=''","pic,pic_big");
		if(is_array($friend_pic))
		{
	    	foreach($friend_pic as $val)
	    	{
	    		$this->obj->unlink_pic($val['pic']);
	    		$this->obj->unlink_pic($val['pic_big']);
	    	}
		}
		$del_array=array("member","company","company_job","company_cert","company_news","company_order","company_product","company_show","banner","company_statis","friend_info","friend_state","question","attention","lt_job","hotjob");
		foreach($del_array as $value)
		{
			$this->obj->DB_delete_all($value,"`uid` in (".$uids.")","");
		}
	    $this->obj->DB_delete_all("company_pay","`com_id` in (".$uids.")"," ");
		$this->obj->DB_delete_all("atn","`uid` in (".$uids.") or `scid` in (".$uids.")","");
		$this->obj->DB_delete_all("look_resume","`com_id` in (".$uids.")","");
		$this->obj->DB_delete_all("fav_job","`com_id` in (".$uids.")","");
		$this->obj->DB_delete_all("userid_msg","`fid` in (".$uids.")","");
		$this->obj->DB_delete_all("userid_job","`com_id` in (".$uids.")","");
		$this->obj->DB_delete_all("message","`fa_uid` in (".$uids.")","");
	    $this->obj->DB_delete_all("friend_reply","`fid` in (".$uids.")","");
	    $this->obj->DB_delete_all("friend","`uid` in (".$uids.") or `nid` in (".$uids.")","");
	    $this->obj->DB_delete_all("friend_foot","`uid` in (".$uids.") or `fid` in (".$uids.")","");
	    $this->obj->DB_delete_all("friend_message","`uid`='".$del."' or `fid`='".$del."'","");
	    $this->obj->DB_delete_all("msg","`job_uid` in (".$uids.")","");
	    $this->obj->DB_delete_all("blacklist","`c_uid` in (".$uids.")","");
	    $this->obj->DB_delete_all("rebates","`job_uid` in (".$uids.") or `uid` in (".$uids.")"," ");
	    $this->obj->DB_delete_all("report","`p_uid` in ($uids) or `c_uid` in ($uids)","");
		$this->write_appadmin_log("删除企业(UID:".$_POST['uids'].")");
		$data['error']=1;
		echo json_encode($data);die;
	}

	function companystatus_action()
	{
		if(!$_POST['uids'] || !$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$member=$this->obj->DB_select_alls("member","company","a.`uid` in (".$_POST['uids'].") and a.uid=b.uid","a.`email`,a.`uid`,b.`name`");
		if(!empty($member))
		{
			foreach($member as $v)
			{
				$this->send_msg_email(array("uid"=>$v['uid'],"name"=>$v['name'],"email"=>$v['email'],"status_info"=>$_POST['statusbody'],"date"=>date("Y-m-d H:i:s"),"type"=>"userstatus"));
			}
			$id=$this->obj->DB_update_all("member","`status`='".(int)$_POST['status']."',`lock_info`='".$_POST['statusbody']."'","`uid` IN (".$_POST['uids'].")");
		}
		if($id)
		{
			$this->write_appadmin_log("企业审核(UID:".$_POST['uids'].")");
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"审核失败");
		}
	}
	function lock_action()
	{
		if(!$_POST['uid'] || !$_POST['status'])
		{
			$this->return_appadmin_msg(2,"参数出错");
		}
		$_POST['uid']=intval($_POST['uid']);
		$_POST['status']=intval($_POST['status']);
		$email=$this->obj->DB_select_alls("member","company","a.`uid`='".$_POST['uid']."' and a.`uid`=b.`uid`","a.`email`,b.`name`");
		$this->obj->DB_update_all("company_job","`r_status`='".$_POST['status']."'","`uid`='".$_POST['uid']."'");
		$this->obj->DB_update_all("company","`r_status`='".$_POST['status']."'","`uid`='".$_POST['uid']."'");
		$id=$this->obj->DB_update_all("member","`status`='".$_POST['status']."',`lock_info`='".$_POST['statusbody']."'","`uid`='".$_POST['uid']."'");
		$this->send_msg_email(array("email"=>$email[0]['email'],"uid"=>$_POST['uid'],"name"=>$email[0]['name'],"lock_info"=>$_POST['statusbody'],"type"=>"lock"));
		if($id)
		{
			$this->write_appadmin_log("企业锁定(UID:".$_POST['uid'].")");
			$data['error']=1;
			echo json_encode($data);die;
		}else{
			$this->return_appadmin_msg(2,"锁定设置失败");
		}
	}
}
?>