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
class my_question_controller extends company
{
	function index_action()
	{
		$this->public_action();
		$_GET['type']=intval($_GET['type']);
		if($_GET['type']==0||$_GET['type']==''){
			$table="question";
		}elseif($_GET['type']==1){
			$table="answer";
		}elseif($_GET['type']==2){
			$table="answer_review";
		}
		$urlarr=array("c"=>"my_question","type"=>$_GET['type'],"page"=>"{{page}}");
		$pageurl=$this->url("index","index",$urlarr);
		$list = $this->get_page($table,"`uid`='".$this->uid."'  ORDER BY `add_time` DESC",$pageurl,"20");

		if($_GET['type']>0&&is_array($list)){
			foreach($list as $val){
				$qids[]=$val['qid'];
			}
			$question=$this->obj->DB_select_all("question","`id` in(".$this->pylode(',',$qids).")","`id`,`title`");
			foreach($list as $key=>$val){
				foreach($question as $value){
					if($val['qid']==$value['id']){
						$list[$key]['title']=$value['title'];
						$list[$key]['aid']=$val['id'];
					}
				}
			}
			if($_GET['type']=='1'){$this->yunset("typename",'�ش�');}else{$this->yunset("typename",'����');}
		}
		$this->yunset("q_list",$list);
		$this->yunset("gettype",$_GET['type']);
		$this->yunset("js_def",7);
		$this->com_tpl('my_question');
	}
	function del_action(){
		$del=(int)$_GET['id'];
		$is_del=$this->obj->DB_delete_all("question","`id`='".$del."' and uid='".$this->uid."'");
		if(!empty($is_del))
		{
			$this->obj->DB_delete_all("answer","`qid`='".$del."'","");
			$nid=$this->obj->DB_delete_all("answer_review","`qid`='".$del."'","");
		}
		if($nid){
			$this->obj->member_log("ɾ���ʴ�");
			$this->layer_msg('ɾ���ɹ���',9,0,$_SERVER['HTTP_REFERER']);
		}else{
			$this->layer_msg('ɾ��ʧ�ܣ�',8,0,$_SERVER['HTTP_REFERER']);
		}
	}
}
?>