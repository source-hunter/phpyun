function post_pass(){
	var pw=$("#pw").val();
	var code=$("#code").val();
	var tid=$("#tid").val();
	var type=$("#type").val();
	if(pw==""){
		layer.msg('����������', 2, 2);return false; 
	}
	if(code==""){
		layer.msg('��������֤��', 2, 2);return false; 
	}
	$.post("index.php?m=once&c=ajax",{pw:pw,code:code,tid:tid,type:type},function(data){
		if(data==1){
			layer.msg('��֤�����', 2, 2);return false; 
		}else if(data==2){
			layer.msg('�������', 2, 2);return false; 
		}else if(data==3){
			layer.msg('ˢ�³ɹ���', 2, 9);
			window.location.reload();
		}else if(data==4){
			layer.msg('ɾ���ɹ���', 2, 9);
			window.location.reload();
		}else{
			layer.closeAll();
			//$(".add").hide();
			var data=eval('('+data+')');
			$("#id").val(data.id);
			$("#title").val(data.title);
			$("#mans").val(data.mans);
			$("#companyname").val(data.companyname);
			$("#email").val(data.email);
			$("#qq").val(data.qq);
			$("#sdate").val(formatDate(data.sdate));
			$("#edate").val(data.edate);
			$("#phone").val(data.phone);
			$("#linkman").val(data.linkman);
			$("#address").val(data.address);
			$("#require").val(data.require);
			$("#password").val(pw);
			$("#botton").val('�޸�');   
			$.layer({
				type : 1,
				title :'�޸�΢��Ƹ', 
				offset: [($(window).height() - 470)/2 + 'px', ''],
				closeBtn : [0 , true],
				border : [10 , 0.3 , '#000', true],
				area : ['590px','490px'],
				page : {dom :"#fabufast"}
			}); 
		}
	})
}

function check_once_job(){
	var password=$("#password").val().length;
	var id=$("#id").val();

		var title=$("#title").val();
		if($.trim(title)==""){
			layer.msg('����д��Ƹ��λ', 2, 2);return false; 
		}
		var mans=$("input[name=mans]").val();
		if(Number(mans)<1){
			layer.msg('����ȷ��д��Ƹ����', 2, 2);return false; 
		}
		var companyname=$("input[name=companyname]").val();
		if($.trim(companyname)==""){
			layer.msg('����д��������', 2, 2);return false; 
		}
		
		var email=$.trim($("input[name=email]").val());
		if(email){
			var reg= /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/;
		    if(!reg.test(email)){
			    layer.msg('����ȷ�����ʽ��', 2, 2);return false;
	        }
		}
	    var qq=$.trim($("input[name=qq]").val());
		var reg_qq =/^[1-9](\d){4,9}$/;
		if(qq!=""){
			if(!reg_qq.test(qq)){
			    layer.msg('����ȷ��дQQ��', 2, 2);return false;  
		    }
			if(qq.length<5 || qq.length>10){
				layer.msg('����ȷ��дQQ��', 2, 2);return false;  
			}
		}
		var phone=$.trim($("input[name=phone]").val());
		var reg_phone= (/^[1][3458]\d{9}$|^([0-9]{3,4}\-)?[0-9]{7,8}$/);
		if(phone){
		    if(!reg_phone.test(phone)){
			    layer.msg('����ȷ��д��ϵ�绰', 2, 2);return false; 
			} 
		}	
		if(!email && !qq && !phone){
		    layer.msg('����д���䣬QQ���绰����һ�', 2, 2);return false; 
		}
		var linkman=$("input[name=linkman]").val();
		if($.trim(linkman)==""){
			layer.msg('����д��ϵ��', 2, 2);return false;  
		}
		var address=$("input[name=address]").val();
		if($.trim(address)==""){
			layer.msg('����д��ַ', 2, 2);return false;  
		}
		if($.trim($("#require").val())==""||$.trim($("#require").val())=='����д��Ƹ�ľ���Ҫ���磺�Ա�ѧ�������䣬�������飬���ʴ����������Ϣ'){ 
			layer.msg('����д����Ҫ��', 2, 2);return false;
		}
	    
		var edate=$("input[name=edate]").val();
	    if(!edate){
			layer.msg('����д��Ч�ڣ�', 2, 2);return false;
	    }
		if(id==""){
			var authcode=$("#authcode").val();
			if(!authcode){
				layer.msg('��������֤��', 2, 2);return false; 
			}
			if(password<"4"){
				layer.msg('���벻������4λ', 2, 2);return false;
			}
		}

}
function check_once_keyword(){
	if($("#Fastkeyword").val()=="" || $("#Fastkeyword").val()=="��������������"){ 
		layer.msg('�������������ݣ�', 2, 2);return false;
	}
}
function formatDate(d){
	var  now=new  Date(parseInt(d) * 1000);
	var  year=now.getFullYear();
	var  month=now.getMonth()+1;
	var  date=now.getDate();
	var  hour=now.getHours();
	var  minute=now.getMinutes();
	var  second=now.getSeconds();
	return  year+"-"+month+"-"+date;
}

