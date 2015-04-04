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
		include(PLUS_PATH."config.php");
		$this->yunset("config",$config);
		$now_url=@explode("/",$_SERVER['REQUEST_URI']);
		$now_url=$now_url[count($now_url)-1];
		$this->yunset("uid",$this->uid);
		$this->yunset("now_url",$now_url);

	}
	function wenda_tpl($tpl)
	{
		$this->yuntpl(array('ask/'.$tpl));
	}
	function index_action(){
		$this->public_action();
		$this->yunset("c","index");
		$this->yunset("order",$_GET['order']);
		$my_attention=$this->obj->DB_select_once("attention","`uid`='".$this->uid."' and `type`='1'","ids");
		$my_atten=@explode(',',rtrim($my_attention['ids'],","));
		$this->seo('ask_index');
		$this->yunset("my_atten",$my_atten);
		$this->wenda_tpl('index');
	}
	function search_action()
	{
		$this->public_action();
		$where='1';
		$urlarr["c"]=$_GET['c'];
		if(trim($_GET['search_title']))
		{
			$where.=" and `title` like '%".$_GET['search_title']."%'";
			$urlarr["search_title"]=trim($_GET['search_title']);
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$search=$this->get_page("question",$where." order by `add_time` desc",$pageurl,"10");
		if($search[0])
		{
			foreach($search as $val)
			{
				$uid[]=$val['uid'];
			}
			$uids=@implode(',',$uid);
			$my_atn=$this->obj->DB_select_all("atn","`uid`='".$this->uid."' and `sc_uid` in (".$uids.")","`sc_uid`");
			$info=$this->obj->DB_select_all("friend_info","`uid` in (".$uids.")","`uid`,`pic`");
			foreach($search as $k=>$v)
			{
				foreach($info as $val)
				{
					if($v['uid']==$val['uid'])
					{
						if($val['pic'])
						{
							$search[$k]['pic']=$val['pic'];
						}else{
							$search[$k]['pic']=$this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
						}
					}
				}
				if($v['uid']==$this->uid)
				{
					$search[$k]['is_atn']='2';
				}
				foreach($my_atn as $val)
				{
					if($v['uid']==$val['sc_uid']){
						$search[$k]['is_atn']='1';
					}
				}
			}
			$this->yunset("search",$search);
		}
		$this->yunset("search_title",$_GET['search_title']);
		$this->yunset("c","index");
		$this->seo('ask_search');
		$this->wenda_tpl('search');
	}
	function get_user_info_action()
	{
		$uid=(int)$_POST['uid'];
		$info=$this->obj->DB_select_once("friend_info","`uid`='".$uid."'","`uid`,`nickname`,`pic`,`description`,`usertype`");
		if($info['usertype']=='1')
		{
			$atn_num=$this->obj->DB_select_once("resume","`uid`='".$uid."'",'ant_num');
		}else if($info['usertype']=='2'){
			$atn_num=$this->obj->DB_select_once("company","`uid`='".$uid."'",'ant_num');
		}else if($info['usertype']=='3'){
			$atn_num=$this->obj->DB_select_once("lt_info","`uid`='".$uid."'",'ant_num');
		}
		if($atn_num['ant_num']=="" ||$atn_num['ant_num']<0)
		{
			$ant_num='0';
		}else{
			$ant_num=$atn_num['ant_num'];
		}
		$answer_num=$this->obj->DB_select_num("answer","`uid`='".$uid."'");
		$result=array();
		if($info['pic'])
		{
			$result['pic']=str_replace("..",$this->config['sy_weburl'],$info['pic']);
		}else{
			$result['pic']=$this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
		}
		$result['description']=urlencode($info['description']);
		$result['nickname']=urlencode($info['nickname']);
		$result['answer_num']=$answer_num;
		$result['sy_friend_icon']=$this->config['sy_friend_icon'];
		$result['ant_num']=$ant_num;
		$result_json = json_encode($result);
		echo urldecode($result_json);die;
	}
	function content_action()
	{
		$question=$this->obj->DB_select_once("question","`id`='".(int)$_GET['id']."'");
		if(empty($question))
		{
 			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"问答不存在或被删除！");
		}
		$this->public_action();
		$answer=$this->obj->DB_select_alls("answer","friend_info","a.`qid`='".(int)$_GET['id']."' and a.`uid`=b.`uid` order by a.`support` desc,a.`add_time` desc","a.`uid`,a.`id`,a.`comment`,a.`support`,a.`add_time`,a.`content`,b.`nickname`,b.`pic`,b.`description`");
		$atn=$this->obj->DB_select_all("atn","`uid`='".$this->uid."'","`sc_uid`");
		$show=$this->obj->DB_select_alls("question","friend_info","a.`id`='".(int)$_GET['id']."' and a.`uid`=b.`uid`","a.*,b.`nickname`,b.`description`,b.`pic`");
		$show[0]['pic']=str_replace("..",$this->config['sy_weburl'],$show[0]['pic']);
		$atten_ask=$this->obj->DB_select_once("attention","`uid`='".$this->uid."' and `type`='1'","ids");
		if($show[0]['uid']==$this->uid)
		{
			$show[0]['is_atn']='2';
		}
		if(!empty($answer))
		{
			foreach($answer as $key=>$val)
			{
				
				if($val['uid']==$this->uid)
				{
					$answer[$key]['is_atn']='2';
				}
				if(!empty($answer))
				{
					
					foreach($atn as $a_v)
					{
						if($a_v['sc_uid']==$val['uid']){
							$answer[$key]['is_atn']='1';
						}
					}
				}
				if($val['pic'])
				{
					$answer[$key]['pic']=str_replace("..",$this->config['sy_weburl'],$val['pic']);
				}else{
					$answer[$key]['pic']=$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
				}
			}
		}
		if($show[0]['uid']==$this->uid)
		{
			$show[0]['is_atn']='2';
		}else if(!empty($atn)){
			
			foreach($atn as $val)
			{
				if($show[0]['uid']==$val['sc_uid'])
				{
					$show[0]['is_atn']='1';
				}
			}
		}
		
		if(!empty($atten_ask))
		{
			$ids=explode(',',rtrim($atten_ask['ids'],','));
			if(in_array($show[0]['id'],$ids)){
				$show[0]['ask_is_atn']='1';
			}
		}
		$reason=$this->obj->DB_select_all("reason","1");
		$this->obj->DB_update_all("question","`visit`=`visit`+1","`id`='".(int)$_GET['id']."'");
		if($this->uid==''||$_COOKIE['username']=='')
		{
			$this->yunset("no_login",'1');
		}
		if($show[0]['pic']=='')
		{
			$show[0]['pic']='../'.$this->config['sy_friend_icon'];
		}
 		$this->yunset("reason",$reason);
		$this->yunset("show",$show[0]);
		$this->yunset("uid",$this->uid);
		$this->yunset("answer",$answer);
		$this->yunset("title",$show[0]['title'].' - '.$this->config['sy_webname']);
		$this->yunset("c","index");
		$this->wenda_tpl('content');
	}
	function get_comment_action()
	{
		$comment=$this->obj->DB_select_alls("answer_review","friend_info","a.`aid`='".(int)$_POST['aid']."' and a.`uid`=b.`uid`   order by a.`add_time` asc","a.`aid`,a.`qid`,a.`id`,a.`content`,a.`add_time`,b.`nickname`,b.`pic`,b.`uid`");
		if(is_array($comment))
		{
			foreach($comment as $k=>$v)
			{
				if($v['pic']!="")
				{
					$comment[$k]['pic']=str_replace("..",$this->config['sy_weburl'],$v['pic']);
				}else{
					$comment[$k]['pic']=$this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
				}
				$comment[$k]['url']=$this->furl(array("url"=>"c:profile,id:".$v['id']));
				$comment[$k]['nickname']=urlencode($v['nickname']);
				$comment[$k]['content']=urlencode($v['content']);
				$comment[$k]['date']=date("Y-m-d H:i:s",$v['add_time']);
				if($v['uid']==$this->uid)
				{
					$comment[$k]['myself']='1';
				}
			}
			$comment_json = json_encode($comment);
			echo urldecode($comment_json);
		}
	}
	function q_repost_action()
	{
		$this->is_login();
		$is_set=$this->obj->DB_select_once("report","`type`='1' and `r_type`='".(int)$_POST['type']."' and `eid`='".(int)$_POST['eid']."' and `r_reason`='".$_POST['reason']."'","`p_uid`");
		if(empty($is_set))
		{
			if($_POST['type']=='1')
			{
				$uid=$this->obj->DB_select_alls("question","friend_info","a.`id`='".(int)$_POST['eid']."' and a.`uid`=b.`uid`","b.`uid`,b.`nickname`");
				$content="举报问答问题";
			}else if($_POST['type']=='2'){
				$uid=$this->obj->DB_select_alls("answer","friend_info","a.`id`='".(int)$_POST['eid']."' and a.`uid`=b.`uid`" ,"b.`uid`,b.`nickname`");
				$content="举报问答回答";
			}else if($_POST['type']=='3'){
				$uid=$this->obj->DB_select_alls("answer_review","friend_info","a.`id`='".(int)$_POST['eid']."' and a.`uid`=b.`uid`","b.`uid`,b.`nickname`");
				$content="举报问答评论";
			}
			$my_nickname=$this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'","`nickname`");
			$data['p_uid']=$this->uid;
			$data['c_uid']=$uid[0]['uid'];
			$data['eid']=(int)$_POST['eid'];
			$data['usertype']=$_COOKIE['usertype'];
			$data['inputtime']=time();
			$data['username']=$my_nickname['nickname'];
			$data['r_name']=$uid[0]['nickname'];
			$data['r_reason']=$_POST['reason'];
			$data['type']=1;
			$data['r_type']=(int)$_POST['type'];
			$new_id=$this->obj->insert_into("report",$data);
			if($new_id)
			{
				$this->obj->member_log($content);
				echo '1';
			}else{
				echo '0';
			}
		}else{
			if($is_set['p_uid']==$this->uid)
			{
				echo '2';
			}else{
				echo '3';
			}
		}
	}
	function for_comment_action()
	{
		$this->is_login();
		$data['aid']=(int)$_POST['aid'];
		$data['qid']=(int)$_POST['qid'];
		$data['content']=str_replace(array("&amp;","background-color:#ffffff","background-color:#fff","white-space:nowrap;"),array("&",'background-color:','background-color:','white-space:'),html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
		$data['content'] = $this->stringfilter($data['content']);

		$data['uid']=$this->uid;
		$data['add_time']=time();
		$new_id=$this->obj->insert_into("answer_review",$data);
		if($new_id)
		{
			$this->obj->member_log("评论问答");
			$this->get_integral_action($this->uid,"integral_answerpl","评论问答");
			$num=$this->obj->DB_select_num("answer_review","`aid`='".(int)$_POST['aid']."'");
			$this->obj->update_once("answer",array("comment"=>$num),array("id"=>(int)$_POST['aid']));
			echo '1';
		}else{
			echo '0';
		}
	}
	function for_support_action()
	{
		if($_SESSION['support_'.$_POST['aid']]=='1')
		{
			echo '2';
		}else{
			$id=$this->obj->DB_update_all("answer","`support`=`support`+1","`id`='".(int)$_POST['aid']."'");
			if($id)
			{
				$this->obj->member_log("给问题回答点赞");
				$_SESSION['support_'.$_POST['aid']]='1';
				echo '1';
			}else{
				echo '0';
			}
		}
	}
	function answer_action(){
		$gourl= $this->aurl(array("url"=>"c:content,id:".$_GET['id']));
		if($_POST['content']){
			$q_title=$this->obj->DB_select_once("question","`id`='".(int)$_GET['id']."'","`uid`,`title`,`content`");
			if($q_title['uid']==$this->uid)
			{
				$content = str_replace("&amp;","&",html_entity_decode("<br/>追加内容：<br/>".$_POST['content'],ENT_QUOTES,"GB2312"));
				$content=$q_title['content'].$content;
				$id=$this->obj->update_once("question",array("content"=>$content,'lastupdate'=>time()),array("id"=>(int)$_GET['id']));
				if($id)
				{
					$this->obj->member_log("问题《".$q_title['title']."》追加提问");
					$this->obj->ACT_layer_msg("提问追加成功！",9,$_SERVER['HTTP_REFERER']);
				}else{
					$this->obj->ACT_layer_msg( "提问追加失败！",8,$_SERVER['HTTP_REFERER']);
				}
			}else{
				$data['qid']=(int)$_GET['id'];
				$data['content']=str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
				$data['uid']=$this->uid;
				$data['comment']=0;
				$data['support']=0;
				$data['oppose']=0;
				$data['add_time']=time();
				$id=$this->obj->insert_into("answer",$data);
				if($id)
				{
					$this->obj->DB_update_all("question","`answer_num`=`answer_num`+1,`lastupdate`='".time()."'","id='".(int)$_GET['id']."'");
					$state_content = "回答了问答《<a href=\"".$gourl."\" target=\"_blank\">".$q_title['title']."</a>》。";
					$this->addstate($state_content);
					$this->obj->member_log("回答了问答《".$q_title['title']."》");
					$this->get_integral_action($this->uid,"integral_answer","回答问题");
					$this->obj->ACT_layer_msg( "回答成功！", 9,$_SERVER['HTTP_REFERER']);
				}else{
					$this->obj->ACT_layer_msg( "回答失败！", 8);
				}
			}
		}else{
			$this->obj->ACT_layer_msg( "内容不能为空！", 2);
		}
	}
	function addquestion_action()
	{
		$this->public_action();
		$class=$this->obj->DB_select_all("q_class","`pid`='0' order by `sort` desc");
		$this->yunset("c","index");
		$this->yunset("uid",$this->uid);
		$this->yunset("class",$class);
		$this->seo('ask_add_question');
		$this->wenda_tpl('add_question');
	}
	function q_class_action()
	{
		$class=$this->obj->DB_select_all("q_class","`pid`='".(int)$_POST['pid']."' order by `sort` desc");
		$html .="<select name='cid' id='cid'>";
		foreach($class as $v)
		{
			$html .="<option value=\"".$v['id']."\">".$v['name']."</option>";
		}
		$html .="</select>";
		echo $html;
	}
	function save_action()
	{
		if($this->uid=='')
		{
			$this->obj->ACT_layer_msg( "请先登录！", 8);
		}
		if(trim($_POST['title'])=="")
		{
			$this->obj->ACT_layer_msg( "标题不能为空！", 8);
		}
		$data['title']=$_POST['title'];
		$data['cid']=(int)$_POST['cid'];
		$data['content']=str_replace("&amp;","&",html_entity_decode($_POST['content'],ENT_QUOTES,"GB2312"));
		$data['uid']=$this->uid;
		$data['add_time']=time();
		$n_ids=$this->obj->insert_into("question",$data);
 		if($n_ids)
 		{
			$nickname=$this->obj->DB_select_once("firend_info","`uid`='".$this->uid."'","`nickname`");
			$gourl= $this->aurl(array("url"=>"c:content,id:".$n_ids));
			$sql['uid']=$this->uid;
			$sql['content']="发布了问答《<a href=\"".$gourl."\" target=\"_blank\">".$_POST['title']."</a>》。";
			$sql['ctime']=time();
			$this->obj->insert_into("friend_state",$sql);
			$this->obj->member_log("发布了问答《".$_POST['title']."》");
			$this->get_integral_action($this->uid,"integral_question","发布问题");
			$gourl= $this->aurl(array("url"=>"c:index"));
			$this->obj->ACT_layer_msg( "提问成功！",9,$gourl);
		}else{
			$this->obj->ACT_layer_msg( "提问失败！", 8);
		}
	}
	function myquestion_action()
	{
		if($this->uid=='')
		{
			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"请先登录！");
		}
		$this->public_action();
		$this->yunset("uid",$this->uid);
		$this->yunset("recom",$_GET['recom']);
		$this->yunset("c","index");
		$this->seo("ask_my_question");
		$this->wenda_tpl('my_question');
	}
	function myanswer_action()
	{
		if($this->uid=='')
		{
			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"请先登录！");
		}
		$this->public_action();
		$where ="`uid`='".$this->uid."' order by `add_time` desc";
		$pageurl=$this->aurl(array("url"=>"c:".$_GET['c'].",page:{{page}}"));
		$my_answer=$this->get_page("answer",$where,$pageurl,"10");
		if(!empty($my_answer))
		{
			foreach($my_answer as $v)
			{
				$qid[]=$v['qid'];
			}
			$qids=@implode(',',$qid);
			$question=$this->obj->DB_select_all("question","`id` in (".$qids.")","`id` as `qid`,`title`,`answer_num`");
			$my_pic=$this->obj->DB_select_once("friend_info","`uid`='".$this->uid."'","`pic`");
			if($my_pic['pic']=='')
			{
				$my_pic['pic']=$this->config['sy_weburl']."/".$this->config['sy_friend_icon'];
			}
			foreach($my_answer as $key=>$val)
			{
				foreach($question as $k=>$v)
				{
					if($val['qid']==$v['qid'])
					{
						$my_answer[$key]['q_title']=$v['title'];
						$my_answer[$key]['qid']=$v['qid'];
						$my_answer[$key]['answer_num']=$v['answer_num'];
					}
				}
				$my_answer[$key]['pic']=$my_pic['pic'];
			}
			$reason=$this->obj->DB_select_all("reason","1");
		}
		$this->yunset("reason",$reason);
 		$this->yunset("my_answer",$my_answer);
		$this->yunset("c","index");
		$this->seo("ask_my_answer");
		$this->wenda_tpl('my_answer');
 	}
	function topic_action(){
		$this->public_action();
		$class=$this->obj->DB_select_all("q_class","`pid`='0' order by `sort` limit 10");
		if($_GET['pid']){
			$where="`pid`='".$_GET['pid']."'";
			$pid=$_GET['pid'];
		}else{
			$where="`pid`='".$class[0]['id']."'";
			$pid=$class[0]['id'];
		}
		$urlarr['c']='topic';
		$urlarr['pid']=$pid;
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$q_class=$this->get_page("q_class",$where." order by `sort`",$pageurl,"10");

		$this->yunset("pid",$pid);
		$this->yunset("q_class",$q_class);
		$this->yunset("class",$class);
		$this->yunset("c","topic");
		$this->seo("ask_topic");
		$this->wenda_tpl('topic');
	}
	function getclass_action()
	{
		$this->public_action();
		$q_class=$this->obj->DB_select_once("q_class","`id`='".(int)$_GET['cid']."'");
		$this->yunset("q_class",$q_class);
		$this->yunset("cid",$_GET['cid']);
		$this->yunset("recom",$_GET['recom']);
		$this->yunset("c","topic");
		$this->seo('ask_topic');
		$this->wenda_tpl('get_class');
	}
	function hotweek_action()
	{
		$this->public_action();
		$time=strtotime("-1 week");
		$pageurl=$this->aurl(array("url"=>"c:".$_GET['c'].",page:{{page}}"));
		$hot_week=$this->get_page("question","`add_time`>'".$time."' order by `answer_num` desc ",$pageurl,"10");
		foreach ($hot_week as $v)
		{
			$uid[]=$v['uid'];
		}
		$uids=@implode(',',$uid);
		$f_info=$this->obj->DB_select_all("friend_info","`uid` in (".$uids.")","`uid`,`pic`,`nickname`");
		$atn=$this->obj->DB_select_all("atn","`uid`='".$this->uid."'","`sc_uid`");
		foreach ($hot_week as $key=>$val)
		{
			foreach($f_info as $v)
			{
				if($val['uid']==$v['uid'])
				{
					$hot_week[$key]['pic']=str_replace("..",$this->config['sy_weburl'],$v['pic']);
					$hot_week[$key]['nickname']=$v['nickname'];
				}
			}
			if($val['uid']==$this->uid)
			{
				$hot_week[$key]['is_atn']='2';
			}
			foreach($atn as $v)
			{
				if($v['sc_uid']==$val['uid'])
				{
					$hot_week[$key]['is_atn']='1';
				}
			}
		}
		$this->yunset("c","topic_cont");
		$this->yunset("hot_week",$hot_week);
		$this->seo('ask_hot_week');
		$this->wenda_tpl('hot_week');
	}
	function attenquestion_action()
	{
		if($this->uid=='')
		{
			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'],"请先登录！");
		}
		$this->public_action();
		$ids=$this->obj->DB_select_once("attention","`uid`='".$this->uid."' and `type`='1'","`ids`");
		$ids=rtrim($ids['ids'],',');
		$pageurl=$this->aurl(array("url"=>"c:".$_GET['c'].",page:{{page}}"));
		$question=$this->get_page("question","`id` in (".$ids.")  order by `add_time` desc",$pageurl,"10");
		if(!empty($question))
		{
			foreach($question as $k=>$v)
			{
				$uid[]=$v['uid'];
			}
			$uids=implode(',',$uid);
			$friend_info=$this->obj->DB_select_all("friend_info","`uid` in (".$uids.")","`uid`,`pic`");
			foreach($question as $key=>$val)
			{
				foreach($friend_info as $k=>$v)
				{
					if($val['uid']==$v['uid'])
					{
						if($val['uid']==$v['uid'])
						{
							if($v['pic'])
							{
								$question[$key]['pic']=$v['pic'];
							}else{
								$question[$key]['pic']=$this->config['sy_weburl'].'/'.$this->config['sy_friend_icon'];
							}
						}
					}
				}
			}
		}
		$this->yunset("question",$question);
		$this->yunset("C",'index');
		$this->seo('atten_question');
		$this->wenda_tpl('atten_question');
	}
	function attention_action()
	{
		$this->is_login();
		$_POST['id'] = (int)$_POST['id'];
		$is_set=$this->obj->DB_select_once("attention","`uid`='".$this->uid."' and `type`='".(int)$_POST['type']."'");
		if($_POST['type']=='1')
		{
			$info=$this->obj->DB_select_once("question","`id`='".(int)$_POST['id']."'","`id`,`title`,`uid`");
			$gourl= $this->aurl(array("url"=>"c:content,id:".$info['id']));
			$content="关注了<a href=\"".$gourl."\" target=\"_blank\">《".$info['title']."》</a>。";
			$n_contemt="取消了对<a href=\"".$gourl."\" target=\"_blank\">《".$info['title']."》</a>的关注。";
			$log="关注了《".$info['title']."》";
			$n_log="取消了对《".$info['title']."》";
		}else{
			$info=$this->obj->DB_select_once("q_class","`id`='".$_POST['id']."'","`id`,`name`");
			$gourl= $this->aurl(array("url"=>"c:getclass,id:".$info['id']));
			$content="关注了<a href=\"".$gourl."\" target=\"_blank\">".$info['name']."</a>。";
			$n_contemt="取消了<a href=\"".$gourl."\" target=\"_blank\">".$info['name']."</a>的关注。";
			$log="关注了".$info['name'];
			$n_log="取消了对".$info['name']."</a>的关注。";
		}
		if($info['uid']==$this->uid){
			echo '4';
		}else if(is_array($is_set)){
			$ids=@explode(',',$is_set['ids']);
			if(in_array($_POST['id'],$ids))
			{
				if($_POST['type']=='1')
				{
					echo '2';
				}else{
					foreach($ids as $k=>$v )
					{
						if($v!=$_POST['id'])
						{
							$i_ids[]=$v;
						}
					}
					if($i_ids)
					{
						$n_id=$this->obj->update_once("attention",array("ids"=>@implode(',',$i_ids)),array("id"=>$is_set['id']));
					}else{
						$n_id=$this->obj->DB_delete_all("attention","`id`='".$is_set['id']."'");
					}
					if($n_id)
					{
						$data['uid']=$this->uid;
						$data['content']=$n_contemt;
						$data['ctime']=time();
						$this->obj->insert_into("friend_state",$data);
						$this->obj->member_log($n_log);
						echo '3';
					}
				}
			}else{
				$i_ids=$is_set['ids'].','.$_POST['id'];
				$n_id=$this->obj->update_once("attention",array("ids"=>$i_ids),array("id"=>$is_set['id']));
				if($n_id)
				{
					$data['uid']=$this->uid;
					$data['content']=$content;
					$data['ctime']=time();
					$this->obj->insert_into("friend_state",$data);
					echo '1';
				}else{
					echo '0';
				}
			}
		}else{
			$data['uid']=$this->uid;
			$data['type']=(int)$_POST['type'];
			$data['ids']=(int)$_POST['id'];
			$n_id=$this->obj->insert_into("attention",$data);
			if($n_id)
			{
				$sql['uid']=$this->uid;
				$sql['content']=$content;
				$sql['ctime']=time();
				$this->obj->insert_into("friend_state",$sql);
				$this->obj->member_log($log);
				echo '1';
			}else{
				echo '0';
			}
		}
	}
	function del_attention_action(){
		$ids=$this->obj->DB_select_once("attention","`uid`='".$this->uid."' and `type`='".(int)$_POST['type']."'");
		$nids=@explode(',',$ids['ids']);
		foreach($nids as $k=>$v)
		{
			if($_POST['id']!=$v)
			{
				$upid[]=$v;
			}
		}
		if($upid){
			$nid=$this->obj->update_once("attention",array("ids"=>@implode(',',$upid)),array("id"=>$ids['id']));
			if($nid){
				$this->obj->member_log("删除关注的问题");
				echo '1';die;
			}else{
				echo '0';die;
			}
		}else{
			$this->obj->DB_delete_all("attention","`id`='".$ids['id']."'");
			$this->obj->member_log("删除关注的问题");
			echo '1';die;
		}
	}
	function dynamic_action()
	{//动态
		$this->public_action();
		include(LIB_PATH."page3.class.php");
		$limit=10;
		$page=$_GET["page"]<1?1:$_GET["page"];
		$ststrsql=($page-1)*$limit;
		$page_url["c"]=$_GET['c'];
		$page_url["page"]="{{page}}";
		$pageurl=$this->url("index","index",$page_url,'1');
		$atn=$this->obj->DB_select_all("atn","`uid`='".$this->uid."'","`sc_uid`");
		if(is_array($atn))
		{
			foreach($atn as $v)
			{
				$uids[]=$v['sc_uid'];
			}
		}
		$uids=@implode(',',$uids);
		$count=$this->obj->DB_select_alls("friend_info","friend_state","a.`uid` in(".$uids.") and a.`uid`=b.`uid`","a.`uid`,a.`nickname`,a.`pic`,b.`content`,b.`ctime`");
		$num=count($count);
		$page = new page($page,$limit,$num,$pageurl);
		$pagenav=$page->numPage();
		$dynamic=$this->obj->DB_select_alls("friend_info","friend_state","a.`uid` in(".$uids.") and a.`uid`=b.`uid` order by b.`ctime` desc limit $ststrsql,$limit","a.`uid`,a.`nickname`,a.`pic`,b.`content`,b.`ctime`");
		if(is_array($dynamic))
		{
			foreach($dynamic as $k=>$v)
			{
				if(in_array($v['uid'], $uids))
				{
					$dynamic[$k]['is_atn']='1';
				}
			}
		}
		$this->yunset("pagenav",$pagenav);
		$this->yunset("C",'index');
		$this->yunset("dynamic",$dynamic);
		$this->seo("seo_dynamic");
		$this->wenda_tpl('dynamic');
	}
	function get_q_class_action()
	{
		$class=$this->obj->DB_select_all("q_class","`name` like '%".$_POST['name']."%' order by id");
		$html .="<ul>";
		foreach($class as $v)
		{
			$html .="<li onclick='get_class(".$v['id'].");' style='cursor:pointer' id='".$v['id']."'>".$v['name']."</li>";
		}
		$html .="</ul>";
		echo $html;
	}
	function  is_login()
	{
		if($this->uid==""||$_COOKIE['username']=='')
		{
			echo 'no_login';die;
		}
	}
}
?>