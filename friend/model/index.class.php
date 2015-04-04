<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class index_controller extends common{
	function public_action()
	{
		$now_url=@explode("/",$_SERVER['REQUEST_URI']);
		$now_url=$now_url[count($now_url)-1];
		$this->yunset("now_url",$now_url);
		if($this->uid=="")
		{
			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"您还未登录，请先登录！");
		}else{
			$member = $this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'");
			if($member['pic']){
				$member['pic']=str_replace("..",$this->config['sy_weburl'],$member['pic']);
			}else{
				$member['pic']=$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
			}
			$this->yunset("member",$member);
		}
	}
	function frienduid_action()
	{
		$info = $this->obj->DB_select_all("friend","`uid`='".$this->uid."' and `status`='1'","`nid`");
		if(!empty($info))
		{
			foreach($info as $k=>$v)
			{
				$uid[] = $v['nid'];
			}
			$uid = @implode(",",$uid);
		}
		return $uid;
	}
	function myfoot_action($id="")
	{
		$id = $id?$id:$this->uid;
		$myfoot = $this->obj->DB_select_all("friend_foot","`fid`='".$id."' order by ctime desc limit 4");
 		if(is_array($myfoot))
 		{
			foreach($myfoot as $val)
			{
				$uids[]=$val['uid'];
			}
			$info = $this->obj->DB_select_all("friend_info","`uid` in (".@implode(',',$uids).")","`uid`,`nickname`,`pic`");
			if(is_array($info))
			{
				foreach($myfoot as $k=>$v)
				{
					$list[$k]['ctime'] = date("Y-m-d H:i",$v['ctime']);
					foreach($info as $key=>$val)
					{
						if($v['uid']==$val['uid'])
						{
							$myfoot[$k]['nickname'] = $val['nickname'];
							$myfoot[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$val['pic']);
						}
					}
				}
			}
		}
		$this->yunset("myfoot",$myfoot);
		return $myfoot;
	}
	function himan_action()
	{
		$addlist = $this->obj->DB_select_all("friend_info","`uid`<>'".$this->uid."' and `pic`<>'' and `iscert`='1' order by rand() limit 5");
		if(is_array($addlist))
		{
			foreach($addlist as $k=>$v)
			{
				$addlist[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$v['pic']);
			}
		}
		$this->yunset("addlist",$addlist);
	}
	function leftinfo_action($id)
	{
		include APP_PATH."/plus/city.cache.php";
		if(!$id)
		{
			$id = $this->uid;
		}else{
			$id=(int)$id;
		}
		$member = $this->obj->DB_select_once("friend_info","`uid`='".$id."'","pic_big,usertype,nickname,birthday,sex");
		if(empty($member))
		{
			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"不存在该用户！");
		}
		if($member['usertype']==1)
		{
			include APP_PATH."/plus/user.cache.php";
			$leftinfo = $this->obj->DB_select_once("resume","`uid`='".$id."'");
			$leftinfo['typename'] = "个人会员";
			$leftinfo['sexinfo'] = $userclass_name[$leftinfo['sex']];
			$leftinfo['home'] = $city_name[$leftinfo['province']] .$city_name[$leftinfo['city']];
		}elseif($member['usertype']==2){
			include APP_PATH."/plus/industry.cache.php";
			include APP_PATH."/plus/job.cache.php";
			$leftinfo = $this->obj->DB_select_once("company","`uid`='".$id."'");
			$leftinfo['typename'] = "企业会员";
			$leftinfo['hyinfo'] = $industry_name[$leftinfo['hy']];
			$leftinfo['home'] = $city_name[$leftinfo['provinceid']] .$city_name[$leftinfo['cityid']];
		}
		$user = $this->obj->DB_select_once("member","`uid`='".$id."'","`username`");
		$leftinfo['uid']=$id;
		$leftinfo['username']=$user['username'];
		$leftinfo['birthday'] = $member['birthday'];
		$leftinfo['sex'] = $member['sex'];
		$member["pic_big"]=str_replace("..",$this->config['sy_weburl'],$member['pic_big']);
		$this->yunset("pic",$member['pic_big']);
		$this->yunset("type",$member['usertype']);
		$this->yunset("nickname",$member['nickname']);
		$this->yunset("leftinfo",$leftinfo);
		return $leftinfo;
	}
	function index_action(){
		$this->yunset("id",$_GET['id']);
		$this->public_action();			
		$fuids = $this->frienduid_action();
		if(trim($fuids)){
			$myfriend = $this->obj->DB_select_all("friend_info","`uid` in (".$fuids.")");
			$num = $this->obj->DB_select_num("friend_state","`uid`='".$this->uid."' or `uid` in (".$fuids.")");
			$pages=ceil($num/11);
			$this->yunset("pages",$pages);
			$list = $this->obj->DB_select_all("friend_state","`uid`='".$this->uid."' or `uid` in (".$fuids.") order by ctime desc limit 11");
			$info = $this->obj->DB_select_all("friend_info","`uid`='".$this->uid."' or `uid` in (".$fuids.")");
		}
		$this->yunset("myfriend",$myfriend);
		if(is_array($list))
		{
			foreach($list as $val){
				$lids[]=$val['id'];
			}
			$comment = $this->obj->DB_select_alls("friend_reply","friend_info","a.uid=b.uid and a.`nid` in(".@implode(',',$lids).") order by a.id asc");
			if(is_array($info))
			{
				foreach($list as $k=>$v)
				{
					$list[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$v['pic']);
					$list[$k]['ctime'] = date("Y-m-d H:i",$v['ctime']);
					foreach($info as $key=>$val)
					{
						if($v['uid']==$val['uid'])
						{
							$list[$k]['nickname'] = $val['nickname'];
							if($val['pic']){
								$list[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$val['pic']);
							}else{
								$list[$k]['pic'] = $this->config['sy_weburl'].$this->config['sy_friend_icon'];
							}
						}
					}
					$list[$k]['commentnum'] = "-1";
					if(is_array($comment))
					{
						foreach($comment as $kk=>$vv)
						{
							$vv['ctime'] = date("Y-m-d H:i",$vv['ctime']);
							if($v['id']==$vv['nid']&&$vv['nid']!="")
							{
								if($vv['pic']){
									$list[$k]['replypic'] = str_replace("..",$this->config['sy_weburl'],$vv['pic']);
								}else{
									$list[$k]['replypic'] =$this->config['sy_weburl'].$this->config['sy_friend_icon'];
								}
								$list[$k]['reply'] = $vv['reply'];
								$list[$k]['replyname'] = $vv['nickname'];
								$list[$k]['replyctime'] = $vv['ctime'];
								$list[$k]['url'] = $this->furl(array("url"=>"c:profile,id:".$vv['uid']));
								$list[$k]['commentnum'] = $list[$k]['commentnum']+1;
							}
						}
					}
				}
			}
		}
		$this->yunset("list",$list);
		$this->himan_action();
		$this->myfoot_action((int)$_GET['id']); 
		$this->seo("fri_index"); 
		$this->yunset("class","1");
		$this->yuntpl(array('friend/default/index'));
	}
	function profile_action()
	{
		$this->public_action();
		$this->leftinfo_action($_GET['id']);
		$withfriend = $this->obj->DB_select_once("friend","`uid`='".$this->uid."' and `nid`='".(int)$_GET['id']."' and `status`='1'");

		if($withfriend||$_GET['id']==$this->uid)
		{
			if($_GET['id']!=$this->uid)
			{
				$footed = $this->obj->DB_select_once("friend_foot","`uid`='".$this->uid."' and `fid`='".(int)$_GET['id']."'");
				if(empty($footed))
				{
					$data['uid']=$this->uid;
					$data['fid']=(int)$_GET['id'];
					$data['ctime']=time();
					$data['num']=1;
					$this->obj->insert_into("friend_foot",$data);
				}else{
					$this->obj->DB_update_all("friend_foot","`num`=`num`+1,`ctime`='".time()."'","`uid`='".$this->uid."' and `fid`='".(int)$_GET['id']."'");
				}
			}
			$friend = $this->obj->DB_select_all("friend","`uid`='".(int)$_GET['id']."' order by `id` desc");
			if(is_array($friend))
			{
				foreach($friend as $k=>$v)
				{
					$fuid[]=$v['nid'];
				}
				$myfriend = $this->obj->DB_select_all("friend_info","`uid` in (".@implode(",",$fuid).")");
				$this->yunset("myfriend",$myfriend);
			}

			$num = $this->obj->DB_select_num("friend_state","`uid`='".(int)$_GET['id']."'","id");
	 		$pages=ceil($num/11);
	 		$this->yunset("pages",$pages);
			$list = $this->obj->DB_select_alls("friend_state","friend_info","a.`uid`='".(int)$_GET['id']."' and a.`uid`=b.`uid` order by ctime desc limit 11","a.*,b.`pic`");
			if(is_array($list)&&$list)
			{
				foreach($list as $val)
				{
					$fsids[]=$val['id'];
				}
				$comment = $this->obj->DB_select_alls("friend_reply","friend_info","a.uid=b.uid and a.`nid` in (".@implode(',',$fsids).") order by a.id asc","a.`nid`,a.`ctime`,a.`reply`,b.`nickname`,b.`uid`,b.`pic`");
			}
			if($_POST['submit'])
			{
				$data['content']=trim($_POST['content']);
				$data['uid']=$this->uid;
				$data['fid']=(int)$_POST['touid'];
				$data['ctime']=time();
				$data['nid']=(int)$_GET['id'];
				$nid=$this->obj->insert_into("friend_message",$data);
				$nid?$this->obj->ACT_layer_msg("留言成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("留言失败！",8,$_SERVER['HTTP_REFERER']);
			}
			$mlist = $this->obj->DB_select_all("friend_message","`fid`='".(int)$_GET['id']."' and `pid`='0' order by ctime desc limit 6");
			if(is_array($mlist))
			{
				foreach($mlist as $v)
				{
					$msg_id[]=$v['id'];
					$ruid[]=$v['uid'];
				}
				$msg_ids=@implode(',',$msg_id);
				$reply=$this->obj->DB_select_all("friend_message","`pid` in (".$msg_ids.")  order by `pid` desc,`ctime`");
				if(is_array($reply))
				{
					foreach($reply as $val)
					{
						$ruid[]=$val['uid'];
						$ruid[]=$val['fid'];
					}
				}
				$info = $this->obj->DB_select_all("friend_info","`uid` in (".@implode(',',$ruid).")","`uid`,`nickname`,`pic`");
				if(is_array($reply))
				{
					foreach($reply as $k=>$v)
					{
						foreach($info as $val)
						{
							if($v['uid']==$val['uid'])
							{
								$reply[$k]['pic']=str_replace("..",$this->config['sy_weburl'],$val['pic']);
								$reply[$k]['u_name']=$val['nickname'];
							}
							if($v['uid']==$val['fid'])
							{
								$reply[$k]['f_name']=$val['nickname'];
							}
							$reply[$k]['r_ctime'] = date("Y-m-d H:i:s",$v['ctime']);
						}
						$reply[$k]['r_url']=$this->config['sy_weburl']."/friend/index.php?c=profile&id=".$v['uid'];
					}
				}
				foreach($mlist as $k=>$v)
				{
					$mlist[$k]['ctime'] = date("Y-m-d H:i:s",$v['ctime']);
					foreach($info as $key=>$val)
					{
						if($v['uid']==$val['uid'])
						{
							$mlist[$k]['nickname'] = $val['nickname'];
							$mlist[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$val['pic']);
						}
					}
					if(is_array($reply))
					{
						$num='0';
						foreach($reply as $val)
						{
							if($v['id']==$val['pid'])
							{
								$mlist[$k]['reply'][]=$val;
								$num +=1;
								$mlist[$k]['num']=$num;
							}
						}
					}
				}
			}
			if(is_array($list))
			{
				if(is_array($info))
				{
					foreach($list as $k=>$v)
					{
						$list[$k]['ctime'] = date("Y-m-d H:i",$v['ctime']);
						foreach($info as $key=>$val)
						{
							if($v['uid']==$val['uid'])
							{
								$list[$k]['nickname'] = $val['nickname'];
								$list[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$val['pic']);
							}
						}
						$list[$k]['commentnum'] = "-1";
						if(is_array($comment))
						{
							foreach($comment as $kk=>$vv)
							{
 								if($v['id']==$vv['nid']&&$vv['nid']!="")
 								{
									$list[$k]['reply'] = $vv['reply'];
									$list[$k]['replypic'] = str_replace("..",$this->config['sy_weburl'],$vv['pic']);
									$list[$k]['replyname'] = $vv['nickname'];
									$list[$k]['replyctime'] = date("Y-m-d H:i:s",$vv['ctime']);
									$list[$k]['url'] = $this->furl(array("url"=>"c:profile,id:".$vv['uid']));
									$list[$k]['commentnum'] = $list[$k]['commentnum']+1;
								}
							}
						}
					}
				}
			}
			$this->yunset("list",$list);
			$this->yunset("mlist",$mlist);
		}else{
			$this->yunset("nofriend","1");
		}		
		if($_GET['delmid'])
		{
			$did = $this->obj->DB_delete_all("friend_state","`id`='".(int)$_GET['delmid']."' and `uid`='".$this->uid."'");
			if($did)
			{
				$this->obj->DB_delete_all("friend_reply","`nid`='".(int)$_GET['delmid']."'","");
				$this->obj->member_log("删除朋友圈动态");
				$this->layer_msg('删除成功！',9,0);
			}else{
				$this->layer_msg('删除失败！',8,0);
			}

		}
		$this->myfoot_action($_GET["id"]); 
		$this->seo("fri_profile"); 
		$this->yunset("ownid",$this->uid);
		$this->yunset("class","2");
		$this->yunset("nid",$_GET['id']);
		$this->yunset("u_name",$this->username);
		$row=$this->obj->DB_select_once("friend_info","`uid`='".(int)$_GET['id']."'");
		$this->yunset("rom",$row);
		$this->yuntpl(array('friend/default/profile'));
	}
	function reply_dynamic_action()
	{
		$data['pid']=(int)$_POST['pid'];
		$data['content']=$this->stringfilter($_POST['content']);
		$data['fid']=(int)$_POST['fid'];
		$data['f_name']=$this->stringfilter($_POST['f_name']);
		$data['status']=0;
		$data['ctime']=time();
		$data['uid']=$this->uid;
		$data['nid']=(int)$_POST['nid'];
		$data['u_name']=$this->username;
		$nid=$this->obj->insert_into("friend_message",$data);
		if($nid)
		{
			$this->obj->member_log("回复朋友圈留言");
			$this->get_integral_action($this->uid,"integral_friend_reply","朋友圈回复");
			echo '1||'.date("Y-m-d H:i:s");
		}else{
			echo '0||0';
		}
	}
	function myfriend_action()
	{
		$this->public_action();
		$this->himan_action();
		$this->leftinfo_action($this->uid);
		$where="`uid`='".$this->uid."' and `status`='1' ";
		if($_GET['keyword'])
		{
			$suid = $this->obj->DB_select_all("friend_info","`nickname` like '%".$_GET['keyword']."%'");
			if(is_array($suid))
			{
				foreach($suid as $k=>$v)
				{
					$fuid[] = $v['uid'];
				}
				$fuids = @implode(",",$fuid);
			}
			$where.= " and `nid` in (".$fuids.")";
		}
		$page_url['page'] = "{{page}}";
		$page_url['c'] = $_GET['c'];
		$page_url['keyword'] = $_GET['keyword'];
		$pageurl=$this->url("index","index",$page_url);
		$list = $this->get_page("friend",$where." ORDER BY `id` DESC",$pageurl,"12");
		if(is_array($list))
		{
			foreach($list as $val){
				$uids[]=$val['nid'];
			}
			$info = $this->obj->DB_select_all("friend_info","`uid` in (".@implode(',',$uids).")");
			foreach($list as $k=>$v)
			{
				foreach($info as $key=>$val)
				{
					if($v['nid']==$val['uid'])
					{
						$list[$k]['nickname'] = $val['nickname'];
						if($val['pic_big']){
							$list[$k]['pic_big'] = str_replace("..",$this->config['sy_weburl'],$val['pic_big']);
						}else{
							$list[$k]['pic_big'] =$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
						}
					}
				}
			}
		}
		$this->yunset("list",$list);
		if($_GET['delid'])
		{
			$this->obj->DB_delete_all("friend","`uid`='".$this->uid."' and `nid`='".(int)$_GET['delid']."'");
			$this->obj->DB_delete_all("friend","`nid`='".$this->uid."' and `uid`='".(int)$_GET['delid']."'");
			$this->obj->member_log("删除好友");
  			$this->layer_msg('好友删除成功！',9);
		}
		$this->myfoot_action((int)$_GET['id']);
		$this->seo("fri_myfriend");
		$row = $this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'");
		$this->yunset("rom",$row);
		$this->yunset("class","3");
		$this->yuntpl(array('friend/default/myfriend'));
	}
	function addfriend_action()
	{
		$this->public_action();
		$this->himan_action();
		$this->myfoot_action((int)$_GET['id']); 
		$this->leftinfo_action($this->uid);
		$frienduid = $this->frienduid_action();
		$where = "`uid`<>'".$this->uid."'";
		if($frienduid)
		{
			$where.=" and `uid` not in (".$frienduid.")";
		}
		$pagurl="c:".$_GET['c'].",page:{{page}}";
		if(trim($_GET['nm'])!='')
		{
			$where .= " and `nickname` like '%".trim($_GET['nm'])."%'";
			$pagurl.=",nm:".$_GET['nm'];
		}
		$pageurl=$this->furl(array("url"=>$pagurl));
		$list = $this->get_page("friend_info",$where."and iscert=1 ORDER BY `uid` DESC",$pageurl,"12");
		if(is_array($list))
		{
			foreach($list as $k=>$v)
			{
				if($v['pic_big'])
				{
					$list[$k]['pic_big']=str_replace("..",$this->config['sy_weburl'],$v['pic_big']);
				}else{
					$list[$k]['pic_big']=$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
				}
			}
		}
		$this->yunset("list",$list);
		$this->yunset("getnm",$_GET['nm']);
		$row = $this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'");
		$this->yunset("rom",$row);
		$this->yunset("class","3");
		$this->seo("fri_addfriend");
		$this->yuntpl(array('friend/default/addfriend'));
	}
	function applyfriend_action(){
		if($_GET['pass']){
			$where1['uid']=$_GET['pass'];
			$where1['nid']=$this->uid;
			$this->obj->update_once("friend",array("status"=>"1"),$where1);
			$friended = $this->obj->DB_select_once("friend","`uid`='".$this->uid."' and `nid`='".(int)$_GET['pass']."' and `status`='0'");
			if(empty($friended)){
				$data['uid']=$this->uid;
				$data['nid']=(int)$_GET['pass'];
				$data['status']=1;
				$data['uidtype']=(int)$_COOKIE['usertype'];
				$data['nidtype']=(int)$_GET['type'];
				$this->obj->insert_into("friend",$data);
			}else{
				$where['uid']=$this->uid;
				$where['nid']=$_GET['pass'];
				$this->obj->update_once("friend",array("status"=>"1"),$where);
			}
			$state_content = "与 <a href=\"".$this->config['sy_weburl']."/friend/index.php?c=profile&id=".$_GET['pass']."\" target=\"_blank\">".$this->stringfilter($_GET['name'])."</a> 成为好友。";
			$sql['uid']=$this->uid;
			$sql['content']=$state_content;
			$sql['ctime']=time();
			$nid=$this->obj->insert_into("friend_state",$sql);
			$this->unset_remind("friend".$_COOKIE['usertype'],$_COOKIE['usertype']);
			$this->obj->member_log("添加好友");
 			$nid?$this->layer_msg('添加好友成功！',9,0,$_SERVER['HTTP_REFERER']):$this->layer_msg('添加好友失败！',8,0,$_SERVER['HTTP_REFERER']);
		}else if($_GET['nopass']){
			$this->obj->DB_delete_all("friend","`uid`='".(int)$_GET['nopass']."' and `nid`='".$this->uid."'");
			$this->unset_remind("friend".$_COOKIE['usertype'],$_COOKIE['usertype']);
			$this->obj->member_log("拒绝好友");
 			$this->layer_msg('已拒绝该好友申请！',9,0,$_SERVER['HTTP_REFERER']);
		}else if($_GET['delid']){
			$this->obj->DB_delete_all("friend","`uid`='".$this->uid."' and `nid`='".(int)$_GET['delid']."'");
			$this->obj->DB_delete_all("friend","`nid`='".$this->uid."' and `uid`='".(int)$_GET['delid']."'");
			$this->obj->member_log("删除好友");
 			$this->layer_msg('好友删除成功！',9,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->public_action();
			$this->himan_action();
			$this->leftinfo_action($this->uid);
			$where="`nid`='".$this->uid."' and `status`='0'";
			if($_GET['keyword']){
				$suid = $this->obj->DB_select_all("friend_info","`nickname` like '%".$_GET['keyword']."%'");
				if(is_array($suid)){
					foreach($suid as $k=>$v){
						$fuid[] = $v['uid'];
					}
					$fuids = @implode(",",$fuid);
				}
				$where.= " and `nid` in (".$fuids.")";
			}
			$page_url["c"] = $_GET['c'];
			$page_url["keyword"] = $_GET['keyword'];
			$page_url["page"] = "{{page}}";
			$pageurl=$this->url("index","index",$page_url,'1');
			$list = $this->get_page("friend",$where."  ORDER BY `uid` DESC",$pageurl,"12");
			if(is_array($list)&&$list){
				foreach($list as $val){
					$uids[]=$val['uid'];
				}
				$info = $this->obj->DB_select_all("friend_info","`uid` in (".@implode(',',$uids).")","`uid`,`nickname`,`pic_big`");
				if(is_array($list)){
					foreach($list as $k=>$v){
						foreach($info as $key=>$val){
							if($v[uid]==$val['uid']){
								$list[$k]['nickname'] = $val['nickname'];
								$list[$k]['pic_big'] = str_replace("..",$this->config['sy_weburl'],$val['pic_big']);
							}
						}
					}
				}
				$this->yunset("list",$list);
			}
			$this->myfoot_action($_GET['id']);
			$this->seo("fri_applyfriend");
			$this->yunset("class","3");
			$this->yuntpl(array('friend/default/applyfriend'));
		}
	}
	function messagelist_action()
	{
		$this->public_action();
		$this->myfoot_action((int)$_GET['id']);
		$this->seo("fri_messagelist");
		$this->leftinfo_action((int)$_GET['id']);
		$this->yunset("ownid",$this->uid);
		$messageid = (int)$_GET['id'];
		$messageid = $messageid?$messageid:$this->uid;
		$page_url['c'] = $_GET['c'];
		$page_url['id'] = $_GET['id'];
		$page_url['page'] = "{{page}}";
		$pageurl=$this->url("index","index",$page_url,'1');
		$mlist = $this->get_page("friend_message","`nid`='".$messageid."' or `fid`='".$messageid."' order by ctime desc",$pageurl,"6");
		$info = $this->obj->DB_select_all("friend_info");
		if(is_array($mlist))
		{
			if(is_array($info))
			{
				foreach($mlist as $k=>$v)
				{
					$mlist[$k]['ctime'] = date("Y-m-d H:i",$v['ctime']);
					foreach($info as $key=>$val)
					{
						if($v['uid']==$val['uid'])
						{
							$mlist[$k]['nickname'] = $val['nickname'];
							$mlist[$k]['pic'] = str_replace("..",$this->config['sy_weburl'],$val['pic']);
						}
					}
				}
			}
		}
		$this->yunset("mlist",$mlist);
		if($_POST['submit'])
		{
			$data['content']=$_POST['content'];
			$data['ctime']=time();
			$data['nid']=$this->uid;
			$data['fid']=(int)$_POST['touid'];
			$data['uid']=$this->uid;
			$nid=$this->obj->insert_into("friend_message",$data);
			if($nid)
			{
				$this->obj->member_log("朋友圈发布留言");
				$this->get_integral_action($this->uid,"integral_friend_msg","朋友圈留言");
				$this->obj->ACT_layer_msg("留言成功！",9,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("留言失败！",8,$_SERVER['HTTP_REFERER']);
			}
 		}
		$row = $this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'");
		$this->yunset("rom",$row);
		$this->yunset("class","2");
		$this->yuntpl(array('friend/default/messagelist'));
	}
	function info_action()
	{
		$this->public_action();
		$this->himan_action();
		$this->myfoot_action((int)$_GET['id']);
		$this->seo("fri_info");
		$this->leftinfo_action($this->uid);
		$row = $this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'");
		if(!$row['uid'])
		{
			$this->obj->DB_insert_once("friend_info","`uid`='".$this->uid."'");
		}
		$this->yunset("rom",$row);
		if($_POST['submitBtn'])
		{
			$data['sex']=(int)$_POST['sex'];
			$data['nickname']=$_POST['nickname'];
			$data['birthday']=$_POST['birthday'];
			$data['description']=$_POST['description'];
			$nid=$this->obj->update_once("friend_info",$data,array("uid"=>$this->uid));
			if($nid)
			{
				$state_content = "我刚修改了个性签名<br>[".$_POST['description']."]。";
				$this->addstate($state_content);
				$this->obj->member_log("修改朋友圈基本信息");
				$this->obj->ACT_layer_msg("更新成功！",9,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("更新失败！",8,$_SERVER['HTTP_REFERER']);
			}
		}
		$this->yunset("class","4");
		$this->yuntpl(array('friend/default/info'));
	}
	function photo_action()
	{
		$this->public_action();
		$this->himan_action();
		$this->myfoot_action($_GET['id']);
		$this->seo("fri_photo");
		$this->leftinfo_action($this->uid);
		$row = $this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'");
		$row['pic'] = str_replace("../",$this->config['sy_weburl']."/",$row['pic']);
		$row['pic_big'] = str_replace("../",$this->config['sy_weburl']."/",$row['pic_big']);
		$this->yunset("rom",$row);
		if($_FILES['Filedata'])
		{
			$upload=$this->upload_pic("../upload/friend/",false,$this->config['user_pickb']);
			$picture=$upload->picture($_FILES['Filedata']);
			$picture = str_replace("../",$this->config['sy_weburl']."/",$picture);
			$pictures = @explode("/",$picture);
			echo $pic_ids = end($pictures);
			echo '<script type="text/javascript">window.parent.hideLoading();window.parent.buildAvatarEditor("'.$pic_ids.'","'.$picture.'","photo");</script>';
		}
		$this->yunset("class","4");
		$this->yuntpl(array('friend/default/photo'));
	}
	function save_avatar_action()
	{
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		if($_GET['type']!='big' && $_GET['type']!='small')
		{
			exit();
		}
		$type = $_GET['type'];
		$pic_id = trim($_GET['photoId']);

		$nameArr=@explode(".",$pic_id);
		$uptypes=array('jpg','png','jpeg','bmp','gif');
		if(count($nameArr)!=2)
		{
			exit();
		}
		if(!is_numeric($nameArr[0]))
		{
			exit();
		}
		if(!in_array(strtolower($nameArr[1]),$uptypes))
		{
			$d['statusText'] = iconv("gbk","utf-8",'文件类型不符!');
			$msg = json_encode($d);
			echo $msg;die;
		}
		$new_avatar_path = 'upload/friend/friend_'.$type.'/'.$pic_id;
		$len = file_put_contents(APP_PATH.$new_avatar_path,file_get_contents("php://input"));
		$avtar_img = imagecreatefromjpeg(APP_PATH.$new_avatar_path);
		imagejpeg($avtar_img,APP_PATH.$new_avatar_path,80);
		$d['data']['urls'][0] ="../".$new_avatar_path;
		$d['status'] = 1;
		$d['statusText'] = iconv("gbk","utf-8",'上传成功!');
		$row = $this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'");
		if($type=="small")
		{
			$this->obj->unlink_pic($row['pic']);
			$this->obj->update_once("friend_info",array("pic"=>"../".$new_avatar_path),array("uid"=>$this->uid));
			$state_content = "我刚更换了新头像。<br><img src=\"".$this->config['sy_weburl']."/".$new_avatar_path."\">";
			$this->addstate($state_content);
			$this->obj->member_log("更换了新头像");
		}else{
			$this->obj->unlink_pic($row['pic_big']);
			$this->obj->update_once("friend_info",array("pic_big"=>"../".$new_avatar_path),array("uid"=>$this->uid));
		}
		$msg = json_encode($d);
		echo $msg;
	}
	function del_action()
	{
		if($_GET['t']&&$_GET['id'])
		{
			if(in_array($_GET['t'],array("info","foot","message","reply","state")))
			{
				$table = "friend_".$_GET['t'];
				$this->obj->DB_delete_all($table,"`id`='".(int)$_GET['id']."' AND `uid`='".$this->uid."'");
				$this->obj->DB_delete_all($table,"`pid`='".(int)$_GET['id']."' AND `uid`='".$this->uid."'","");
				$this->obj->member_log("删除朋友圈信息");
 				$this->layer_msg('删除成功！',9,0,$_SERVER['HTTP_REFERER']);
			}else{
				$this->layer_msg('非法操作！',8,0,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function get_pic_action()
	{
		$pic=$this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'","pic");
		if($pic['pic'])
		{
			$pic=$pic['pic'];
		}else{
			$pic=$this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
		}
		echo  $pic;
	}
	function GetFace_action()
	{
		include_once(CONFIG_PATH."db.data.php");
		foreach($arr_data['imface'] as $key=>$va)
		{
			$data.='<li><a href="javascript:;" title="['.$key.']"><img src="'.$this->config['sy_weburl'].$arr_data['faceurl'].$va.'"></a></li>';
		}
		echo $data;die;
	}
	function addstate_action()
	{
		include_once(CONFIG_PATH."db.data.php");
		if($this->uid=='')
		{
			$this->obj->ACT_layer_msg("您还未登录或登录超时请重新登录！",4,"index.php");
		}

		if($_POST['content'])
		{
			$content=$_POST['content'];
			$content = str_replace("&amp;","&",html_entity_decode($content,ENT_QUOTES,"GB2312"));
			foreach($arr_data['imface'] as $k=>$v)
			{
				if(strstr($content,"[".$k."]"))
				{
					$content=str_replace("[".$k."]","<img src=\"".$this->config[sy_weburl].$arr_data['faceurl'].$v."\">",$content);
				}
			}
			$data['content']=$content;
			$data['uid']=$this->uid;
			$data['ctime']=time();
			if($_FILES['msg_img']['name'])
			{
				$upload=$this->upload_pic("../upload/friend/");
				$pictures=$upload->picture($_FILES['msg_img']);
				$data['msg_pic']=$pictures;
			}
			$cid = $this->obj->insert_into("friend_state",$data);
			if($cid)
			{
				$this->obj->member_log("发表朋友圈动态");
				$this->obj->ACT_layer_msg("发表成功！",9,"index.php");
			}else{
				$this->obj->ACT_layer_msg("发表成功！",8,"index.php");
			}
		}
	}
}
?>