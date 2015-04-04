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
class Page {
     public $firstRow;			
     public $listRows;			
     public $parameter;			
     protected $totalPages;		
     protected $totalRows  ;	
     protected $nowPage    ;	
     protected $coolPages   ;	
     protected $rollPage   ;	
     protected $config  =    array('header'=>'����¼','prev'=>'��һҳ','next'=>'��һҳ','first'=>'��һҳ','last'=>'���һҳ','theme'=>' %totalRow% %header% %nowPage%/%totalPage% ҳ %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%'); 

     public function __construct($totalRows,$listRows=6,$parameter='',$rollPage=5) {
         $this->totalRows = $totalRows;
         $this->parameter = $parameter;
         $this->rollPage = $rollPage;
         $this->listRows = $listRows;
         $this->totalPages = ceil($this->totalRows/$this->listRows);
         $this->coolPages  = ceil($this->totalPages/$this->rollPage);
         $this->nowPage  = !empty($_GET['page'])?$_GET['page']:1;
         if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
             $this->nowPage = $this->totalPages;
         }
         $this->firstRow = $this->listRows*($this->nowPage-1);
     }

     public function setConfig($name,$value){
         if(isset($this->config[$name])){
             $this->config[$name]=$value;
        }
     }

     public function show() {
         if(0 == $this->totalRows) return '';
         $p = 'page';
         $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
         $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
         $parse = parse_url($url);
         if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
             unset($params[$p]);
             $url   =  $parse['path'].'?'.http_build_query($params);
         }

        
         $upRow   = $this->nowPage-1;
         $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="<a href='".$url."&".$p."=$upRow'>".$this->config['prev']."</a>";
        }else{
             $upPage="";
         }
         if ($downRow <= $this->totalPages){
             $downPage="<a href='".$url."&".$p."=$downRow'>".$this->config['next']."</a>";
         }else{
             $downPage="";
        }
        
         if($nowCoolPage == 1){
             $theFirst = "";
             $prePage = "";
         }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<a href='".$url."&".$p."=$preRow' >��".$this->rollPage."ҳ</a>";
            $theFirst = "<a href='".$url."&".$p."=1' >".$this->config['first']."</a>";
         }
       if($nowCoolPage == $this->coolPages){
             $nextPage = "";
             $theEnd="";
         }else{
             $nextRow = $this->nowPage+$this->rollPage;
             $theEndRow = $this->totalPages;
             $nextPage = "<a href='".$url."&".$p."=$nextRow' >��".$this->rollPage."ҳ</a>";
             $theEnd = "<a href='".$url."&".$p."=$theEndRow' >".$this->config['last']."</a>";
         }
		
         $linkPage = "";
         for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                 if($page<=$this->totalPages){
                   $linkPage .= "&nbsp;<a href='".$url."&".$p."=$page'>&nbsp;".$page."&nbsp;</a>";
                 }else{
                    break;
                 }
            }else{
                 if($this->totalPages != 1){
                     $linkPage .= "&nbsp;<span class='current'>".$page."</span>";
                }
             }
        }
         $pageStr=str_replace(
             array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'), array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
         return $pageStr;
     }
}
?>