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
class paylog_controller extends company
{
	function index_action(){
		include(CONFIG_PATH."db.data.php");
		$this->yunset("arr_data",$arr_data);
		$this->public_action();
		if($_GET['consume']=="ok")
		{
			$urlarr=array("c"=>"paylog","consume"=>"ok","page"=>"{{page}}");
			$pageurl=$this->url("index","index",$urlarr);
			$where="`com_id`='".$this->uid."'";
			$where.=" and `order_price`<0 order by pay_time desc";
			$rows = $this->get_page("company_pay",$where,$pageurl,"10");
			if(is_array($rows)){
				foreach($rows as $k=>$v)
				{
					$rows[$k]['pay_time']=date("Y-m-d H:i:s",$v['pay_time']);
				}
			}
			$this->yunset("rows",$rows);
			$this->yunset("ordertype","ok");
		}else{
			$urlarr=array("c"=>"paylog","page"=>"{{page}}");
			$pageurl=$this->url("index","index",$urlarr);
			$where="`uid`='".$this->uid."'  order by order_time desc";
			$rows=$this->get_page("company_order",$where,$pageurl,"10");
			if($rows&&is_array($rows)&&$this->config['sy_com_invoice']=='1'){
				$last_days=strtotime("-7 day");
				foreach($rows as $key=>$val){
					if($val['order_time']>=$last_days){
						$rows[$key]['invoice']='1';
					}
				}
				$this->yunset("rows",$rows);
			}
		}
		if($_POST['submit']){
			if(trim($_POST['order_remark'])==""){
				$this->obj->ACT_layer_msg("备注不能为空！",8,$_SERVER['HTTP_REFERER']);
			}
			$nid=$this->obj->DB_update_all("company_order","`order_remark`='".trim($_POST['order_remark'])."'","`id`='".(int)$_POST['id']."' and `uid`='".$this->uid."'");
			if($nid)
			{
				$this->obj->member_log("修改订单备注");
				$this->obj->ACT_layer_msg("修改成功！",9,"index.php?c=paylog");
			}else{
				$this->obj->ACT_layer_msg("修改失败！",8,"index.php?c=paylog");
			}
		}

		$this->yunset("js_def",4);
		$this->com_tpl('paylog');
	}
	function saveinvoice_action(){
		if($_POST['rid']){
			$this->obj->update_once("invoice_record","`title`=''","`id`='".intval($_POST['rid'])."' and `uid`='".$this->uid."'");
			$data['title']=$_POST['title'];
			$data['link_man']=$_POST['link_man'];
			$data['link_moblie']=$_POST['link_moblie'];
			$data['address']=$_POST['address'];
			$nid=$this->obj->update_once('invoice_record',$data,array('id'=>intval($_POST['rid']),'uid'=>$this->uid));
		}else{
			$value.="`order_id`='".$_POST['order_id']."',";
			$value.="`oid`='".$_POST['oid']."',";
			$value.="`uid`='".$this->uid."',";
			$value.="`title`='".$_POST['title']."',";
			$value.="`link_man`='".$_POST['link_man']."',";
			$value.="`link_moblie`='".$_POST['link_moblie']."',";
			$value.="`address`='".$_POST['address']."',";
			$value.="`status`='0',";
			$value.="`addtime`='".time()."'";
			$nid=$this->obj->DB_insert_once("invoice_record",$value);
			if($nid){$this->obj->update_once("company_order",array("is_invoice"=>'1'),array('order_id'=>$_POST['order_id'],'uid'=>$this->uid));}
		}
		$nid?$this->obj->ACT_layer_msg("操作成功！",9,$_SERVER['HTTP_REFERER']):$this->obj->ACT_layer_msg("操作失败！",8,$_SERVER['HTTP_REFERER']);
	}
	function del_action(){
		if($_COOKIE['usertype']!='2' || $this->uid==''){
			echo '0';die;
		}else{
			$oid=$this->obj->DB_select_once("company_order","`uid`='".$this->uid."' and `id`='".(int)$_GET['id']."' and `order_state`='1'");
			if(empty($oid)){
				echo '0';die;
			}else{
				$this->obj->DB_delete_all("company_order","`id`='".$oid['id']."'");
				echo '1';die;
			}
		}
	}
	function invoice_action()
	{
		$invoice_record=$this->obj->DB_select_once("invoice_record","`order_id`='".$_POST['order_id']."' and `uid`='".$this->uid."'");
		if($invoice_record['id']){
			$data['status']='1';
			$data['id']=iconv("gbk","utf-8",$invoice_record['id']);
			$data['title']=iconv("gbk","utf-8",$invoice_record['title']);
			$data['link_man']=iconv("gbk","utf-8",$invoice_record['link_man']);
			$data['link_moblie']=iconv("gbk","utf-8",$invoice_record['link_moblie']);
			$data['address']=iconv("gbk","utf-8",$invoice_record['address']);
		}else{
			$data['status']='0';
		}
		$data = json_encode($data);
		echo $data;die;
	}
}
?>