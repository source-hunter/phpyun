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
class blacklist_controller extends user{ 
	function index_action(){
		$this->public_action();
		$urlarr=array("c"=>"blacklist","page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$this->get_page("blacklist","`c_uid`='".$this->uid."' and usertype='1' order by id desc",$pageurl,"10");
 		$this->user_tpl('blacklist');
	}
	function del_action(){
		if($_GET['id']){
			$del=(int)$_GET['id'];
			$nid=$this->obj->DB_delete_all("blacklist","`id`='".$del."' and `c_uid`='".$this->uid."'");
			if($nid){
				$this->obj->member_log("ɾ����˾��������Ϣ");
				$this->layer_msg('ɾ���ɹ���',9,0,"index.php?c=blacklist");
			}else{
				$this->layer_msg('ɾ��ʧ�ܣ�',8,0,"index.php?c=blacklist");
			}
 		}
	}
	function save_action(){
		if(is_array($_POST['buid'])&&$_POST['buid']){

			$company=$this->obj->DB_select_all("company","`uid` in(".$this->pylode(',',$_POST['buid']).")","`uid`,`name`");
			foreach($company as $val){
				$this->obj->insert_into("blacklist",array('p_uid'=>$val['uid'],'c_uid'=>$this->uid,"inputtime"=>time(),'usertype'=>'1','com_name'=>$val['name']));
			}
			$this->layer_msg('�����ɹ���',9,1,"index.php?c=blacklist");
		}else{
			$this->layer_msg('��ѡ��Ҫ���εĹ�˾��',8,1,"index.php?c=blacklist");
		}
	}
	function searchcom_action(){
		$blacklist=$this->obj->DB_select_all("blacklist","`c_uid`='".$this->uid."'","`p_uid`");
		if($blacklist&&is_array($blacklist)){
			$uids=array();
			foreach($blacklist as $val){
				$uids[]=$val['p_uid'];
			}
			$where=" and `uid` not in(".@implode(',',$uids).")";
		}
		$company=$this->obj->DB_select_all("company","`name` like '%".$this->stringfilter(trim($_POST['name']))."%' ".$where,"`uid`,`name`");
		$html="";
		if($company&&is_array($company)){
			foreach($company as $val){
				$html.="<li class=\"cur\"><input class=\"re-company\" type=\"checkbox\" value=\"".$val['uid']."\" name=\"buid[]\"><a href=\"".$this->curl(array("url"=>"id:".$val['uid']))."\" target=\"_blank\">".$val['name']."</a></li>";
			}
		}else{
			$html="���޷���������ҵ";
		}
		echo $html;die;
	}
}
?>