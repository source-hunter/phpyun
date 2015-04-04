function getshow(id,title){
	var moblie=$("#moblieval").val();
	$("input[name=moblie]").val(moblie);
	var email=$("#emailval").val();
	$("input[name=email]").val(email);
	$.layer({
		type : 1,
		title :title,
		offset: [($(window).height() - 380)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['500px','auto'],
		page : {dom :"#"+id}
	});
}
function sendmoblie(){
	if($("#send").val()=="1"){
		return false;
	}
	var moblie=$("input[name=moblie]").val();
	var reg= /^[1][3458]\d{9}$/; 
	if(moblie==''){
		layer.msg('�ֻ��Ų���Ϊ�գ�',2,8);return false;
	}else if(!reg.test(moblie)){
		layer.msg('�ֻ������ʽ����',2,8);return false;
	}
	var i=layer.load('ִ���У����Ժ�...',0);
	$.post(weburl+"/index.php?m=ajax&c=mobliecert", {"str[]":[moblie]},function(data) {
		layer.close(i);
		if(data==1){ 
			send(121); 
		}else if(data==2){
			layer.msg('�벻Ҫ�ظ����ͣ�',2,8);
		}else if(data==3){
			layer.msg('����֪ͨ�ѹرգ�����ϵ����Ա��',2,8);
		}else{
			layer.msg('��Ȩ������',2,8);
		}
	})
}
function send(i){
	i--;
	if(i==-1){
		$("#time").html("���»�ȡ");
		$("#send").val(0)
	}else{
		$("#send").val(1)
		$("#time").html(i+"��");
		setTimeout("send("+i+");",1000);
	}
}
function check_moblie(){
	var moblie=$("input[name=moblie]").val();
	layer.closeAll();
	if(moblie==""){ 
		layer.msg('�������ֻ����룡',2,8,function(){getshow('moblie','���ֻ�����');});return false;
	}
	var code=$("#moblie_code").val();
	if(code==""){ 
		layer.msg('�����������֤�룡',2,8,function(){getshow('moblie','���ֻ�����');});return false;
	}
	
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=binding&act=save",{moblie:moblie,code:code},function(data){
		if(data==1){
			layer.msg('�ֻ��󶨳ɹ���',2,9,function(){location.reload();}); 
		}else if(data==3){
			layer.msg('������֤�벻��ȷ��',2,8);
		}else{
			layer.msg('��������',2,8); 
		}
	})
}
function check_email(){
	var email=$("input[name=email]").val();
	var myreg = /^([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9\-]+@([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	layer.closeAll();
	if(email==''){
		layer.msg('���䲻��Ϊ�գ�',2,8,function(){getshow('email','������');});return false;
	}else if(!myreg.test(email)){
		layer.msg('�����ʽ����',2,8,function(){getshow('email','������');});return false;
	}
	var authcode=$("input[name=email_code]").val();
	if(authcode==""){
		layer.msg('��֤�벻��Ϊ�գ�',2,8,function(){getshow('email','������');});return false;
	}
	
	layer.load('ִ���У����Ժ�...',0);
	$.post(weburl+"/index.php?m=ajax&c=emailcert",{email:email,authcode:authcode},function(data){
		if(data){
			if(data=="4"){
				layer.msg('��֤�벻��ȷ��',2,8);
			}
			if(data=="3"){
				layer.msg('�ʼ�û�����ã�����ϵ����Ա��',2,8);
			}
			if(data=="2"){
				layer.msg('�ʼ�֪ͨ�ѹرգ�����ϵ����Ա��',2,8);
			}
			if(data=="1"){
				layer.msg('�ʼ��ѷ��͵������䣬��ע�������֤��',2,9,function(){location.reload();});
			}
		}else{
			layer.msg('�����µ�¼��',2,8);
		} 
	})
}
function check_company_cert(){
	layer.closeAll();
	if($.trim($("#company_name").val())==''){
		layer.msg('��ҵȫ�Ʋ���Ϊ�գ�',2,8,function(){getyyzz('�ϴ�Ӫҵִ��');});
		return false;
	}
	if($.trim($("#company_cert_pic").val())==''){
		layer.msg('���ϴ�Ӫҵִ�գ�',2,8,function(){getyyzz('�ϴ�Ӫҵִ��');});
		return false;
	}
	layer.load('ִ���У����Ժ�...',0);return true;
}
function check_user_cert(){
	layer.closeAll(); 
	if($.trim($("#user_cert_pic").val())==''){
		layer.msg('���ϴ����֤��Ƭ��',2,8,function(){getyyzz('�ϴ����֤');});return false;
	}
	layer.load('ִ���У����Ժ�...',0);return true;
}
function getyyzz(title){
	$.layer({
		type : 1,
		title :title,
		offset: [($(window).height() - 380)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['500px','auto'],
		page : {dom :"#yyzz"}
	});
}
function look(title,pic){
	$("#picshow").attr("src",pic);
	$.layer({
		type : 1,
		title :title,
		offset: [($(window).height() - 380)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['500px','auto'],
		page : {dom :"#pic"}
	});
}