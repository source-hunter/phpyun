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
class save_avatar_controller extends user{
	function index_action(){
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		if($_GET['type']!='big' && $_GET['type']!='small')
		{
			exit();
		}
		$type = $_GET['type'];
		
		$pic_id = trim($_GET['photoId']);
		$nameArr=@explode(".",$pic_id);
		$uptypes=array('jpg','png','jpeg','bmp','gif');
		if(count($nameArr)!=2){
			exit();
		}
		if(!is_numeric($nameArr[0])){
			exit();
		}
		if(!in_array(strtolower($nameArr[1]),$uptypes)){
			$d['statusText'] = iconv("gbk","utf-8",'�ļ����Ͳ���!');
			$msg = json_encode($d);
			echo $msg;die;
		}
		$new_avatar_path = 'upload/user/user_'.$type.'/'.$pic_id;
		$len = file_put_contents(APP_PATH.$new_avatar_path,file_get_contents("php://input"));
		$avtar_img = imagecreatefromjpeg(APP_PATH.$new_avatar_path);
		imagejpeg($avtar_img,APP_PATH.$new_avatar_path,80);
		$d['data']['urls'][0] ="../".$new_avatar_path;
		$d['status'] = 1;
		$d['statusText'] = iconv("gbk","utf-8",'�ϴ��ɹ�!');
		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`photo`,`resume_photo`");
		if($type=="small"){
			if($resume['photo']!="")
			{
				$this->obj->unlink_pic('.'.$resume['photo']);
			}else{
				$this->get_integral_action($this->uid,"integral_avatar","�ϴ�ͷ��");
			}
			$this->obj->update_once('resume',array('photo'=>'./'.$new_avatar_path),array('uid'=>$this->uid));
		}else{
			$this->obj->update_once('resume',array('resume_photo'=>'./'.$new_avatar_path),array('uid'=>$this->uid));
			$this->obj->unlink_pic('.'.$resume['resume_photo']);
			$this->obj->member_log("�ϴ�����ͷ��");
		}
		$msg = json_encode($d);
		echo $msg;
	}
}
?>