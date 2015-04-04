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
				$this->obj->member_log("设置企业地图");
				$row=$this->obj->DB_select_once("company","`uid`='".$this->uid."'","x");
				if($row['x']==""){
					$this->get_integral_action($this->uid,"integral_map","设置企业地图");
				}
				$this->obj->ACT_layer_msg("地图设置成功！",9,"index.php?c=map");
			}else{
				$this->obj->ACT_layer_msg("地图设置失败，请稍后再试！",8,$_SERVER['HTTP_REFERER']);
			}
		}
	}
}
?>