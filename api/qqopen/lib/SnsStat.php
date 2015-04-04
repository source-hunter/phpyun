<?php
/* *
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
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


