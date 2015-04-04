<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 */
class admin_city_controller extends common
{
   
	function index_action(){
		global $city_ABC;
		$this->yunset("letter",$city_ABC);
		
		$city=$this->obj->DB_select_all("city_class","`keyid`='0' order by sort asc");
		$this->yunset("city",$city);
		$this->yuntpl(array('admin/admin_city'));
	}
	function CityArr($city,$keyid,$keyids)
	{
		if(is_array($city))
		{
			foreach($city as $k=>$value)
			{
				if($value['keyid']==$keyid)
				{
					$cityarr[$value['id']] = $value;
					if(in_array($value['id'],$keyids))
					{
						$cityarr[$value['id']]['son']=$this->CityArr($city,$value['id'],$keyids);
					}
				}
			}
		}
		return $cityarr;
	}
	function upp_action(){
		if($_POST['id']!="" || $_POST['addcityname_0'])
		{
			if($_POST['updateall'])
			{
				if($_POST['addcityname_0'])
				{
					$_POST['id']="0,".$_POST['id'];
				}
				$id_arr = @explode(",",$_POST['id']);
				foreach($id_arr as $key=>$value)
				{
					$name = $_POST["cityname_".$value];
					$sort = $_POST["citysort_".$value];
					$letter = $_POST["letter_".$value];
					$display = $_POST["display_".$value];
					$site = $_POST["sitetype_".$value];
					if($name!="")
					{
						$this->obj->DB_update_all("city_class","`name`='$name',`sort`='$sort',`letter`='$letter',`display`='$display',`sitetype`='$site'","`id`='$value'");
					}
					if(is_array($_POST["addcityname_".$value]))
					{
						foreach($_POST["addcityname_".$value] as $k=>$v)
						{
							if($v!="")
							{
								$addletter = $_POST["addletter_".$value][$k];
								$adddisplay = $_POST["adddisplay_".$value][$k];
								$addsite = $_POST["addsitetype_".$value[$k]];
								$this->obj->DB_insert_once("city_class","`keyid`='$value',`name`='$v',`letter`='$addletter',`display`='$adddisplay',`sitetype`='$addsite'");
							}
						}
					}
				}
				$this->cache_action();
				$this->obj->ACT_layer_msg("区域(ID:".$value.")修改成功！",9,$_SERVER['HTTP_REFERER'],2,1);
			}
			if($_POST['id']){
				$this->obj->DB_delete_all("city_class","`id` IN (".$_POST['id'].") OR `keyid` IN (".$_POST['id'].")"," ");
				$this->cache_action();
				$this->layer_msg( "区域(ID:".$_POST['id'].")删除成功！",9,1,$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->obj->ACT_layer_msg("请选择需要修改或增加子类的城市！",8,$_SERVER['HTTP_REFERER'],2,1);
		}
	}
	function AddCity_action(){
		global $city_ABC;
		extract($_POST);
		if( $kid && $type ){
			$adcity = $this->obj->DB_select_all("city_class","`keyid`='$kid' order by sort asc");
			if($type=="2"){
				$style="|--------";
			}else{
				$style="|----------------";
			}
			if(is_array($adcity)){
				foreach($adcity as $key=>$value){
					if($type=="2"){
						$img="<b  id=\"img".$value['id']."\"><a href=\"javascript:;\" onClick=\"addcity('".$value['id']."','3','son');\"><img src=\"images/iconv/jia.png\" /></a></b>";
					}else{
						$img="";
					}
					$html.="<tr  align=\"left\" class=\"parent".$kid."\" id=\"".$value["id"]."\" style=\"display:;\">";
					$html.="<td class=\"ud\"> <input type=\"checkbox\" class=\"checkbox_all\" name=\"checkbox_all\" value=\"".$value["id"]."\" onclick=\"get_comindes_jobid();\"></td>";
					$html.="<td class=\"ud\"><input type=\"text\" name=\"citysort_".$value["id"]."\" id=\"citysort_".$value["id"]."\" value=\"".$value[sort]."\" class=\"input-text\" size=\"3\"></td>";
					$html.="<td class=\"ud\">".$style."<input class=\"input-text\" type=\"text\" id=\"cityname_".$value["id"]."\" name=\"cityname_".$value["id"]."\" value=\"".$value["name"]."\" /> ".$img."</td>";
					$html.="<td class=\"ud\"><select id=\"letter_".$value["id"]."\" name=\"letter_".$value["id"]."\">";
					foreach($city_ABC as $k=>$v){
						if($value['letter']==$v){
							$checked="selected";
						}else{
							$checked="";
						}
						$html.="<option ".$checked.">".$v."</option>";
					}
					if($value['display']=="1"){
						$option1="selected";
						$option2="";
					}else{
						$option2="selected";
						$option1="";
					}
					$html.=" <td class=\"ud\"><select id=\"display_".$value['id']."\" name=\"display_".$value['id']."\"><option value=\"1\" ".$option1." >是</option><option value=\"0\" ".$option2.">否</option></select></td> ";
					if($type=="2"){
						if($value['sitetype']=="1"){
							$site1="selected";
							$site2="";
						}else{
							$site2="selected";
							$site1="";
						}
						$html.=" <td class=\"ud\"><select id=\"sitetype_".$value['id']."\" name=\"sitetype_".$value['id']."\"><option value=\"0\" ".$site2." >否</option><option value=\"1\" ".$site1.">是</option></select></td> ";
					}else{
						$html.="<td class=\"ud\"></td>";
					}
					$html.="<td class=\"ud\"><input class=\"admin_submit4\" onclick=\"checkedtr('".$value['id']."');\" type=\"button\" name=\"update\" value=\"更新\" /> | <a href=\"javascript:;\"><img src=\"images/iconv/del_icon2.gif\" onclick=\"delsingle('".$value['id']."','2');\" alt=\"删除\" title=\"删除\"/></a></td>";
					$html.="</select> </td>";
					$html.="</tr>";
				}
			}
			echo $html;die;
		}
	}
	
	function del_action(){
		if((int)$_POST['delid']){
			$where="`id`='".$_POST['delid']."' or `keyid`='".$_POST['delid']."'";
			if($_POST['type']=="1"){
				$city_arr = $this->obj->DB_select_all("city_class",$where);
				foreach($city_arr as $key=>$value){
					$id_arr[] = $value['id'];
				}
				$idlist = @implode(",",$id_arr);
				$where="";
				$where = "`id` IN ($idlist) or `keyid` IN ($idlist)";
			}else{
				$where="`id`='".$_POST['delid']."' or `keyid`='".$_POST['delid']."'";
			}
			$del=$this->obj->DB_delete_all("city_class",$where," ");
			$this->obj->admin_log("删除城市");
			if(isset($del)){
				$this->cache_action();
				echo "1";
			}else{
				echo "2";
			}
		}
		die;
	}

	function Single_action(){
		extract($_POST);
		$name =$this->stringfilter($name);
		if($name!=""){
			$this->obj->DB_update_all("city_class","`name`='$name',`sort`='$c_sort',`letter`='$letter',`display`='$display',`sitetype`='$sitetype'","`id`='".$id."'");
			$this->cache_action();
			$this->obj->admin_log("更新城市(ID:".$id.")");
			echo "1";
		}else{
			echo "2";
		}
		die;
	}

	function cache_action(){
		include(LIB_PATH."cache.class.php");
		$cacheclass= new cache("../plus/",$this->obj);
		$makecache=$cacheclass->city_cache("city.cache.php");
	}
}
?>