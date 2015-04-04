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
class SplitWord
{
	var $TagDic = Array();
	var $RankDic = Array();
	var $OneNameDic = Array();
	var $TwoNameDic = Array();
	var $SourceString = '';
	var $ResultString = '';
	var $SplitChar = ',';
	var $SplitLen = 4; 
	var $EspecialChar = "��|��|��";
	var $NewWordLimit = "��|��|��|��|��|��|��|��|��|��|��|��|��|��|��|��";
	var $CommonUnit = "��|��|��|ʱ|��|��|��|Ԫ|��|ǧ|��|��|λ|��";
	var $CnNumber = "��|��|��|��|��|��|��|��|��|��|��|��|��|��";
	var $CnSgNum = "һ|��|��|��|��|��|��|��|��|ʮ|��|ǧ|��|��|��";
	var $MaxLen = 13; 
	var $MinLen = 3;  
	var $CnTwoName = "��ľ �Ϲ� ���� ��ԯ ��� ���� ���� ���� ���� ���� ˾ͽ ˾�� �Ϲ� ŷ�� ���� ���� ���� ���� ���� ���� Ľ�� ˾�� �ĺ� ��� ���� ���� �ʸ� ξ�� ����";
	var $CnOneName = "��Ǯ��������֣��������������������������ʩ�ſײ��ϻ���κ�ս���л������ˮ��������˸��ɷ�����³Τ������ﻨ������Ԭ��ۺ��ʷ�Ʒ����Ѧ�׺����������ޱϺ�����������ʱ��Ƥ���뿵����Ԫ������ƽ��������Ҧ�ۿ�����ë����ױ���갼Ʒ��ɴ�̸��é���ܼ�������ף������������ϯ����ǿ��·¦Σ��ͯ�չ�÷ʢ�ֵ�����������Ĳ��﷮���������֧�¾̹�¬Ī�������Ѹɽ�Ӧ�������ڵ��������������ʯ�޼�ť�������ϻ���½��������춻���κ�ӷ����ഢ���������ɾ��θ����ڽ��͹�����ȳ������ȫۭ�����������������ﱩ�����������������ղ����Ҷ��˾��۬�輻��ӡ�ް׻���̨�Ӷ����̼���׿�����ɳ����������ܲ�˫��ݷ����̷�����̼������Ƚ��۪Ӻ�S�ɣ���ţ��ͨ�����༽ۣ����ũ�±�ׯ�̲���ֳ�Ľ����ϰ�°���������������θ����߾Ӻⲽ�����������Ŀܹ�»�ڶ�Ź�����εԽ��¡ʦ�������˹��������������Ǽ��Ŀ���ɳ��ᳲ������������";
 
  function SplitWord(){
  	$this->__construct();
  }

  function __construct(){
  	
  	for($i=0;$i<strlen($this->CnOneName);$i++)
  	{
  		$this->OneNameDic[$this->CnOneName[$i].$this->CnOneName[$i+1]] = 1;
  		$i++;
  	}
  	$twoname = explode(" ",$this->CnTwoName);
  	foreach($twoname as $n){ $this->TwoNameDic[$n] = 1; }
  	unset($twoname);
  	unset($this->CnOneName);
  	unset($this->CnTwoName);
  	
  	$dicfile = dirname(__FILE__)."/keyword.csv";
  	$fp = fopen($dicfile,'r');
  	while($line = fgets($fp,256)){
  		  $ws = @explode(' ',$line);
  		  $this->TagDic[$ws[0]] = $ws[1];
  		  $this->RankDic[strlen($ws[0])][$ws[0]] = $ws[2];
  	}
  	fclose($fp);
  }
 
  function Clear()
  {
  	@fclose($this->QuickDic);
  }
 
  function SetSource($str){
  	$this->SourceString = trim($this->ReviseString($str));
  	$this->ResultString = "";
  }
  function NotGBK($str)
  {
    if($str=="") return "";
  	if( ord($str[0])>0x80 ) return false;
  	else return true;
  }

