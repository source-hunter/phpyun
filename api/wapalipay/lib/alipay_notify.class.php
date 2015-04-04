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

require_once("alipay_core.function.php");
require_once("alipay_rsa.function.php");
require_once("alipay_md5.function.php");

class AlipayNotify {
    
	var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	var $alipay_config;

	function __construct($alipay_config){
		$this->alipay_config = $alipay_config;
	}
    function AlipayNotify($alipay_config) {
    	$this->__construct($alipay_config);
    }
    
	function verifyNotify(){
		if(empty($_POST)) {
			return false;
		}
		else {
			
			
			$decrypt_post_para = $_POST;
			if ($this->alipay_config['sign_type'] == '0001') {
				$decrypt_post_para['notify_data'] = rsaDecrypt($decrypt_post_para['notify_data'], $this->alipay_config['private_key_path']);
			}
			
			
			$doc = new DOMDocument();
			$doc->loadXML($decrypt_post_para['notify_data']);
			$notify_id = $doc->getElementsByTagName( "notify_id" )->item(0)->nodeValue;
			
			
			$responseTxt = 'true';
			if (! empty($notify_id)) {$responseTxt = $this->getResponse($notify_id);}
			
			
			$isSign = $this->getSignVeryfy($decrypt_post_para, $_POST["sign"],false);
			
			
			if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    
	function verifyReturn(){
		if(empty($_GET)) {
			return false;
		}
		else {
			
			$isSign = $this->getSignVeryfy($_GET, $_GET["sign"],true);
			
			
			if ($isSign) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	function decrypt($prestr) {
		return rsaDecrypt($prestr, trim($this->alipay_config['private_key_path']));
	}
	
	
	function sortNotifyPara($para) {
		$para_sort['service'] = $para['service'];
		$para_sort['v'] = $para['v'];
		$para_sort['sec_id'] = $para['sec_id'];
		$para_sort['notify_data'] = $para['notify_data'];
		return $para_sort;
	}
	
   
	function getSignVeryfy($para_temp, $sign, $isSort) {
		
		$para = paraFilter($para_temp);
		
		
		if($isSort) {
			$para = argSort($para);
		} else {
			$para = $this->sortNotifyPara($para);
		}
		
		
		$prestr = createLinkstring($para);
		
		$isSgin = false;
		switch (strtoupper(trim($this->alipay_config['sign_type']))) {
			case "MD5" :
				$isSgin = md5Verify($prestr, $sign, $this->alipay_config['key']);
				break;
			case "RSA" :
				$isSgin = rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
				break;
			case "0001" :
				$isSgin = rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
				break;
			default :
				$isSgin = false;
		}
		
		return $isSgin;
	}

    
	function getResponse($notify_id) {
		$transport = strtolower(trim($this->alipay_config['transport']));
		$partner = trim($this->alipay_config['partner']);
		$veryfy_url = '';
		if($transport == 'https') {
			$veryfy_url = $this->https_verify_url;
		}
		else {
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
		
		return $responseTxt;
	}
}
?>
