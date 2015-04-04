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
				$this->obj->ACT_layer_msg("����(ID:".$value.")�޸ĳɹ���",9,$_SERVER['HTTP_REFERER'],2,1);
			}
			if($_POST['id']){
				$this->obj->DB_delete_all("city_class","`id` IN (".$_POST['id'].") OR `keyid` IN (".$_POST['id'].")"," ");
				$this->cache_action();
				$this->layer_msg( "����(ID:".$_POST['id'].")ɾ���ɹ���",9,1,$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->obj->ACT_layer_msg("��ѡ����Ҫ�޸Ļ���������ĳ��У�",8,$_SERVER['HTTP_REFERER'],2,1);
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
					$html.=" <td class=\"ud\"><select id=\"display_".$value['id']."\" name=\"display_".$value['id']."\"><option value=\"1\" ".$option1." >��</option><option value=\"0\" ".$option2.">��</option></select></td> ";
					if($type=="2"){
						if($value['sitetype']=="1"){
							$site1="selected";
							$site2="";
						}else{
							$site2="selected";
							$site1="";
						}
						$html.=" <td class=\"ud\"><select id=\"sitetype_".$value['id']."\" name=\"sitetype_".$value['id']."\"><option value=\"0\" ".$site2." >��</option><option value=\"1\" ".$site1.">��</option></select></td> ";
					}else{
						$html.="<td class=\"ud\"></td>";
					}
					$html.="<td class=\"ud\"><input class=\"admin_submit4\" onclick=\"checkedtr('".$value['id']."');\" type=\"button\" name=\"update\" value=\"����\" /> | <a href=\"javascript:;\"><img src=\"images/iconv/del_icon2.gif\" onclick=\"delsingle('".$value['id']."','2');\" alt=\"ɾ��\" title=\"ɾ��\"/></a></td>";
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
			$this->obj->admin_log("ɾ������");
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
			$this->obj->admin_log("���³���(ID:".$id.")");
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