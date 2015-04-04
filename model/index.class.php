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
class index_controller extends common
{
	function index_action()
	{ 
		$CacheArr['city'] =array('city_index','city_type','city_name');
		$CacheArr['job'] =array('job_index','job_type','job_name');
		$CacheArr['user'] =array('userdata','userclass_name');
		$CacheArr['com'] =array('comdata','comclass_name');
		$CacheArr['industry'] =array('industry_index','industry_name');
 		$this->CacheInclude($CacheArr);
		$jobnum=$this->obj->DB_select_num("company_job","`sdate`<'".time()."' and `edate`>'".time()."' and `state`='1' and `r_status`<>2 and `status`<>1");
		$this->yunset("jobnum",$jobnum);
		$this->seo("index");
		$this->yun_tpl(array('index'));
	}
    function integral_action(){
		$this->seo("integral");
		$this->yun_tpl(array('integral'));
	}
	function top_action(){
		$this->seo("top");
		$this->yun_tpl(array('top'));
	}
	function moblie_action(){
		$this->seo("moblie");
		$this->yun_tpl(array('moblie'));
	}
	function wap_action(){
		$this->seo("wap");
		$this->yun_tpl(array('wap'));
	}
	function weixin_action(){
		$this->seo("weixin");
		$this->yun_tpl(array('weixin'));
	}
	function android_action(){
		$this->seo("android");
		$this->yun_tpl(array('android'));
	}
	function ios_action(){
		$this->seo("ios");
		$this->yun_tpl(array('ios'));
	}
	function site_action()
	{
		if($this->config["sy_web_site"]!="1")
		{
			$this->obj->ACT_msg($_SERVER['HTTP_REFERER'], $msg = "暂未开启多站点模式！");
		}
		$this->seo("index");
		$this->yun_tpl(array('site'));
	}
	function logout_action()
	{
		$this->logout();
	}
	function GetHits_action()
    {
    	if($_GET['id']){
    		$this->obj->DB_update_all("news_base","`hits`=`hits`+1","`id`='".(int)$_GET['id']."'");
    		$news_info = $this->obj->DB_select_once("news_base","`id`='".(int)$_GET['id']."'");
    		echo "document.write('".$news_info["hits"]."')";
    	}
    }
	function get_action(){
		$row=$this->obj->DB_select_once("description","`id`='".(int)$_GET['id']."'");
		$top="";$footer="";
		if($row["top_tpl"]==1){
			 $top="../template/".$this->config['style']."/header";
		}else if($row["top_tpl"]==3){
			 $top=$row["top_tpl_dir"];
		}
		if($row["footer_tpl"]==1){
			 $footer="../template/".$this->config['style']."/footer";
		}else if($row["footer_tpl"]==3){
			 $footer=$row["footer_tpl_dir"];
		}
		$seo["title"]=$row["title"];
		$seo["keywords"]=$row["keyword"];
		$seo["description"]=$row["descs"];
		$row=$this->obj->DB_select_once("description","`id`='".(int)$_GET['id']."'");
		$this->yunset("seo",$seo);
		$this->yunset("name",$row["name"]);
		$this->yunset("content",$row["content"]);
		$this->header_desc($row["title"],$row["keyword"],$row["descs"]);
		$make="../template/".$this->config['style']."/make";
		$make_top="../template/".$this->config['style']."/make_top";
		$this->yuntpl(array($make_top,$top,$make,$footer));
	}
	function clickHits_action(){
		if($_GET['id']){
			$id=(int)$_GET['id'];
			$ad=$this->obj->DB_select_once("ad","`id`='".$id."'","pic_src,id");
			if(!empty($ad)){
				$ip = $this->obj->fun_ip_get();
				if($this->config['sy_adclick']>"0"){
					$num=$this->obj->DB_select_num("adclick","`ip`='".$ip."' and `aid`='".$id."' and `addtime`>'".strtotime('-'.$this->config['sy_adclick'].' hour')."'");
					if($num>"0"){
						header('Location: '.$ad['pic_src']);
					}
				}
				$data['aid']=$id;
				$data['uid']=$this->uid;
				$data['ip']=$ip;
				$data['addtime']=time();
				$nid=$this->obj->insert_into("adclick",$data);
				if($nid){$this->obj->DB_update_all("ad","`hits`=`hits`+1","`id`='".$id."'");}
				if(!$ad['pic_src'])
				{
					$ad['pic_src']=$this->config['sy_weburl'];
				}
				header('Location: '.$ad['pic_src']);
			}
		}
	}
}
?>