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
class uploadall_controller extends company
{
	function index_action()
	{
		if (isset($_POST['phpsessionid']))
		{
				session_id($_POST['phpsessionid']);
			} else if (isset($_GET['phpsessionid'])){
				session_id($_GET['phpsessionid']);
			}
			session_start();
			$POST_MAX_SIZE = ini_get('post_max_size');
			$unit = strtoupper(substr($POST_MAX_SIZE, -1));
			$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
			if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE)
			{
				header("HTTP/1.1 500 Internal Server Error");
				echo "fai:������������ĳߴ�";
				exit(0);
			}
			$filenameset=false;
			$upbool=1;
			$tipmsg="";
			$dir_file=date("Ymd");
			$qhjsw=date('YmdHis');
			$imgpath="../upload/show/";
			$rootfoldername="null";
			$save_path = getcwd() .'/'.$imgpath.$dir_file.'/';
			$upload_name = "Filedata";
			$max_file_size_in_bytes = 2147483647;
			$extension_whitelist = array("jpg","jpeg","gif","png");
			$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';
			$MAX_FILENAME_LENGTH = 260;
			$file_name = "";
			$file_extension = "";
			$uploadErrors = array(
		        0=>"û�д���,�ļ��ϴ��г�Ч",
		        1=>"�ϴ����ļ���upload_max_filesizeָ������ֻ�г���",
		        2=>"�ϴ����ļ��ĳ���MAX_FILE_SIZEָʾ�Ǹ�û�б�ָ����HTML��",
		        3=>"δ�����ϴ����ļ��ϴ�",
		        4=>"û���ļ����ϴ�",
		        6=>"���һ����ʱ�ļ���"
			);
			if($upbool===0)
			{
				$this->HandleError("fai:".$tipmsg);
				exit(0);
			}
			if (!isset($_FILES['$upload_name']))
			{
				$this->HandleError("fai:û�з����ϴ� \$_FILES for " . $upload_name);
				exit(0);
			} else if (isset($_FILES['$upload_name']['error']) && $_FILES['$upload_name']['error'] != 0)
			{
				$this->HandleError($uploadErrors[$_FILES['$upload_name']['error']]);
				exit(0);
			} else if (!isset($_FILES['$upload_name']['tmp_name']) || !@is_uploaded_file($_FILES['$upload_name']['tmp_name']))
			{
				$this->HandleError("fai:Upload failed is_uploaded_file test.");
				exit(0);
			} else if (!isset($_FILES['$upload_name']['name']))
			{
				$this->HandleError("fai:�ļ�û������.");
				exit(0);
			}
			list($width,$height,$type,$attr) = getimagesize($_FILES['$upload_name']['tmp_name']);
		 	if(empty($width) || empty($height) || empty($type) || empty($attr))
			{
		  		$this->HandleError("fai:�ϴ�ͼƬΪ�Ƿ�����");
		  		exit(0);
		  	}
			$file_size = @filesize($_FILES['$upload_name']['tmp_name']);
			if ($file_size > $max_file_size_in_bytes)
			{
				$this->HandleError("fai:�������������ļ��Ĵ�С");
				exit(0);
			}
			if ($file_size <= 0)
			{
				$this->HandleError("fai:�����ļ�����С��С");
				exit(0);
			}
			$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES['$upload_name']['name']));
			if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH)
			{
				$this->HandleError("fai:��Ч���ļ�");
				exit(0);
			}
			if(!$this->create_folders($save_path))
			{
				$this->HandleError("fai:�ļ��д���ʧ��");
				exit(0);
			}
			if (file_exists($save_path . $file_name))
			{
				$this->HandleError("fai:������ֵ��ļ��Ѿ�����");
				exit(0);
			}
			$path_info = pathinfo($_FILES['$upload_name']['name']);
			$file_extension = $path_info['extension'];
			$is_valid_extension = false;
			foreach ($extension_whitelist as $extension)
			{
				if (strcasecmp($file_extension, $extension) == 0)
				{
					$is_valid_extension = true;
					break;
				}
			}
			if (!$is_valid_extension)
			{
				$this->HandleError("fai:��Ч����չ��");
				exit(0);
			}
			if (file_exists($save_path . $file_name))
			{
				$this->HandleError("fai:����ļ��������Ѿ�����");
				exit(0);
			}
			if(is_dir($imgpath.$dir_file))
			{
				$fileName=$filenameset?$this->createdatefilename($file_extension):$this->CreateNextName($file_extension,$save_path);
				if(!move_uploaded_file($_FILES['$upload_name']['tmp_name'], $save_path.$fileName))
				{
					$this->HandleError("fai:�ļ��ƶ�ʧ��");
					exit(0);
			 	}else{
			 	     if($rootfoldername!=="null")
			         {
				       $this->HandleError("suc".$rootfoldername."/".$imgpath.$dir_file."/,".$fileName.",".$file_size);
			         }
			       else
			          {
				       $this->HandleError("suc".$imgpath.$dir_file."/,".$fileName.",".$file_size);
			          }
			 			exit(0);
			 		}
			}else{
				if(mkdir($imgpath.$dir_file))
				{
					$fileName=$filenameset?$this->createdatefilename($file_extension):$this->CreateFirstName($file_extension);
					if(!move_uploaded_file($_FILES['$upload_name']['tmp_name'], $save_path.$fileName))
					{
						$this->HandleError("fai:�ļ��ƶ�ʧ��");
						exit(0);
			 		}else{
				 		if($rootfoldername!=="null")
				 		{
				 			$this->HandleError("suc:".$rootfoldername."/".$imgpath.$dir_file."/,".$fileName.",".$file_size);
				 		}else{
					        $this->HandleError("suc:".$imgpath.$dir_file."/,".$fileName.",".$file_size);
				        }
			 			exit(0);
			 		}
				}
				else {
					$this->HandleError("fai:����Ŀ¼ʧ��");
					exit(0);
				}
			}
			exit(0);
	}
}
?>