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
class checkimg_controller extends user{
	function index_action()
	{
		if($_POST["subuppic"])
		{
			$upload=$this->upload_pic("../upload/user/",false,$this->config['user_pickb']);
			$pictures=$upload->picture($_FILES['file']);
			$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
			if($pictures)
			{
				list($width, $height, $type, $attr) = getimagesize($pictures);
				$f1="<img src='$pictures' id='ImageDrag'>";
				$f2="<img src='$pictures' id='ImageIcon'>";
				echo '<script language="javascript">parent.$("#ImageDragContainer").html("'.$f1.'");parent.$("#IconContainer").html("'.$f2.'");parent.$("#bigImage").val("'.$pictures.'");parent.run('.$width.','.$height.');</script>';
				echo "<script>location.href='index.php?m=index&c=checkimg'</script>";exit;
			}else{
				echo "<script>alert('上传文件失败');</script>";
				echo "<script>location.href=''</script>";exit;
			}
		}
		$this->user_tpl('checkimg');
	}
}
?>