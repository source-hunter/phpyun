<?php
/*
 * 职位调用
 * ----------------------------------------------------------------------------
 * 分别对 职位，对应招聘公司，公司所属企业级别，认证等级等作操作
 *
 * ============================================================================
*/
function smarty_function_jobsimple($paramer,&$smarty){
	global $db,$db_config,$config;
	$time = time();
	if($config[sy_web_site]=="1")
	{
		if($_SESSION[cityid]>0)
		{
			if($paramer[type]=="urgent")
			{
				$sitesql = "AND b.cityid='$_SESSION[cityid]'";

			}else{

				$sitesql = "AND `cityid`='$_SESSION[cityid]'";
			}

		}

	}
	if($paramer[limit])
	{
		$limit = "limit ".$paramer[limit];

	}
	if($paramer[type]=="bid")
	{
		$query=$db->query("SELECT name,com_name,id,uid FROM $db_config[def]company_job WHERE `xuanshang`!='' AND `state`='1' AND `status`!='1' AND `edate`>='$time' AND `sdate`<='$time' $sitesql  order by `xuanshang` desc $limit");//竞价招聘职位

	}elseif($paramer[type]=="urgent"){

		$query=$db->query("SELECT * FROM $db_config[def]company_job where urgent='1' order by `id` desc $limit");

	}elseif($paramer[type]=="com"){

		$query=$db->query("SELECT * FROM $db_config[def]company order by `uid` desc $limit");

	}elseif($paramer[type]=="job"){

		$query=$db->query("SELECT * FROM $db_config[def]company_job WHERE `edate`>='$time' AND `sdate`<='$time' AND `state`='1' AND `status`!='1' $sitesql order by 'lastupdate' DESC $limit");

	}elseif($paramer[type]=="logo"){

		$query=$db->query("SELECT * FROM $db_config[def]company WHERE `logo`!='' order by `uid` desc limit 4");//热门企业招聘

	}elseif($paramer[type]=="hits"){

		$query=$db->query("SELECT * FROM $db_config[def]company_job WHERE `edate`>='$time' AND `sdate`<='$time' AND `state`='1' AND `status`!='1' $sitesql order by 'hits' DESC limit 3");
	}

	while($rs = $db->fetch_array($query)){
		$list[] = $rs;
	}

	$smarty->assign("$paramer[assign_name]",$list);
}