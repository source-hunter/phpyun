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
if($config['sy_web_site']=="1"){

	$host =  "http://".$_SERVER['HTTP_HOST'];
	if(!strpos($host,"localhost")&&!strpos($host,"127.0.0.1"))
	{
		$config['sy_old_weburl'] = $config['sy_weburl'];
		$config['sy_weburl'] = $host;

		if($host!=$_SESSION['host'] || $_SESSION['newsite']=="new"){
			if(file_exists(APP_PATH."/plus/domain_cache.php"))
			{
				include(APP_PATH."/plus/domain_cache.php");
				include(APP_PATH."/plus/city.cache.php");

				if(is_array($site_domain))
				{
					unset($_SESSION['cityid']);unset($_SESSION['three_cityid']);unset($_SESSION['cityname']);
					unset($_SESSION['host']);unset($_SESSION['did']);unset($_SESSION['webtitle']);
					unset($_SESSION['webkeyword']);unset($_SESSION['webmeta']);unset($_SESSION['weblogo']);
					foreach($site_domain as $key=>$value){
						if($value['host']==$_SERVER['HTTP_HOST']){
							$_SESSION['did']=$value['id'];
							if($value['three_cityid']>0){
								$_SESSION['three_cityid']=$value['three_cityid'];
								$_SESSION['cityname'] = $city_name[$value['three_cityid']];
							}else{
								$_SESSION['cityid']=$value['cityid'];
								$_SESSION['cityname'] = $city_name[$value['cityid']];
							}
							$_SESSION['webtitle']   =$value['webtitle'];
							$_SESSION['weblogo']    =$value['weblogo'];
							$_SESSION['webkeyword'] =$value['webkeyword'];
							$_SESSION['webmeta']    =$value['webmeta'];
							$config['style']        =$value['style'];
							$_SESSION['host']       =$host;
						}
					}
				}
			}

		}
	}
}
?>