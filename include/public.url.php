<?php

function get_seo_url($paramer,$config,$seo,$type=''){
	
	
	$rewrite_url = '';
	if($paramer['url'])
	{	
		$urNewArr = @explode(',',$paramer['url']); 
		foreach($urNewArr as $key=>$value)
		{
			if($value)
			{
				$valueNewArr = @explode(':',$value);
				$paramer[$valueNewArr[0]] = $valueNewArr[1];
			}
		}
		unset($paramer['url']);
	}
	
	if($type)
	{
		$typeDir = $config['sy_'.$type.'dir'];
		if($config['sy_'.$type.'domain'])
		{
			$defaultUrl = "http://".$config['sy_'.$type.'domain'];

		}else{
			$defaultUrl = $config['sy_weburl']."/".$typeDir;
		}
	}
	$i=0;
	$url="index.php?";
	if(!$paramer[m]){
		$paramer[m] = 'index';
	}
	
	foreach($seo as $k=>$v){
		
		$urlFileds=array();
		
		if($v[$i]['rewrite_url'] && $v[$i]['php_url']){

			$vUrl = @explode('?',$v[$i]['php_url']);
			
			if($vUrl[1])
			{
				$urlArray = @explode("&",$vUrl[1]);
				foreach($urlArray as $key=>$value)
				{
					$valueArray = @explode('=',$value);
					
					if($valueArray[0])
					{
						$urlFileds[$valueArray[0]] = $valueArray[1];
					}
				}
			
			}
			
			if($type!='')
			{
				$urlDir = array_filter(@explode('/',$vUrl[0]));
				
				if($urlDir[0] == $typeDir)
				{
					$rewrite_url=$defaultUrl.$v[$i]['rewrite_url'];
				}
			}else{
				
			
				if(!$urlFileds['m'])
				{
					$urlFileds['m'] = 'index';
				}
				if((!$paramer['c'] && $paramer['m']==$urlFileds['m'] && !$urlFileds['c']) || ($paramer['c'] && $paramer['m']==$urlFileds['m'] && $paramer['c']==$urlFileds['c']))
				{
					$rewrite_url=$config['sy_weburl'].$type.$v[$i]['rewrite_url'];
				}
			}
		}

		$i++;
	}
	
	if($rewrite_url){

		foreach($paramer as $key=>$value)
		{
			$rewrite_url = str_replace("{".$key."}",$value,$rewrite_url);
		}
		$rewrite_url = str_replace('{page}',"1", $rewrite_url);
		$rewrite_url = preg_replace('/{(.*?)}/',"", $rewrite_url); 

		return $rewrite_url;
	}
	return null;
}

function get_url($paramer,$config,$seo,$type='',$index){

		
		if($type)
		{
			$type = $type;
			$typeDir = $config['sy_'.$type.'dir'];
			if($config['sy_'.$type.'domain'])
			{
				$defaultUrl = "http://".$config['sy_'.$type.'domain'];
				$defaultUrlRewrite = $defaultUrl;
			}else{
				$defaultUrl = $config['sy_weburl']."/".$typeDir;
				$defaultUrlRewrite = $config['sy_weburl']."/".$typeDir;
			}
		}else{
		
			$defaultUrl = $config['sy_weburl'];
			$defaultUrlRewrite = $config['sy_weburl'];
		}
		
	

		if(empty($paramer[con])){
			$con='index';
		}else{
			$con=$paramer[con];
		}
		if(empty($paramer['m'])){
			$m='index';
		}else{
			$m=$paramer['m'];
		}
		if(!empty($paramer[url])){
			$paramers = @explode(",",$paramer[url]);
		}
		if($paramer['wxid'])
		{
			$paramers[] = "wxid:".$_GET['wxid'];
		}

		if($config['sy_seo_rewrite'])
		{
			
			$url=get_seo_url($paramer,$config,$seo,$type);
			if($url){
				return $url;
			}
		


			if($con!='index' && !empty($con))
			{
				$urlarr['con']=str_replace('_','',str_replace('-','',$con));
			}
			if($m!='index' && !empty($m))
			{
				$urlarr['m']=str_replace('_','',str_replace('-','',$m));
			}
			if($paramers){
				$p='';
				foreach($paramers as $v)
				{
					if(!empty($v)){
						$url_info = @explode(":",$v);
					$urlarr[$url_info[0]]=str_replace('_','',str_replace('-','',$url_info[1]));
					}
				}
			}
			if($urlarr){
				foreach($urlarr as $k=>$v)
				{
					$a[]=$k.'_'.$v;
				}
				$urltemp=@implode('-',$a);
				$url.=$urltemp.'.html';
				
				if($type)
				{
					$url=$defaultUrlRewrite."-".$url;
				}else{
					$url=$defaultUrlRewrite."/".$url;
				}
				
				
			}else{
				
				$url=$defaultUrlRewrite;
				
			}
			
		}else{
			if($con=='index' && $m=='index')
			{
				$url.='index.php';
			}elseif($con=='index'){
				$url.='index.php?m='.$m;
			}elseif($m=='index'){
				$url.='index.php?con='.$con;
			}else{
				$url.='index.php?con='.$con.'&m='.$m;
			}
			if($paramers){
				$p='';
				foreach($paramers as $v){
					if(!empty($v)){
					$url_info = @explode(":",$v);
					$p.='&'.$url_info[0].'='.$url_info[1];
					}
				}
				if(strpos($url,'?')){
					$url.=$p;
				}else{
					$url.='?'.substr($p,1);
				}
			}
			$url=$defaultUrl.'/'.$url;
		}
		
		return $url;
}

