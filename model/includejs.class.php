<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class includejs_controller extends common
{
	function RedLoginHead_action()
	{
		if($_COOKIE['uid']!=""&&$_COOKIE['username']!="")
		{
			if($_COOKIE['remind_num']>0)
			{
				$html.='<div class="header_Remind header_Remind_hover"> <em class="header_Remind_em "><i class="header_Remind_msg"></i></em><div class="header_Remind_list" style="display:none;">';
				if($_COOKIE['usertype']==1)
				{
					$html.='<div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=msg">��������</a><span class="header_Remind_list_r fr">('.$_COOKIE['userid_msg'].')</span></div><div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/friend/index.php?c=applyfriend">�������</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend1'].')</span></div><div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=xin">վ����</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend_message1'].')</span></div><div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=sysnews">ϵͳ��Ϣ</a><span class="header_Remind_list_r fr">('.$_COOKIE['sysmsg1'].')</span></div><div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=commsg">��ҵ�ظ���ѯ</a><span class="header_Remind_list_r fr">('.$_COOKIE['usermsg'].')</span></div>';
				}elseif($_COOKIE['usertype']==2){
					$html.='<div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=hr"class="fl">����ְλ</a><span class="header_Remind_list_r fr">('.$_COOKIE['userid_job'].')</span></div><div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/friend/index.php?c=applyfriend"class="fl">�������</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend2'].')</span></div><div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=xin"class="fl">վ����</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend_message2'].')</span></div><div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=sysnews" class="fl"> ϵͳ��Ϣ</a><span class="header_Remind_list_r fr">('.$_COOKIE['sysmsg2'].')</span></div><div class="header_Remind_list_a"><a href="'.$this->config['sy_weburl'].'/member/index.php?c=msg"class="fl">��ְ��ѯ</a><span class="header_Remind_list_r fr">('.$_COOKIE['commsg'].')</span></div>';
				}
				$html.='</div> </div>';
			}
			$html2= "���ã�<a href=\"".$this->config['sy_weburl']."/member\" ><font color=\"red\">".$_COOKIE['username']."</font></a>��<a href=\"javascript:void(0)\" onclick=\"logout(\'".$this->config['sy_weburl']."/index.php?c=logout\');\">[��ȫ�˳�]</a>";

			$html.='<div class=" fr">'.$html2.'</div>';

			echo "document.write('".$html."');";
		}else{
			$login_url = $this->url("index","login",array(),"1");
			$reg_url = $this->url("index","register",array("usertype"=>"1"),"1");
			$reg_com_url = $this->url("index","register",array("usertype"=>"2"),"1");
			$style = $this->config['sy_weburl']."/template/".$this->config['style'];

			$login='<li><a href="'.$login_url.'">��Ա��¼</a></li>';		
			$user_reg='<li><a href="'.$reg_url.'">����ע��</a></li>';
			$com_reg='<li><a href="'.$reg_com_url.'">��ҵע��</a></li>';
	
			$html='<div class=" fr"><div class="yun_topLogin_cont"><div class="yun_topLogin"><a class="yun_More" href="javascript:void(0)">�û���¼</a><ul class="yun_Moredown" style="display:none">'.$login.'</ul></div><div class="yun_topLogin"> <a class="yun_More" href="javascript:void(0)">�û�ע��</a><ul class="yun_Moredown fn-hide" style="display:none">'.$user_reg.$com_reg.'</ul></div></div></div>';
			if($this->config['sy_qqlogin']=='1'||$this->config['sy_sinalogin']=='1'||$this->config['sy_wxlogin']=='1'){
				$flogin='<div class="fastlogin fr">';
				if($this->config['sy_qqlogin']=='1'){
					$flogin.='<span style="width:70px;"><img src="'.$this->config['sy_weburl'].'/template/'.$this->config['style'].'/images/yun_qq.png" class="png" > <a href="'.$this->url("index","qqconnect",array("c"=>"qqlogin"),'1').'">QQ��¼</a></span>';
				}
				if($this->config['sy_sinalogin']=='1'){
					$flogin.='<span><img src="'.$this->config['sy_weburl'].'/template/'.$this->config['style'].'/images/yun_sina.png" class="png"> <a href="'.$this->url("index","sinaconnect",array(),"1").'">����</a></span>';
				} 
				if($this->config['sy_wxlogin']=='1'){
					$flogin.='<span><img src="'.$this->config['sy_weburl'].'/template/'.$this->config['style'].'/images/yun_wx.png" class="png"> <a href="'.$this->url("index","wxconnect",array(),"1").'">΢��</a></span>';
				}  
				$flogin.='</div>';
				$html.=$flogin;
			}
			
			echo "document.write('".$html."');";
		}
	}
	function DefaultLoginIndex_action()
	{
		if($_COOKIE['usertype']=='1' && $this->uid)
		{
			$member=$this->obj->DB_select_alls("member_statis","resume","a.`uid`='".$this->uid."' and b.`uid`='".$this->uid."'","a.*,b.`photo`");
			if($member[0]['photo']==''){
				$member[0]['photo']=$this->config['sy_weburl']."/".$this->config['sy_member_icon'];
			}
			$this->yunset("member",$member[0]);
		}else if($_COOKIE['usertype']=='2' && $this->uid){ 
			$company=$this->obj->DB_select_alls("company_statis","company","a.`uid`='".$this->uid."' and b.`uid`=a.`uid`","a.`sq_job`,a.`fav_job`,b.`logo`");
			if($company[0]['logo']==''){
				$company[0]['logo']=$this->config['sy_weburl']."/".$this->config['sy_unit_icon'];
			}
			$company=$company[0];
			$company['job']=$this->obj->DB_select_num("company_job","`uid`='".$this->uid."'");
			$company['status2']=$this->obj->DB_select_num("company_job","`edate`<time() and `uid`='".$this->uid."'");
			$this->yunset("company",$company);
		}
		$this->yunset("cookie",$_COOKIE);
		$this->yun_tpl(array('login'));
	}
	
	function Site_action(){
		if($this->config['sy_web_site']=="1"){
			if($_SESSION['cityname']){
				$cityname = $_SESSION['cityname'];
			}else{
				$cityname = $this->config['sy_indexcity'];
			}
			$site_url = $this->url("index","index",array("c"=>"site"),"1");
		    $html = "<div class=\"heder_city_line  icon2\"><div class=\"header_city_h1\">".$cityname."</div><div class=\"header_city_more icon2\"><a href=\"".$site_url."\">�������</a></div></div>";
		} echo "document.write('".$html."');";
	}
	function SiteCity_action()
	{
		if($_POST[cityid]=="nat")
		{
			unset($_SESSION['cityid']);unset($_SESSION['three_cityid']);unset($_SESSION['cityname']);unset($_SESSION['hyclass']);
			if($this->config['sy_indexdomain'])
			{
				$_SESSION['host'] = $this->config['sy_indexdomain'];
			}else{

				$_SESSION['host'] = $this->config['sy_weburl'];
			}
			echo $_SESSION['host'];
			die;
		}
		unset($_SESSION['cityid']);unset($_SESSION['three_cityid']);unset($_SESSION['cityname']);unset($_SESSION['newsite']);unset($_SESSION['host']);unset($_SESSION['did']);unset($_SESSION['hyclass']);
		if((int)$_POST['cityid']>0)
		{
			if(file_exists(APP_PATH."/plus/domain_cache.php"))
			{
				include(APP_PATH."/plus/domain_cache.php");

				if(is_array($site_domain))
				{
					foreach($site_domain as $key=>$value)
					{
						if($value['cityid']==$_POST['cityid'] || $value['three_cityid']==$_POST['cityid'])
						{
							$_SESSION['host'] = $value['host'];
						}
						if($value['three_cityid']==$_POST['cityid'])
						{
							$_SESSION['three_cityid'] = $value['three_cityid'];
						}
					}
				}
			}
			if($_SESSION['host'] && "http://".$_SESSION['host']==$this->config['sy_weburl'] )
			{
				$_SESSION[newsite]="new";
			}
			$_SESSION['host'] = $_SESSION['host']!=""?"http://".$_SESSION['host']:$this->config['sy_weburl'];
			if(!$_SESSION['three_cityid']){
				$_SESSION['cityid'] = $_POST['cityid'];
			}
			$_SESSION['cityname'] = $this->stringfilter($_POST['cityname']);
			echo $_SESSION['host'];
			die;
		}else{
			$this->obj->ACT_layer_msg("�����˷Ƿ�������",8,$_SERVER['HTTP_REFERER']);
		}
	}
	function SiteHy_action(){
		if($_POST['hyid']=="0"){
			unset($_SESSION['cityid']);unset($_SESSION['three_cityid']);unset($_SESSION['cityname']);unset($_SESSION['hyclass']);
			$_SESSION['host'] = $this->config['sy_indexdomain'];
			echo $_SESSION['host'];die;
		}
		unset($_SESSION['cityid']);
		unset($_SESSION['three_cityid']);
		unset($_SESSION['cityname']);
		unset($_SESSION['newsite']);
		unset($_SESSION['host']);
		unset($_SESSION['did']);
		unset($_SESSION['hyclass']);
		if((int)$_POST['hyid']>0)
		{
			if(file_exists(APP_PATH."/plus/domain_cache.php"))
			{
				include(APP_PATH."/plus/domain_cache.php");

				if(is_array($site_domain))
				{
					foreach($site_domain as $key=>$value)
					{
						if($value['hy']==$_POST['hyid'])
						{
							$_SESSION['host'] = $value['host'];
						}
					}
				}
			}
			if($_SESSION['host'] && "http://".$_SESSION['host']==$this->config['sy_weburl'] )
			{
				$_SESSION['newsite']="new";
			}
			$_SESSION['host'] = $_SESSION['host']!=""?"http://".$_SESSION['host']:$this->config['sy_weburl'];
			$_SESSION['hyclass'] = $_POST['hyid'];
			echo $_SESSION['host'];die;
		}else{
			$this->obj->ACT_layer_msg("�����˷Ƿ�������",8,$_SERVER['HTTP_REFERER']);
		}
	}
}