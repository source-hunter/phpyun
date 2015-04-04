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
class cache_controller extends common
{
	function index_action(){
		if($_POST["madeall"]){
			$url=$this->config["sy_weburl"]."/index.php";
			$fw=$this->obj->html($_POST["url"],$url);
			$fw?$this->obj->ACT_layer_msg( "���ɳɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "����ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}
		$this->yunset("type","index");
		$this->yuntpl(array('admin/admin_makenews'));
	}
	function cache_action(){
		include_once(CONFIG_PATH."db.data.php");
		$this->yunset("type",$arr_data['cache']);
		if($_POST["madeall"]){
			$this->makecache_action();
		}
		$this->yuntpl(array('admin/admin_cache'));
	}
	function once_action(){
		set_time_limit(200);
		if($_POST['make'])
		{
			$where="`is_type`='1'";
			if($_POST['desc']>0){
				$where.=" and  `id`='".$_POST['desc']."'";
			}
			$rows=$this->obj->DB_select_all("description",$where);
			if(@is_array($rows)){
				foreach($rows as $row){
					$url=$this->config['sy_weburl']."/index.php?m=index&c=get&id=".$row[id];
					$fw=$this->obj->html("../".$row['url'],$url);
				}
			}
			echo 1;die;
		}
		$rows=$this->obj->DB_select_all("description","1","`id`,`name`");
		$this->yunset("rows",$rows);
		$this->yunset("type","once");
		$this->yuntpl(array('admin/admin_makenews'));
	}
	function news_action(){
		set_time_limit(200);
		if($_POST["madeall"]){
			$url=$this->config["sy_weburl"]."/index.php?m=news";
			$fw=$this->obj->html($_POST["url"],$url);
			$fw?$this->obj->ACT_layer_msg( "��������(ID:$fw)�ɹ���",9,$_SERVER['HTTP_REFERER'],2,1):$this->obj->ACT_layer_msg( "��������(ID:$fw)ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}
		$this->yunset("type","news");
		$this->yuntpl(array('admin/admin_makenews'));
	}
	function newsclass_action(){
		set_time_limit(200);
		if($_POST['action']=="makeclass"){
			$val=$this->mk_newsclass();
			if(is_array($val)){
				foreach($val as $va){
					if($name==""){$name=$this->stringfilter($va);}
				}
				$this->get_return("class",$val,"���������������--".$name);
			}else{
				$this->get_return("ok",0,"ȫ���������");
			}
		}
		$rows=$this->obj->DB_select_all("news_group");
		$this->yunset("rows",$rows);
		foreach($rows as $v){
			$classid[]=$v["id"];
		}
		$this->yunset("classid",@implode(',',$classid));
		$this->yunset("type","newsclass");
		$this->yuntpl(array('admin/admin_makenews'));
	}
	function archive_action(){
		set_time_limit(200);
		if($_POST['action']=="makearchive"){
			$pagesize=$_POST['limit'];
			$page=$this->mk_archive($pagesize);
			if($page){
				if($page!=1){
					$npage=$page;
					$page=$page-1;
					$spage=$page*$pagesize;
					$topage=$spage+$pagesize;
				}else{
					$npage=$page;
					$spage=$page;
					$topage=$pagesize;
				}
				$name=$spage."-".$topage;
				$this->get_return("archive",$npage,"��������".$name."����");
			}else{
				$this->get_return("ok",0,"ȫ���������");
			}
		}
		$rows=$this->obj->DB_select_all("news_group");
		$this->yunset("rows",$rows);
		$this->yunset("type","archive");
		$this->yuntpl(array('admin/admin_makenews'));
	}
	function all_action(){
		set_time_limit(200);
		if($_POST['action']=="makeall"){
			if($_POST['type']=="cache"){
				include_once(LIB_PATH."cache.class.php");
				include_once("model/model/advertise_class.php");
				$cacheclass= new cache("../plus/",$this->obj);
				include_once(CONFIG_PATH."db.data.php");
				$value=$_POST['value']+1;
				$cache=array(1,2,3,4,5,6,7,8,9,10,11,12);
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->city_cache("city.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->industry_cache("industry.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->job_cache("job.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->user_cache("user.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->com_cache("com.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->menu_cache("menu.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->seo_cache("seo.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->domain_cache("domain_cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->keyword_cache("keyword.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->link_cache("link.cache.php");
				}
				if(@in_array($value,$cache)){
					$makecache=$cacheclass->group_cache("group.cache.php");
				}
				if(@in_array($value,$cache)){
					$adver = new advertise($this->obj);
					$adver->model_ad_arr_action();
				}
				if(@in_array($value,$cache)){
					$makecache=$this->tpl->clear_all_cache();
				}
				if($value<=10){
					$v=$value+1;
					$this->get_return("cache",$value,"��������".$arr_data['cache'][$v]);
				}else{
					$this->get_return("index","index","����������ҳ");
				}
				echo json_encode($data);die;
			}
			if($_POST['type']=="index"){
				if($_POST['value']=="index"){
					$url=$this->config["sy_weburl"]."/index.php";
					$fw=$this->obj->html($_POST["index"],$url);
					$this->get_return("index","news","��������������ҳ");
				}else{
					$url=$this->config["sy_weburl"]."/index.php?m=news";
					$fw=$this->obj->html($_POST["news"],$url);
					$this->get_return("class",0,"���ڻ�ȡ���������Ŀ");
				}
				echo json_encode($data);die;
			}
			if($_POST['type']=="class"){
				$val=$this->mk_newsclass();
				if(is_array($val)){
					foreach($val as $va){
						if($name==""){
							$name=$this->stringfilter($va);
						}
					}
					$this->get_return("class",$val,"���������������--".$name);
				}else{
					$this->get_return("archive",0,"���ڻ�ȡ������ϸҳ��Ŀ");
				}
			}
			if($_POST['type']=="archive"){
				$pagesize="20";
				$page=$this->mk_archive($pagesize);
				if($page){
					if($page!=1){
						$npage=$page;
						$page=$page-1;
						$spage=$page*$pagesize;
						$topage=$spage+$pagesize;
					}else{
						$npage=$page;
						$spage=$page;
						$topage=$pagesize;
					}
					$name=$spage."-".$topage;
					$this->get_return("archive",$npage,"��������".$name."����");
				}else{
					$this->get_return("ok",0,"ȫ���������");
				}
			}
		}
		$this->yunset("type","all");
		$this->yuntpl(array('admin/admin_makenews'));
	}
	function makecache_action(){
		set_time_limit(200);
		extract($_POST);
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		if(@in_array("1",$cache)){
			$makecache=$cacheclass->city_cache("city.cache.php");
		}
		if(@in_array("2",$cache)){
			$makecache=$cacheclass->industry_cache("industry.cache.php");
		}
		if(@in_array("3",$cache)){
			$makecache=$cacheclass->job_cache("job.cache.php");
		}
		if(@in_array("4",$cache)){
			$makecache=$cacheclass->user_cache("user.cache.php");
		}
		if(@in_array("5",$cache)){
			$makecache=$cacheclass->com_cache("com.cache.php");
		}
		if(@in_array("6",$cache)){
			$makecache=$cacheclass->menu_cache("menu.cache.php");
		}
		if(@in_array("7",$cache)){
			$makecache=$this->tpl->clear_all_cache();
		}
		if(@in_array("8",$cache)){
			$makecache=$cacheclass->seo_cache("seo.cache.php");
		}
		if(@in_array("9",$cache)){
			$makecache=$cacheclass->domain_cache("domain_cache.php");
		}
		if(@in_array("10",$cache)){
			$makecache=$cacheclass->keyword_cache("keyword.cache.php");
		}
		if(@in_array("11",$cache)){
			$makecache=$cacheclass->link_cache("link.cache.php");
		}
		if(@in_array("12",$cache)){
			$makecache=$cacheclass->group_cache("group.cache.php");
		}

		if($makecache){
			$this->obj->ACT_layer_msg( "����(ID:$makecache)�ɹ���",9,"index.php?m=cache&c=cache",2,1);
		}else{
			$this->obj->ACT_layer_msg( "����(ID:$makecache)ʧ�ܣ�",8,"index.php?m=cache&c=cache");
		}
	}

	function mk_newsclass(){
		if($_POST['value']==0){
			$where=1;
			if($_POST['group']!="all" && $_POST['group']){
				$where.=" and `id`='".$_POST['group']."'";
			}
			$rows=$this->obj->DB_select_all("news_group",$where);
			if(is_array($rows)){
				foreach($rows as $v){
					$val[$v['id']]=iconv("gbk","UTF-8",$v['name']);
				}
			}
		}else{
			$rows=$_POST['value'];
			if(is_array($rows)){
				foreach($rows as $k=>$va){
					if($nid==""){
						$nid=$k;
					}else{
						$val[$k]=$va;
					}
				}
			}
			$this->makenewsclass($nid);
		}
		return $val;
	}
	function mk_archive($pagesize){
		if($_POST['value']==0){
			$where="1";
			if($_POST['group']>0){
				$where.=" and `nid`='".$_POST['group']."'";
			}
			if($_POST['startid']>0){
				$where.=" and `id`>='".$_POST['startid']."'";
			}
			if($_POST['endid']>0){
				$where.=" and `id`<='".$_POST['endid']."'";
			}
			$rows=$this->obj->DB_select_all("news_base",$where,"`id`,`datetime`");
			$allnum=count($rows);
			$allpage=ceil(($allnum)/$pagesize);
			$i=1;
			foreach($rows as $v){
				if(count($val[$i])<=$pagesize){
					$val[$i][$v['id']]=$v['datetime'];
				}else{
					$i++;
					$val[$i][$v['id']]=$v['datetime'];
				}
			}
			include_once(LIB_PATH."public.function.php");
			$this->obj->made_web("../plus/news.cache.php",ArrayToString2($val),"newscache");
			$page=1;
		}else{
			$page=$_POST['value'];
			include_once(PLUS_PATH."news.cache.php");
			if(is_array($newscache)){
				foreach($newscache as $k=>$va){
					if($k==$page){
						foreach($va as $key=>$value){$this->makearchive($key,$value);}
					}elseif($k>$page){
						$val[$k]=$va;
					}
				}
			}
			$page=$page+1;
			if(!is_array($val)){$page=0;unlink("../plus/news.cache.php");}
		}
		return $page;
	}
	function get_return($type,$value,$msg){
		$data['type']=$type;
		$data['value']=$value;
		$data['msg']=iconv("gbk","UTF-8",$msg);
		echo json_encode($data);die;
	}
	function makenewsclass($nid){
		$allpage=ceil($this->obj->DB_select_num("news_base","`nid`='".$nid."'","id")/20);
		for($i=1;$i<=$allpage;$i++){
			if($allpage>=$i){
				$url=$this->config['sy_weburl']."/index.php?m=news&c=list&nid=".$nid."&page=".$i;
				if($i==1){
					$fw=$this->obj->html("../news/".$nid."/"."index.html",$url);
				}
				$fw=$this->obj->html("../news/".$nid."/".$i.".html",$url);
			}
		}
	}
	function makearchive($id,$datetime){
		$url=$this->config['sy_weburl']."/index.php?m=news&c=show&id=".$id;
		$dir=date("Ymd",$datetime);
		$fw=$this->obj->html("../news/".$dir."/".$id.".html",$url);
	}
}
?>