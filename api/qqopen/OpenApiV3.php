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

require_once 'lib/SnsNetwork.php';
require_once 'lib/SnsSigCheck.php';
require_once 'lib/SnsStat.php';


if (!function_exists('curl_init'))
{
	throw new Exception('OpenAPI needs the cURL PHP extension.');
}

if (!function_exists('json_decode'))
{
	throw new Exception('OpenAPI needs the JSON PHP extension.');
}


define('OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY', 2001); 
define('OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID', 2002); 
define('OPENAPI_ERROR_RESPONSE_DATA_INVALID', 2003); 
define('OPENAPI_ERROR_CURL', 3000); 


class OpenApiV3
{
	private $appid  = 0;
	private $appkey = '';
	private $server_name = '';
	private $format = 'json';
	private $stat_url = "apistat.tencentyun.com";
	private $is_stat = true;

	
	function __construct($appid, $appkey)
	{
		$this->appid = $appid;
		$this->appkey = $appkey;
	}

	public function setServerName($server_name)
	{
		$this->server_name = $server_name;
	}

	public function setStatUrl($stat_url)
	{
		$this->stat_url = $stat_url;
	}

	public function setIsStat($is_stat)
	{
		$this->is_stat = $is_stat;
	}

	
	public function api($script_name, $params, $method='post', $protocol='http')
	{
		
		if (!isset($params['openid']) || empty($params['openid']))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'openid is empty');
		}
		
		if (!isset($params['openkey']) || empty($params['openkey']))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'openkey is empty');
		}
		
		if (!self::isOpenId($params['openid']))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
				'msg' => 'openid is invalid');
		}

		
		unset($params['sig']);

		
		$params['appid'] = $this->appid;
		$params['format'] = $this->format;

		
		$secret = $this->appkey . '&';
		$sig = SnsSigCheck::makeSig( $method, $script_name, $params, $secret);
		$params['sig'] = $sig;

		$url = $protocol . '://' . $this->server_name . $script_name;
		$cookie = array();

		
		$start_time = SnsStat::getTime();

		
		$ret = SnsNetwork::makeRequest($url, $params, $cookie, $method, $protocol);

		if (false === $ret['result'])
		{
			$result_array = array(
				'ret' => OPENAPI_ERROR_CURL + $ret['errno'],
				'msg' => $ret['msg'],
			);
		}

		$result_array = json_decode($ret['msg'], true);

		
		if (is_null($result_array)) {
			$result_array = array(
				'ret' => OPENAPI_ERROR_RESPONSE_DATA_INVALID,
				'msg' => $ret['msg']
			);
		}

		
		if ($this->is_stat)
		{
			$stat_params = array(
					'appid' => $this->appid,
					'pf' => $params['pf'],
					'rc' => $result_array['ret'],
					'svr_name' => $this->server_name,
					'interface' => $script_name,
					'protocol' => $protocol,
					'method' => $method,
			);
			SnsStat::statReport($this->stat_url, $start_time, $stat_params);
		}

		return $result_array;
	}

	
	private static function isOpenId($openid)
	{
		return (0 == preg_match('/^[0-9a-fA-F]{32}$/', $openid)) ? false : true;
	}
}


