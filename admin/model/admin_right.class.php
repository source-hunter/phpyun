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
class admin_right_controller extends common
{
	function index_action(){
		global $db_config;

		$this->yunset("db_config",$db_config);
		$base=base64_encode($db_config["coding"]."|phpyun|".$this->config["sy_webname"]."|phpyun|".$this->config["sy_weburl"]."|phpyun|".$this->config["sy_webemail"]."|phpyun|".$db_config["version"]);
		$this->yunset("base",$base);
		$nav_user=$this->obj->DB_select_alls("admin_user","admin_user_group","a.`m_id`=b.`id` and a.`uid`='".$_SESSION['auid']."'");
		$soft=$_SERVER['SERVER_SOFTWARE'];
		$kongjian=round(@disk_free_space(".")/(1024*1024),2);
		$banben=@mysql_get_server_info();
		$yonghu=@get_current_user();
		$server=$_SERVER['SERVER_NAME'];
		$today=strtotime(date("Y-m-d 00:00:00"));
		$comcert=$this->obj->DB_select_num("company_cert","`type`='3' and `status`='0'");
		$company_job=$this->obj->DB_select_num("company_job","`state`='0'",'id');
		$company=$this->obj->DB_select_num("member","`status`='0' and `usertype`='2'",'uid');
		$once_job=$this->obj->DB_select_num("once_job","`status`='0' and `edate`>'".time()."'");
		$admin_link=$this->obj->DB_select_num("admin_link","`link_state`='0' ");
		$company_order=$this->obj->DB_select_num("company_order","`order_state`='3' ");
		if(is_dir("../admin"))$dirname[]="admin";
		if(is_dir("../install"))$dirname[]="install";
		$this->yunset("dirname",@implode(',',$dirname));
		$mruser=$nav_user[0]['username']=="admin" && $nav_user[0]['password']==md5('admin')?1:0;
		$this->yunset("mruser",$mruser);
		$this->yunset("comcert",$comcert);
		$this->yunset("company_job",$company_job);
		$this->yunset("company",$company);
		$this->yunset("once_job",$once_job);
		$this->yunset("admin_link",$admin_link);
		$this->yunset("company_order",$company_order);
		$this->yunset("soft",$soft);
		$this->yunset("kongjian",$kongjian);
		$this->yunset("banben",$banben);
		$this->yunset("yonghu",$yonghu);
		$this->yunset("server",$server);
		$this->yunset("nav_user",$nav_user[0]);
		$this->yuntpl(array('admin/admin_right'));
	}
	function getweb_action(){
		$this->tj("member","reg_date","个人注册统计","usertype=1 and ");
	}
	function comtj_action(){
		$this->tj("member","reg_date","企业注册统计","usertype=2 and");
	}
	function resumetj_action(){
		$this->tj("resume_expect","ctime","简历统计");
	}
	function newstj_action(){
		$this->tj("news_base","datetime","新闻统计");
	}
	function adtj_action(){
		$this->tj("adclick","addtime","广告点击统计");
	}
	function jobtj_action(){
		$this->tj("company_job","lastupdate","职位统计");
	}
	function wzptj_action(){
		$this->tj("once_job","ctime","微招聘统计");
	}
	function wjltj_action(){
		$this->tj("resume_tiny","ctime","微简历统计");
	}
	function payordertj_action(){
		$this->tj("company_order","order_time","充值统计");
	}

	function tj($tablename,$field,$name,$where=''){
		$TimeDate = $this->day();
		$sdate = $TimeDate['sdate'];
		$edate = $TimeDate['edate'];
		$days = $TimeDate['days'];
		$RegWhere = $where." $field >= ".strtotime($sdate)." AND $field <= ".strtotime($edate.' 23:59:59');
		$RegStats = $this->obj->DB_select_all($tablename," $RegWhere GROUP BY td ORDER BY td DESC","FROM_UNIXTIME(`$field`,'%Y%m%d') as td,count(*) as cnt");
		if(is_array($RegStats))
		{
			$AllNum = 0;
			foreach($RegStats as $key=>$value)
			{
				$AllNum +=$value['cnt'];
				$Date[$value['td']] = $value;
			}
			if($days>0)
			{
				for($i=0;$i<=$days;$i++){
					$onday = date("Ymd", strtotime(' -'. $i . 'day'));
					$td    = date('m-d', strtotime(' -'. $i . 'day'));
					$date    = date('Y-m-d', strtotime(' -'. $i . 'day'));
					if(!empty($Date[$onday]))
					{
						$Date[$onday]['td'] = $td;
						$Date[$onday]['date'] = $date;
						$List[$onday] = $Date[$onday];
					}else{
						$List[$onday] = array('td'=>$td,'cnt'=>0,'date'=>$date);
					}
				}
			}
		}
		ksort($List);
		$this->yunset('AllNum',$AllNum);
		$this->yunset('list',$List);
		$this->yunset('name',$name);
		$this->yuntpl(array('admin/admin_right_web'));
	}

	function day(){

		if((int)$_GET['days']>0)
		{
			$days = (int)$_GET['days'];
			$sdate = date('Y-m-d',(time()-$days*86400));
			$edate = date('Y-m-d');
		}elseif($_GET['sdate']){
			$sdate = $_GET['sdate'];
			$days = ceil(abs(time() - strtotime($sdate))/86400);

			if($_GET['edate'])
			{
				$edate = $_GET['edate'];
				$days = ceil(abs(strtotime($edate) - strtotime($sdate))/86400);			}
		}else{
			$days = 30;
			$sdate = date('Y-m-d',(time()-$days*86400));
			$edate = date('Y-m-d');
		}

		return array('sdate'=>$sdate,'days'=>$days,'edate'=>$edate);

	}
}

?>