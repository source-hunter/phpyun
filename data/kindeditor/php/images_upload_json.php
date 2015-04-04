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
require_once '../../../global.php';
require_once 'JSON.php';

$php_path = APP_PATH;
$php_url = $config['sy_weburl'];


$save_path = $php_path . 'upload/kindeditor/';

$save_url = '/upload/kindeditor/';

$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('no'),
	'media' => array('no'),
	'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt',  'txt', 'zip', 'rar', 'gz'),
);

$max_size = 1000000;

$save_path = realpath($save_path) . '/';


if (!empty($_FILES['imgFile']['error'])) {
	switch($_FILES['imgFile']['error']){
		case '1':
			$error = '����php.ini����Ĵ�С��';
			break;
		case '2':
			$error = '����������Ĵ�С��';
			break;
		case '3':
			$error = 'ͼƬֻ�в��ֱ��ϴ���';
			break;
		case '4':
			$error = '��ѡ��ͼƬ��';
			break;
		case '6':
			$error = '�Ҳ�����ʱĿ¼��';
			break;
		case '7':
			$error = 'д�ļ���Ӳ�̳���';
			break;
		case '8':
			$error = 'File upload stopped by extension��';
			break;
		case '999':
		default:
			$error = 'δ֪����';
	}
	alert($error);
}


if (empty($_FILES) === false) {
	
	$file_name = $_FILES['imgFile']['name'];
	
	$tmp_name = $_FILES['imgFile']['tmp_name'];
	
	$file_size = $_FILES['imgFile']['size'];
	
	if (!$file_name) {
		alert("��ѡ���ļ���");
	}
	
	if (@is_dir($save_path) === false) {
		alert("�ϴ�Ŀ¼�����ڡ�");
	}
	
	if (@is_writable($save_path) === false) {
		alert("�ϴ�Ŀ¼û��дȨ�ޡ�");
	}
	
	if (@is_uploaded_file($tmp_name) === false) {
		alert("�ϴ�ʧ�ܡ�");
	}
	
	if ($file_size > $max_size) {
		alert("�ϴ��ļ���С�������ơ�");
	}
	
	$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
	if (empty($ext_arr[$dir_name])) {
		alert("Ŀ¼������ȷ��");
	}
	
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	
	if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
		alert("�ϴ��ļ���չ���ǲ��������չ����\nֻ����" . implode(",", $ext_arr[$dir_name]) . "��ʽ��");
	}
	
	if ($dir_name !== '') {
		$save_path .= $dir_name . "/";
		$save_url .= $dir_name . "/";
		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
	}
	$ymd = date("Ymd");
	$save_path .= $ymd . "/";
	$save_url .= $ymd . "/";
	if (!file_exists($save_path)) {
		mkdir($save_path);
	}
	
	$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
	
	$file_path = $save_path . $new_file_name;
	if (move_uploaded_file($tmp_name, $file_path) === false) {
		alert("�ϴ��ļ�ʧ�ܡ�");
	}
	@chmod($file_path, 0644);
	$file_url = $save_url . $new_file_name;

	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 0, 'url' => $file_url));
	exit;
}

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}
