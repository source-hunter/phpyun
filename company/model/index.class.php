<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2015 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class index_controller extends common
{
	function public_action()
	{
		include(PLUS_PATH."config.php");
		$this->yunset("config",$config);
		include(PLUS_PATH."menu.cache.php");
		if(is_array($menu_name))
		{
			foreach($menu_name[1] as $key=>$value)
			{
				if($value['type']=='1')
				{
					$menu_name[1][$key]['url'] = $this->config['sy_weburl']."/".$value['url'];
				}
			}
		}
		$this->job_cache();
		$this->yunset("menu_name",$menu_name);
		$now_url=@explode("/",$_SERVER['REQUEST_URI']);
		$now_url=$now_url[count($now_url)-1];
		$this->yunset("now_url",$now_url);
		$this->yunset("id",$_GET['id']);
		$this->yunset("defaultstyle","../template/default/");
		$this->registrs_com();
		$this->GetNewPorduct();

	}
	function index_action()
	{
		if($this->uid!=$_GET['id']&&$_COOKIE['usertype']=='1')
		{
			$show=$this->obj->DB_select_once("atn","`sc_uid`='".(int)$_GET['id']."' and `uid`='".$this->uid."'");
			$this->yunset("usertype",'1');
			$this->yunset("show",$show);
		}
		if($_POST['submit'])
		{
			$black=$this->obj->DB_select_once("blacklist","`p_uid`='".$this->uid."' and `c_uid`='".(int)$_POST['id']."'");
			if(!empty($black))
			{
				$this->obj->ACT_layer_msg("您已被该企业列入黑名单，不能评论该企业！",8,$_SERVER['HTTP_REFERER']);
			}			
      		$qiye=$this->obj->DB_select_once("company","`uid`='".(int)$_POST['id']."'","`pl_status`");
			$data['uid']=$this->uid;
			$data['content']=$_POST['content'];
			$data['cuid']=(int)$_POST['id'];
			$data['ctime']=time();
			if ($qiye['pl_status']=='2')
			{
				$data['status']=0;
				$nid=$this->obj->insert_into("company_msg",$data);
 			    isset($nid)?$this->obj->ACT_layer_msg("评论成功，请等待企业审核！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("评论失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$data['status']=1;
				$nid=$this->obj->insert_into("company_msg",$data);
	 			isset($nid)?$this->obj->ACT_layer_msg("评论成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("评论失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
			}
		}
		if($_POST['submit2'])
		{
			$data['reply']=$_POST['content'];
			$data['reply_time']=time();
			$where['id']=(int)$_POST['id'];
			$where['cuid']=$this->uid;
			$nid=$this->obj->update_once("company_msg",$data,$where);
 			isset($nid)?$this->obj->ACT_layer_msg("回复成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("回复失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
		}
		$msglist=$this->obj->DB_select_alls("company_msg","resume","a.`cuid`='".(int)$_GET['id']."' and a.`uid`=b.`uid` and a.`status`='1' order by a.`id` desc limit 3","a.*,b.`name`,b.`photo`,b.`def_job` as eid");
		$msg_num=$this->obj->DB_select_num("company_msg","`cuid`='".(int)$_GET['id']."' and `status`='1'");
		if($msg_num>3){
			$this->yunset("msg_num",$msg_num);
		}
		$row=$this->obj->DB_select_alls("company","company_statis","a.`uid`=b.`uid` and a.uid='".(int)$_GET['id']."'");
		if(!is_array($row))
		{
			$this->obj->ACT_msg($this->config['sy_weburl'],"没有找到该企业！");
		}elseif($row[r_status]==2){
 			$this->obj->ACT_msg($this->config['sy_weburl'],"该企业暂被锁定，请稍后查看！");
		}
		$this->obj->DB_update_all("company","`hits`=`hits`+1","`uid`='".(int)$_GET['id']."'");
		$row=$row[0];
		include(PLUS_PATH."city.cache.php");
		include(PLUS_PATH."com.cache.php");
		include(PLUS_PATH."industry.cache.php");
        $row['provinceid']=$city_name[$row['provinceid']];
		$row['mun_info']=$comclass_name[$row['mun']];
		$row['pr_info']=$comclass_name[$row['pr']];
		$row['hy_info']=$industry_name[$row['hy']];
		$row['logo']=str_replace("./",$this->config['sy_weburl']."/",$row['logo']);
		$banner=$this->obj->DB_select_once("banner","`uid`='".(int)$_GET['id']."'");
		$banner['pic']=str_replace("..",$this->config['sy_weburl'],$banner['pic']);
		$this->yunset("banner",$banner);
		if($this->config['com_login_link']=="1"||$this->config['com_resume_link']=='1')
		{
			if($this->uid=="" && $this->username=="")
			{
				$look_msg="您还没有登录，登录后才可以查看联系方式！";
				$looktype="2";
			}else if($_GET['id']!=$_COOKIE['uid']){
				if($_COOKIE["usertype"]!="1")
				{
					$look_msg="您不是个人用户，不能查看联系方式！";
				}else{
					if($this->config['com_resume_link']=="1")
					{
						$rows=$this->obj->DB_select_num("resume_expect","`uid`='".$this->uid."'");
						if($rows<1)
						{
							$look_msg="您还没有已审核个人简历，不能查看联系方式！";
							$looktype="1";
						}
					}
				}
			}
		}
		if ($_GET['style'] && !preg_match('/^[a-zA-Z]+$/',$_GET['style']))
		{
			exit();
		}
		
		if ($_GET['tp'] && !preg_match('/^[a-zA-Z]+$/',$_GET['tp']))
		{
			exit();
		}
		if($row['comtpl'] && $row['comtpl']!="default" && !$_GET['style']){
			$tplurl=$row[comtpl];
			$this->registrs();
		}else{
			$tplurl="default";
		}
		if($_GET['style']){
			$tplurl=$_GET['style'];
		}
		$tp=$_GET['tp']?$_GET['tp']:"index";
		$this->public_action();
		$this->yunset("msglist",$msglist);
		$this->yunset("usertype",$_COOKIE['usertype']);
		$this->yunset("uid",$this->uid);
		$this->yunset("comclass_name",$comclass_name);
		$this->yunset("com",$row);
		$this->yunset("looktype",$looktype);
		$this->yunset("look_msg",$look_msg);
		$this->seo("company_".$tp);
		$this->yunset("com_style",$this->config['sy_weburl']."/template/company/".$tplurl."/");
		$this->yunset("comstyle","../template/company/".$tplurl."/");
		$this->yuntpl(array('company/'.$tplurl."/".$tp));
	}
	function msg_action()
	{
		$urlarr['company']='';
		$urlarr['id']=$_GET['id'];
		$urlarr['c']='msg';
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","",$urlarr,'1');
		$msglist=$this->get_page("company_msg","`cuid`='".(int)$_GET['id']."' and `status`='1' order by id desc",$pageurl,"10");
		if(is_array($msglist)&&$msglist){
			foreach($msglist as $v){
				$uid[]=$v['uid'];
			}
			$uid=@implode(",",$uid);
			$user=$this->obj->DB_select_all("resume","`uid` in (".$uid.")","`uid`,`name`,`photo`,`def_job`");
			foreach($msglist as $k=>$v){
				foreach($user as $val){
					if($v['uid']==$val['uid']){
						$msglist[$k]['name']=$val['name'];
						$msglist[$k]['photo']=$val['photo'];
						$msglist[$k]['eid'] = $val['def_job'];
					}
				}
			}
		}
		$row=$this->obj->DB_select_alls("company","company_statis","a.`uid`=b.`uid` and a.uid='".(int)$_GET['id']."'");
		if(!is_array($row))
		{
			$this->obj->ACT_msg($this->config['sy_weburl'],"没有找到该企业！");
		}elseif($row[r_status]==2){
 			$this->obj->ACT_msg($this->config['sy_weburl'],"该企业暂被锁定，请稍后查看！");
		}
		$this->obj->update_once("company",array("hits"=>"`hits`+1"),array("uid"=>(int)$_GET['id']));
		$row=$row[0];
		$cert=@explode(",",$row['cert']);
		include(PLUS_PATH."city.cache.php");
		include(PLUS_PATH."com.cache.php");
		include(PLUS_PATH."industry.cache.php");
        $row['provinceid']=$city_name[$row['provinceid']];
		$row['mun_info']=$comclass_name[$row['mun']];
		$row['pr_info']=$comclass_name[$row['pr']];
		$row['hy_info']=$industry_name[$row['hy']];
		$row['logo']=str_replace("./",$this->config['sy_weburl']."/",$row['logo']);
		if($row['comtpl'] && $row['comtpl']!="default" && !$_GET['style']){
			$tplurl=$row[comtpl];
			$this->registrs();
		}else{
			$tplurl="default";
		}
		if($_GET['style']){
			$tplurl=$_GET['style'];
		}
		$this->seo('commsg');
		$this->public_action();
		$this->yunset("com_style",$this->config['sy_weburl']."/template/company/".$tplurl."/");
		$this->yunset("comstyle","../template/company/".$tplurl."/");
		$this->yunset("com",$row);
		$this->yunset("msglist",$msglist);
		$this->yuntpl(array('company/'.$tplurl."/msg"));
	}
	function GetNewPorduct()
	{
		if($_GET['nid'])
		{
			if($_GET['id']!=$this->uid)
			{
				$where=" and `status`='1'";
			}
			$row=$this->obj->DB_select_once("company_news","`id`='".(int)$_GET['nid']."' and uid='".(int)$_GET['id']."'".$where);
		}
		if($_GET['pid'])
		{
			if($_GET['id']!=$this->uid)
			{
				$where=" and `status`='1'";
			}
			$row=$this->obj->DB_select_once("company_product","`id`='".(int)$_GET['pid']."' and uid='".(int)$_GET['id']."'".$where);
		}
		$this->yunset("row",$row);
	}

	function registrs_com()
	{
		include(LIB_PATH."com.libs.php");
		$this->tpl->register_function("date_now","print_current_date");
		$this->tpl->register_function("job","getComJob");
		$this->tpl->register_function("jobpage","getComJobPage");
		$this->tpl->register_function("show","getComShow");
		$this->tpl->register_function("showpage","getComShowPage");
		$this->tpl->register_function("newspage","getComNewsPage");
		$this->tpl->register_function("productpage","getComProductPage");
		$this->tpl->register_function("news","getComNews");
		$this->tpl->register_function("product","getComProduct");
		$this->tpl->register_function("api","getComApi");
	}
}
?>