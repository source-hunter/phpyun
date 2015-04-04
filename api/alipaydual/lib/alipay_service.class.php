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

require_once("alipay_submit.class.php");

class AlipayService {
	
	var $aliapy_config;
	
	var $alipay_gateway_new = 'https://mapi.alipay.com/gateway.do?';

	function __construct($aliapy_config){
		$this->aliapy_config = $aliapy_config;
	}
    function AlipayService($aliapy_config) {
    	$this->__construct($aliapy_config);
    }
	
	function trade_create_by_buyer($para_temp) {
		
		$button_name = "确认";
		
		$alipaySubmit = new AlipaySubmit();
		$html_text = $alipaySubmit->buildForm($para_temp, $this->alipay_gateway_new, "get", $button_name, $this->aliapy_config);

		return $html_text;
	}

	
	function query_timestamp() {
		$url = $this->alipay_gateway_new."service=query_timestamp&partner=".trim($this->aliapy_config['partner']);
		$encrypt_key = "";		

		$doc = new DOMDocument();
		$doc->load($url);
		$itemEncrypt_key = $doc->getElementsByTagName( "encrypt_key" );
		$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
		
		return $encrypt_key;
	}
	
	
	function alipay_interface($para_temp) {
		
		$alipaySubmit = new AlipaySubmit();
		$html_text = "";
		
		
		return $html_text;
	}
}
?>