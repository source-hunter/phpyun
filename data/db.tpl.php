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
$arr_tpl = array (
	'emailreg' => array (
		'name'=>'邮件注册模板',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{username}'=>'用户名',
		'{password}'=>'密码',
		'{email}'=>'邮箱'
	),
	'emailyqms' => array (
		'name'=>'邀请面试',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{company}'=>'公司名称',
		'{linkman}'=>'联系人',
		'{comtel}'=>'联系电话',
		'{username}'=>'求职者名称',
		'{jobname}'=>'职位名称',
		'{comemail}'=>'邮箱'
	),
	'emailgetpass' => array (
		'name'=>'找回密码',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{username}'=>'用户名',
		'{password}'=>'密码'
	),
	'emailfkcg' => array (
		'name'=>'付款成功',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{order_id}'=>'交易单号',
		'{price}'=>'金额',
		'{date}'=>'时间'
	),
	'emailzzshtg' => array (
		'name'=>'职位审核成功',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{date}'=>'通过时间',
		'{jobname}'=>'职位名称'
	),
	'emaillock' => array (
		'name'=>'会员锁定',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{date}'=>'通过时间',
		'{lock_info}'=>'锁定原因',
	),
	'emailuserstatus' => array (
		'name'=>'会员锁定',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{date}'=>'通过时间',
		'{status_info}'=>'审核原因',
	),
	'emailzzshwtg' => array (
		'name'=>'职位审核未通过',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{date}'=>'通过时间',
		'{jobname}'=>'职位名称',
		'{status_info}'=>'审核原因'
	),
	'emailsqzw' => array (
		'name'=>'申请职位',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{jobname}'=>'职位名称',
		'{date}'=>'申请时间'
	),
	'emailcert' => array (
		'name'=>'邮箱认证',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{url}'=>'链接地址',
		'{date}'=>'认证时间'
	),
		'emailcomcert' => array (
		'name'=>'企业认证',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{comname}'=>'企业名称',
		'{certinfo}'=>'审核说明'
	),
		'emailusercert' => array (
		'name'=>'个人认证',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{username}'=>'用户名',
		'{certinfo}'=>'审核说明'
	),
		'emailjobed' => array (
		'name'=>'职位过期提醒',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{com_name}'=>'公司名称',
		'{job_name}'=>'职位名称'
	),
	'emailremind' => array (
		'name'=>'邮件提醒',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
	),
	'emailuserdy' => array (
		'name'=>'邮件订阅',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{jobname}'=>'职位名称',
	),
	'emailcomdy' => array (
		'name'=>'邮件订阅',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{resumename}'=>'简历名称',
	),
	'emailnotice' => array (
		'name'=>'自动发送职位通知',
		'type'=>'email',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{jobname}'=>'职位名称',
	),
	'msgcert' => array (
		'name'=>'手机认证',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{code}'=>'验证码',
		'{date}'=>'认证时间'
	),
	'msgreg' => array (
		'name'=>'短信注册模板',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{username}'=>'用户名',
		'{password}'=>'密码',
		'{email}'=>'邮箱'
	),
	'msgyqms' => array (
		'name'=>'短信邀请面试',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{company}'=>'公司名称',
		'{jobname}'=>'邀请职位',
		'{linkman}'=>'联系人',
		'{comtel}'=>'联系电话',
		'{username}'=>'求职者名称',
		'{jobname}'=>'职位名称',
		'{comemail}'=>'邮箱'
	),
	'msgfkcg' => array (
		'name'=>'付款成功',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{order_id}'=>'交易单号',
		'{price}'=>'金额',
		'{date}'=>'时间'
	),
	'msgzzshtg' => array (
		'name'=>'职位审核成功',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{date}'=>'通过时间',
		'{jobname}'=>'职位名称'
	),
	'msgzzshwtg' => array (
		'name'=>'职位审核未通过',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{date}'=>'通过时间',
		'{jobname}'=>'职位名称'
	),
	'msggetpass' => array (
		'name'=>'找回密码',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{username}'=>'用户名',
		'{password}'=>'密码'),
	'msgsqzw' => array (
		'name'=>'申请职位',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{jobname}'=>'职位名称',
		'{date}'=>'申请时间'
	),
	'msgremind' => array (
		'name'=>'短信提醒',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话'
	),
	'msgcomdy' => array (
		'name'=>'企业订阅',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{resumename}'=>'简历名称'
	),
	'msguserdy' => array (
		'name'=>'个人订阅',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{jobname}'=>'职位名称'
	),
	'msgnotice' => array (
		'name'=>'自动发送职位通知',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{jobname}'=>'职位名称',
	),
	'msgregcode' => array (
		'name'=>'注册验证码',
		'type'=>'msg',
		'{webname}'=>'网站名称',
		'{weburl}'=>'网站域名',
		'{webtel}'=>'网站电话',
		'{code}'=>'验证码',
	)
);
?>