  function SplitRMM($str=""){
  	if($str!="") $this->SetSource(trim($str));
  	if($this->SourceString=="") return "";
  
  	$this->SourceString = $this->ReviseString($this->SourceString);

  	$spwords = explode(" ",$this->SourceString);
  	$spLen = count($spwords);
  	$spc = $this->SplitChar;
  	for($i=($spLen-1);$i>=0;$i--){
  		if(trim($spwords[$i])=="") continue;
  		if($this->NotGBK($spwords[$i])){
  			if(ereg("[^0-9\.\+\-]",$spwords[$i]))
  			{ $this->ResultString = $spwords[$i].$spc.$this->ResultString; }
  			else
  			{
  				$nextword = "";
  				@$nextword = substr($this->ResultString,0,strpos($this->ResultString," "));
  				if(ereg("^".$this->CommonUnit,$nextword)){
  					$this->ResultString = $spwords[$i].$this->ResultString;
  				}else{
  					$this->ResultString = $spwords[$i].$spc.$this->ResultString;
  				}
  			}
  		}
  		else
  		{
  		  $c = $spwords[$i][0].$spwords[$i][1];
  		  $n = hexdec(bin2hex($c));
  		  if($c=="��") 
  		  { $this->ResultString = $spwords[$i].$spc.$this->ResultString; }
  		  else if($n>0xA13F && $n < 0xAA40)
  		  { $this->ResultString = $spwords[$i].$spc.$this->ResultString; }
  		  else 
  		  {
  		  	if(strlen($spwords[$i]) <= $this->SplitLen)
  		  	{
  		  		
  		  		if(ereg($this->EspecialChar."$",$spwords[$i],$regs)){
  		  				$spwords[$i] = ereg_replace($regs[0]."$","",$spwords[$i]).$spc.$regs[0];
  		  		}
  		  	
  		  		if(!ereg("^".$this->CommonUnit,$spwords[$i]) || $i==0){
  		  			$this->ResultString = $spwords[$i].$spc.$this->ResultString;
  		  		}else{
  		  			$this->ResultString = $spwords[$i-1].$spwords[$i].$spc.$this->ResultString;
  		  			$i--;
  		  		}
  		  	}
  		  	else
  		  	{
  		  		$this->ResultString = $this->RunRMM($spwords[$i]).$spc.$this->ResultString;
  		  	}
  		  }
  	  }
  	}
  	return $this->ResultString;
  }
  function getkeyword($str){
	  $keyword=$this->SplitRMM($str);
	  $keyarr=explode($this->SplitChar,$keyword);
	  if(is_array($keyarr)){
		  $keywordarr=array();
		  foreach($keyarr as $key=>$v){
			 if(strlen($v)>2){
			  	$keywordarr[]=$v;
				if(in_array($v,$keywordarr)){
					$keywordarr2[$v]++;
				}else{
					$keywordarr2[$v]=1;
					$keywordarr[]=$v;
				}
			 }
		  }
	  }
	  $keywordarr3=array();
	  foreach($keywordarr2 as $key=>$v){
		  	if($keywordarr3[$v]!=""){
				if($keywordarr3[$v+1]!=""){
					if($keywordarr3[$v+2]!=""){
						if($keywordarr3[$v+3]!=""){
							if($keywordarr3[$v+4]!=""){
							}else{
								$keywordarr3[$v+4]=$key;
							}
						}else{
							$keywordarr3[$v+3]=$key;
						}
					}else{
						$keywordarr3[$v+2]=$key;
					}
				}else{
					$keywordarr3[$v+1]=$key;
				}
			}else{
				$keywordarr3[$v]=$key;
			}
	  }
	  krsort($keywordarr3);
	  return $keywordarr3;
  }
  function getarray($array,$i){
		if($array[$i]!=""){
			return $this->getarray($array,$i++);
		}else{
			return $i;
		}
  }
  
