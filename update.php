<?php

set_time_limit(0);
include("global.php");

//��д�汾��
$config=@fopen(APP_PATH."data/db.config.php","w+");
if($config){
	  $db="<?php \r\n";
	  $db.="  \$db_config = array(\r\n";
	  $db.="      'dbtype'=>'".$db_config['dbtype']."',\r\n";
	  $db.="      'dbhost'=>'".$db_config['dbhost']."',\r\n";
	  $db.="      'dbuser'=>'".$db_config['dbuser']."',\r\n";
	  $db.="      'dbpass'=>'".$db_config['dbpass']."',\r\n";
	  $db.="      'dbname'=>'".$db_config['dbname']."',\r\n";
	  $db.="      'def'=>'".$db_config['def']."',\r\n";
	  $db.="      'charset'=>'".$db_config['charset']."',\r\n";
	  $db.="      'timezone'=>'".$db_config['timezone']."',\r\n";
	  $db.="      'coding'=>'".$db_config['coding']."', //����cookie����\r\n";
	  $db.="      'version'=>'3.2',//�汾��\r\n";
	  $db.="    );\r\n";
	  $db.="    \r\n?>";
	}
fwrite($config,$db);
fclose($config);

echo "�汾�������ɹ�����ɾ�����ļ���";


	
?>