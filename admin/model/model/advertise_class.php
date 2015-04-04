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
class advertise
{
	function __construct($obj)
	{
		$this->obj = $obj;
		include(APP_PATH."/plus/config.php");
		$this->config = $config;
	}
	function model_ad_arr_action(){
		$show.="<?php\r\n\$ad_label='';\r\n";
		$ad_list = $this->obj->DB_select_all("ad","`is_open`='1' order by `sort` desc,`id` desc");
		if(is_array($ad_list)){
			$time = time();
			foreach($ad_list as $key=>$value){
				$start = @strtotime($value[time_start]." 00:00:00");
				$end = @strtotime($value[time_end]." 23:59:59");
				if($end!=""){
					if($end>$time){
						$end_type = 1;
					}else{
						$end_type = 2;
					}
				}else{$end_type=1;}
				if($start&&$start<$time && $end_type==1 && $value[is_check]=="1"){
					extract($value);
					if($ad_type=="word"){
						$show.= "\$ad_label['$value[class_id]']['ad_$id']['html']=\"<a href='$word_url'>$word_info</a>\";\r\n";
					}elseif($ad_type=="pic"){
						if(@!stripos("ttp://",$pic_url)){
							$pic_url = str_replace("../",$this->config["sy_weburl"]."/",$pic_url);
						}
						$height = $width="";
						if($pic_height){
							$height = "height='$pic_height'";
						}
						if($pic_width){
							$width = "width='$pic_width'";
						}
						$pic_src=$this->config['sy_weburl']."/index.php?c=clickHits&id=".$id;
						if($value['target']==1){
							$show.= "\$ad_label['$value[class_id]']['ad_$id']['html']=\"<a href='$pic_src' target='_blank' rel='nofollow'><img src='$pic_url'  ".$height." ".$width." ></a>\";\r\n";
						}else{
							$show.= "\$ad_label['$value[class_id]']['ad_$id']['html']=\"<a href='$pic_src' rel='nofollow'><img src='$pic_url' ".$height." ".$width." ></a>\";\r\n";
						}
						$show.= "\$ad_label['$value[class_id]']['ad_$id']['pic']=\"$pic_url\";\r\n";
						$show.= "\$ad_label['$value[class_id]']['ad_$id']['src']=\"$pic_src\";\r\n";
					}elseif($ad_type=="flash"){
						if(@!stripos("ttp://",$flash_url)){
							$flash_url = str_replace("../",$this->config["sy_weburl"]."/",$flash_url);
						}
						$show.= "\$ad_label['$value[class_id]']['ad_$id']['html']=\"<object type='application/x-shockwave-flash' data='$flash_url' width='$flash_width' height='$flash_height'><param name='movie' value='$flash_url' /><param value='transparent' name='wmode'></object>\";\r\n";
					}
					$show.= "\$ad_label['$value[class_id]']['ad_$id']['start']=\"".@strtotime(date('Y-m-d H:i:s',$start))."\";\r\n";
					$show.= "\$ad_label['$value[class_id]']['ad_$id']['end']=\"".@strtotime(date('Y-m-d H:i:s',$end))."\";\r\n";
					$show.= "\$ad_label['$value[class_id]']['ad_$id']['type']=\"".$ad_type."\";\r\n";
					$show.="\$ad_label['$value[class_id]']['ad_$id']['name']=\"".$ad_name."\";\r\n";
					$show.="\$ad_label['$value[class_id]']['ad_$id']['did']=\"".$did."\";\r\n";
					$show.="\$ad_label['$value[class_id]']['ad_$id']['id']=\"".$id."\";\r\n";
					$show.="\$ad_label['$value[class_id]']['ad_$id']['class_id']=\"".$value['class_id']."\";\r\n";
				}
			}
		}
		$show.="?>";
		$path = APP_PATH."/plus/pimg_cache.php";
		$fp = @fopen($path,"w");
		@fwrite($fp,$show);
		@fclose($fp);
		@chmod($path,0777);
		$show="";
	}

