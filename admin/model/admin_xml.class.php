<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2015 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
class admin_xml_controller extends common{
	function index_action(){ 
		$this->yuntpl(array('admin/admin_xml'));
	}
	function archive_action(){
		if($_POST['pytoken']){
			
			$type=trim($_POST['type']);
			if($type=='ask'||$type=='all'){
				if($_POST['order']=='uptime'){
					$order='lastupdate';
				}else{$order='add_time';}
				$rows['ask']=$this->obj->DB_select_all("question","1 order by ".$order." desc limit ".intval($_POST['limit']),"`id` as `id`,`".$order."` as `time`");
			}
			if($type=='news'||$type=='all'){
				if($_POST['order']=='uptime'){
					$order='lastupdate';
				}else{$order='datetime';}
				$rows['news']=$this->obj->DB_select_all("news_base","1 order by ".$order." desc limit ".intval($_POST['limit']),"`id`,`".$order."` as `time`,`datetime`");
			}
			if($type=='company'||$type=='all'){
				
				if($_POST['order']=='uptime'){
					$order='lastupdate';
				}else{$order='jobtime';}
				$rows['company']=$this->obj->DB_select_all("company","1 order by ".$order." desc limit ".intval($_POST['limit']),"`uid` as `id`,`".$order."` as `time`");
			}
			if($type=='job'||$type=='all'){
				if($_POST['order']=='uptime'){
					$order='lastupdate';
				}else{$order='sdate';}
				$rows['job']=$this->obj->DB_select_all("company_job","`sdate`<'".time()."' and `edate`>'".time()."' and `state`='1' order by ".$order." desc limit ".intval($_POST['limit']),"`id`,`".$order."` as `time`");
			}
			if($type=='resume'||$type=='all'){
				if($_POST['order']=='uptime'){
					$order='lastupdate';
				}else{$order='addtime';}
				$rows['resume']=$this->obj->DB_select_alls("resume","resume_expect","a.`status`<>'2' and a.`r_status`<>'2' and b.`job_classid`<>'' and b.`open`='1' and a.`uid`=b.`uid`  ORDER BY b.`".$order."` desc limit ".intval($_POST['limit']),"a.`uid` as `id`,b.`".$order."` as `time`");
			} 
			if(strpos(trim($_POST['name']),'.xml')==true){
				$_POST['name']=substr(trim($_POST['name']),0,strpos(trim($_POST['name']),'.xml'));
			}
			
			if($rows&&is_array($rows)){
				$show="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<urlset>\r\n"; 
				foreach($rows as $key=>$v){
					if($key=='ask'){
						foreach($v as $val){
							$url=$this->aurl(array("url"=>"c:content,id:".$val['id']));
							$show.="<url><loc>".$url."</loc><lastmod>".date("Y-m-d",$val['time'])."</lastmod></url><changefreq>".$_POST['frequency']."</changefreq>\r\n";
						}
					}
					if($key=='news'&&$this->config["sy_news_rewrite"]=='2'){
						foreach($v as $val){
							$url=$this->config['sy_weburl']."/news/".date("Ymd",$val["datetime"])."/".$val[id].".html";
							$show.="<url><loc>".$url."</loc><lastmod>".date("Y-m-d",$val['time'])."</lastmod></url><changefreq>".$_POST['frequency']."</changefreq>\r\n";
						}
					}
					if($key=='news'&&$this->config["sy_news_rewrite"]!='2'){
						foreach($v as $val){
							$url= $this->url("index","news",array("c"=>"show","id"=>$val[id]),"1");
							$show.="<url><loc>".$url."</loc><lastmod>".date("Y-m-d",$val['time'])."</lastmod></url><changefreq>".$_POST['frequency']."</changefreq>\r\n";
						}
					}
					if($key=='company'){
						foreach($v as $val){
							$url= $this->curl(array("url"=>"id:".$val['id']));
							$show.="<url><loc>".$url."</loc><lastmod>".date("Y-m-d",$val['time'])."</lastmod></url><changefreq>".$_POST['frequency']."</changefreq>\r\n";
						}
					}
					if($key=='job'){
						foreach($v as $val){
							$url= $this->url("index","com",array("c"=>"comapply","id"=>$val['id']),"1");
							$show.="<url><loc>".$url."</loc><lastmod>".date("Y-m-d",$val['time'])."</lastmod></url><changefreq>".$_POST['frequency']."</changefreq>\r\n";
						}
					}
					if($key=='resume'){
						foreach($v as $val){
							$url=$this->url("index","resume",array("id"=>$val['id']),"1");
							$show.="<url><loc>".$url."</loc><lastmod>".date("Y-m-d",$val['time'])."</lastmod></url><changefreq>".$_POST['frequency']."</changefreq>\r\n";
						}
					}
				}
				
				$show.="</urlset>";
				if(!$this->CheckRegUser($_POST['name']))
				{
					$this->layer_msg("XML名称包含特殊字符！",8,0,'index.php?m=admin_xml');
				}
				$path = APP_PATH."/".$_POST['name'].".xml";
				$fp = @fopen($path,"w");
				@fwrite($fp,$show);
				@fclose($fp);
				@chmod($path,0777);
				$this->layer_msg("生成成功！",9,0,'index.php?m=admin_xml');
			}
		}
	}

}
?>