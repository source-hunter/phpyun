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
class binding_controller extends company
{
	function index_action()
	{
		$member=$this->obj->DB_select_once("member","`uid`='".$this->uid."'");
		$this->yunset("member",$member);
		$company=$this->obj->DB_select_once("company","`uid`='".$this->uid."'");
		$this->yunset("company",$company);
		$cert=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='3'");
		$this->yunset("cert",$cert);
		$this->public_action();
		$this->yunset("js_def",2);
		$this->com_tpl("binding");
	}
	function save_action()
	{
		if($_POST['moblie'])
		{
			$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and `check`='".$_POST['moblie']."'");
			if(!empty($row))
			{
				if($row['check2']!=$_POST['code'])
				{
					echo 3;die;
				}
				$this->obj->DB_update_all("resume","`moblie_status`='0'","`telphone`='".$row['check']."'");
				$this->obj->DB_update_all("company","`moblie_status`='0'","`linktel`='".$row['check']."'");

				$this->obj->DB_update_all("member","`moblie`='".$row['check']."'","`uid`='".$this->uid."'");
				$this->obj->DB_update_all("company","`linktel`='".$row['check']."',`moblie_status`='1'","`uid`='".$this->uid."'");
				$this->obj->DB_update_all("company_cert","`status`='1'","`uid`='".$this->uid."' and `check2`='".$_POST['code']."'");
				$this->obj->member_log("�ֻ���");
				$this->get_integral_action($this->uid,"integral_mobliecert","�ֻ���");
				echo 1;die;
			}else{
				echo 2;die;
			}
		}
		if($_POST['upfile'])
		{
			if(is_uploaded_file($_FILES['pic']['tmp_name'])){
				$upload=$this->upload_pic("../upload/cert/",false,$this->config['com_uppic']);
				$pictures=$upload->picture($_FILES['pic']);
				$this->picmsg($pictures,$_SERVER['HTTP_REFERER']);
				if($this->config['com_cert_status']=="1")
				{
					$sql['status']=0;
				}else{
					$sql['status']=1;
				}
				$company_name=$_POST['company_name'];
				if(strlen(trim($company_name))<=0){
					$this->obj->ACT_layer_msg("��ҵȫ�Ʋ���Ϊ�գ�",9,1);die;
				}
				$this->obj->DB_update_all("company","`name`='$company_name',`yyzz_status`='".$sql['status']."'","`uid`='".$this->uid."'");
				$this->obj->DB_update_all("friend_info","iscert='".$sql['status']."'","`uid`='".$this->uid."'");
				$sql['step']=1;
				$sql['check']=str_replace("../","/",$pictures);
				$sql['check2']="0";
				$sql['ctime']=mktime();
				$row=$this->obj->DB_select_once("company_cert","`uid`='".$this->uid."' and type='3'");
				if(is_array($row))
				{
					$this->obj->unlink_pic("../".$row['check']);
					$where['uid']=$this->uid;
					$where['type']='3';
					$this->obj->update_once("company_cert",$sql,$where);
					$this->obj->member_log("����Ӫҵִ��");
				}else{
					$sql['uid']=$this->uid;
					$sql['type']=3;
					$this->obj->insert_into("company_cert",$sql);
					$this->obj->member_log("�ϴ�Ӫҵִ��");
					if($this->config['com_cert_status']!="1")
					{
						$this->get_integral_action($this->uid,"integral_comcert","��֤Ӫҵִ��");
					}
				}
				$this->obj->ACT_layer_msg("�ϴ�Ӫҵִ�ճɹ���",9,1);
			}else{
				$this->obj->ACT_layer_msg("���ϴ�Ӫҵִ�գ�",8,1);
			}
		}
	}
	function del_action()
	{
		if($_GET['type']=="moblie")
		{
			$this->obj->DB_update_all("company","`moblie_status`='0'","`uid`='".$this->uid."'");
		}
		if($_GET['type']=="email")
		{
			$this->obj->DB_update_all("company","`email_status`='0'","`uid`='".$this->uid."'");
		}
		if($_GET['type']=="qqid")
		{
			$this->obj->DB_update_all("member","`qqid`=''","`uid`='".$this->uid."'");
		}
		if($_GET['type']=="sinaid")
		{
			$this->obj->DB_update_all("member","`sinaid`=''","`uid`='".$this->uid."'");
		}
		$this->layer_msg("����󶨳ɹ���",8,0,$_SERVER['HTTP_REFERER']);
	}
}
?>