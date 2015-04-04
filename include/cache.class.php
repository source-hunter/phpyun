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
 class cache{
	public $cachedir;
	public $obj;
	public function __construct($cachedir,$obj) {
		$this->cachedir = $cachedir;
		$this->obj=$obj;
		include_once(LIB_PATH."public.function.php");
	}
	public function city_cache($dir){
		$cityarr=$this->obj->DB_select_all("city_class","`display`='1' order by sort asc");
		if(is_array($cityarr)){
			foreach($cityarr as $v){
				if($v['keyid']==0){
					$city_index[]=$v['id'];
				}
				if($v['keyid']!=0){
					$city_type[$v['keyid']][]=$v['id'];
				}
				$cityname[$v['id']]=$v['name'];
			}
		}
		$data['city_index']=ArrayToString($city_index,false);
		$data['city_type']=ArrayToString($city_type);
		$data['city_name']=ArrayToString($cityname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function cron_cache($dir){
		$rows=$this->obj->DB_select_all("cron","`display`='1' order by id asc");
		if(is_array($rows)){
			foreach($rows as $key=>$value){
				$cron_cache[$key]['id']=$value["id"];
				$cron_cache[$key]['dir']=$value["dir"];
				$cron_cache[$key]['type']=$value["type"];
				$cron_cache[$key]['week']=$value["week"];
				$cron_cache[$key]['month']=$value["month"];
				$cron_cache[$key]['hour']=$value["hour"];
				$cron_cache[$key]['minute']=$value["minute"];
				$cron_cache[$key]['nexttime']=$value["nexttime"];
			}
		}
		$data['cron']=ArrayToString2($cron_cache);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function ltjob_cache($dir){
		$rows=$this->obj->DB_select_all("ltjob_class"," 1 order by sort asc");
		if(is_array($rows)){
			foreach($rows as $v){
				if($v['keyid']==0){
					$ltjob_index[]=$v['id'];
				}
				if($v['keyid']!=0){
					$ltjobtype[$v['keyid']][]=$v['id'];
				}
				$ltjobname[$v['id']]=$v['name'];
			}
		}
		$data['ltjob_index']=ArrayToString($ltjob_index,false);
		$data['ltjob_type']=ArrayToString($ltjobtype);
		$data['ltjob_name']=ArrayToString($ltjobname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function lthy_cache($dir){
		$rows=$this->obj->DB_select_all("lthy_class");
		if(is_array($rows)){
			foreach($rows as $v){
				if($v['keyid']==0){
					$lthy_index[]=$v['id'];
				}
				if($v['keyid']!=0){
					$lthytype[$v['keyid']][]=$v['id'];
				}
				$lthyname[$v['id']]=$v['name'];
			}
		}
		$data['lthy_index']=ArrayToString($lthy_index,false);
		$data['lthy_type']=ArrayToString($lthytype);
		$data['lthy_name']=ArrayToString($lthyname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function job_cache($dir){
		$rows=$this->obj->DB_select_all("job_class"," 1 order by sort DESC");
		if(is_array($rows)){
			foreach($rows as $v){
				if($v['keyid']==0){
					$job_index[]=$v['id'];
				}
				if($v['keyid']!=0){
					$jobtype[$v['keyid']][]=$v['id'];
				}
				if($v['content']){
					$content[]=$v['id'];
				}
				$jobname[$v['id']]=$v['name'];
			}
		}
		$data['content']=ArrayToString($content,false);
		$data['job_index']=ArrayToString($job_index,false);
		$data['job_type']=ArrayToString($jobtype);
		$data['job_name']=ArrayToString($jobname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function industry_cache($dir){
		$rows=$this->obj->DB_select_all("industry","1 order by sort desc");
		if(is_array($rows)){
			foreach($rows as $v){
				$industry_index[]=$v['id'];
				$industryname[$v['id']]=$v['name'];
			}
		}
		$data['industry_index']=ArrayToString($industry_index,false);
		$data['industry_name']=ArrayToString($industryname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function subject_cache($dir){
		$rows=$this->obj->DB_select_all("px_subject_class","1 order by sort desc");
		if(is_array($rows)){
			foreach($rows as $v){
				if($v['keyid']==0){
					$subject_index[]=$v['id'];
				}
				if($v['keyid']!=0){
					$subjecttype[$v['keyid']][]=$v['id'];
				}
				$subjectname[$v['id']]=$v['name'];
			}
		}
		$data['subject_index']=ArrayToString($subject_index,false);
		$data['subject_type']=ArrayToString($subjecttype);
		$data['subject_name']=ArrayToString($subjectname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function subject_type_cache($dir){
		$rows=$this->obj->DB_select_all("px_subject_type","1 order by sort desc");
		if(is_array($rows)){
			foreach($rows as $v){
				$subject_type_index[]=$v['id'];
				$subject_type_name[$v['id']]=$v['name'];
			}
		}
		$data['subject_type_index']=ArrayToString($subject_type_index,false);
		$data['subject_type_name']=ArrayToString($subject_type_name);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function user_cache($dir){
		$rows=$this->obj->DB_select_all("userclass","1 order by sort asc");
		if(is_array($rows)){
			foreach($rows as $v){
				if($v['keyid']!=0){
					$com_index[$v["keyid"]][]=$v["id"];
				}
				$jobname[$v['id']]=$v['name'];
			}
			foreach($rows as $v){
				if($v['keyid']==0){
					$data2[$v['variable']]=$com_index[$v['id']];
				}
			}
		}
		$data['userdata']=ArrayToString($data2);
		$data['userclass_name']=ArrayToString($jobname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function lt_cache($dir){
		$rows=$this->obj->DB_select_all("ltclass","1 order by sort asc");
		if(is_array($rows)){
			foreach($rows as $v){
				if($v['keyid']!=0){
					$lt_index[$v["keyid"]][]=$v["id"];
				}
				$ltname[$v['id']]=$v['name'];
			}
			foreach($rows as $v){
				if($v['keyid']==0){
					$data2[$v['variable']]=$lt_index[$v['id']];
				}
			}
		}
		$data['ltdata']=ArrayToString($data2);
		$data['ltclass_name']=ArrayToString($ltname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function com_cache($dir){
		$rows=$this->obj->DB_select_all("comclass","1 order by sort asc");
		if(is_array($rows)){
			foreach($rows as $v){
				if($v["keyid"]!=0){
					$com_index[$v["keyid"]][]=$v["id"];
				}
				$comname[$v['id']]=$v['name'];
			}
			foreach($rows as $v){
				if($v['keyid']==0){
					$data2[$v['variable']]=$com_index[$v['id']];
				}
			}
		}
		$data['comdata']=ArrayToString($data2);
		$data['comclass_name']=ArrayToString($comname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function menu_cache($dir){
		$rows=$this->obj->DB_select_all("navigation","display=1 order by sort asc");
		if(is_array($rows)){
			foreach($rows as $key=>$v){
				if(!is_array($com_index[$v["nid"]]))
					$a[$v["nid"]]=0;
					$com_index[$v["nid"]][$a[$v["nid"]]]['name']=$v['name'];
					$com_index[$v["nid"]][$a[$v["nid"]]]['url']=$v['url'];
					$com_index[$v["nid"]][$a[$v["nid"]]]['furl']=$v['furl'];
					$com_index[$v["nid"]][$a[$v["nid"]]]['eject']=$v['eject'];
					$com_index[$v["nid"]][$a[$v["nid"]]]['type']=$v['type'];
					$com_index[$v["nid"]][$a[$v["nid"]]]['color']=$v['color'];
					$com_index[$v["nid"]][$a[$v["nid"]]]['model']=$v['model'];
					$com_index[$v["nid"]][$a[$v["nid"]]]['bold']=$v['bold'];
					$com_index[$v["nid"]][$a[$v["nid"]]]['sort']=$v['sort'];
					$a[$v["nid"]]++;
			}
		}
		$data['menu_name']=ArrayToString2($com_index);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	//网站地图
	public function navmap_cache($dir)
	{
		$rows=$this->obj->DB_select_all("navmap","`display`='1' order by `sort` desc");
		if(is_array($rows))
		{
			foreach($rows as $key=>$v)
			{
				$navmap[$v['nid']][$key]['id']=$v['id'];
				$navmap[$v['nid']][$key]['name']=$v['name'];
				$navmap[$v['nid']][$key]['url']=$v['url'];
				$navmap[$v['nid']][$key]['furl']=$v['furl'];
				$navmap[$v['nid']][$key]['eject']=$v['eject'];
				$navmap[$v['nid']][$key]['type']=$v['type'];
			}
		}
		$data['navmap']=ArrayToString2($navmap);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	//SEO缓存
	public function seo_cache($dir){
		$rows=$this->obj->DB_select_all("seo");
		if(is_array($rows)){
			foreach($rows as $key=>$v){
				$seo_index[$v["ident"]][$key]['title']=$v["title"];
				$seo_index[$v["ident"]][$key]['keywords']=$v["keywords"];
				$seo_index[$v["ident"]][$key]['description']=$v["description"];
				$seo_index[$v["ident"]][$key]['affiliation']=$v["affiliation"];
				$seo_index[$v["ident"]][$key]['php_url']=$v["php_url"];
				$seo_index[$v["ident"]][$key]['rewrite_url']=$v["rewrite_url"];
			}
		}
		$data['seo']=ArrayToString2($seo_index);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	//域名缓存
	public function domain_cache($dir){
		$rows=$this->obj->DB_select_all("domain","1");
		include(APP_PATH."/plus/city.cache.php");
		include(APP_PATH."/plus/industry.cache.php");
		if(is_array($rows)){
			foreach($rows as $key=>$value){
				if($value["three_cityid"]){
					$site_domain[$key]['cityname'] =$city_name[$value["three_cityid"]];
					$site_domain[$key]['three_cityid']=$value["three_cityid"];
				}else{
					$site_domain[$key]['cityname'] =$city_name[$value["cityid"]];
					$site_domain[$key]['cityid']=$value["cityid"];
				}

				$hyname =$industry_name[$value["hy"]];
				$site_domain[$key]['id']=$value["id"];
				$site_domain[$key]['host']=$value["domain"];
				$site_domain[$key]['hy']=$value["hy"];
				$site_domain[$key]['type']=$value["type"];
				$site_domain[$key]['tpl']=$value["tpl"];
				$site_domain[$key]['hyname']=$hyname;
				$site_domain[$key]['style']=$value["style"];
				$site_domain[$key]['fz_type']=$value["fz_type"];
				$site_domain[$key]['webtitle']=$value["webtitle"];
				$site_domain[$key]['webkeyword']=$value["webkeyword"];
				$site_domain[$key]['webmeta']=$value["webmeta"];
				$site_domain[$key]['weblogo']=$value["weblogo"];
			}
		}
		$data['site_domain']=ArrayToString2($site_domain);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function company_cache($dir){
		$rows=$this->obj->DB_select_all("company","1");
		if(is_array($rows)){
			foreach($rows as $v){
				$comname[$v['uid']]=$v['name'];
			}
		}
		$data['company_name']=ArrayToString($comname);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function keyword_cache($dir){
		$rows=$this->obj->DB_select_all("hot_key","`check`='1' ORDER BY `num` DESC");
		if(is_array($rows)){
			foreach($rows as $key=>$value){
				$row[$key]['id']=$value["id"];
				$row[$key]['key_name']=$value["key_name"];
				$row[$key]['num']=$value["num"];
				$row[$key]['type']=$value["type"];
				$row[$key]['size']=$value["size"];
				$row[$key]['color']="#".$value["color"];
				$row[$key]['bold']=$value["bold"];
				$row[$key]['tuijian']=$value["tuijian"];
			}
		}
		$data['keyword']=ArrayToString2($row);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function link_cache($dir){
		$rows=$this->obj->DB_select_all("admin_link","`link_state`='1' ORDER BY `link_sorting` DESC");
		if(is_array($rows)){
			foreach($rows as $key=>$value){
				$row[$key]['id']=$value["id"];
				$row[$key]['link_name']=$value["link_name"];
				$row[$key]['link_url']=$value["link_url"];
				$row[$key]['img_type']=$value["img_type"];
				$row[$key]['pic']=$value["pic"];
				$row[$key]['link_type']=$value["link_type"];
				$row[$key]['domain']=$value["domain"];
				$row[$key]['tem_type']=$value["tem_type"];
			}
		}
		$data['link']=ArrayToString2($row);
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
	public function group_cache($dir){
		$rows=$this->obj->DB_select_all("news_group","1 order by sort asc");
		if(is_array($rows)){
			foreach($rows as $v){
				if($v['keyid']==0){
					$group_index[]=$v['id'];
				}
				if($v['keyid']!=0){
					$grouptype[$v['keyid']][]=$v['id'];
				}
				if($v['rec']=='1'){
					$group_rec[]=$v['id'];
				}
				if($v['rec_news']=='1'){
					$group_recnews[]=$v['id'];
				}
				$groupname[$v['id']]=$v['name'];
			}
		}
		if(!empty($group_rec))
		{
			$data['group_rec']=ArrayToString($group_rec,false);
		}
		if(!empty($group_recnews))
		{
			$data['group_recnews']=ArrayToString($group_recnews,false);
		}
		if(!empty($group_index))
		{
			$data['group_index']=ArrayToString($group_index,false);
		}
		if(!empty($grouptype))
		{
			$data['group_type']=ArrayToString($grouptype);
		}
		if(!empty($groupname))
		{
			$data['group_name']=ArrayToString($groupname);
		}
		return $this->obj->made_web_array($this->cachedir.$dir,$data);
	}
 }
?>