function get_index_url($paramer,$config,$seo,$type='',$index){

		
	
		if($type)
		{
			$type = $type;
			$typeDir = $config['sy_'.$type.'dir'];
			if($config['sy_'.$type.'domain'])
			{
				$defaultUrl = "http://".$config['sy_'.$type.'domain'];
				$defaultUrlRewrite = $defaultUrl;
			}else{
				$defaultUrl = $config['sy_weburl']."/".$typeDir;
				$defaultUrlRewrite = $config['sy_weburl']."/".$typeDir;
			}
		}else{
		
			$defaultUrl = $config['sy_weburl'];
			$defaultUrlRewrite = $config['sy_weburl'];
		}
		
		
		
		if($config['sy_seo_rewrite'] && $index=="1")
		{
			
			$url=get_seo_url($paramer,$config,$seo);
			if($url){
				return $url;
			}
			$con = $paramer['con'];
			$m = $paramer['m'];
			unset($paramer['con']);unset($paramer['m']);
			
			
			if($con!='index' && !empty($con))
			{
				$urlarr['con']=str_replace('_','',str_replace('-','',$con));
			}
			if(!empty($m))
			{
				$urlarr['m']=str_replace('_','',str_replace('-','',$m));
			}
			if($paramer)
			{
				$p='';
				foreach($paramer as $k=>$v)
				{
					if(!empty($v))
					{
					$urlarr[$k]=str_replace('_','',str_replace('-','',$v));
					}
				}
			}
			if($urlarr)
			{
				foreach($urlarr as $k=>$v)
				{
					$a[]=$k.'_'.$v;
				}
				$urltemp=@implode('-',$a);
				$url.=$urltemp.'.html';
				
				if($type)
				{
					$url=$defaultUrlRewrite."-".$url;
				}else{
					$url=$defaultUrlRewrite."/".$url;
				}
				
				
			}else{
				
				$url=$defaultUrlRewrite;
				
			}
		}else{
			$con = $paramer['con'];
			$m = $paramer['m'];
			unset($paramer['con']);unset($paramer['m']);

			if($con=='index' && $m=='index')
			{
				$url.='index.php';
			}elseif($con=='index'){
				$url.='index.php?m='.$m;
			}elseif($m=='index'){
				$url.='index.php?con='.$con;
			}else{
				$url.='index.php?con='.$con.'&M='.$m;
			}
			if($paramer)
			{
				$p='';
				foreach($paramer as $k=>$v)
				{
					if(!empty($v))
					{
					$p.='&'.$k.'='.$v;
					}
				}
				if(strpos($url,'?'))
				{
					$url.=$p;
				}else{
					$url.='?'.substr($p,1);
				}
			}
			$url=$defaultUrl.'/'.$url;
		}
		
		return $url;
}
?>