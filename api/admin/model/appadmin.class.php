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
class appadmin extends common{
	function get_appadmin_source(){
		if($_GET[m]=="index" && $_GET[c]=="login"){}else{
			$tokey=$_GET['tokey']?$_GET['tokey']:$_POST['tokey'];
			if(!$tokey)$this->return_appadmin_msg(3,"����tokey����Ϊ��");
			global $user_tokey;
			$userlist=$this->get_appadmin_cache();
			if(is_array($userlist)){
				foreach($userlist as $key=>$v){
					if($v['tokey']==$tokey){
						$username=$key;
					}
				}
				if(!$username){
					$this->return_appadmin_msg(3,"����tokey����ȷ");
				}else{
					$row=$this->obj->DB_select_once("admin_user","`username`='".$username."'");
					$datatokey=md5($userlist[$username]['ctime'].$row['password']);
					if(!is_array($row) || $datatokey!=$tokey){
						$this->return_appadmin_msg(3,"����tokey����ȷ");
					}
					if((time()-$userlist[$username]['actiontime'])>3600){
						$this->return_appadmin_msg(3,"���Ѿ��ܳ�ʱ��û�в����ˣ������µ�¼");
					}
					$userlist[$username]['ctime']=$userlist[$username]['ctime'];
					$userlist[$username]['actiontime']=mktime();
					$userlist[$username]['username']=$username;
					$userlist[$username]['uid']=$row['uid'];
					$userlist[$username]['status']=1;
					$userlist[$username]['hits']=3;
					$userlist[$username]['tokey']=$tokey;
					$this->write_appadmin_cache($userlist);
					$user_tokey=$userlist[$username];
				}
			}else{
				$this->return_appadmin_msg(2,"���ȵ�¼");
			}
		}
	}
	function stringfilter($string){ 
		$e=mb_detect_encoding($string, array('UTF-8', 'GBK'));
		if($e=="UTF-8"){
			$str=iconv("utf-8","gbk",trim($string));
		}else{
			$str=iconv("gbk","utf-8",trim($string));
		}
		$regex = "/\\$|\'|\\\|\|/";
		$str=preg_replace($regex,"",$str); 
		return $str;
	}
	function write_appadmin_log($data){
		global $user_tokey;
		$value="`uid`='".$user_tokey['uid']."',";
		$value.="`username`='".$user_tokey['username']."',";
		$value.="`content`='".$data."',";
		$value.="`ctime`='".time()."'";
		if($user_tokey['uid'] && $user_tokey['username']){$this->obj->DB_insert_once("admin_log",$value);}
	}
	function return_appadmin_msg($error,$msg,$cont=array()){
		$data['error']=$error;
		$data['msg']=iconv("gbk","utf-8",$msg);
		if($cont){
			$data['data']=$cont;
		}
		echo json_encode($data);die;
	}
	function get_appadmin_cache(){
		include(PLUS_PATH."/appadmin.cache.php");
		return $row=unserialize(base64_decode($userlist));
	}
	function write_appadmin_cache($data){
		$content=base64_encode(serialize($data));
		$cont="<?php";
		$cont.="\r\n";
		$cont.="\$userlist='".$content."';";
		$cont.="?>";
		$fp=@fopen(PLUS_PATH."/appadmin.cache.php","w+");
		$filetouid=@fwrite($fp,$cont);
		@fclose($fp);
	}
}
?>