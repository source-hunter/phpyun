<?php
/*
* $Author ��PHPYUN�����Ŷ�
*
* ����: http://www.phpyun.com
*
* ��Ȩ���� 2009-2014 ��Ǩ�γ���Ϣ�������޹�˾������������Ȩ����
*
* ���������δ����Ȩǰ���£�����������ҵ��Ӫ�����ο����Լ��κ���ʽ���ٴη�����
 * ע:���ļ�Ϊϵͳ�ļ����벻Ҫ�޸�
 */
$arr_data = array (
	'pay' => array ('alipay'=>'֧����','tenpay'=>'�Ƹ�ͨ','bank'=>'����ת��','adminpay'=>'����Ա��ֵ','balance'=>'���֧��','admincut'=>'����Ա�ۿ�'),
	'paystate' => array ('<font color=red>֧��ʧ��</font>','<font color=green>�ȴ�����</font>','<font color=blsue>֧���ɹ�</font>','<font color=#c30ad9>�ȴ�ȷ��</font>'),
	'cache' => array ('1'=>"����",'2'=>"��ҵ",'3'=>"ְλ",'4'=>"���˻�Ա����",'5'=>"��ҵ��Ա����",'6'=>"����",'7'=>"��վ����",'8'=>"SEO����",'9'=>"��վ",'10'=>"�ؼ���",'11'=>'��������','12'=>'���ŷ���'),
	'faceurl' => "/data/face/",
	'imface' => array ('CNM'=>"shenshou_org.gif",'SM'=>"horse2_org.gif",'FU'=>"fuyun_org.gif",'GL'=>"geili_org.gif",'WG'=>"wg_org.gif",'VW'=>"vw_org.gif",'XM'=>"panda_org.gif",'TZ'=>"rabbit_org.gif",'OTM'=>"otm_org.gif",'JU'=>"j_org.gif",'HF'=>"hufen_org.gif",'LW'=>"liwu_org.gif",'HH'=>"smilea_org.gif",'XX'=>"tootha_org.gif",'HH2'=>"laugh.gif",'TZA'=>"tza_org.gif",'KL'=>"kl_org.gif",'WBS'=>"kbsa_org.gif",'CJ'=>"cj_org.gif",'HX'=>"shamea_org.gif",'ZY'=>"zy_org.gif",'BZ'=>"bz_org.gif",'BS2'=>"bs2_org.gif",'LOVE'=>"lovea_org.gif",'LEI'=>"sada_org.gif",'TX'=>"heia_org.gif",'QQ'=>"qq_org.gif",'SB'=>"sb_org.gif",'TKX'=>"mb_org.gif",'LD'=>"ldln_org.gif",'YHH'=>"yhh_org.gif",'ZHH'=>"zhh_org.gif",'XU'=>"x_org.gif",'cry'=>"cry.gif",'WQ'=>"wq_org.gif",'T'=>"t_org.gif",'DHQ'=>"k_org.gif",'BBA'=>"bba_org.gif",'N'=>"angrya_org.gif",'YW'=>"yw_org.gif",'CZ'=>"cza_org.gif",'88'=>"88_org.gif",'SI'=>"sk_org.gif",'HAN'=>"sweata_org.gif",'sl'=>"sleepya_org.gif",'SJ'=>"sleepa_org.gif",'P'=>"money_org.gif",'SW'=>"sw_org.gif",'K'=>"cool_org.gif",'HXA'=>"hsa_org.gif",'H'=>"hatea_org.gif",'GZ'=>"gza_org.gif",'YD'=>"dizzya_org.gif",'BS'=>"bs_org.gif",'ZK'=>"crazya_org.gif",'HX2'=>"h_org.gif",'YX'=>"yx_org.gif",'NM'=>"nm_org.gif",'XIN'=>"hearta_org.gif",'SX'=>"unheart.gif",'PIG'=>"pig.gif",'ok'=>"ok_org.gif",'ye'=>"ye_org.gif",'good'=>"good_org.gif",'no'=>"no_org.gif",'Z'=>"z2_org.gif",'go'=>"come_org.gif",'R'=>"sad_org.gif",'lz'=>"lazu_org.gif",'CL'=>"clock_org.gif",'ht'=>"m_org.gif",'dg'=>"cake.gif"),
	'datacall' => array(
		'resume'=>array("����","order"=>array("id desc"=>"���¼���","hits desc"=>"���ż���","lastedit desc"=>"����ʱ��"),"field"=>array("resumename"=>"��������","name"=>"����","url"=>"����","birthday"=>"����","edu"=>"ѧ��","lastedit"=>"����ʱ��","hits"=>"�������","big_pic"=>"��ͷ��","small_pic"=>"Сͷ��","email"=>"EMAIL","tel"=>"�绰","moblie"=>"�ֻ�","hy"=>"����������ҵ","hyurl"=>"����������ҵ����","job_classid"=>"��������ְλ","report"=>"����ʱ��","salary"=>"����нˮ","type"=>"������������","gz_city"=>"���������ص�(����-�Ͼ�)","domicile"=>"�������ڵ�","living"=>"�־�ס��","exp"=>"��������","address"=>"��ϸ��ַ","description"=>"���˼��","idcard"=>"���֤����","homepage"=>"������ҳ/����")),
		'member'=>array("�û�","order"=>array("uid desc"=>"�����û�","login_date desc"=>"����¼ʱ��","login_hits desc"=>"�����û�"),"field"=>array("name"=>"�û���","url"=>"����","email"=>"EMAIL","moblie"=>"�ֻ�","usertype"=>"�û�����","hits"=>"��¼����","reg_date"=>"ע��ʱ��","login_date"=>"��¼ʱ��"),"where"=>array("usertype"=>array("0"=>"�û�����","1"=>"�����û�","3"=>"��ͷ�û�","2"=>"��ҵ�û�"))),
		'company'=>array("��˾","order"=>array("uid desc"=>"������ҵ","hits desc"=>"������ҵ","lastedit desc"=>"����ʱ��"),"field"=>array("companyname"=>"��˾����","url"=>"��˾����","hy"=>"��ҵ","hy_url"=>"��ҵ����","pr"=>"��˾����","city"=>"��ҵ��ַ","mun"=>"��ҵ��ģ","address"=>"��ҵ��ַ","linkphone"=>"�̶��绰","linkmail"=>"��ϵ����","sdate"=>"����ʱ��","money"=>"ע���ʽ�","zip"=>"��������","linkman"=>"��ϵ��","job_num"=>"ְλ��","linkqq"=>"��ϵQQ","linktel"=>"��ϵ�绰","website"=>"��ҵ��ַ","logo"=>"��ҵLOGO")),
		'job'=>array("ְλ","order"=>array("id desc"=>"����ְλ","hits desc"=>"����ְλ","lastedit desc"=>"����ʱ��"),"field"=>array("jobname"=>"ְλ����","companyname"=>"��˾����","url"=>"ְλ����","com_url"=>"��˾����","hy"=>"������ҵ","hy_url"=>"��ҵ����","num"=>"��Ƹ����","jobtype"=>"ְλ����","edu"=>"ѧ��Ҫ��","age"=>"����Ҫ��","report"=>"����ʱ��","exp"=>"��������","lang"=>"����Ҫ��","salary"=>"�ṩ��н","welfare"=>"��������","time"=>"��Ч����","city"=>"�����ص�")),
		'zph'=>array("��Ƹ��","order"=>array("id desc"=>"������Ƹ��"),"field"=>array("title"=>"��Ƹ�����","url"=>"����","organizers"=>"���췽","time"=>"�ٰ�ʱ��","address"=>"�ٰ�᳡","phone"=>"��ѯ�绰","linkman"=>"��ϵ��","website"=>"��ַ","logo"=>"��Ƹ��LOGO","com_num"=>"������ҵ��")),
		'news'=>array("����","order"=>array("a.id desc"=>"��������","a.hits desc"=>"��������"),"field"=>array("title"=>"���ű���","url"=>"����","keyword"=>"�ؼ���","author"=>"����","time"=>"����ʱ��","hits"=>"�����","description"=>"����","thumb"=>"����ͼ","source"=>"��Դ")),
		'ask'=>array("�ʴ�","order"=>array("id desc"=>"�����ʴ�","answer_num desc"=>"�����ʴ�"),"field"=>array("title"=>"�ʴ����","url"=>"�ʴ�����","content"=>"�ʴ�����","name"=>"������","time"=>"����ʱ��","answer_num"=>"�ش�����","img"=>"������ͷ��","user_url"=>"����������")),
		'lt_job'=>array("��ͷְλ","order"=>array("a.id desc"=>"������ͷְλ","a.hits desc"=>"������ͷְλ","a.lastedit desc"=>"����ʱ��"),"field"=>array("jobname"=>"ְλ����","url"=>"ְλ����","companyname"=>"��Ƹ��ҵ","com_url"=>"��ҵ����","address"=>"�����ص�(����-�Ͼ�)","department"=>"��������","hy"=>"������ҵ","mun"=>"��ҵ��ģ","pr"=>"��ҵ����","report"=>"�㱨����","jobtype"=>"ְλ���","constitute"=>"н�ʹ���","years"=>"��ٸ���","social"=>"�籣����","live"=>"��ס����","sdate"=>"����ʱ��","edate"=>"��ֹ����","job_desc"=>"ְλ����","salary"=>"��н","edu"=>"ѧ��Ҫ��","sex"=>"�Ա�Ҫ��","language"=>"����Ҫ��","full"=>"�Ƿ�ͳ��ȫ����","age"=>"����Ҫ��","exp"=>"�ܹ�������","qw_hy"=>"������ҵ","eligible"=>"��ְ�ʸ�","desc"=>"��ҵ����","name"=>"ְλ������")),
		'link'=>array("��������","order"=>array("id desc"=>"��������","link_sorting desc"=>"����(��ǰС��)","link_sorting asc"=>"����(Сǰ���)"),"field"=>array("link_name"=>"����","link_url"=>"����","link_src"=>"ͼƬ��ַ(ͼƬ����ʹ��)"),"where"=>array("img_type"=>array("0"=>"��������","1"=>"��������","2"=>"ͼƬ����"))),
		'once'=>array("һ�仰��Ƹ","order"=>array("id desc"=>"����΢��Ƹ","lastedit desc"=>"����ʱ��"),"field"=>array("jobname"=>"ְλ����","companyname"=>"��˾����","mans"=>"��Ƹ����","require"=>"��ƸҪ��","phone"=>"��ϵ�绰","linkman"=>"��ϵ��","address"=>"��ϵ��ַ","time"=>"����ʱ��")),
		'tiny'=>array("΢����","order"=>array("id desc"=>"����΢����","lastedit desc"=>"����ʱ��"),"field"=>array("name"=>"����","url"=>"����","sex"=>"�Ա�","exp"=>"��������","job"=>"ӦƸְλ","mobile"=>"��ϵ�绰","describe"=>"����˵��","time"=>"����ʱ��")),
		'keyword'=>array("���Źؼ���","order"=>array("num desc"=>"��������"),"field"=>array("name"=>"�ؼ�������","url"=>"����","num"=>"��������"),"where"=>array("keytype"=>array("0"=>"�ؼ�������","1"=>"һ�仰��Ƹ","3"=>"ְλ","4"=>"��˾","5"=>"����","6"=>"��ͷ","7"=>"��ͷְλ")))
	),
	'seoconfig'=>array(
		'��������'=>array(
			'webname'=>"��վ����",
			'webkeyword'=>"��վ�ؼ���",
			'webdesc'=>"��վ����",
			'weburl'=>"��ַ",
			'city'=>"��ǰ����"
		),
		'����ҳ'=>array(
			'seacrh_class'=>"�������"
		),
		'����'=>array(
			'news_class'=>"�������",
			'news_title'=>"���ű���",
			'news_keyword'=>"���Źؼ���",
			'news_source'=>"������Դ",
			'news_author'=>"��������",
			'news_desc'=>"��������",
		),
		'��˾'=>array(
			'company_name'=>"��ҵ����",
			'company_name_desc'=>"��ҵ���",
			'company_product'=>"��ҵ��Ʒ",
			'company_news'=>"��ҵ����",
			'company_news_desc'=>"��ҵ��������",
			'industry_class'=>"��ҵ���",
		),
		'ְλ'=>array(
			'industry_class'=>"��ҵ���",
			'job_class'=>"ְλ���",
			'job_name'=>"ְλ����",
			'job_desc'=>"ְλ����",
		),
		'��Ƹ��'=>array(
			'zph_title'=>"��Ƹ�����",
			'zph_desc'=>"��Ƹ������",
		),
		'�ʴ�'=>array(
			'ask_title'=>"�ʴ����",
			'ask_desc'=>"�ʴ�����",
		),
		'����'=>array(
			'resume_username'=>"��������",
			'resume_job'=>"��������ְλ",
			'resume_city'=>"������������",
		),
		'΢����'=>array(
			'tiny_username'=>"΢��������",
			'tiny_job'=>"΢����ְλ",
			'tiny_desc'=>"΢��������",
		),
		'΢��Ƹ'=>array(
			'once_username'=>"΢��Ƹ����",
			'once_job'=>"΢��Ƹְλ",
			'once_desc'=>"΢��Ƹ����",
		),
		'��ѵ'=>array(
			'px_subject_name'=>"�γ�����",
			'px_subject_desc'=>"�γ�����",
			'px_teacher_name'=>"��ѵʦ����",
			'px_teacher_desc'=>"��ѵʦ����",
			'px_agency_name'=>"��ѵ��������",
			'px_agency_desc'=>"��ѵ��������",
		),
		'������'=>array(
			'hr_class'=>"�������",
			'hr_desc'=>"�������",
		)
	)

);
?>