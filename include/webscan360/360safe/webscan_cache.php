<?php
//用户唯一key
//数据回调统计地址
define('WEBSCAN_API_LOG_T', 'http://safe.webscan.360.cn/papi/log');
//版本更新地址
define('WEBSCAN_UPDATE_FILE_T','http://safe.webscan.360.cn/papi/update');
//拦截开关(1为开启，0关闭)
$webscan_switch=1;
//提交方式拦截(1开启拦截,0关闭拦截,post,get,cookie,referre选择需要拦截的方式)
$webscan_post=1;
$webscan_get=1;
$webscan_cookie=1;
$webscan_referre=1;
//后台白名单,后台操作将不会拦截,添加"|"隔开白名单目录下面默认是网址带 admin  /dede/ 放行
$webscan_white_directory='admin|\/dede\/|\/install\/';
//url白名单,可以自定义添加url白名单,默认是对phpcms的后台url放行
//写法：比如phpcms 后台操作url index.php?m=admin php168的文章提交链接post.php?job=postnew&step=post ,dedecms 空间设置edit_space_info.php
$webscan_white_url = array('index.php' => 'admin_dir=admin','post.php' => 'job=postnew&step=post','edit_space_info.php'=>'');
?>