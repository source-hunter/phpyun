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
class search_controller extends company
{
	function index_action(){
		$this->public_action();
		$urlarr["c"]="search";
		$where="`uid`='".$this->uid."'";
		if($_GET['keyword']){
			$where.=" and name like '%".$_GET['keyword']."%'";
			$urlarr['keyword']=$_GET['keyword'];
		}
		$urlarr["page"]="{{page}}";
		$pageurl=$this->url("index","index",$urlarr);
		$rows=$this->get_page("company_job",$where,$pageurl,"10","`id`,`name`,`state`,`sdate`,`edate`,`lastupdate`,`jobhits`");
		if(is_array($rows) && !empty($rows))
		{
			foreach($rows as $v)
			{
				$jobid[]=$v['id'];
			}
			$jobrows=$this->obj->DB_select_all("userid_job","`job_id` in (".$this->pylode(',',$jobid).")");
			if(is_array($jobrows))
			{
				foreach($jobrows as $v)
				{
					$jobnum[$v['job_id']]++;
				}
			}
		}
		$this->company_satic();
		$this->yunset("jobnum",$jobnum);
		$this->yunset("js_def",3);
		$this->com_tpl('search');
	}
}
?>