	function model_saveadd_action($post,$pic=NULL)
	{
		extract($post);
		if(empty($did))
		{
			$did=0;
		}
		$value = "`ad_name`='$ad_name',`target`='$target',`time_start`='$time_start',`time_end`='$time_end',`ad_type`='$ad_type',`class_id`='$class_id',`is_check`='1',`did`='$did',`is_open`='$is_open',`sort`='".$sort."',`remark`='".$remark."'";
		if($ad_type=="word")
		{
			if($pic!="")
			{
				$word_url = $pic;
			}
			$value .= ",`word_info`='$word_info',`word_url`='$word_url'";
			$nid = $this->obj->DB_insert_once("ad",$value);
		}elseif($ad_type=="pic"){
			if($pic!="")
			{
				$pic_url = $pic;
			}
			$pic_src = str_replace("amp;","",$pic_src);
			$value.=",`pic_url`='$pic_url',`pic_src`='$pic_src',`pic_width`='$pic_width',`pic_height`='$pic_height'";
			$nid = $this->obj->DB_insert_once("ad",$value);
		}elseif($ad_type=="flash"){
			if($pic!=""){
				$flash_url = $pic;
			}
			$value.=",`flash_url`='$flash_url',`flash_width`='$flash_width',`flash_height`='$flash_height'";
			$nid = $this->obj->DB_insert_once("ad",$value);
		}else{
			$this->obj->ACT_layer_msg("您还未选择广告类型！",8,"index.php?m=advertise&c=ad_add");
		}
		$this->model_ad_arr_action();
		$this->obj->ACT_layer_msg("广告添加成功！",9,"index.php?m=advertise",2,1);
	}

	function model_modify_save_action($post,$pic)
	{
		extract($post);
		if(empty($did))
		{
			$did=0;
		}
		$value = "`ad_name`='$ad_name',`target`='$target',`time_start`='$time_start',`time_end`='$time_end',`ad_type`='$ad_type',`class_id`='$class_id',`did`='$did',`is_open`='$is_open',`sort`='".$sort."',`remark`='".$remark."'";
		if($ad_type=="word")
		{
			if($pic!="")
			{
				$word_url = $pic;
			}
			$value .= ",`word_info`='$word_info',`word_url`='$word_url'";
			$nid = $this->obj->DB_update_all("ad",$value,"`id`='$id'");

		}elseif($ad_type=="pic"){

			if($pic!="")
			{
				$ad=$this->obj->DB_select_once("ad","`id`='$id'");
				@unlink($ad[pic_url]);
				$pic_url = $pic;
			}
			$pic_src = str_replace("amp;","",$pic_src);
			$value.=",`pic_url`='$pic_url',`pic_src`='$pic_src',`pic_width`='$pic_width',`pic_height`='$pic_height'";
			$nid = $this->obj->DB_update_all("ad",$value,"`id`='$id'");

		}elseif($ad_type=="flash"){
			if($pic!="")
			{
				$ad=$this->obj->DB_select_once("ad","`id`='$id'");
				@unlink($ad[flash_url]);
				$flash_url = $pic;
			}
			$value.=",`flash_url`='$flash_url',`flash_width`='$flash_width',`flash_height`='$flash_height'";
			$nid = $this->obj->DB_update_all("ad",$value,"`id`='$id'");
		}else{
			$this->obj->get_admin_msg("index.php?m=advertise&c=ad_add","您还未选择广告类型");
			$this->obj->ACT_layer_msg("您还未选择广告类型！",8,"index.php?m=advertise&c=ad_add");
		}
		$this->model_ad_arr_action();
		$this->obj->ACT_layer_msg("广告修改成功！",9,$lasturl,2,1);
	}

	function model_del_ad_action($id)
	{
		if($id)
		{
			$ad=$this->obj->DB_select_once("ad","`id`='$id'");
			$this->obj->unlink_pic($ad[pic_url]);
			@unlink($ad[flash_url]);
			$this->obj->DB_delete_all("ad","`id`='$id'");
		}
		$this->model_ad_arr_action();
		$this->layer_msg('删除成功！',9,0,"index.php?m=advertise");

	}

}