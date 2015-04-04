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
class redeem_controller extends common
{	
	function public_action()
	{
		if($this->config['sy_redeem_web']=="2")
		{
			header("location:".$this->url("index","error"));
		}
	}
	function index_action(){ 
		$this->public_action();
		$row=$this->obj->DB_select_all("reward_class","1 order by id asc","id,name");
		$this->yunset("row",$row);
		$rows=$this->obj->DB_select_all("reward","1","id,nid,pic,name,integral,stock");
		$this->yunset("rows",$rows);
		$lipin=$this->obj->DB_select_alls("change","reward","a.gid=b.id order by a.id desc limit 10","a.uid,a.username,a.name,a.ctime,a.integral,b.pic,b.id");
		if(is_array($lipin)){
        	foreach($lipin as $k=>$v)
        	{
				$time=time()-$v['ctime'];
				if($time>86400 && $time<604800){
					$lipin[$k]['time'] = ceil($time/86400)."天前";
				}elseif($time>3600 && $time<86400){
					$lipin[$k]['time'] = ceil($time/3600)."小时前";
				}elseif($time>60 && $time<3600){
					$lipin[$k]['time'] = ceil($time/60)."分钟前";
				}elseif($time<60){
					$lipin[$k]['time'] = "刚刚";
				}else{
					$lipin[$k]['time'] = date("Y-m-d",$v['ctime']);
				}
        		$uid[]=$v['uid'];
        	}
		    include APP_PATH."/plus/city.cache.php";
			$city1=$this->obj->DB_select_all("resume_expect","`uid` in (".@implode(",",$uid).")","uid,provinceid,cityid");
			$city2=$this->obj->DB_select_all("company","`uid` in (".@implode(",",$uid).")","uid,provinceid,cityid");
			$city3=$this->obj->DB_select_all("lt_info","`uid` in (".@implode(",",$uid).")","uid,provinceid,cityid");
			$city4=$this->obj->DB_select_all("px_train","`uid` in (".@implode(",",$uid).")","uid,provinceid,cityid");
			$city = array_merge($city1,$city2,$city3,$city4);
        	foreach($lipin as $k=>$v)
        	{
        		foreach($city as $val)
        		{
        			 if($v['uid']==$val['uid'])
                     {
        				$lipin[$k]['provinceid']=$city_name[$val['provinceid']];
						$lipin[$k]['cityid']=$city_name[$val['cityid']];
        		     }
        		}
        	}
        	$this->yunset("lipin",$lipin);
        }

		$paihang=$this->obj->DB_select_all("reward","`status`='1' order by `num` desc limit 3","name,id,pic");
		$this->yunset("paihang",$paihang);
		$this->seo("redeem");
		$this->yun_tpl(array('index'));
	}
	function list_action(){
		$this->public_action();
		$where="`nid`='".$_GET['id']."'";
		$where.=" order by `id` desc";
		$urlarr['c']="list";
		$urlarr["id"]=$_GET['id'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$rows=$this->get_page("reward",$where,$pageurl,13);
		$this->yunset("rows",$rows);
		$row=$this->obj->DB_select_all("reward_class");
		$this->yunset("row",$row);
		$this->seo("redeem");
		$this->yun_tpl(array('list'));
	}
	function show_action(){
		$this->public_action();
	    $where="`gid`='".$_GET['id']."'";
		
		$where.=" order by `id` desc";	
		$urlarr['c']="show";
		$urlarr["id"]=$_GET['id'];
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$jilu=$this->get_page("change",$where,$pageurl,13);
		$this->yunset("jilu",$jilu);

		$row=$this->obj->DB_select_once("reward","`id`='".$_GET['id']."'");
		$this->yunset("row",$row); 
		$remen=$this->obj->DB_select_all("reward","`rec`=1 order by id desc limit 5 ");
		$this->yunset("remen",$remen);

		$this->seo("redeem");
		$this->yun_tpl(array('show'));
	}
	function dh_action(){
		$this->public_action();
		if(!$this->uid && !$this->username)
	    {
		     $this->obj->ACT_layer_msg("您还没有登录，请先登录！",8,$_SERVER['HTTP_REFERER']);
		}

		if($_POST['submit']){
			if(!$_POST['password']){
				$this->obj->ACT_layer_msg("密码不能为空！",8,$_SERVER['HTTP_REFERER']);
			}
			if(!$_POST['linkman'] || !$_POST['linktel'] ){
				$this->obj->ACT_layer_msg("联系人或联系电话不能为空！",8,$_SERVER['HTTP_REFERER']);
			}
			$info=$this->obj->DB_select_once("member","`uid`='".$this->uid."'","`password`,`salt`");
			$passwrod=md5(md5($_POST['password']).$info['salt']);
			if($info['password']!=$passwrod){
				$this->obj->ACT_layer_msg("密码不正确！",8,$_SERVER['HTTP_REFERER']);
			}
			if(!$this->uid && !$this->username){
				 $this->obj->ACT_layer_msg("您还没有登录，请先登录！",8,$_SERVER['HTTP_REFERER']);
			}else{
				$_POST['num'] = (int)$_POST['num'];
				if($_POST['num']<1){
					$this->obj->ACT_layer_msg("请填写正确的数量！",8,$_SERVER['HTTP_REFERER']);
				}else{
					if($_COOKIE['usertype']=="1"){
						$table="member_statis";
					}elseif($_COOKIE['usertype']=="2"){
						$table="company_statis";
					}elseif($_COOKIE['usertype']=="3"){
						$table="lt_statis";
					}elseif($_COOKIE['usertype']=="4"){
						$table="px_train_statis";
					}
					$info=$this->obj->DB_select_once($table,"`uid`='".$this->uid."'","integral");
					$gift=$this->obj->DB_select_once("reward","`id`='".(int)$_GET['id']."'");
					if($_POST['num']>$gift['stock']){
						$this->obj->ACT_layer_msg("已超出库存数量！",8,$_SERVER['HTTP_REFERER']);
					}else{
						if($gift['restriction']!="0"&&$_POST['num']>$gift['restriction']){
							$this->obj->ACT_layer_msg("已超出限购数量！",8,$_SERVER['HTTP_REFERER']);
						}else{
							$integral=$gift['integral']*$_POST['num'];
							if($info['integral']<$integral){
								$this->obj->ACT_layer_msg("您的积分不足！",8,$_SERVER['HTTP_REFERER']);
							}else{
								$this->obj->company_invtal($this->uid,$integral,false,"积分兑换",true,2,'integral',24);
								$value.="`uid`='".$this->uid."',";
								$value.="`username`='".$this->username."',";
								$value.="`usertype`='".$_COOKIE['usertype']."',";
								$value.="`name`='".$gift['name']."',";
								$value.="`gid`='".$gift['id']."',";
								$value.="`linkman`='".$_POST['linkman']."',";
								$value.="`linktel`='".$_POST['linktel']."',";
								$value.="`body`='".$_POST['body']."',";
								$value.="`integral`='".$integral."',";
								$value.="`num`='".$_POST['num']."',";
								$value.="`ctime`='".time()."'";
								$this->obj->DB_insert_once("change",$value);
								$this->obj->DB_update_all("reward","`stock`=`stock`-".$_POST['num']."","`id`='".(int)$_GET['id']."'");
								$this->obj->ACT_layer_msg("兑换成功，请等待管理员审核！",9,$_SERVER['HTTP_REFERER']);
							}
						}
					}
				}
			  }
		}
 
		$jilu=$this->obj->DB_select_all("change","`gid`='".(int)$_GET['id']."' order by `id` desc limit 10");
		$this->yunset("jilu",$jilu); 
		$row=$this->obj->DB_select_once("reward","`id`='".(int)$_GET['id']."'");
		$this->yunset("row",$row); 
		$this->yunset("title","兑换确认 - ".$this->config['sy_webname']);
		$this->yun_tpl(array('dh_show'));
	}
}
?>