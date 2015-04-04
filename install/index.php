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
header("Content-Type: text/html; charset=gb2312");
ob_start();
error_reporting(0);
$i_model = 1;
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'install_function.php';
require_once 'install_var.php';
require_once 'install_lang.php';
require_once 'install_mysql.php';
if(@include (dirname(dirname(__FILE__))."/data/phpyun.lock")) {
	show_view('<div class="centent"><div class="step"><div class="server"><table width="100%"><tbody><tr><td class="td1" width="100">提示信息</td><td class="td1" width="200">&nbsp;</td><td class="td1">&nbsp;</td></tr><tr><td colspan="3" style="width:100%;">你已经安装过PHPyun人才系统，请删除phpyun_lock文件再安装！</td></tr></tbody></table></div></div></div>');
	exit ();
}
if(empty($_GET['step']))
	$_GET['step'] = 'start';
if ($_GET['step'] == 'start'){
	//安装开始
	show_view('<div class="main">
	<textarea class="pact" readonly="readonly">
	
安装协议：

本授权协议适用且仅适用于PHPYUN.3.2 Beta 版本，宿迁鑫潮信息技术有限公司拥有对本授权协议的最终解释权。



I. 协议许可的权利

1. 您可以在完全遵守本最终用户授权协议的基础上，将本软件应用于非商业用途(包括个人用户：不具备法人资格的自然人，以个人名义从事网络威客交易；非盈利性用途：从事非盈利活动的商业机构及非盈利性组织，将PHPYUN 人才系统仅用于产品演示、展示及发布，而并不是用来买卖及盈利的运营活动的)

2. 您可以在协议规定的约束和限制范围内修改 PHPYUN人才网系统 源代码(如果被提供的话)或界面风格以适应您的网站要求。

3. 您拥有使用本软件构建的人才系统中全部招聘信息，求职，用户信息及相关信息的所有权，并独立承担与其内容的相关法律义务。

4. 获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持期限、技术支持方式和技术支持内容，自授权时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。



II. 协议规定的约束和限制

1. 未获商业授权之前，不得将本软件用于商业用途(包括但不限于企业法人经营的企业网站、经营性网站、以盈利为目或实现盈利的网站)。

2. 不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。

3. 无论如何，即无论用途如何、是否经过修改或美化、修改程度如何，只要使用PHPYUN 人才系统 的整体或任何部分，未经书面许可，网站标题的Powered by PHPYun.都必须保留，而不能清除或修改。

4. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。



III. 有限担保和免责声明

1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。

2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。

3. 宿迁鑫潮信息技术有限公司不对使用本软件构建的人才系统中的文章或任务信息承担责任，但在不侵犯用户隐私信息的前提下，保留以任何方式获取用户及商品信息的权利。

有关 phpyun人才网系统! 最终用户授权协议、商业授权与技术服务的详细内容，均由PHPYUN 官方网站独家提供。 宿迁鑫潮信息技术有限公司拥有在不事先通知的情况下，修改授权协议和服务价目表的权力，修改后的协议或价目表对自改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始安装 PHPYUN3.2 Beta，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。 		</textarea>
	</div>
	<div class="bottom">
		<form action="" autocomplete="off" method="get">
		<input name="step" value="checkset" type="hidden">
<input type="submit" value="我同意" name="" class="submit">
<input type="button"  value="我不同意" onclick="window.close();" name="exit" class="submit">
</form>
	</div>');
}elseif ($_GET['step'] == 'checkset') {
	function_check($func_items);
	env_check($env_items);
	dirfile_check($dirfile_items);
	echo(show_env_result($env_items, $dirfile_items, $func_items));
}
elseif ($_GET['step'] == 'sql') {
    $url_this = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
	$url_this = explode('/install/', $url_this);
	$url_this = $url_this[0];
	$form_str ='<div class="step"><ul>
    <li class="on"><em>1</em>检测环境</li>
    <li class="current"><em>2</em>创建数据</li>
    <li><em>3</em>完成安装</li></ul>
<div class="server">
<form id="install" method="post" name="frm_sql" action="index.php?step=data" onsubmit="return checkweb();">
<table width="100%">
<tbody>
    <tr>
        <td class="td1" width="100">数据库信息</td>
        <td class="td1" width="200">&nbsp;</td>
        <td class="td1">&nbsp;</td>
    </tr>
    <tr>
        <td class="tar">网站地址： </td>
        <td><input class="input" type="text" value="'.$url_this.'" name="weburl"></td>
        <td><span class="gray" id="weburl_msg">	站点的url </span></td>
    </tr>
    <tr>
        <td class="tar">数据库服务器：</td>
        <td><input class="input" name="dbhost" type="text" value="localhost"></td>
        <td><span class="gray" id="dbhost_msg">数据库服务器地址，一般为localhost</span></td>
    </tr>
    <tr>
        <td class="tar">数据库用户名：</td>
        <td><input class="input" name="dbuser" type="text" value="root"></td>
        <td><span class="gray" id="dbuser_msg"></span></td>
    </tr>
    <tr>
        <td class="tar">数据库密码：</td>
        <td><input class="input" name="dbpwd" type="text" value=""></td>
        <td></td>
    </tr>
    <tr>
        <td class="tar">数据库名：</td>
        <td><input class="input" name="dbname" type="text" value="phpyun"></td>
        <td><span class="gray" id="dbname_msg"></span></td>
    </tr>
    <tr>
        <td class="tar">数据库表前缀：</td>
        <td><input class="input" name="tablepre" type="text" value="phpyun_"></td>
        <td><span class="gray" id="tablepre_msg">同一数据库运行多个系统时，请修改前缀</span></td>
    </tr>
</tbody>
</table>
<table width="100%">
<tbody>
    <tr>
        <td class="td1" width="100">创始人信息</td>
        <td class="td1" width="200">&nbsp;</td>
        <td class="td1">&nbsp;</td>
    </tr>
    <tr>
        <td class="tar">管理员帐号：</td>
        <td><input class="input" name="username" type="text" value="admin"></td>
        <td><span class="gray" id="username_msg"></span></td>
    </tr>
    <tr>
        <td class="tar">密码：</td>
        <td><input class="input" type="password" name="password" value="admin"></td>
        <td><span class="gray" id="password_msg">默认密码：admin</span></td>
    </tr>
    <tr>
        <td class="tar">重复密码：</td>
        <td><input class="input" name="password2" type="password" value="admin"></td>
        <td><span class="gray" id="password2_msg">默认密码：admin</span></td>
    </tr>
    <tr>
        <td class="tar">Email：</td>
        <td><input class="input" type="text" value="admin@admin.com" name="manager_email"></td>
        <td></td>
    </tr>
</tbody>
</table>
</div>
</div>
<div class="bottom">
<input type="submit" value="确定" name="setup_sql" class="submit">
<input type="button" value="返回" onclick="history.back()" name="exit" class="submit">
</form>
	</div>';
		show_header();
		echo ($form_str);
		show_footer();

}elseif ($_GET['step'] == 'data') {
	if ($_POST[setup_sql]) {
		$manager_email = $_POST['manager_email'];
		$weburl = $_POST['weburl'];
		$dbhost = $_POST['dbhost'];
		$dbname = $_POST['dbname'];
		$dbuser = $_POST['dbuser'];
		$dbpwd = $_POST['dbpwd'];
		$username = $_POST[username];
		$name = $_POST[name];
		$password = md5($_POST[password]);
		$password2 = md5($_POST[password2]);
		//判断内容完整性
		if (empty ($dbhost) || empty ($dbname) || empty ($dbuser)) {
			show_view('<div class="centent"><div class="step"><div class="server"><table width="100%"><tbody><tr><td class="td1" width="100">提示信息</td><td class="td1" width="200">&nbsp;</td><td class="td1">&nbsp;</td></tr><tr><td colspan="3" style="width:100%;">你填写的服务器配置资料不完整！<a href="javascript:history.back();">点击返回</a></td></tr></tbody></table></div></div></div>');
		} else {
		 //--------------->>
         $conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
         if($conn==false){
		 show_view('<div class="centent"><div class="step"><div class="server"><table width="100%"><tbody><tr><td class="td1" width="100">提示信息</td><td class="td1" width="200">&nbsp;</td><td class="td1">&nbsp;</td></tr><tr><td colspan="3" style="width:100%;">服务器的用户名或者密码错误！<a href="javascript:history.back();">点击返回</a></td></tr></tbody></table></div></div></div>');exit();}
        //--------------->>
		}//end if
		 $dbname_states="连接";
		if(@mysql_select_db($dbname)==false){
			if(mysql_get_server_info() > '4.1') {
			   mysql_query("CREATE DATABASE `$dbname` DEFAULT CHARACTER SET gbk COLLATE gbk_chinese_ci",$conn);
			   $dbsql=@mysql_select_db($dbname);
			   $dbname_states="建立";
			} else {
				show_view('<div class="centent"><div class="step"><div class="server"><table width="100%"><tbody><tr><td class="td1" width="100">提示信息</td><td class="td1" width="200">&nbsp;</td><td class="td1">&nbsp;</td></tr><tr><td colspan="3" style="width:100%;">您的数据库版本太低,请高于4.1以上！<a href="javascript:history.back();">点击返回</a></td></tr></tbody></table></div></div></div>');
				exit();
			}
		}
	}//end if
	mysql_query("set names 'GBK'");
	echo(show_sql_result($env_items, $dirfile_items, $func_items));
	//判断提交数据 -------------------------->
	echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>".$dbname_states."数据库 $dbname...成功</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
	sleep(1);
			//导入数据表结构
		  $tablepre = $_POST[tablepre];
		  $fp=iconv("utf-8","gbk",@file_get_contents("data/phpyun.sql"));
		  $fp=str_replace("phpyun_",$tablepre,$fp);
		  preg_match_all("/CREATE(.*);/Uis",$fp,$arrdata);
		  $preg="/CREATE TABLE IF NOT EXISTS `(.*)` \(/Uis";
		  foreach($arrdata[0] as $v){
			   $sql=str_replace("\r\n","",$v);
			   preg_match_all($preg,$sql,$tablearr);
			   if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$tablearr[1][0]."'"))==1){				   
				   mysql_query("DROP TABLE `".$tablearr[1][0]."`;");
			   }
			   mysql_query($sql)or die(mysql_error());
			   echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>创建数据表 ".$tablearr[1][0]."...成功</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		  }
		//导入数据内容
		  $fpp=iconv("utf-8","gbk",@file_get_contents("data/phpyun_data.sql"));
		  $fpp=str_replace("phpyun_",$tablepre,$fpp);
		  preg_match_all("/INSERT(.*)\);/Uis",$fpp,$data);
		foreach($data[0] as $v){
		  $sql=str_replace("\r\n","",$v);
		  mysql_query($sql) or die(mysql_error());
		}

		//添加管理员帐户
		$table_user=$tablepre."admin_user";
		$table_config=$tablepre."admin_config";
		mysql_query("INSERT INTO $table_user SET `m_id`='1',`username`='".$username."',`password`='".$password."',`name`='超级管理员',`domain`='0',`lasttime`='0'");
		mysql_query("update $table_config set `config`='$weburl' where `name`='sy_weburl'");
		mysql_query("update $table_config set `config`='$manager_email' where `name`='sy_webemail'");
		
		echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>添加管理员...成功</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		
         $coding=md5($weburl.$name.mktime());
		 $config=@fopen("../data/db.config.php","w+");
  			if($config){
			      $db="<?php \r\n";
                  $db.="  \$db_config = array(\r\n";
		          $db.="      'dbtype'=>'mysql',\r\n";
		          $db.="      'dbhost'=>'$dbhost',\r\n";
		          $db.="      'dbuser'=>'$dbuser',\r\n";
		          $db.="      'dbpass'=>'$dbpwd',\r\n";
		          $db.="      'dbname'=>'$dbname',\r\n";
		          $db.="      'def'=>'$tablepre',\r\n";
		          $db.="      'charset'=>'GBK',\r\n";
		          $db.="      'timezone'=>'PRC',\r\n";
				  $db.="      'coding'=>'$coding', //生成cookie加密\r\n";
				  $db.="      'version'=>'3.2 Beta',//版本号\r\n";
                  $db.="    );\r\n";
                  $db.="    \r\n?>";
				}
			fwrite($config,$db);
            fclose($config);
			mysql_close($conn);
			
		echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>生成系统文件...成功</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>初始化数据...成功</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>更新网站缓存...成功</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		echo "<script>setTimeout('location.href=\"index.php?step=finish\"',2000);</script>";
}elseif ($_GET['step'] == 'finish') {
	@fopen("../data/phpyun.lock", "w+");
	$url_this = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
	$url_this = explode('/install/', $url_this);
	$url_this = $url_this[0];
	//清除之前的SESSION数据
	unset($_SESSION["authcode"]);
	unset($_SESSION["auid"]);
	unset($_SESSION["ausername"]);
	unset($_SESSION["ashell"]);
	unset($_SESSION["md"]);
	unset($_SESSION["tooken"]);
	
	show_view('
	<div class="step"><ul>
    <li class="on"><em>1</em>检测环境</li>
    <li class="on"><em>2</em>创建数据</li>
    <li class="current"><em>3</em>完成安装</li></ul>
	<div class="server_2">
				<div class="info">
				<p class="t">安装成功，欢迎使用PHPYUN人才系统</p>
				<p>PHPYUN人才系统致力于帮助站长提高网站流量，增强网站运营能力，增加网站收入。</p>
				<p>PHPYUN人才系统目前免费提供了QQ互联、整合UC、多套模板、邮件提醒等功能。PHPYUN人才系统将陆续提供更多优质的服务项目。</p>
				<p style="color:#999;">大家在使用过程中，可以通过论坛向我们反馈意见及BUG。<a href="http://bbs.phpyun.com" target="_blank">论坛地址</a></p>
							<br><br><br><br>
							<center>
							<input type="button" value="浏览首页" class="submit" onclick="location.href=\'../index.php\'">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="button" value="后台管理" class="submit" onclick="location.href=\'../admin/index.php\'">
							</center>
							<script type="text/javascript" src="$notice"></script>
				</div>
				</div>');
				echo "<div style=\"display:none\"><script src='".$url_this."/index.php'></script></div>";
}
?>