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

require_once("alipay_function.php");

class alipay_service {

    var $gateway;			
    var $security_code;		
    var $mysign;			
    var $sign_type;			
    var $parameter;			
    var $_input_charset;    

   
    function alipay_service($parameter,$security_code,$sign_type) {
        $this->gateway	      = "https://www.alipay.com/cooperate/gateway.do?";
        $this->security_code  = $security_code;
        $this->sign_type      = $sign_type;
        $this->parameter      = para_filter($parameter);

        
        if($parameter['_input_charset'] == '')
            $this->parameter['_input_charset'] = 'GBK';

        $this->_input_charset   = $this->parameter['_input_charset'];

        
        $sort_array   = arg_sort($this->parameter);   
        $this->mysign = build_mysign($sort_array,$this->security_code,$this->sign_type);
    }

    
    function create_url() {
        $url         = $this->gateway;
        $sort_array  = array();
        $sort_array  = arg_sort($this->parameter);
        $arg         = create_linkstring_urlencode($sort_array);	
        
		
        $url.= $arg."&sign=" .$this->mysign ."&sign_type=".$this->sign_type;
        return $url;
    }

   
    function build_postform() {

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gateway."_input_charset=".$this->parameter['_input_charset']."' method='post'>";

        while (list ($key, $val) = each ($this->parameter)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        $sHtml = $sHtml."<input type='hidden' name='sign' value='".$this->mysign."'/>";
        $sHtml = $sHtml."<input type='hidden' name='sign_type' value='".$this->sign_type."'/></form>";

        $sHtml = $sHtml."<input type='button' name='v_action' value='支付宝确认付款' onClick='document.forms[\"alipaysubmit\"].submit();'>";
        return $sHtml;
    }
  

}
?>