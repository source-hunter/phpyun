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
				echo "<script>alert('�ϴ��ļ�ʧ��');</script>";
				echo "<script>location.href=''</script>";exit;
			}
		}
		$this->user_tpl('checkimg');
	}
}
?>