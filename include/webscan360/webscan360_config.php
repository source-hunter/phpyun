<?php
if (!defined('WEBSCAN360')) exit('no access!');
define('SITE_PATH', dirname(__FILE__));
include(dirname(dirname(__FILE__))."/data/db.config.php");
return array(
	'DB_HOST'	=>	$db_config['dbhost'],
	'DB_USER'	=>	$db_config['dbuser'],
	'DB_PWD'	=>	$db_config['dbpass'],
	'DB_NAME'	=>	$db_config['dbname'],
	'DB_PREFIX'	=>	$db_config['def'],
	'port'		=>	'3306',
	'DB_TYPE'	=>	'mysql',
	'MID'		=>	'360webscan_phpyun',	//如：webscan360
	'WRITABLE_PATH'	=>'',
	'SITE_URL'	=>	'',
	
);
