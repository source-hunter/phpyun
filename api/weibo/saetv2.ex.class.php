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
class OAuthException extends Exception {
	
}



class SaeTOAuthV2 {
	
	public $client_id;
	
	public $client_secret;
	
	public $access_token;
	
	public $refresh_token;
	
	public $http_code;
	
	public $url;
	
	public $host = "https://api.weibo.com/2/";
	
	public $timeout = 30;
	
	public $connecttimeout = 30;
	
	public $ssl_verifypeer = FALSE;
	
	public $format = 'json';
	
	public $decode_json = TRUE;
	
	public $http_info;
	
	public $useragent = 'Sae T OAuth2 v0.1';

	
	public $debug = FALSE;

	
	public static $boundary = '';

	
	function accessTokenURL()  { return 'https://api.weibo.com/oauth2/access_token'; }
	
	function authorizeURL()    { return 'https://api.weibo.com/oauth2/authorize'; }

	
	function __construct($client_id, $client_secret, $access_token = NULL, $refresh_token = NULL) {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->access_token = $access_token;
		$this->refresh_token = $refresh_token;
	}

	
	function getAuthorizeURL( $url, $response_type = 'code', $state = NULL, $display = NULL ) {
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['redirect_uri'] = $url;
		$params['response_type'] = $response_type;
		$params['state'] = $state;
		$params['display'] = $display;
		return $this->authorizeURL() . "?" . http_build_query($params);
	}

	
	function getAccessToken( $type = 'code', $keys ) {
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['client_secret'] = $this->client_secret;
		if ( $type === 'token' ) {
			$params['grant_type'] = 'refresh_token';
			$params['refresh_token'] = $keys['refresh_token'];
		} elseif ( $type === 'code' ) {
			$params['grant_type'] = 'authorization_code';
			$params['code'] = $keys['code'];
			$params['redirect_uri'] = $keys['redirect_uri'];
		} elseif ( $type === 'password' ) {
			$params['grant_type'] = 'password';
			$params['username'] = $keys['username'];
			$params['password'] = $keys['password'];
		} else {
			throw new OAuthException("wrong auth type");
		}

		$response = $this->oAuthRequest($this->accessTokenURL(), 'POST', $params);
		$token = json_decode($response, true);
		if ( is_array($token) && !isset($token['error']) ) {
			$this->access_token = $token['access_token'];
			
		} else {
			throw new OAuthException("get access token failed." . $token['error']);
		}
		return $token;
	}

	
	function parseSignedRequest($signed_request) {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
		$sig = self::base64decode($encoded_sig) ;
		$data = json_decode(self::base64decode($payload), true);
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') return '-1';
		$expected_sig = hash_hmac('sha256', $payload, $this->client_secret, true);
		return ($sig !== $expected_sig)? '-2':$data;
	}

	
	function base64decode($str) {
		return base64_decode(strtr($str.str_repeat('=', (4 - strlen($str) % 4)), '-_', '+/'));
	}

	
	function getTokenFromJSSDK() {
		$key = "weibojs_" . $this->client_id;
		if ( isset($_COOKIE[$key]) && $cookie = $_COOKIE[$key] ) {
			parse_str($cookie, $token);
			if ( isset($token['access_token']) && isset($token['refresh_token']) ) {
				$this->access_token = $token['access_token'];
				$this->refresh_token = $token['refresh_token'];
				return $token;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	
	function getTokenFromArray( $arr ) {
		if (isset($arr['access_token']) && $arr['access_token']) {
			$token = array();
			$this->access_token = $token['access_token'] = $arr['access_token'];
			if (isset($arr['refresh_token']) && $arr['refresh_token']) {
				$this->refresh_token = $token['refresh_token'] = $arr['refresh_token'];
			}

			return $token;
		} else {
			return false;
		}
	}

	
	function get($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'GET', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	
	function post($url, $parameters = array(), $multi = false) {
		$response = $this->oAuthRequest($url, 'POST', $parameters, $multi );
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	
	function delete($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'DELETE', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	
	function oAuthRequest($url, $method, $parameters, $multi = false) {

		if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->host}{$url}.{$this->format}";
	}

	switch ($method) {
		case 'GET':
			$url = $url . '?' . http_build_query($parameters);
			return $this->http($url, 'GET');
		default:
			$headers = array();
			if (!$multi && (is_array($parameters) || is_object($parameters)) ) {
				$body = http_build_query($parameters);
			} else {
				$body = self::build_http_query_multi($parameters);
				$headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
			}
			return $this->http($url, $method, $body, $headers);
	}
	}

	
	function http($url, $method, $postfields = NULL, $headers = array()) {
		$this->http_info = array();
		$ci = curl_init();
		
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		if ( isset($this->access_token) && $this->access_token )
			$headers[] = "Authorization: OAuth2 ".$this->access_token;

		if ( !empty($this->remote_ip) ) {
			if ( defined('SAE_ACCESSKEY') ) {
				$headers[] = "SaeRemoteIP: " . $this->remote_ip;
			} else {
				$headers[] = "API-RemoteIP: " . $this->remote_ip;
			}
		} else {
			if ( !defined('SAE_ACCESSKEY') ) {
				$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
			}
		}
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;

		if ($this->debug) {
			echo "=====post data======\r\n";
			var_dump($postfields);

			echo "=====headers======\r\n";
			print_r($headers);

			echo '=====request info====='."\r\n";
			print_r( curl_getinfo($ci) );

			echo '=====response====='."\r\n";
			print_r( $response );
		}
		curl_close ($ci);
		return $response;
	}

	
	function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	
	public static function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {

			if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
				$url = ltrim( $value, '@' );
				$content = file_get_contents( $url );
				$array = explode( '?', basename( $url ) );
				$filename = $array[0];

				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= "Content-Type: image/unknown\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}

		}

		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}
}



class SaeTClientV2
{
	function __construct( $akey, $skey, $access_token, $refresh_token = NULL)
	{
		$this->oauth = new SaeTOAuthV2( $akey, $skey, $access_token, $refresh_token );
	}

	
	function set_debug( $enable )
	{
		$this->oauth->debug = $enable;
	}

	
	function account_profile_basic( $uid = NULL  )
	{
		$params = array();
		if ($uid) {
			$this->id_format($uid);
			$params['uid'] = $uid;
		}
		return $this->oauth->get( 'account/profile/basic', $params );
	}

	
	function get_uid()
	{
		return $this->oauth->get( 'account/get_uid' );
	}



	
	protected function request_with_pager( $url, $page = false, $count = false, $params = array() )
	{
		if( $page ) $params['page'] = $page;
		if( $count ) $params['count'] = $count;

		return $this->oauth->get($url, $params );
	}

	
	protected function request_with_uid( $url, $uid_or_name, $page = false, $count = false, $cursor = false, $post = false, $params = array())
	{
		if( $page ) $params['page'] = $page;
		if( $count ) $params['count'] = $count;
		if( $cursor )$params['cursor'] =  $cursor;

		if( $post ) $method = 'post';
		else $method = 'get';

		if ( $uid_or_name !== NULL ) {
			$this->id_format($uid_or_name);
			$params['id'] = $uid_or_name;
		}

		return $this->oauth->$method($url, $params );

	}

	
	protected function id_format(&$id) {
		if ( is_float($id) ) {
			$id = number_format($id, 0, '', '');
		} elseif ( is_string($id) ) {
			$id = trim($id);
		}
	}

}
