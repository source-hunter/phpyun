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
			$d['statusText'] = iconv("gbk","utf-8",'文件类型不符!');
			$msg = json_encode($d);
			echo $msg;die;
		}
		$new_avatar_path = 'upload/user/user_'.$type.'/'.$pic_id;
		$len = file_put_contents(APP_PATH.$new_avatar_path,file_get_contents("php://input"));
		$avtar_img = imagecreatefromjpeg(APP_PATH.$new_avatar_path);
		imagejpeg($avtar_img,APP_PATH.$new_avatar_path,80);
		$d['data']['urls'][0] ="../".$new_avatar_path;
		$d['status'] = 1;
		$d['statusText'] = iconv("gbk","utf-8",'上传成功!');
		$resume=$this->obj->DB_select_once("resume","`uid`='".$this->uid."'","`photo`,`resume_photo`");
		if($type=="small"){
			if($resume['photo']!="")
			{
				$this->obj->unlink_pic('.'.$resume['photo']);
			}else{
				$this->get_integral_action($this->uid,"integral_avatar","上传头像");
			}
			$this->obj->update_once('resume',array('photo'=>'./'.$new_avatar_path),array('uid'=>$this->uid));
		}else{
			$this->obj->update_once('resume',array('resume_photo'=>'./'.$new_avatar_path),array('uid'=>$this->uid));
			$this->obj->unlink_pic('.'.$resume['resume_photo']);
			$this->obj->member_log("上传个人头像");
		}
		$msg = json_encode($d);
		echo $msg;
	}
}
?>