  function RunRMM($str)
  {
  	$spc = $this->SplitChar;
  	$spLen = strlen($str);
  	$rsStr = "";
  	$okWord = "";
  	$tmpWord = "";
  	$WordArray = Array();
  	
  	for($i=($spLen-1);$i>=0;)
  	{
  		
  		if($i<=$this->MinLen){
  			if($i==1){
  			  $WordArray[] = substr($str,0,2);
  			
  		  }else
  			{
  			   $w = substr($str,0,$this->MinLen+1);
  			   if($this->IsWord($w)){
  			   	$WordArray[] = $w;
  			   }else{
  				   $WordArray[] = substr($str,2,2);
  				   $WordArray[] = substr($str,0,2);
  				  
  			   }
  		  }
  			$i = -1; break;
  		}
  		
  		if($i>=$this->MaxLen) $maxPos = $this->MaxLen;
  		else $maxPos = $i;
  		$isMatch = false;
  		for($j=$maxPos;$j>=0;$j=$j-2){
  			 $w = substr($str,$i-$j,$j+1);
  			 if($this->IsWord($w)){
  			 	$WordArray[] = $w;
  			 
  			 	$i = $i-$j-1;
  			 	$isMatch = true;
  			 	break;
  			 }
  		}
  		if(!$isMatch){
  			if($i>1) {
  				$WordArray[] = $str[$i-1].$str[$i];
  				
  				$i = $i-2;
  			}
  		}
  	}
  	$rsStr = $this->ParOther($WordArray);
  	return $rsStr;
  }
 
  function ParOther($WordArray)
  {
  	$wlen = count($WordArray)-1;
  	$rsStr = "";
  	$spc = $this->SplitChar;
  	for($i=$wlen;$i>=0;$i--)
  	{
  		
  		if(ereg($this->CnSgNum,$WordArray[$i])){
  			$rsStr .= $spc.$WordArray[$i];
  			if($i>0 && ereg("^".$this->CommonUnit,$WordArray[$i-1]))
  			{ $rsStr .= $WordArray[$i-1]; $i--; }
  			else{
  				while($i>0 && ereg($this->CnSgNum,$WordArray[$i-1]))
  				{ $rsStr .= $WordArray[$i-1]; $i--; }
  			}
  			continue;
  		}
  		
  		if(strlen($WordArray[$i])==4 && isset($this->TwoNameDic[$WordArray[$i]]))
  		{
  			$rsStr .= $spc.$WordArray[$i];
  			if($i>0&&strlen($WordArray[$i-1])==2){
  				$rsStr .= $WordArray[$i-1];$i--;
  				if($i>0&&strlen($WordArray[$i-1])==2){ $rsStr .= $WordArray[$i-1];$i--; }
  			}
  		}
  		
  		else if(strlen($WordArray[$i])==2 && isset($this->OneNameDic[$WordArray[$i]]))
  		{
  			$rsStr .= $spc.$WordArray[$i];
  			if($i>0&&strlen($WordArray[$i-1])==2){
  				 $rsStr .= $WordArray[$i-1];$i--;
  				 if($i>0 && strlen($WordArray[$i-1])==2){ $rsStr .= $WordArray[$i-1];$i--; }
  			}
  		}
  		
  		else{
  			$rsStr .= $spc.$WordArray[$i];
  		}
  	}
  	
  	$rsStr = preg_replace("/^".$spc."/","",$rsStr);
  	return $rsStr;
  }
 
  function IsWord($okWord){
  	$slen = strlen($okWord);
  	if($slen > $this->MaxLen) return false;
  	else return isset($this->RankDic[$slen][$okWord]);
  }
 
