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


class SnsNetwork
{
	
	static public function makeRequest($url, $params, $cookie, $method='post', $protocol='http')
	{
		$query_string = self::makeQueryString($params);
	    $cookie_string = self::makeCookieString($cookie);

	    $ch = curl_init();

	    if ('get' == $method)
	    {
		    curl_setopt($ch, CURLOPT_URL, "$url?$query_string");
	    }
	    else
        {
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
	    }

	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

	    if (!empty($cookie_string))
	    {
	    	curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
	    }

	    if ('https' == $protocol)
	    {
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    }

	    $ret = curl_exec($ch);
	    $err = curl_error($ch);

	    if (false === $ret || !empty($err))
	    {
		    $errno = curl_errno($ch);
		    $info = curl_getinfo($ch);
		    curl_close($ch);

	        return array(
	        	'result' => false,
	        	'errno' => $errno,
	            'msg' => $err,
	        	'info' => $info,
	        );
	    }

       	curl_close($ch);

        return array(
        	'result' => true,
            'msg' => $ret,
        );

	}

	static private function makeQueryString($params)
	{
		if (is_string($params))
			return $params;

		$query_string = array();
	    foreach ($params as $key => $value)
	    {
	        array_push($query_string, rawurlencode($key) . '=' . rawurlencode($value));
	    }
	    $query_string = join('&', $query_string);
	    return $query_string;
	}

	static private function makeCookieString($params)
	{
		if (is_string($params))
			return $params;

		$cookie_string = array();
	    foreach ($params as $key => $value)
	    {
	        array_push($cookie_string, $key . '=' . $value);
	    }
	    $cookie_string = join('; ', $cookie_string);
	    return $cookie_string;
	}
}


