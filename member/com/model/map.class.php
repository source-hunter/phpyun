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
class map_controller extends company
{
	function index_action()
	{
		$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","x,y,address,provinceid,cityid");
		$this->yunset("row",$row);
		$this->public_action();
		$this->city_cache();
		$this->yunset("js_def",2);
		$this->com_tpl('map');
	}
	function save_action(){
		if($_POST['savemap']){
			$data['x']=(float)$_POST['xvalue'];
			$data['y']=(float)$_POST['yvalue'];
			$oid=$this->obj->update_once("company",$data,array("uid"=>$this->uid));
			if($oid){
				$this->obj->member_log("������ҵ��ͼ");
				$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","x");
				if($row['x']==""){
					$this->get_integral_action($this->uid,"integral_map","������ҵ��ͼ");
				}
				$this->obj->ACT_layer_msg("��ͼ���óɹ���",9,"index.php?c=map");
			}else{
				$this->obj->ACT_layer_msg("��ͼ����ʧ�ܣ����Ժ����ԣ�",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
}
?>