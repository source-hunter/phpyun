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
//Ϊ�˱����ظ������ļ�����ɴ��󣬼����жϺ����Ƿ���ڵ�������
@$page = $_GET['page'];
if(!function_exists('pageft')){
//���庯��pageft(),���������ĺ���Ϊ��
//$totle����Ϣ������
//$displaypg��ÿҳ��ʾ��Ϣ������������ΪĬ����20��
//$url����ҳ�����е����ӣ����˼��벻ͬ�Ĳ�ѯ��Ϣ��page����Ĳ��ֶ������URL��ͬ��
//������Ĭ��ֵ������Ϊ��ҳURL����$_SERVER["REQUEST_URI"]����������Ĭ��ֵ���ұ�ֻ��Ϊ���������Ը�Ĭ��ֵ��Ϊ���ַ������ں����ڲ�������Ϊ��ҳURL��
function pageft($totle,$displaypg=20,$shownum=0,$showtext=0,$showselect=0,$showlvtao=7,$url=''){

//���弸��ȫ�ֱ�����
//$page����ǰҳ�룻
//$firstcount�������ݿ⣩��ѯ����ʼ�
//$pagenav��ҳ�浼�������룬�����ڲ���û�н��������
//$_SERVER����ȡ��ҳURL��$_SERVER["REQUEST_URI"]�������롣
global $page,$firstcount,$pagenav,$_SERVER;

//Ϊʹ�����ⲿ���Է�������ġ�$displaypg��������Ҳ��Ϊȫ�ֱ�����ע��һ���������¶���Ϊȫ�ֱ�����ԭֵ�����ǣ���������������¸�ֵ��
$GLOBALS["displaypg"]=$displaypg;


if(!$page) $page=1;

//���$urlʹ��Ĭ�ϣ�����ֵ����ֵΪ��ҳURL��
if(!$url){ $url=$_SERVER["REQUEST_URI"];}

//URL������
$parse_url=parse_url($url);
$url_query=$parse_url["query"]; //����ȡ��URL�Ĳ�ѯ�ִ�
if($url_query){
//��ΪURL�п��ܰ�����ҳ����Ϣ������Ҫ����ȥ�����Ա�����µ�ҳ����Ϣ��
//�����õ���������ʽ����ο���PHP�е�������ʽ��
$url_query=ereg_replace("(^|&)page=$page","",$url_query);

//��������URL�Ĳ�ѯ�ִ��滻ԭ����URL�Ĳ�ѯ�ִ���
$url=str_replace($parse_url["query"],$url_query,$url);

//��URL���page��ѯ��Ϣ��������ֵ��
if($url_query) $url.="&page"; else $url.="page";
}else {
$url.="?page";
}

//ҳ����㣺
$lastpg=ceil($totle/$displaypg); //���ҳ��Ҳ����ҳ��
$page=min($lastpg,$page);
$prepg=$page-1; //��һҳ
$nextpg=($page==$lastpg ? 0 : $page+1); //��һҳ
$firstcount=($page-1)*$displaypg;

//��ʼ��ҳ���������룺
if ($showtext==1){
$pagenav="<span class='disabled'>".($totle?($firstcount+1):0)."-".min($firstcount+$displaypg,$totle)."/$totle ��¼</span><span class='disabled'>$page/$lastpg ҳ</span>";
}else{
$pagenav="";
}
//���ֻ��һҳ������������
if($lastpg<=1) return false;

if($prepg) $pagenav.="<a href='$url=1'>��ҳ</a>"; else $pagenav.='<span class="disabled">��ҳ</span>';
if($prepg) $pagenav.="<a href='$url=$prepg'>��һҳ</a>"; else $pagenav.='<span class="disabled">��һҳ</span>';
if ($shownum==1){
	$o=$showlvtao;//�м�ҳ����ܳ��ȣ�Ϊ����
	$u=ceil($o/2);//����$o���㵥��ҳ����$u
	$f=$page-$u;//���ݵ�ǰҳ$currentPage�͵�����$u�������һҳ����ʼ����
	//str_replace('{p}',,$fn)//�滻��ʽ
	if($f<0){$f=0;}//����һҳС��0ʱ����ֵΪ0
	$n=$lastpg;//��ҳ��,20ҳ
	if($n<1){$n=1;}//������С��1ʱ����ֵΪ1
	if($page==1){
		$pagenav.='<span class="current">1</span>';
	}else{
		$pagenav.="<a href='$url=1'>1</a>";
	}
	///////////////////////////////////////
	for($i=1;$i<=$o;$i++){
		if($n<=1){break;}//����ҳ��Ϊ1ʱ
		$c=$f+$i;//�ӵ�$c��ʼ�ۼӼ���
		if($i==1 && $c>2){
			$pagenav.='...';
		}
		if($c==1){continue;}
		if($c==$n){break;}
		if($c==$page){
			$pagenav.='<span class="current">'.$page.'</span>';
		}else{
			$pagenav.="<a href='$url=$c'>$c</a>";
		}
		if($i==$o && $c<$n-1){
			$pagenav.='...';
		}
		if($i>$n){break;}//����ҳ��С��ҳ�����ʱ
	}
	if($page==$n && $n!=1){
		$pagenav.='<span class="current">'.$n.'</span>';
	}else{
		$pagenav.="<a href='$url=$n'>$n</a>";
		}
}

if($nextpg) $pagenav.="<a href='$url=$nextpg'>��һҳ</a>"; else $pagenav.='<span class="disabled">��һҳ</span>';
if($nextpg) $pagenav.="<a href='$url=$lastpg'>βҳ</a>"; else $pagenav.='<span class="disabled">βҳ</span>';
if ($showselect==1){
//������ת�б�ѭ���г�����ҳ�룺
$pagenav.="����<select name='topage' size='1' onchange='window.location=\"$url=\"+this.value'>\n";
for($i=1;$i<=$lastpg;$i++){
if($i==$page) $pagenav.="<option value='$i' selected>$i</option>\n";
else $pagenav.="<option value='$i'>$i</option>\n";
}
$pagenav.="</select>ҳ";
}
}
}
?>