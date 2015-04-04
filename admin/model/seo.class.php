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
class seo_controller extends common
{
	function set_search(){
		$ad_time=array('1'=>'今天','3'=>'最近三天','7'=>'最近七天','15'=>'最近半月','30'=>'最近一个月');
		$search_list[]=array("param"=>"end","name"=>'更新时间',"value"=>$ad_time);
		$this->yunset("search_list",$search_list);
	}
	function index_action(){
		$where="1";
		$this->set_search();
		if(trim($_GET['keyword'])){
			$where.=" and `".$_GET['type']."` like '%".trim($_GET['keyword'])."%'";
			$urlarr['type']=$_GET['type'];
			$urlarr['keyword']=$_GET['keyword'];
		}
		if($_GET['end']){
			if($_GET['end']=='1'){
				$where.=" and `time` >= '".strtotime(date("Y-m-d 00:00:00"))."'";
			}else{
				$where.=" and `time` >= '".strtotime('-'.(int)$_GET['end'].'day')."'";
			}
			$urlarr['end']=$_GET['end'];
		}
		$urlarr['page']="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr);
		$seolist = $this->get_page("seo",$where." order by id desc",$pageurl,$this->config['sy_listnum']);
		$this->yunset("get_type",$_GET);
		$this->yunset("seolist",$seolist);
		$this->yuntpl(array('admin/admin_list_seo'));
	}
	function SeoAdd_action()
	{
		include(CONFIG_PATH."db.data.php");
		$this->yunset("seoconfig",$arr_data['seoconfig']);
		$list = $this->obj->DB_select_all("domain");
		$this->yunset("list",$list);
		$this->yuntpl(array('admin/admin_add_seo'));
	}
	function Modify_action()
	{
		$where=1;
		
		$shell=$this->obj->DB_select_once("admin_user","`uid`='".$_SESSION['auid']."'");
		$where="`id` in (".$shell['domain'].")";
	
		$domain = $this->obj->DB_select_all("domain",$where,"`id`,`title`");
		$this->yunset("domain",$domain);
		if($_GET['id'])
		{
			$seo= $this->obj->DB_select_once("seo","`id`='".$_GET['id']."'");
			if($seo['affiliation']=="0")
			{
				$seo['domain_name']="全站使用";
			}else{
				$domains=@explode(",",$seo['affiliation']);
				foreach($domains as $v)
				{
					foreach($domain as $val)
					{
						if($v==$val['id'])
						{
							$domain_name[]=$val['title'];
						}
					}
				}
				$seo['domain_name']=@implode(",",$domain_name);
			}
			$this->yunset("seo",$seo);
		}
		include(CONFIG_PATH."db.data.php");
		$this->yunset("seoconfig",$arr_data['seoconfig']);
		$this->yuntpl(array('admin/admin_add_seo'));
	}
	function Save_action()
	{
		extract($_POST);
		if(empty($did))
		{
			$did=0;
		}
		$value = "`seoname`='$seoname',";
		$value.= "`ident`='$ident',";
		$value.= "`title`='$title',";
		$value.= "`keywords`='$keywords',";
		$value.= "`php_url`='$php_url',";
		$value.= "`rewrite_url`='$rewrite_url',";
		$value.= "`description`='$description',";
		$value.= "`affiliation`='$did',";
		$value.= "`time`='".time()."'";

		if($_POST['update'])
		{
			$this->obj->DB_update_all("seo",$value,"`id`='$id'");
			$this->cache_action();
			$msg = "SEO 修改成功！";
		}elseif($_POST['add']){
			$this->obj->DB_insert_once("seo",$value);
			$this->cache_action();
			$msg = "SEO 添加成功！";
		}
		$this->obj->ACT_layer_msg( $msg,9,"index.php?m=seo",2,1);
		$this->yuntpl(array('admin/admin_add_seo'));
	}

