<?php
/*
* $Author ：PHPYUN开发团队
*
* 官网: http://www.phpyun.com
*
* 版权所有 2009-2014 宿迁鑫潮信息技术有限公司，并保留所有权利。
*
* 软件声明：未经授权前提下，不得用于商业运营、二次开发以及任何形式的再次发布。
 * 注:本文件为系统文件，请不要修改
 */
$arr_data = array (
	'pay' => array ('alipay'=>'支付宝','tenpay'=>'财富通','bank'=>'银行转帐','adminpay'=>'管理员充值','balance'=>'余额支付','admincut'=>'管理员扣款'),
	'paystate' => array ('<font color=red>支付失败</font>','<font color=green>等待付款</font>','<font color=blsue>支付成功</font>','<font color=#c30ad9>等待确认</font>'),
	'cache' => array ('1'=>"区域",'2'=>"行业",'3'=>"职位",'4'=>"个人会员分类",'5'=>"企业会员分类",'6'=>"导航",'7'=>"网站缓存",'8'=>"SEO设置",'9'=>"分站",'10'=>"关键字",'11'=>'友情链接','12'=>'新闻分类'),
	'faceurl' => "/data/face/",
	'imface' => array ('CNM'=>"shenshou_org.gif",'SM'=>"horse2_org.gif",'FU'=>"fuyun_org.gif",'GL'=>"geili_org.gif",'WG'=>"wg_org.gif",'VW'=>"vw_org.gif",'XM'=>"panda_org.gif",'TZ'=>"rabbit_org.gif",'OTM'=>"otm_org.gif",'JU'=>"j_org.gif",'HF'=>"hufen_org.gif",'LW'=>"liwu_org.gif",'HH'=>"smilea_org.gif",'XX'=>"tootha_org.gif",'HH2'=>"laugh.gif",'TZA'=>"tza_org.gif",'KL'=>"kl_org.gif",'WBS'=>"kbsa_org.gif",'CJ'=>"cj_org.gif",'HX'=>"shamea_org.gif",'ZY'=>"zy_org.gif",'BZ'=>"bz_org.gif",'BS2'=>"bs2_org.gif",'LOVE'=>"lovea_org.gif",'LEI'=>"sada_org.gif",'TX'=>"heia_org.gif",'QQ'=>"qq_org.gif",'SB'=>"sb_org.gif",'TKX'=>"mb_org.gif",'LD'=>"ldln_org.gif",'YHH'=>"yhh_org.gif",'ZHH'=>"zhh_org.gif",'XU'=>"x_org.gif",'cry'=>"cry.gif",'WQ'=>"wq_org.gif",'T'=>"t_org.gif",'DHQ'=>"k_org.gif",'BBA'=>"bba_org.gif",'N'=>"angrya_org.gif",'YW'=>"yw_org.gif",'CZ'=>"cza_org.gif",'88'=>"88_org.gif",'SI'=>"sk_org.gif",'HAN'=>"sweata_org.gif",'sl'=>"sleepya_org.gif",'SJ'=>"sleepa_org.gif",'P'=>"money_org.gif",'SW'=>"sw_org.gif",'K'=>"cool_org.gif",'HXA'=>"hsa_org.gif",'H'=>"hatea_org.gif",'GZ'=>"gza_org.gif",'YD'=>"dizzya_org.gif",'BS'=>"bs_org.gif",'ZK'=>"crazya_org.gif",'HX2'=>"h_org.gif",'YX'=>"yx_org.gif",'NM'=>"nm_org.gif",'XIN'=>"hearta_org.gif",'SX'=>"unheart.gif",'PIG'=>"pig.gif",'ok'=>"ok_org.gif",'ye'=>"ye_org.gif",'good'=>"good_org.gif",'no'=>"no_org.gif",'Z'=>"z2_org.gif",'go'=>"come_org.gif",'R'=>"sad_org.gif",'lz'=>"lazu_org.gif",'CL'=>"clock_org.gif",'ht'=>"m_org.gif",'dg'=>"cake.gif"),
	'datacall' => array(
		'resume'=>array("简历","order"=>array("id desc"=>"最新简历","hits desc"=>"热门简历","lastedit desc"=>"更新时间"),"field"=>array("resumename"=>"简历名称","name"=>"姓名","url"=>"链接","birthday"=>"年龄","edu"=>"学历","lastedit"=>"更新时间","hits"=>"浏览次数","big_pic"=>"大头像","small_pic"=>"小头像","email"=>"EMAIL","tel"=>"电话","moblie"=>"手机","hy"=>"期望从事行业","hyurl"=>"期望从事行业链接","job_classid"=>"期望从事职位","report"=>"到岗时间","salary"=>"期望薪水","type"=>"期望工作性质","gz_city"=>"期望工作地点(江苏-南京)","domicile"=>"户籍所在地","living"=>"现居住地","exp"=>"工作经验","address"=>"详细地址","description"=>"个人简介","idcard"=>"身份证号码","homepage"=>"个人主页/博客")),
		'member'=>array("用户","order"=>array("uid desc"=>"最新用户","login_date desc"=>"最后登录时间","login_hits desc"=>"热门用户"),"field"=>array("name"=>"用户名","url"=>"链接","email"=>"EMAIL","moblie"=>"手机","usertype"=>"用户类型","hits"=>"登录次数","reg_date"=>"注册时间","login_date"=>"登录时间"),"where"=>array("usertype"=>array("0"=>"用户类型","1"=>"个人用户","3"=>"猎头用户","2"=>"企业用户"))),
		'company'=>array("公司","order"=>array("uid desc"=>"最新企业","hits desc"=>"热门企业","lastedit desc"=>"更新时间"),"field"=>array("companyname"=>"公司名称","url"=>"公司链接","hy"=>"行业","hy_url"=>"行业链接","pr"=>"公司性质","city"=>"企业地址","mun"=>"企业规模","address"=>"企业地址","linkphone"=>"固定电话","linkmail"=>"联系邮箱","sdate"=>"创办时间","money"=>"注册资金","zip"=>"邮政编码","linkman"=>"联系人","job_num"=>"职位数","linkqq"=>"联系QQ","linktel"=>"联系电话","website"=>"企业网址","logo"=>"企业LOGO")),
		'job'=>array("职位","order"=>array("id desc"=>"最新职位","hits desc"=>"热门职位","lastedit desc"=>"更新时间"),"field"=>array("jobname"=>"职位名称","companyname"=>"公司名称","url"=>"职位链接","com_url"=>"公司链接","hy"=>"从事行业","hy_url"=>"行业链接","num"=>"招聘人数","jobtype"=>"职位类型","edu"=>"学历要求","age"=>"年龄要求","report"=>"到岗时间","exp"=>"工作经验","lang"=>"语言要求","salary"=>"提供月薪","welfare"=>"福利待遇","time"=>"有效日期","city"=>"工作地点")),
		'zph'=>array("招聘会","order"=>array("id desc"=>"最新招聘会"),"field"=>array("title"=>"招聘会标题","url"=>"链接","organizers"=>"主办方","time"=>"举办时间","address"=>"举办会场","phone"=>"咨询电话","linkman"=>"联系人","website"=>"网址","logo"=>"招聘会LOGO","com_num"=>"参与企业数")),
		'news'=>array("新闻","order"=>array("a.id desc"=>"最新新闻","a.hits desc"=>"热门新闻"),"field"=>array("title"=>"新闻标题","url"=>"链接","keyword"=>"关键字","author"=>"作者","time"=>"发布时间","hits"=>"点击率","description"=>"描述","thumb"=>"缩略图","source"=>"来源")),
		'ask'=>array("问答","order"=>array("id desc"=>"最新问答","answer_num desc"=>"热门问答"),"field"=>array("title"=>"问答标题","url"=>"问答链接","content"=>"问答内容","name"=>"发布人","time"=>"发布时间","answer_num"=>"回答人数","img"=>"发布人头像","user_url"=>"发布人链接")),
		'lt_job'=>array("猎头职位","order"=>array("a.id desc"=>"最新猎头职位","a.hits desc"=>"热门猎头职位","a.lastedit desc"=>"更新时间"),"field"=>array("jobname"=>"职位名称","url"=>"职位链接","companyname"=>"招聘企业","com_url"=>"企业链接","address"=>"工作地点(江苏-南京)","department"=>"所属部门","hy"=>"所属行业","mun"=>"企业规模","pr"=>"企业性质","report"=>"汇报对象","jobtype"=>"职位类别","constitute"=>"薪资构成","years"=>"年假福利","social"=>"社保福利","live"=>"居住福利","sdate"=>"发布时间","edate"=>"截止日期","job_desc"=>"职位描述","salary"=>"年薪","edu"=>"学历要求","sex"=>"性别要求","language"=>"语言要求","full"=>"是否统招全体制","age"=>"年龄要求","exp"=>"总工资年限","qw_hy"=>"期望行业","eligible"=>"任职资格","desc"=>"企业介绍","name"=>"职位发布人")),
		'link'=>array("友情链接","order"=>array("id desc"=>"最新友链","link_sorting desc"=>"排序(大前小后)","link_sorting asc"=>"排序(小前大后)"),"field"=>array("link_name"=>"名称","link_url"=>"链接","link_src"=>"图片地址(图片链接使用)"),"where"=>array("img_type"=>array("0"=>"友链类型","1"=>"文字连接","2"=>"图片链接"))),
		'once'=>array("一句话招聘","order"=>array("id desc"=>"最新微招聘","lastedit desc"=>"更新时间"),"field"=>array("jobname"=>"职位名称","companyname"=>"公司名称","mans"=>"招聘人数","require"=>"招聘要求","phone"=>"联系电话","linkman"=>"联系人","address"=>"联系地址","time"=>"更新时间")),
		'tiny'=>array("微简历","order"=>array("id desc"=>"最新微简历","lastedit desc"=>"更新时间"),"field"=>array("name"=>"姓名","url"=>"链接","sex"=>"性别","exp"=>"工作经验","job"=>"应聘职位","mobile"=>"联系电话","describe"=>"个人说明","time"=>"更新时间")),
		'keyword'=>array("热门关键字","order"=>array("num desc"=>"搜索次数"),"field"=>array("name"=>"关键字名称","url"=>"链接","num"=>"搜索次数"),"where"=>array("keytype"=>array("0"=>"关键字类型","1"=>"一句话招聘","3"=>"职位","4"=>"公司","5"=>"简历","6"=>"猎头","7"=>"猎头职位")))
	),
	'seoconfig'=>array(
		'公共参数'=>array(
			'webname'=>"网站名称",
			'webkeyword'=>"网站关键字",
			'webdesc'=>"网站描述",
			'weburl'=>"网址",
			'city'=>"当前城市"
		),
		'搜索页'=>array(
			'seacrh_class'=>"搜索类别"
		),
		'新闻'=>array(
			'news_class'=>"新闻类别",
			'news_title'=>"新闻标题",
			'news_keyword'=>"新闻关键字",
			'news_source'=>"新闻来源",
			'news_author'=>"新闻作者",
			'news_desc'=>"新闻描述",
		),
		'公司'=>array(
			'company_name'=>"企业名称",
			'company_name_desc'=>"企业简介",
			'company_product'=>"企业产品",
			'company_news'=>"企业新闻",
			'company_news_desc'=>"企业新闻描述",
			'industry_class'=>"行业类别",
		),
		'职位'=>array(
			'industry_class'=>"行业类别",
			'job_class'=>"职位类别",
			'job_name'=>"职位名称",
			'job_desc'=>"职位描述",
		),
		'招聘会'=>array(
			'zph_title'=>"招聘会标题",
			'zph_desc'=>"招聘会描述",
		),
		'问答'=>array(
			'ask_title'=>"问答标题",
			'ask_desc'=>"问答描述",
		),
		'简历'=>array(
			'resume_username'=>"简历姓名",
			'resume_job'=>"简历意向职位",
			'resume_city'=>"简历工作城市",
		),
		'微简历'=>array(
			'tiny_username'=>"微简历名称",
			'tiny_job'=>"微简历职位",
			'tiny_desc'=>"微简历描述",
		),
		'微招聘'=>array(
			'once_username'=>"微招聘名称",
			'once_job'=>"微招聘职位",
			'once_desc'=>"微招聘描述",
		),
		'培训'=>array(
			'px_subject_name'=>"课程名称",
			'px_subject_desc'=>"课程描述",
			'px_teacher_name'=>"培训师名称",
			'px_teacher_desc'=>"培训师描述",
			'px_agency_name'=>"培训机构名称",
			'px_agency_desc'=>"培训机构描述",
		),
		'工具箱'=>array(
			'hr_class'=>"类别名称",
			'hr_desc'=>"类别描述",
		)
	)

);
?>