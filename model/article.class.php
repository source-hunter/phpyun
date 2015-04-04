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
class article_controller extends common
{
	function index_action(){
		$this->seo("news");
		$this->yun_tpl(array('index'));
	}
	function list_action()
	{
		$_GET['nid']=(int)$_GET['nid'];
		$this->yunset("nid",$_GET['nid']);
		$class=$this->obj->DB_select_once("news_group","id='".(int)$_GET['nid']."'");
		$this->yunset("class",$class);
		$this->seo("newslist");
		$this->yun_tpl(array('list'));
	}
	function show_action(){
		$_GET['id']=(int)$_GET['id'];
		$news=$this->obj->DB_select_alls("news_base","news_content","a.`id`='".(int)$_GET['id']."' and a.`id`=b.`nbid`");
		$news_last=$this->obj->DB_select_once("news_base","`id`<'".(int)$_GET['id']."' order by `id` desc");
		$news_last["url"]="/news/".date("Ymd",$news_last["datetime"])."/".$news_last['id'].".html";
		$news_next=$this->obj->DB_select_once("news_base","`id`>'$id' order by `id` asc");
		$news_next["url"]="/news/".date("Ymd",$news_next["datetime"])."/".$news_next['id'].".html";
		$class=$this->obj->DB_select_once("news_group","`id`='".$news[0]['nid']."'");
		
		if($news[0]["keyword"]!="")
		{
			$keyarr = @explode(",",$news[0]["keyword"]);
			if(is_array($keyarr) && !empty($keyarr))
			{
				foreach($keyarr as $key=>$value)
				{
					$sqlkeyword[]= " `keyword` LIKE '%$value%'";
				}
				$sqlkw = @implode("OR",$sqlkeyword);
				$about=$this->obj->DB_select_all("news_base"," 1 AND  ($sqlkw) AND `id`<>'".(int)$_GET['id']."' order by `id` desc limit 6");//相关文章
				if(is_array($about)){
					foreach($about as $k=>$v){
						$about[$k]["url"]="/news/".date("Ymd",$v["datetime"])."/".$v['id'].".html";
					}
				}
			}
		}
		$info=$news[0];
		$data['news_title']=$news[0]['title'];
		$data['news_keyword']=$news[0]['keyword'];
		$data['news_class']=$class['name'];
		$data['news_desc']=$this->obj->GET_content_desc($news[0]['description']);
		$this->data=$data;
		$info["last"]=$news_last;
		$info["next"]=$news_next;
		$info["like"]=$about;
		$this->yunset("Info",$info);
		$this->seo("news_article");
		$this->yun_tpl(array('show'));
	}
}
?>