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


class SnsStat
{
	
	static public function statReport($stat_url, $start_time, $params)
	{
		$end_time = self::getTime();
		$params['time'] = round($end_time - $start_time, 4);
		$params['timestamp'] = time();
		$params['collect_point'] = 'sdk-php-v3';
		$stat_str = json_encode($params);
		
		$host_ip = gethostbyname($stat_url);
		if ($host_ip != $stat_url)
		{
			$sock = socket_create(AF_INET, SOCK_DGRAM, 0);
			if (false === $sock)
			{
				return;
			}
			socket_sendto($sock, $stat_str, strlen($stat_str), 0, $host_ip, 19888);
			socket_close($sock);
		}
	}

	static public function getTime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}