  function ReviseString($str)
  {
  	$spc = $this->SplitChar;
    $slen = strlen($str);
    if($slen==0) return '';
    $okstr = '';
    $prechar = 0;
    for($i=0;$i<$slen;$i++){
      if(ord($str[$i]) < 0x81)
      {
       
        if(ord($str[$i]) < 33){
          if($prechar!=0&&$str[$i]!="\r"&&$str[$i]!="\n") $okstr .= $spc;
          $prechar=0;
          continue;
        }else if(ereg("[^0-9a-zA-Z@\.%#:/\\&_-]",$str[$i]))
        {
          if($prechar==0)
          {	$okstr .= $str[$i]; $prechar=3;}
          else
          { $okstr .= $spc.$str[$i]; $prechar=3;}
        }else
        {
        	if($prechar==2||$prechar==3)
        	{ $okstr .= $spc.$str[$i]; $prechar=1;}
        	else
        	{
        	  if(ereg("@#%:",$str[$i])){ $okstr .= $str[$i]; $prechar=3; }
        	  else { $okstr .= $str[$i]; $prechar=1; }
        	}
        }
      }
      else{
       
        if($prechar!=0 && $prechar!=2) $okstr .= $spc;
       
        if(isset($str[$i+1])){
          $c = $str[$i].$str[$i+1];

          if(ereg($this->CnNumber,$c))
          { $okstr .= $this->GetAlabNum($c); $prechar = 2; $i++; continue; }

          $n = hexdec(bin2hex($c));
          if($n>0xA13F && $n < 0xAA40)
          {
            if($c=="��"){
            	if($prechar!=0) $okstr .= $spc." ��";
            	else $okstr .= " ��";
            	$prechar = 2;
            }
            else if($c=="��"){
            	$okstr .= "�� ";
            	$prechar = 3;
            }
            else{
            	if($prechar!=0) $okstr .= $spc.$c;
            	else $okstr .= $c;
            	$prechar = 3;
            }
          }
          else{
            $okstr .= $c;
            $prechar = 2;
          }
          $i++;
        }
      }
    }
    return $okstr;
  }

  function FindNewWord($spwords,$maxlen=6)
  {
    $okstr = '';
    $ws = explode(' ',$spwords);
    $newword = '';
    $nws = '';
    foreach($ws as $w)
    {
      $w = trim($w);
      if(strlen($w)==2 && !preg_match("/[0-9a-zA-Z]/",$w) && !preg_match("/".$this->NewWordLimit."/",$w) )
      { $newword .= " ".$w;}
      else
      {
        if($newword!="")
        {
          $nw = str_replace(' ','',$newword);
          if(strlen($nw)>2)
          {
            if(strlen($nw) <= $maxlen){ $okstr .= ' '.$nw; $nws[$nw] = 0; }
            else $okstr .= ' '.$newword;
          }
          else
          { $okstr .= ' '.$newword; }
          $newword = '';
        }
        $okstr .= ' '.$w;
      }
    }
    if($newword!="") $okstr .= $newword;
    $okstr = preg_replace("/ {1,}/"," ",$okstr);
    if(is_array($nws))
    {
      $this->m_nws = $nws;
      foreach($nws as $k=>$w)
      {
        $w = "";
        for($i=0;$i<strlen($k);$i++){
          if( ord($k[$i]) > 0x80 ){
            $w .= " ".$k[$i];
            if(isset($k[$i+1])){ $w .= $k[$i+1]; $i++;}
          }
          else
            $w .= " ".$k[$i];
          $w .= " ";
        }
        $w = preg_replace("/ {1,}/"," ",$w);
        $okstr = str_replace($w," ".$k." ",$okstr);
        $okstr = str_replace($k." "," ".$k." ",$okstr);
        $okstr = str_replace(" ".$k," ".$k." ",$okstr);
      }
    }
    return $okstr;
  }

  function GetIndexText($okstr,$ilen=-1)
  {
    if($okstr=="") return "";
    $ws = explode(" ",$okstr);
    $okstr = "";
    $wks = "";
    foreach($ws as $w)
    {
      $w = trim($w);
     
      if(strlen($w)<2 || strlen($w)>6) continue;
    
      if(!ereg("[^0-9:-]",$w)) continue;
      if(strlen($w)==2&&ord($w[0])>0x80) continue;
      if(isset($wks[$w])) $wks[$w]++;
      else $wks[$w] = 1;
    }
    if(is_array($wks))
    {
      arsort($wks);
      if($ilen==-1)
      { foreach($wks as $w=>$v) $okstr .= $w." "; }
      else
      {
        foreach($wks as $w=>$v){
          if((strlen($okstr)+strlen($w)+1)<$ilen) $okstr .= $w." ";
          else break;
        }
      }
    }
    return trim($okstr);
  }
 
  function GetAlabNum($fnum)
  {
	  $nums = array("��","��","��","��","��","��","��","��","��","��","��","��","��","��");
	  $fnums = "0123456789+-%.";
	  for($i=0;$i<count($nums);$i++){
	  	if($nums[$i]==$fnum) return $fnums[$i];
	  }
	  return $fnum;
  }
}
?>