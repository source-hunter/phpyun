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
$arr_tpl = array (
	'emailreg' => array (
		'name'=>'�ʼ�ע��ģ��',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{username}'=>'�û���',
		'{password}'=>'����',
		'{email}'=>'����'
	),
	'emailyqms' => array (
		'name'=>'��������',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{company}'=>'��˾����',
		'{linkman}'=>'��ϵ��',
		'{comtel}'=>'��ϵ�绰',
		'{username}'=>'��ְ������',
		'{jobname}'=>'ְλ����',
		'{comemail}'=>'����'
	),
	'emailgetpass' => array (
		'name'=>'�һ�����',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{username}'=>'�û���',
		'{password}'=>'����'
	),
	'emailfkcg' => array (
		'name'=>'����ɹ�',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{order_id}'=>'���׵���',
		'{price}'=>'���',
		'{date}'=>'ʱ��'
	),
	'emailzzshtg' => array (
		'name'=>'ְλ��˳ɹ�',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{date}'=>'ͨ��ʱ��',
		'{jobname}'=>'ְλ����'
	),
	'emaillock' => array (
		'name'=>'��Ա����',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{date}'=>'ͨ��ʱ��',
		'{lock_info}'=>'����ԭ��',
	),
	'emailuserstatus' => array (
		'name'=>'��Ա����',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{date}'=>'ͨ��ʱ��',
		'{status_info}'=>'���ԭ��',
	),
	'emailzzshwtg' => array (
		'name'=>'ְλ���δͨ��',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{date}'=>'ͨ��ʱ��',
		'{jobname}'=>'ְλ����',
		'{status_info}'=>'���ԭ��'
	),
	'emailsqzw' => array (
		'name'=>'����ְλ',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{jobname}'=>'ְλ����',
		'{date}'=>'����ʱ��'
	),
	'emailcert' => array (
		'name'=>'������֤',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{url}'=>'���ӵ�ַ',
		'{date}'=>'��֤ʱ��'
	),
		'emailcomcert' => array (
		'name'=>'��ҵ��֤',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{comname}'=>'��ҵ����',
		'{certinfo}'=>'���˵��'
	),
		'emailusercert' => array (
		'name'=>'������֤',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{username}'=>'�û���',
		'{certinfo}'=>'���˵��'
	),
		'emailjobed' => array (
		'name'=>'ְλ��������',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{com_name}'=>'��˾����',
		'{job_name}'=>'ְλ����'
	),
	'emailremind' => array (
		'name'=>'�ʼ�����',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
	),
	'emailuserdy' => array (
		'name'=>'�ʼ�����',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{jobname}'=>'ְλ����',
	),
	'emailcomdy' => array (
		'name'=>'�ʼ�����',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{resumename}'=>'��������',
	),
	'emailnotice' => array (
		'name'=>'�Զ�����ְλ֪ͨ',
		'type'=>'email',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{jobname}'=>'ְλ����',
	),
	'msgcert' => array (
		'name'=>'�ֻ���֤',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{code}'=>'��֤��',
		'{date}'=>'��֤ʱ��'
	),
	'msgreg' => array (
		'name'=>'����ע��ģ��',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{username}'=>'�û���',
		'{password}'=>'����',
		'{email}'=>'����'
	),
	'msgyqms' => array (
		'name'=>'������������',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{company}'=>'��˾����',
		'{jobname}'=>'����ְλ',
		'{linkman}'=>'��ϵ��',
		'{comtel}'=>'��ϵ�绰',
		'{username}'=>'��ְ������',
		'{jobname}'=>'ְλ����',
		'{comemail}'=>'����'
	),
	'msgfkcg' => array (
		'name'=>'����ɹ�',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{order_id}'=>'���׵���',
		'{price}'=>'���',
		'{date}'=>'ʱ��'
	),
	'msgzzshtg' => array (
		'name'=>'ְλ��˳ɹ�',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{date}'=>'ͨ��ʱ��',
		'{jobname}'=>'ְλ����'
	),
	'msgzzshwtg' => array (
		'name'=>'ְλ���δͨ��',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{date}'=>'ͨ��ʱ��',
		'{jobname}'=>'ְλ����'
	),
	'msggetpass' => array (
		'name'=>'�һ�����',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{username}'=>'�û���',
		'{password}'=>'����'),
	'msgsqzw' => array (
		'name'=>'����ְλ',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{jobname}'=>'ְλ����',
		'{date}'=>'����ʱ��'
	),
	'msgremind' => array (
		'name'=>'��������',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰'
	),
	'msgcomdy' => array (
		'name'=>'��ҵ����',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{resumename}'=>'��������'
	),
	'msguserdy' => array (
		'name'=>'���˶���',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{jobname}'=>'ְλ����'
	),
	'msgnotice' => array (
		'name'=>'�Զ�����ְλ֪ͨ',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{jobname}'=>'ְλ����',
	),
	'msgregcode' => array (
		'name'=>'ע����֤��',
		'type'=>'msg',
		'{webname}'=>'��վ����',
		'{weburl}'=>'��վ����',
		'{webtel}'=>'��վ�绰',
		'{code}'=>'��֤��',
	)
);
?>