function showdd(type,id){
	$("#tid").val(id);
	$("#type").val(type);
	$("#pw").val('');
	$("#code").val('');
	$.layer({
		type : 1,
		title :'��֤����', 
		offset: [($(window).height() - 200)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['370px','180px'],
		page : {dom :"#postpw"}
	});  
}
function check_resume_tiny(){ 
	var password=$("#password").val().length;
	var id=$("#id").val();
	var username=$("#username").val();
	if($.trim(username)==""){ 
		layer.msg('����д����', 2, 2);return false; 
	}	
	var job=$("input[name=job]").val();
	if($.trim(job)==""){
		layer.msg('����д��ְ����', 2, 2);return false; 
	}
	var mobile=$.trim($("input[name=mobile]").val());
	if(!mobile){ 
		layer.msg('����д�ֻ���', 2, 2);return false; 
	}else{
		var reg= /^[1][3458]\d{9}$/;   
		if(!reg.test(mobile)){ 
			layer.msg('�ֻ���ʽ����', 2, 2);return false;
		}
	}
	var qq=$.trim($("input[name=qq]").val());
	var reg_tqq =/^[1-9](\d){4,9}$/;
	if(qq!=""){
		if(!reg_tqq.test(qq)){
			layer.msg('����ȷ��дQQ��', 2, 2);return false;  
		}
		if(qq.length<5 || qq.length>10){
			layer.msg('����ȷ��дQQ��', 2, 2);return false;  
		}
	}
	var production=$("#production").val();
	if($.trim(production)==""){ 
		layer.msg('����д���ҽ���', 2, 2);return false;
	}
	if(id==""){
		if(password<"4"){
			layer.msg('����ȷ�������룡', 2, 2);return false; 
		}	
		var authcode=$("#authcode").val();
		if(authcode==''){
			layer.msg('��������֤��', 2, 2);return false; 
		}	
	}
}
function showdd1(type,id){
	$("#tid").val(id);
	$("#type").val(type);
	$("#pw").val('');
	$("#code").val('');
	$.layer({
		type : 1,
		title :'��֤����', 
		offset: [($(window).height() - 200)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['370px','180px'],
		page : {dom :"#postpw"}
	});  
}
function post_password(){
	var pw=$("#pw").val();
	var code=$("#code").val();
	var tid=$("#tid").val();
	var type=$("#type").val();
	if(pw==""){
		layer.msg('����������', 2, 2);return false; 
	}
	if(code==""){
		layer.msg('��������֤��', 2, 2);return false; 
	}
	$.post("index.php?m=tiny&c=ajax",{pw:pw,code:code,tid:tid,type:type},function(data){
		if(data==1){
			layer.msg('��֤�����', 2, 2);return false; 
		}else if(data==2){
			layer.msg('�������', 2, 2);return false; 
		}else if(data==3){
			layer.msg('ˢ�³ɹ���', 2, 9);
			window.location.reload();
		}else if(data==4){
			layer.msg('ɾ���ɹ���', 2, 9);
			window.location.reload();
		}else{
			layer.closeAll();
			$(".add").hide();
			var data=eval('('+data+')');
			$("#id").val(data.id);
			$("#username").val(data.username);
			$("#sex"+data.sex).attr("checked","checked");
			$("#exp").val(data.exp);
			$("#job").val(data.job);
			$("#mobile").val(data.mobile);
			$("#qq").val(data.qq);
			$("#production").val(data.production);
			$("#password").val(pw);
			$("#botton").val('�޸�'); 
			$.layer({
				type : 1,
				title :'�޸�΢����', 
				offset: [($(window).height() - 430)/2 + 'px', ''],
				closeBtn : [0 , true],
				border : [10 , 0.3 , '#000', true],
				area : ['460px','430px'],
				page : {dom :"#fabufast1"}
			});  
		}
	})
}

function showfabu(){
	$("#id").val('');
	$("#title").val('');
	$("#mans").val('');
	$("#companyname").val('');
	$("#qq").val('');
	$("#email").val('');
	$("#sdate").val('');
	$("#edate").val('');
	$("#phone").val('');
	$("#linkman").val('');
	$("#address").val('');
	$("#require").val('');
	$("#password").val('');
	$(".add").show();
	if($("#authcode").val()!='12345')
	{
		$("#authcode").val('');
	}
	
	$("#botton").val("����");  
	$.layer({
		type : 1,
		title :'����΢��Ƹ', 
		offset: [($(window).height() - 535)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['590px','490px'],
		page : {dom :"#fabufast"}
	}); 
}

function showfabu1(){
	$("#id").val('');
	$("#username").val('');
	$("#password").val('');
	$("#sex").val('');
	$("#exp").val('');
	$("#job").val('');
	$("#mobile").val('');
	$("#qq").val('');
	$("#production").val('');
	
	if($("#authcode").val()!='12345')
	{
		$("#authcode").val('');
	}
	$("#botton").val("����"); 
	$.layer({
		type : 1,
		title :'΢����', 
		offset: [($(window).height() - 510)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['460px','510px'],
		page : {dom :"#fabufast1"}
	});  	 
} 