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
	show_view('<div class="centent"><div class="step"><div class="server"><table width="100%"><tbody><tr><td class="td1" width="100">��ʾ��Ϣ</td><td class="td1" width="200">&nbsp;</td><td class="td1">&nbsp;</td></tr><tr><td colspan="3" style="width:100%;">���Ѿ���װ��PHPyun�˲�ϵͳ����ɾ��phpyun_lock�ļ��ٰ�װ��</td></tr></tbody></table></div></div></div>');
	exit ();
}
if(empty($_GET['step']))
	$_GET['step'] = 'start';
if ($_GET['step'] == 'start'){
	//��װ��ʼ
	show_view('<div class="main">
	<textarea class="pact" readonly="readonly">
	
��װЭ�飺

����ȨЭ�������ҽ�������PHPYUN.3.2 Beta �汾����Ǩ�γ���Ϣ�������޹�˾ӵ�жԱ���ȨЭ������ս���Ȩ��



I. Э����ɵ�Ȩ��

1. ����������ȫ���ر������û���ȨЭ��Ļ����ϣ��������Ӧ���ڷ���ҵ��;(���������û������߱������ʸ����Ȼ�ˣ��Ը�����������������ͽ��ף���ӯ������;�����·�ӯ�������ҵ��������ӯ������֯����PHPYUN �˲�ϵͳ�����ڲ�Ʒ��ʾ��չʾ��������������������������ӯ������Ӫ���)

2. ��������Э��涨��Լ�������Ʒ�Χ���޸� PHPYUN�˲���ϵͳ Դ����(������ṩ�Ļ�)�����������Ӧ������վҪ��

3. ��ӵ��ʹ�ñ�����������˲�ϵͳ��ȫ����Ƹ��Ϣ����ְ���û���Ϣ�������Ϣ������Ȩ���������е��������ݵ���ط�������

4. �����ҵ��Ȩ֮�������Խ������Ӧ������ҵ��;��ͬʱ�������������Ȩ������ȷ���ļ���֧�����ޡ�����֧�ַ�ʽ�ͼ���֧�����ݣ�����Ȩʱ�����ڼ���֧��������ӵ��ͨ��ָ���ķ�ʽ���ָ����Χ�ڵļ���֧�ַ�����ҵ��Ȩ�û����з�ӳ����������Ȩ����������������Ϊ��Ҫ���ǣ���û��һ�������ɵĳ�ŵ��֤��



II. Э��涨��Լ��������

1. δ����ҵ��Ȩ֮ǰ�����ý������������ҵ��;(��������������ҵ���˾�Ӫ����ҵ��վ����Ӫ����վ����ӯ��ΪĿ��ʵ��ӯ������վ)��

2. ���öԱ��������֮��������ҵ��Ȩ���г��⡢���ۡ���Ѻ�򷢷������֤��

3. ������Σ���������;��Ρ��Ƿ񾭹��޸Ļ��������޸ĳ̶���Σ�ֻҪʹ��PHPYUN �˲�ϵͳ ��������κβ��֣�δ��������ɣ���վ�����Powered by PHPYun.�����뱣����������������޸ġ�

4. �����δ�����ر�Э������������Ȩ������ֹ��������ɵ�Ȩ�������ջأ����е���Ӧ�������Ρ�



III. ���޵�������������

1. ����������������ļ�����Ϊ���ṩ�κ���ȷ�Ļ��������⳥�򵣱�����ʽ�ṩ�ġ�

2. �û�������Ը��ʹ�ñ�������������˽�ʹ�ñ�����ķ��գ�����δ�����Ʒ��������֮ǰ�����ǲ���ŵ�ṩ�κ���ʽ�ļ���֧�֡�ʹ�õ�����Ҳ���е��κ���ʹ�ñ���������������������Ρ�

3. ��Ǩ�γ���Ϣ�������޹�˾����ʹ�ñ�����������˲�ϵͳ�е����»�������Ϣ�е����Σ����ڲ��ַ��û���˽��Ϣ��ǰ���£��������κη�ʽ��ȡ�û�����Ʒ��Ϣ��Ȩ����

�й� phpyun�˲���ϵͳ! �����û���ȨЭ�顢��ҵ��Ȩ�뼼���������ϸ���ݣ�����PHPYUN �ٷ���վ�����ṩ�� ��Ǩ�γ���Ϣ�������޹�˾ӵ���ڲ�����֪ͨ������£��޸���ȨЭ��ͷ����Ŀ���Ȩ�����޸ĺ��Э����Ŀ����Ըı�֮���������Ȩ�û���Ч�������ı���ʽ����ȨЭ����ͬ˫������ǩ���Э��һ����������ȫ�ĺ͵�ͬ�ķ���Ч������һ����ʼ��װ PHPYUN3.2 Beta��������Ϊ��ȫ��Ⲣ���ܱ�Э��ĸ�������������������������Ȩ����ͬʱ���ܵ���ص�Լ�������ơ�Э����ɷ�Χ�������Ϊ����ֱ��Υ������ȨЭ�鲢������Ȩ��������Ȩ��ʱ��ֹ��Ȩ������ֹͣ�𺦣�������׷��������ε�Ȩ���� 		</textarea>
	</div>
	<div class="bottom">
		<form action="" autocomplete="off" method="get">
		<input name="step" value="checkset" type="hidden">
<input type="submit" value="��ͬ��" name="" class="submit">
<input type="button"  value="�Ҳ�ͬ��" onclick="window.close();" name="exit" class="submit">
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
    <li class="on"><em>1</em>��⻷��</li>
    <li class="current"><em>2</em>��������</li>
    <li><em>3</em>��ɰ�װ</li></ul>
<div class="server">
<form id="install" method="post" name="frm_sql" action="index.php?step=data" onsubmit="return checkweb();">
<table width="100%">
<tbody>
    <tr>
        <td class="td1" width="100">���ݿ���Ϣ</td>
        <td class="td1" width="200">&nbsp;</td>
        <td class="td1">&nbsp;</td>
    </tr>
    <tr>
        <td class="tar">��վ��ַ�� </td>
        <td><input class="input" type="text" value="'.$url_this.'" name="weburl"></td>
        <td><span class="gray" id="weburl_msg">	վ���url </span></td>
    </tr>
    <tr>
        <td class="tar">���ݿ��������</td>
        <td><input class="input" name="dbhost" type="text" value="localhost"></td>
        <td><span class="gray" id="dbhost_msg">���ݿ��������ַ��һ��Ϊlocalhost</span></td>
    </tr>
    <tr>
        <td class="tar">���ݿ��û�����</td>
        <td><input class="input" name="dbuser" type="text" value="root"></td>
        <td><span class="gray" id="dbuser_msg"></span></td>
    </tr>
    <tr>
        <td class="tar">���ݿ����룺</td>
        <td><input class="input" name="dbpwd" type="text" value=""></td>
        <td></td>
    </tr>
    <tr>
        <td class="tar">���ݿ�����</td>
        <td><input class="input" name="dbname" type="text" value="phpyun"></td>
        <td><span class="gray" id="dbname_msg"></span></td>
    </tr>
    <tr>
        <td class="tar">���ݿ��ǰ׺��</td>
        <td><input class="input" name="tablepre" type="text" value="phpyun_"></td>
        <td><span class="gray" id="tablepre_msg">ͬһ���ݿ����ж��ϵͳʱ�����޸�ǰ׺</span></td>
    </tr>
</tbody>
</table>
<table width="100%">
<tbody>
    <tr>
        <td class="td1" width="100">��ʼ����Ϣ</td>
        <td class="td1" width="200">&nbsp;</td>
        <td class="td1">&nbsp;</td>
    </tr>
    <tr>
        <td class="tar">����Ա�ʺţ�</td>
        <td><input class="input" name="username" type="text" value="admin"></td>
        <td><span class="gray" id="username_msg"></span></td>
    </tr>
    <tr>
        <td class="tar">���룺</td>
        <td><input class="input" type="password" name="password" value="admin"></td>
        <td><span class="gray" id="password_msg">Ĭ�����룺admin</span></td>
    </tr>
    <tr>
        <td class="tar">�ظ����룺</td>
        <td><input class="input" name="password2" type="password" value="admin"></td>
        <td><span class="gray" id="password2_msg">Ĭ�����룺admin</span></td>
    </tr>
    <tr>
        <td class="tar">Email��</td>
        <td><input class="input" type="text" value="admin@admin.com" name="manager_email"></td>
        <td></td>
    </tr>
</tbody>
</table>
</div>
</div>
<div class="bottom">
<input type="submit" value="ȷ��" name="setup_sql" class="submit">
<input type="button" value="����" onclick="history.back()" name="exit" class="submit">
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
		//�ж�����������
		if (empty ($dbhost) || empty ($dbname) || empty ($dbuser)) {
			show_view('<div class="centent"><div class="step"><div class="server"><table width="100%"><tbody><tr><td class="td1" width="100">��ʾ��Ϣ</td><td class="td1" width="200">&nbsp;</td><td class="td1">&nbsp;</td></tr><tr><td colspan="3" style="width:100%;">����д�ķ������������ϲ�������<a href="javascript:history.back();">�������</a></td></tr></tbody></table></div></div></div>');
		} else {
		 //--------------->>
         $conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
         if($conn==false){
		 show_view('<div class="centent"><div class="step"><div class="server"><table width="100%"><tbody><tr><td class="td1" width="100">��ʾ��Ϣ</td><td class="td1" width="200">&nbsp;</td><td class="td1">&nbsp;</td></tr><tr><td colspan="3" style="width:100%;">���������û��������������<a href="javascript:history.back();">�������</a></td></tr></tbody></table></div></div></div>');exit();}
        //--------------->>
		}//end if
		 $dbname_states="����";
		if(@mysql_select_db($dbname)==false){
			if(mysql_get_server_info() > '4.1') {
			   mysql_query("CREATE DATABASE `$dbname` DEFAULT CHARACTER SET gbk COLLATE gbk_chinese_ci",$conn);
			   $dbsql=@mysql_select_db($dbname);
			   $dbname_states="����";
			} else {
				show_view('<div class="centent"><div class="step"><div class="server"><table width="100%"><tbody><tr><td class="td1" width="100">��ʾ��Ϣ</td><td class="td1" width="200">&nbsp;</td><td class="td1">&nbsp;</td></tr><tr><td colspan="3" style="width:100%;">�������ݿ�汾̫��,�����4.1���ϣ�<a href="javascript:history.back();">�������</a></td></tr></tbody></table></div></div></div>');
				exit();
			}
		}
	}//end if
	mysql_query("set names 'GBK'");
	echo(show_sql_result($env_items, $dirfile_items, $func_items));
	//�ж��ύ���� -------------------------->
	echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>".$dbname_states."���ݿ� $dbname...�ɹ�</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
	sleep(1);
			//�������ݱ�ṹ
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
			   echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>�������ݱ� ".$tablearr[1][0]."...�ɹ�</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		  }
		//������������
		  $fpp=iconv("utf-8","gbk",@file_get_contents("data/phpyun_data.sql"));
		  $fpp=str_replace("phpyun_",$tablepre,$fpp);
		  preg_match_all("/INSERT(.*)\);/Uis",$fpp,$data);
		foreach($data[0] as $v){
		  $sql=str_replace("\r\n","",$v);
		  mysql_query($sql) or die(mysql_error());
		}

		//��ӹ���Ա�ʻ�
		$table_user=$tablepre."admin_user";
		$table_config=$tablepre."admin_config";
		mysql_query("INSERT INTO $table_user SET `m_id`='1',`username`='".$username."',`password`='".$password."',`name`='��������Ա',`domain`='0',`lasttime`='0'");
		mysql_query("update $table_config set `config`='$weburl' where `name`='sy_weburl'");
		mysql_query("update $table_config set `config`='$manager_email' where `name`='sy_webemail'");
		
		echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>��ӹ���Ա...�ɹ�</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		
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
				  $db.="      'coding'=>'$coding', //����cookie����\r\n";
				  $db.="      'version'=>'3.2 Beta',//�汾��\r\n";
                  $db.="    );\r\n";
                  $db.="    \r\n?>";
				}
			fwrite($config,$db);
            fclose($config);
			mysql_close($conn);
			
		echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>����ϵͳ�ļ�...�ɹ�</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>��ʼ������...�ɹ�</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		echo "<script>\$(document).ready(function(){\$('.server_1 dl').append('<dd>������վ����...�ɹ�</dd>');\$('#kays').attr('scrollTop',$('#kays').attr('scrollHeight'));})</script>";
		echo "<script>setTimeout('location.href=\"index.php?step=finish\"',2000);</script>";
}elseif ($_GET['step'] == 'finish') {
	@fopen("../data/phpyun.lock", "w+");
	$url_this = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
	$url_this = explode('/install/', $url_this);
	$url_this = $url_this[0];
	//���֮ǰ��SESSION����
	unset($_SESSION["authcode"]);
	unset($_SESSION["auid"]);
	unset($_SESSION["ausername"]);
	unset($_SESSION["ashell"]);
	unset($_SESSION["md"]);
	unset($_SESSION["tooken"]);
	
	show_view('
	<div class="step"><ul>
    <li class="on"><em>1</em>��⻷��</li>
    <li class="on"><em>2</em>��������</li>
    <li class="current"><em>3</em>��ɰ�װ</li></ul>
	<div class="server_2">
				<div class="info">
				<p class="t">��װ�ɹ�����ӭʹ��PHPYUN�˲�ϵͳ</p>
				<p>PHPYUN�˲�ϵͳ�����ڰ���վ�������վ��������ǿ��վ��Ӫ������������վ���롣</p>
				<p>PHPYUN�˲�ϵͳĿǰ����ṩ��QQ����������UC������ģ�塢�ʼ����ѵȹ��ܡ�PHPYUN�˲�ϵͳ��½���ṩ�������ʵķ�����Ŀ��</p>
				<p style="color:#999;">�����ʹ�ù����У�����ͨ����̳�����Ƿ��������BUG��<a href="http://bbs.phpyun.com" target="_blank">��̳��ַ</a></p>
							<br><br><br><br>
							<center>
							<input type="button" value="�����ҳ" class="submit" onclick="location.href=\'../index.php\'">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="button" value="��̨����" class="submit" onclick="location.href=\'../admin/index.php\'">
							</center>
							<script type="text/javascript" src="$notice"></script>
				</div>
				</div>');
				echo "<div style=\"display:none\"><script src='".$url_this."/index.php'></script></div>";
}
?>