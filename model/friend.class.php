<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 */
class friend_controller extends common
{
	function index_action(){
		if($this->config['sy_linksq']=="1")
		{
			$this->obj->get_admin_msg("index.php","�������������ѹرգ�����ϵ����Ա��");
		}
		if($_POST['submit'])
		{
			if(md5($_POST['authcode'])!=$_SESSION['authcode'])
			{
				unset($_SESSION['authcode']);
				$this->obj->ACT_layer_msg("��֤�벻��ȷ��",8,$_SERVER['HTTP_REFERER']);
			}
			$data['link_name']=trim($_POST['title']);
			$data['link_url']=$_POST['url'];
			$data['link_type']=$_POST['type'];
			$data['link_time']=mktime();
			if($_POST['phototype']!='')
			{
				$data['img_type']=$_POST['phototype'];

				if($_POST['phototype']==1)
				{
					$upload=$this->upload_pic("upload/friend/",false);
					$pictures=$upload->picture($_FILES['uplocadpic'],false);

					$data['pic']=$pictures;
				}else{
					$data['pic']=$_POST['uplocadpic'];
				}
			}
			$nbid=$this->obj->insert_into("admin_link",$data);
			isset($nbid)?$this->obj->ACT_layer_msg("��ȴ�����Ա��ˣ�",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("���ʧ�ܣ�",8,$_SERVER['HTTP_REFERER']);
		}
		$this->seo("friend");
		$this->yun_tpl(array('index'));
	}
}

?>