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
class comtpl_controller extends company{
   function index_action() {
		$statis=$this->company_satic();
		$this->yunset("buytpl",@explode(",",$statis['comtpl_all']));
		$list=$this->obj->DB_select_all("company_tpl","`status`='1' order by id desc");
		$this->yunset("list",$list);
		$this->public_action();
		$this->yunset("js_def",6);
		$this->com_tpl('comtpl');
	}
	function settpl_action(){
		if($_POST['savetpl']){
			$list=$this->obj->DB_select_all("company_tpl","`status`='1' order by id desc");
			foreach($list as $v){
				$tplid[]=$v['id'];
			}
			$statis=$this->company_satic();
			if(in_array($_POST['tpl'],$tplid)){
				$row=$this->obj->DB_select_once("company_tpl","`id`='".(int)$_POST['tpl']."'");
				if(strstr($statis['comtpl_all'],$row['url'])==false){
					if($row['type']==1){
						if($statis['integral']<$row['price']){
							$this->obj->ACT_layer_msg("����".$this->config['integral_pricename']."���㣬���ȳ�ֵ��",8,"index.php?c=pay");
						}
						$content="��������ҵģ�� <a href=\"".$this->config['sy_weburl']."/company/index.php?id=".$this->uid."\">".$_POST[tplname.$_POST['tpl']]."</a>";
						$this->addstate($content);
						$nid=$this->obj->company_invtal($this->uid,$row['price'],false,"������ҵģ��",true,2,'integral',15);
					}else{
						if($statis['pay']<$row['price'])
						{
							$this->obj->ACT_layer_msg("���������������ȳ�ֵ��",8,"index.php?c=pay");
						}
						$content="��������ҵģ�� <a href=\"".$this->config['sy_weburl']."/company/index.php?id=".$this->uid."\">".$_POST[tplname.$_POST['tpl']]."</a>";
						$this->addstate($content);
						$nid=$this->obj->company_invtal($this->uid,$row['price'],false,"������ҵģ��",true,2,"pay",15);
					}
					if($statis['comtpl_all']==''){
						$this->obj->update_once("company_statis",array("comtpl_all"=>$row['url']),array("uid"=>$this->uid));
					}else{
						$this->obj->DB_update_all("company_statis","`comtpl_all`=concat(`comtpl_all`,',$row[url]')","`uid`='".$this->uid."'");
					}
				}
				$oid=$this->obj->update_once("company_statis",array("comtpl"=>$row['url']),array("uid"=>$this->uid));
				if($oid){
					$this->obj->member_log("������ҵģ��");
					$this->obj->ACT_layer_msg("���óɹ���",9,"index.php?c=comtpl");
				}else{
					$this->obj->ACT_layer_msg("����ʧ�ܣ����Ժ����ԣ�",8,$_SERVER['HTTP_REFERER']);
				}
			}else{
 				$this->obj->ACT_layer_msg("����ȷѡ��ģ�棡",8,"index.php?c=comtpl");
			}
		}
	}
}
?>