	function getseo_action()
	{
		include(PLUS_PATH."seo.cache.php");
		$this->get_apache_url($seo);
		
	}
	function del_action()
	{
		if($_GET['del'])
		{
			$this->check_token();
	    	if(is_array($_GET['del']))
	    	{
	    		$del=@implode(",",$_GET['del']);
	    		$layer_status=1;
	    	}else{
	    		$del=$_GET['del'];
	    		$layer_status=0;
	    	}
			$id=$this->obj->DB_delete_all("seo","`id` in (".$del.")","");
			if($id)
			{
				$this->cache_action();
				$this->layer_msg('SEO(ID:'.$del.')删除成功！',9,$layer_status,"index.php?m=seo");
			}else{
				$this->layer_msg('SEO(ID:'.$del.')删除失败！',8,$layer_status,"index.php?m=seo");
			}
		}

	}
	function cache_action(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->seo_cache("seo.cache.php");
	}
	function get_apache_url($seo){
		$i=0;
		foreach($seo as $k=>$v){
			if($v[$i][rewrite_url] && $v[$i][php_url]){
			$rewrite_url=$v[$i][rewrite_url];
			$php_url=$v[$i][php_url];
			$rewrite_url=str_replace("[weburl]/",'',$rewrite_url);
			$rewrite_url=str_replace(".","\.",$rewrite_url);
			$php_url=str_replace("[weburl]/",'',$php_url);
			$apacheurl.="RewriteRule ^".$rewrite_url."$ ".$php_url." [L]";
			}
			$i++;
		}
		$apache="<IfModule mod_rewrite.c>";
		$apache.="RewriteEngine On"."\n";
		$apache.="RewriteCond %{REQUEST_FILENAME} !-f";
		$apache.="RewriteCond %{REQUEST_FILENAME} !-d";
		$apache.=$apacheurl;
		$apache.="RewriteRule ^news\.html$ news\.html [L]";
		$apache.="RewriteRule ^index\.html$ index\.html [L]";
		$apache.="RewriteRule ^act_(.*)\.html$ index.php?yunurl=act_$1 [L]";
		$apache.="RewriteRule ^company-(.*)\.html$ company/index.php?yunurl=$1 [L]";
		$apache.="RewriteRule ^ask-(.*)\.html$ ask/index.php?yunurl=$1 [L]";
		$apache.="RewriteRule ^friend-(.*)\.html$ friend/index.php?yunurl=$1 [L]";
		$apache.="RewriteRule ^m_(.*)\.html$ index.php?yunurl=m_$1 [L]";
		$apache.="RewriteRule ^c_(.*)\.html$ index.php?yunurl=c_$1 [L]";
		$apache.="ErrorDocument 404 /Error.php";
		$apache.="</IfModule>";
		return $apache;
	}
	function get_iis60_url($seo){
		$i=0;
		foreach($seo as $k=>$v){
			if($v[$i][rewrite_url] && $v[$i][php_url]){
			$rewrite_url=$v[$i][rewrite_url];
			$php_url=$v[$i][php_url];
			$rewrite_url=str_replace("[weburl]",'',$rewrite_url);
			$rewrite_url=str_replace(".","\.",$rewrite_url);
			$php_url=str_replace("[weburl]",'',$php_url);
			$apacheurl.="RewriteRule ^".$rewrite_url."$ ".$php_url." [I,L]";
			}
			$i++;
		}
		$iis60="[ISAPI_Rewrite]";
		$iis60.="# phpyun.com V2.5";
		$iis60.="# 3600 = 1 hour";
		$iis60.="CacheClockRate 3600";
		$iis60.="RepeatLimit 32";
		$iis60.="# Protect httpd.ini and httpd.parse.errors files";
		$iis60.="# from accessing through HTTP";
		$iis60.="RewriteEngine on";
		$iis60.="RewriteBase /";
		$iis60.="RewriteCond %{REQUEST_FILENAME} !-f [NC] ";
		$iis60.="RewriteCond %{REQUEST_FILENAME} !-d ";
		$iis60.=$apacheurl;
		$iis60.="RewriteRule /news\.html /news.html [I,L]";
		$iis60.="RewriteRule /index.html /index.html [I,L]";
		$iis60.="RewriteRule /company-(.*)\.html /company/index.php\?yunurl=$1 [I,L]";
		$iis60.="RewriteRule /act_(.*)\.html /index.php\?yunurl=act_$1 [I,L]";
		$iis60.="RewriteRule /ask-(.*)\.html /ask/index.php?yunurl=$1 [I,L]";
		$iis60.="RewriteRule /friend-(.*)\.html /friend/index.php?yunurl=$1 [I,L]";
		$iis60.="RewriteRule /m_(.*)\.html /index.php?yunurl=m_$1 [I,L]";
		$iis60.="RewriteRule /c_(.*)\.html /index.php?yunurl=c_$1 [I,L]";
		return $iis60;
	}
	function get_iis70_url($seo){
		$i=0;
		foreach($seo as $k=>$v){
			if($v[$i][rewrite_url] && $v[$i][php_url]){
			$rewrite_url=$v[$i][rewrite_url];
			$php_url=$v[$i][php_url];
			$rewrite_url=str_replace("[weburl]/",'',$rewrite_url);
			$php_url=str_replace("[weburl]/",'',$php_url);
				$apacheurl.='<rule name="'.$k.'">';
				$apacheurl.='<match url="^'.$rewrite_url.'$" />';
				$apacheurl.='<action type="Rewrite" url="'.$php_url.'" />';
				$apacheurl.="</rule>";
			}
			$i++;
		}
		$iis70.='<?xml version="1.0" encoding="utf-8"?>';
        $iis70.="<configuration>";
        $iis70.="<system.webServer>";
        $iis70.="<rewrite>";
        $iis70.="<rules>";
        $iis70.='<rule name="urlRewrite">';
            $iis70.='<conditions logicalGrouping="MatchAll">';
            $iis70.='<add input="{REQUEST_FILENAME}" pattern=".(html|xml|json|htm|php|php2|php3|php4|php5|phtml|pwml|inc|asp|aspx|ascx|jsp|cfm|cfc|pl|cgi|shtml|shtm|phtm)$" ignoreCase="false" />';
            $iis70.='<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" pattern="" ignoreCase="false" />';
            $iis70.='<add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" pattern="" ignoreCase="false" />';
            $iis70.="</conditions>";
        $iis70.="</rule>";
		 $iis70.=$apacheurl;
        $iis70.='<rule name="news">';
        $iis70.='<match url="^news.html$" />';
        $iis70.='<action type="Rewrite" url="news.html" />';
        $iis70.="</rule>";
        $iis70.='<rule name="index">';
        $iis70.='<match url="^index.html$" />';
        $iis70.='<action type="Rewrite" url="index.html" />';
        $iis70.="</rule>";
        $iis70.='<rule name="company">';
        $iis70.='<match url="^company-(.*).html$" />';
        $iis70.='<action type="Rewrite" url="company/index.php?yunurl={R:1}" />';
        $iis70.="</rule>";
        $iis70.='<rule name="act">';
        $iis70.='<match url="^act-(.*).html$" />';
        $iis70.='<action type="Rewrite" url="index.php?yunurl={R:1}" />';
        $iis70.="</rule>";
        $iis70.='<rule name="ask">';
        $iis70.='<match url="^ask-(.*).html$" />';
        $iis70.='<action type="Rewrite" url="index.php?yunurl={R:1}" />';
        $iis70.="</rule>";
        $iis70.='<rule name="friend">';
        $iis70.='<match url="^friend-(.*).html$" />';
        $iis70.=' <action type="Rewrite" url="index.php?yunurl={R:1}" />';
        $iis70.="</rule>";
        $iis70.='<rule name="m">';
        $iis70.='<match url="^m_(.*).html$" />';
        $iis70.='<action type="Rewrite" url="index.php?yunurl=m_{R:1}" />';
        $iis70.="</rule>";
        $iis70.='<rule name="c">';
        $iis70.='<match url="^c_(.*).html$" />';
        $iis70.='<action type="Rewrite" url="index.php?yunurl=m_{R:1}" />';
        $iis70.="</rule>";
        $iis70.="</rules>";
        $iis70.="</rewrite>";
        $iis70.="</system.webServer>";
        $iis70.="<system.web>";
        $iis70.="</system.web>";
        $iis70.="</configuration>";
		return $iis70;
	}
	function get_nginx_url($seo){
		$i=0;
		foreach($seo as $k=>$v){
			if($v[$i][rewrite_url] && $v[$i][php_url]){
				$rewrite_url=$v[$i][rewrite_url];
				$php_url=$v[$i][php_url];
				$rewrite_url=str_replace("[weburl]",'',$rewrite_url);
				$php_url=str_replace("[weburl]",'',$php_url);
				$apacheurl.="rewrite ^".$rewrite_url."$ ".$php_url." last;";
			}
			$i++;
		}
		$nginx="if (!-f $request_filename){";
		$nginx.="set $rule_0 1$rule_0;";
		$nginx.="}";
		$nginx.="if (!-d $request_filename){";
		$nginx.="set $rule_0 2$rule_0;";
		$nginx.="}";
		$nginx.=$apacheurl;
		$nginx.='if ($rule_0 = "21"){';
		$nginx.="rewrite ^/news.html$ /news.html last;";
		$nginx.="}";
		$nginx.="rewrite ^/index.html$ /index.html last;";
		$nginx.="rewrite ^/act_(.*).html$ /index.php?yunurl=act_$1 last;";
		$nginx.="rewrite ^/company-(.*).html$ /company/index.php?yunurl=$1 last;";
		$nginx.="rewrite ^/ask-(.*).html$ /ask/index.php?yunurl=$1 last;";
		$nginx.="rewrite ^/friend-(.*).html$ /friend/index.php?yunurl=$1 last;";
		$nginx.="rewrite ^/m_(.*).html$ /index.php?yunurl=m_$1 last;";
		$nginx.="rewrite ^/c_(.*).html$ /index.php?yunurl=c_$1 last;";
		return $nginx;
	}
}