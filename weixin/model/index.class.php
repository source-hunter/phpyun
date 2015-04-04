<?php
/* *
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
*/
header("Content-type: text/html; charset=utf-8");

class index_controller extends common
{
	public $MsgType;
	public function index_action()
	{
		
		if($_GET["echostr"])
		{
			$this->valid();
		}else{
			if(!$this->checkSignature()){echo "非法来源地址！";exit();};

			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
			if (!empty($postStr))
			{
			  $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);  
			  $fromUsername = $postObj->FromUserName;
			  $toUsername = $postObj->ToUserName;
			  $keyword = trim($postObj->Content);
			  $times = time();
			  $MsgType = $postObj->MsgType;
			 
			  $topTpl = "<xml>
				   <ToUserName><![CDATA[%s]]></ToUserName>
				   <FromUserName><![CDATA[%s]]></FromUserName>
				   <CreateTime>%s</CreateTime>
				   <MsgType><![CDATA[%s]]></MsgType>
				   ";
			 
			  $bottomStr = "<FuncFlag>0</FuncFlag></xml>";
			  
			  if($MsgType=='event')
			  {
				$MsgEvent = $postObj->Event;
				if ($MsgEvent=='subscribe')
				{
					$centerStr = "<Content><![CDATA[欢迎您关注".iconv('gbk','utf-8',$this->config['sy_webname'])."！\n 1：您可以直接回复关键字如【销售】、【销售 XX公司】查找您想要的职位\n绑定您的账户体验更多精彩功能\n感谢您的关注！]]></Content>";
					$this->MsgType = 'text';

				}elseif ($MsgEvent=='CLICK')
				{
					$EventKey = $postObj->EventKey;
					if($EventKey=='我的帐号'){
						$centerStr = $this->bindUser($fromUsername);

					}elseif($EventKey=='我的消息')
					{
						$centerStr = $this->myMsg($fromUsername);
					}elseif($EventKey=='面试邀请')
					{
						$centerStr = $this->Audition($fromUsername);

					}elseif($EventKey=='简历查看')
					{

						$centerStr = $this->lookResume($fromUsername);

					}elseif($EventKey=='刷新简历')
					{

						$centerStr = $this->refResume($fromUsername);

					}elseif($EventKey=='推荐职位')
					{
						$centerStr = $this->recJob();

					}elseif($EventKey=='职位搜索'){
						
						$centerStr = "<Content><![CDATA[直接回复城市、职位、公司名称等关键字搜索您需要的职位信息。\n 如：【经理】、【xx公司】]]></Content>";
						$this->MsgType = 'text';
					}elseif($EventKey=='周边职位'){
						
						$centerStr = "<Content><![CDATA[/可怜 亲，把您的位置先发我一下。\n\n方法：点屏幕左下角输入框旁的“+”，选择“位置”，点“发送”。]]></Content>";
						$this->MsgType = 'text';
					}
				}
			  }elseif($MsgType=='location'){
					 $latitude = $postObj->Location_X;
					 $longitude = $postObj->Location_Y;
					 $url = "http://api.map.baidu.com/geocoder/v2/?ak=42966293429086ba859198a2a69bedad&callback=renderReverse&location=". $latitude.",".$longitude."&output=json";
					 $mapinfo = file_get_contents($url);
					 $mapinfo = str_replace(array('renderReverse&&renderReverse(',')'),'',$mapinfo);
				     $map_info = json_decode($mapinfo,true);
					
					 if($map_info['result']['addressComponent']['district'])
					 {
						$centerStr = $this->searchJob($map_info['result']['addressComponent']['district'],1);
					 }
			  
			  }elseif($MsgType=='text'){
				if($keyword){
					
					$centerStr = $this->searchJob($keyword);
				}
			  }
			 
			  $topStr = sprintf($topTpl, $fromUsername, $toUsername, $times, $this->MsgType);
			  echo $topStr.$centerStr.$bottomStr;
			}
		}
	}
	
	private function myMsg($wxid='')
	{
		$userBind = $this->isBind($wxid);
		
		if($userBind['bindtype']=='1')
		{
			$this->MsgType = 'text';
			$centerStr = "<Content><![CDATA[您最新没有新的消息！]]></Content>";
			return $centerStr;
			
		}else{
			$this->MsgType = 'text';
			return $userBind['cenetrTpl'];
		}
	}

	private function Audition($wxid='')
	{
		$userBind = $this->isBind($wxid);
		if($userBind['bindtype']=='1')
		{
			$Aud = $this->obj->DB_select_all("userid_msg","`uid`='".$userBind['uid']."' ORDER BY `datetime` DESC limit 5");
			
			if(is_array($Aud) && !empty($Aud))
			{
				foreach($Aud as $key=>$value)
				{
					$Info['title'] = "【".iconv('gbk','utf-8',$value['fname'])."】邀您面试\n邀请时间：".date('Y-m-d H:i:s');
					$Info['pic']   = $this->config['sy_weburl'].'/data/wx/jt.jpg';
					$Info['url']   = $this->config['sy_weburl']."/wap/member/index.php?c=invite";
					$List[]        = $Info;
				}
				$Msg['title'] = '面试邀请';
				$Msg['pic'] = $this->config['sy_weburl'].'/'.$this->config['sy_wx_logo'];
				$Msg['url'] = $this->config['sy_weburl']."/wap/member/index.php?c=invite";
				$articleTpl = $this->Handle($List,$Msg);
			}else{

				$articleTpl='<Content><![CDATA[最近暂无面试邀请]]></Content>';
				$this->MsgType = 'text';
			}
			return $articleTpl;
		}else{
			$this->MsgType = 'text';
			return $userBind['cenetrTpl'];
		}
	}

	private function lookResume($wxid='')
	{
		$userBind = $this->isBind($wxid);
		if($userBind['bindtype']=='1')
		{
			$Aud = $this->obj->DB_select_all("look_resume","`uid`='".$userBind['uid']."'  ORDER BY `datetime`  DESC limit 5");
			if(is_array($Aud) && !empty($Aud))
			{
				
				foreach($Aud as $key=>$value)
				{
					$comid[] = $value['com_id'];
				}
				$comids = @implode(',',$comid);
		
				if($comids){
					$comList = $this->obj->DB_select_all('company','`uid` IN ('.$comids.')','`uid`,`name`');
					if(is_array($comList)){
						foreach($comList as $key=>$value)
						{
							$comname[$value['uid']] = $value['name'];
						}
					}
					foreach($Aud as $key=>$value)
					{
						$Info['title'] = "查看企业：【".iconv('gbk','utf-8',$comname[$value['com_id']])."】\n查看时间：".date('Y-m-d H:i:s',$value['datetime']);
						$Info['pic']   = $this->config['sy_weburl'].'/data/wx/jt.jpg';
						$Info['url']   = $this->config['sy_weburl']."/wap/member/index.php?c=look";
						$List[]        = $Info;
					}
					$Msg['title'] = '最近查看我的简历';
					$Msg['pic'] = $this->config['sy_weburl'].'/'.$this->config['sy_wx_logo'];
					$Msg['url'] = $this->config['sy_weburl']."/wap/member/index.php?c=look";
					$articleTpl = $this->Handle($List,$Msg);
				}else{
					$articleTpl='<Content><![CDATA[已经很久没公司查看您的简历了！]]></Content>';
					$this->MsgType = 'text';
				}
			}else{

				$articleTpl='<Content><![CDATA[已经很久没公司查看您的简历了！]]></Content>';
				$this->MsgType = 'text';
			}
			return $articleTpl;

		}else{
	
			$this->MsgType = 'text';
			return $userBind['cenetrTpl'];
		}
	}

	private function refResume($wxid='')
	{
		$userBind = $this->isBind($wxid);
		if($userBind['bindtype']=='1')
		{
			$Resume = $this->obj->DB_select_num("resume_expect","`uid`='".$userBind['uid']."'");
			
			if($Resume>0)
			{
				$this->obj->DB_update_all("resume_expect","`lastupdate`='".time()."'","`uid` = '".$userBind['uid']."'");
				$articleTpl="<Content><![CDATA[简历刷新成功\n刷新时间:".date('Y-m-d H:i:s')."]]></Content>";

			}else{

				$articleTpl='<Content><![CDATA[请先完善您的简历！]]></Content>';
				
			}
			$this->MsgType = 'text';
			return $articleTpl;
		}else{
			$this->MsgType = 'text';
			return $userBind['cenetrTpl'];
		}
	}
	private function searchJob($keyword)
	{

		$keyword = trim($keyword);
		
		include(APP_PATH."/plus/city.cache.php");
		if($keyword)
		{
			$keywords = @explode(' ',$keyword);
		
			if(is_array($keywords))
			{
				foreach($keywords as $key=>$value)
				{
					if($value!='')
					{
						$searchJob[] = "(`name` LIKE '%".$this->stringfilter(trim($value))."%') OR (`com_name` LIKE '%".$this->stringfilter(trim($value))."%')";

						foreach($city_name as $k=>$v)
						{
							if(strpos($v,iconv('utf-8','gbk',trim($value)))!==false)
							{
								$CityId[] = $k;
							}
						}
					}
				}
				
				$searchWhere = "`sdate`<='".time()."' AND `edate`>= '".time()."' AND `status`<>'1' AND `r_status`<>'1' AND (".implode(' OR ',$searchJob).")";
				if(!empty($CityId))
				{
					$City_id = implode(',',$CityId);
					$searchWhere .= " AND (`provinceid` IN (".$City_id.") OR `cityid` IN (".$City_id.") OR `three_cityid` IN (".$City_id."))";
				}
				echo $searchWhere;
				$jobList = $this->obj->DB_select_all("company_job",$searchWhere." order by `lastupdate` desc limit 5","`id`,`name`,`com_name`");
			}
		}	
	
		if(is_array($jobList) && !empty($jobList))
		{

			foreach($jobList as $key=>$value)
			{
				$Info['title'] = "【".iconv('gbk','utf-8',$value['name'])."】\n".iconv('gbk','utf-8',$value['com_name']);
				$Info['pic'] = $this->config['sy_weburl'].'/data/wx/gt.jpg';
				$Info['url'] = $this->config['sy_weburl']."/wap/index.php?m=com&c=view&id=".$value['id'];
				$List[]     = $Info;
			}
			$Msg['title'] = '与【'.$keyword.'】相关的职位';
			$Msg['pic'] = $this->config['sy_weburl'].'/'.$this->config['sy_wx_logo'];
			$Msg['url'] = $this->config['sy_weburl'].'/wap/index.php?m=com';
			
			$articleTpl = $this->Handle($List,$Msg);
		}else{

			$articleTpl='<Content><![CDATA[未找到合适的职位！]]></Content>';
			$this->MsgType = 'text';
		}
		
		return $articleTpl;
		
	}
	
	private function bindUser($wxid='')
	{
	
		$bindType = $this->isBind($wxid);
		$this->MsgType = 'text';
		
		return $bindType['cenetrTpl'];
		
	}
	private function isBind($wxid='')
	{
	
		if($wxid)
		{
			$User = $this->obj->DB_select_once("member","`wxid`='".$wxid."'","`uid`,`username`,`usertype`");
		}
		if($User['uid']>0)
		{
			if($User['usertype']=='2')
			{
				$User['cenetrTpl'] = "<Content><![CDATA[您的".iconv('gbk','utf-8',$this->config['sy_webname'])."帐号：".$User['username']."为企业帐号，请登录您的个人帐号进行绑定！ \n\n\n 您也可以<a href=\"".$this->config['sy_weburl']."/wap/index.php?m=login&bind=1&wxid=".$wxid."\">点击这里</a>进行解绑或绑定其他帐号]]></Content>";
			}else{
				$User['bindtype'] = '1';
				$User['cenetrTpl'] = "<Content><![CDATA[您的".iconv('gbk','utf-8',$this->config['sy_webname'])."帐号：".$User['username']."已成功绑定！ \n\n\n 您也可以<a href=\"".$this->config['sy_weburl']."/wap/index.php?m=login&wxid=".$wxid."\">点击这里</a>进行解绑或绑定其他帐号]]></Content>";
			}
			

		}else{

			$Token = $this->getToken();
			$Url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$Token.'&openid='.$wxid.'&lang=zh_CN';
			$CurlReturn  = $this->CurlPost($Url);
			$UserInfo    = json_decode($CurlReturn);
			
			$wxid        = $wxid;
			$wxname      = $UserInfo->nickname;
			$this->config['token_time'] = time();

			$User['cenetrTpl'] = '<Content><![CDATA[您还没有绑定帐号，<a href="'.$this->config['sy_weburl'].'/wap/index.php?m=login&wxid='.$wxid.'">点击这里</a>进行绑定!]]></Content>';
		}

		return $User;
	}
	private function recJob()
	{

		$JobList = $this->obj->DB_select_all("company_job","`sdate`<='".time()."' AND `edate`>= '".time()."' AND `status`<>1 AND `r_status`<>1 AND `rec_time`>'".time()."' order by `lastupdate` desc limit 5","`id`,`name`,`com_name`,`lastupdate`");
		
		if(is_array($JobList) && !empty($JobList))
		{
			foreach($JobList as $key=>$value)
			{
				$Info['title'] = "【".iconv('gbk','utf-8',$value['name'])."】\n".iconv('gbk','utf-8',$value['com_name']);
				$Info['pic'] = $this->config['sy_weburl'].'/data/wx/jt.jpg';
				$Info['url'] = $this->config['sy_weburl']."/wap/index.php?m=com&c=view&id=".$value['id'];
				$List[]        = $Info;
			}
			$Msg['title'] = '推荐职位';
			$Msg['pic'] = $this->config['sy_weburl'].'/'.$this->config['sy_wx_logo'];
			$Msg['url'] = $this->config['sy_weburl'].'/wap/index.php?m=com';
			$articleTpl = $this->Handle($List,$Msg);
			
		}else{
			$articleTpl='<Content><![CDATA[没有合适的职位！]]></Content>';
			$this->MsgType = 'text';
		}
		
		return $articleTpl;
	}

	private function Handle($List,$Msg)
	{

		$articleTpl = '<Content><![CDATA['.$Msg['title'].']]></Content>';

		$articleTpl .= '<ArticleCount>'.(count($List)+1).'</ArticleCount><Articles>';

		$centerTpl = "<item>
		<Title><![CDATA[%s]]></Title>  
		<Description><![CDATA[%s]]></Description>
		<PicUrl><![CDATA[%s]]></PicUrl>
		<Url><![CDATA[%s]]></Url>
		</item>";

		$articleTpl.=sprintf($centerTpl,$Msg['title'],'',$Msg['pic'],$Msg['url']); 

		foreach($List as $value)
		{	
			$articleTpl.=sprintf($centerTpl,$value['title'],'',$value['pic'],$value['url']);
		}
		$articleTpl .= '</Articles>';
		$this->MsgType = 'news';
		return $articleTpl;
	}
	
	

    private function responseMsg()
    {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		
  
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }

	private function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = $this->config['wx_token'];
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature  && $token!=''){
			return true;
		}else{
			return false;
		}
	}

	private function ArrayToString($obj,$withKey=true,$two=false)
	{
		if(empty($obj))	return array();
		$objType=gettype($obj);
		if ($objType=='array') {
			$objstring = "array(";
			foreach ($obj as $objkey=>$objv) {
				if($withKey)$objstring .="\"$objkey\"=>";
				$vtype =gettype($objv) ;
				if ($vtype=='integer') {
				  $objstring .="$objv,";
				}else if ($vtype=='double'){
				  $objstring .="$objv,";
				}else if ($vtype=='string'){
				  $objv= str_replace('"',"\\\"",$objv);
				  $objstring .="\"".$objv."\",";
				}else if ($vtype=='array'){
				  $objstring .="".$this->ArrayToString($objv,false).",";
				}else if ($vtype=='object'){
				  $objstring .="".$this->ArrayToString($objv,false).",";
				}else {
				  $objstring .="\"".$objv."\",";
				}
	    }
		$objstring = substr($objstring,0,-1)."";
		return $objstring.")\n";
	}
}
private function markLog($wxid,$wxuser,$content,$reply){

	$this->obj->DB_insert_once("wxlog","`wxid`='".$wxid."',`wxuser`='".$wxuser."',`content`='".$content."',`reply`='".$reply."',`time`='".time()."'");
}

}

?>
