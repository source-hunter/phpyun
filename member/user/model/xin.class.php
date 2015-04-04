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
class xin_controller extends user{
	function index_action(){
		$where.= "`uid`='".$this->uid."' or (`fid`='".$this->uid."' and `status`<>'1') order by ctime desc";
		$urlarr["c"] = $_GET["c"];
		$urlarr["page"] = "{{page}}";
		$pageurl = $this->url("index","index",$urlarr);
		$rows=$this->get_page("friend_message",$where,$pageurl,"20");
		if(is_array($rows))
		{
			foreach($rows as $v)
			{
				$uids[]=$v['uid'];
				$uids[]=$v['fid'];
			}
			$statis =$this->obj->DB_select_all("friend_info","`uid` in (".$this->pylode(",",$uids).")","uid,nickname");
			foreach($rows as $key=>$value)
			{
				$rows[$key]['ctime'] = date("Y-m-d H:i",$value['ctime']);
				foreach($statis as $k=>$v)
				{
					if($value['uid']==$v['uid'])
					{
						  $rows[$key]['nickname'] = $v['nickname'];
					}
					if($value['fid']==$v['uid'])
					{
						  $rows[$key]['name'] = $v['nickname'];
					}
				}
			}
		}
		$this->yunset("rows",$rows);
		$this->public_action();
		$this->obj->DB_update_all("friend_message","`remind_status`='1'","`fid`='".$this->uid."' and `remind_status`='0'");
		$this->unset_remind("friend_message1","1");
		$this->user_tpl('xin');
	}
	function reply_action(){
		if($_POST['submit']){
			$data['content'] = trim($_POST['content']);
			$data['ctime']   = time();
			$data['fid']     = intval($_POST['fid']);
			$data['uid']     = $this->uid;
			$nid=$this->obj->insert_into("friend_message",$data);
			if($nid){
				$this->obj->member_log("回复站内信");
				$this->obj->ACT_layer_msg("留言成功！",9,$_SERVER['HTTP_REFERER']);
			}else{
				$this->obj->ACT_layer_msg("留言失败！",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
	function del_action(){
		if($_GET['id']){
			$id=(int)$_GET['id'];
			$nid = $this->obj->DB_delete_all("friend_message","`id`='".$id."' and `uid`='".$this->uid."'");
		}else{
			$did=(int)$_GET['did'];
			$nid = $this->obj->update_once('friend_message',array('status'=>1),array('id'=>$did,'fid'=>$this->uid));
		}
		if($nid){
			$this->obj->member_log("删除站内信");
			$this->layer_msg('删除成功！',9);
		}else{
			$this->layer_msg('删除失败！',8);
		}
	}
}
?>