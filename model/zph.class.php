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
class zph_controller extends common
{
	function public_action()
	{
		if($this->config['sy_zhp_web']=="2")
		{
			header("location:".$this->url("index","error"));
		}
	}
	function index_action()
	{
		$this->public_action();
		$this->seo("zph");
		$this->yun_tpl(array('index'));
	}
	function show_action()
	{
		$this->public_action();
		$this->seo("zph_show");
		$this->yun_tpl(array('zphshow'));
	}
	function com_action()
	{
		$this->public_action();
		$this->job_cache();
		$row=$this->obj->DB_select_once("zhaopinhui","`id`='".(int)$_GET['id']."'");
		$this->yunset("row",$row);
		$where="`zid`='".(int)$_GET['id']."' and status='1'";
		$urlarr["c"]=$_GET['c'];
		$urlarr["id"]=$_GET['id'];
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index",$_GET['m'],$urlarr,"1");
		$rows=$this->get_page("zhaopinhui_com",$where."  order by id desc",$pageurl,"13");
		if(is_array($rows)){
			foreach($rows as $v)
			{
				$uid[]=$v['uid'];
			}
			$com=$this->obj->DB_select_alls("company","company_statis","a.uid=b.uid and a.uid in (".@implode(",",$uid).")","a.uid,a.name,b.comtpl");
			foreach($rows as $key=>$v)
			{
				foreach($com as $val)
				{
					if($v['uid']==$val['uid'])
					{
						$rows[$key]['comname']=$val['name'];
						if($val['comtpl'] && $val['comtpl']!="default")
						{
							$rows[$key]['url']=$this->curl(array("url"=>"id:".$v[uid]))."#job";
						}else{
							$rows[$key]['url']=$this->curl(array("url"=>"tp:post,id:".$v[uid]));
						}
					}
				}
				$rows[$key]['job']=$this->obj->DB_select_all("company_job","id in (".$v['jobid'].") and `status`<>'1' and `r_status`<>'2'","name,id");
			}
		}
		$this->yunset("rows",$rows);
		$data['zph_title']=$row['title'];
		$data['zph_desc']=$this->obj->GET_content_desc($row['body']);
		$this->data=$data;
		$this->seo("zph_show");
		$this->yun_tpl(array('zphcom'));
	}
}
?>