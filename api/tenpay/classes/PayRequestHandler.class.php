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

require ("RequestHandler.class.php");
class PayRequestHandler extends RequestHandler {
	
	function __construct() {
		$this->PayRequestHandler();
	}
	
	function PayRequestHandler() {
		
		$this->setGateURL("https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi");	
	}
	
	
	function init() {
		
		$this->setParameter("cmdno", "1");
		
		
		$this->setParameter("date",  date("Ymd"));
		
		
		$this->setParameter("bargainor_id", "");
		
		
		$this->setParameter("transaction_id", "");
		
		
		$this->setParameter("sp_billno", "");
		
		
		$this->setParameter("total_fee", "");
		
		
		$this->setParameter("fee_type",  "1");
		
		
		$this->setParameter("return_url",  "");
		
		
		$this->setParameter("attach",  "");
		
		
		$this->setParameter("spbill_create_ip",  "");
		
		
		$this->setParameter("desc",  "");
		
		
		$this->setParameter("bank_type",  "0");
		
		
		$this->setParameter("cs",  "gbk");
		
		
		$this->setParameter("sign",  "");
		
	}
	
	
	function createSign() {
		$cmdno = $this->getParameter("cmdno");
		$date = $this->getParameter("date");
		$bargainor_id = $this->getParameter("bargainor_id");
		$transaction_id = $this->getParameter("transaction_id");
		$sp_billno = $this->getParameter("sp_billno");
		$total_fee = $this->getParameter("total_fee");
		$fee_type = $this->getParameter("fee_type");
		$return_url = $this->getParameter("return_url");
		$attach = $this->getParameter("attach");
		$spbill_create_ip = $this->getParameter("spbill_create_ip");
		$key = $this->getKey();
		
		$signPars = "cmdno=" . $cmdno . "&" .
				"date=" . $date . "&" .
				"bargainor_id=" . $bargainor_id . "&" .
				"transaction_id=" . $transaction_id . "&" .
				"sp_billno=" . $sp_billno . "&" .
				"total_fee=" . $total_fee . "&" .
				"fee_type=" . $fee_type . "&" .
				"return_url=" . $return_url . "&" .
				"attach=" . $attach . "&";
		
		if($spbill_create_ip != "") {
			$signPars .= "spbill_create_ip=" . $spbill_create_ip . "&";
		}
		
		$signPars .= "key=" . $key;
		
		$sign = strtolower(md5($signPars));
		
		$this->setParameter("sign", $sign);
		
		
		$this->_setDebugInfo($signPars . " => sign:" . $sign);
		
	}

}

?>