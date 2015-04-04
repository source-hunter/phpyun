function checkcity(id,type){
	if(id>0){
		$.post(weburl+"/index.php?m=ajax&c=wap_city",{id:id,type:type},function(data){ 
			if(type==1){
				$("#cityid").html(data);
			}else{
				$("#three_cityid").html(data);
			}
		})
	}else{
		if(type==1){
			$("#cityid").html('<option value="">��ѡ��</option>');
		}
		$("#three_cityid").html('<option value="">��ѡ��</option>');
	}
}
function checkinfo(){
	var name=$.trim($("input[name='name']").val());
	var birthday=$.trim($("input[name='birthday']").val());
	var living=$.trim($("input[name='living']").val());
	var email=$.trim($("input[name='email']").val());
	var telphone=$.trim($("input[name='telphone']").val());
	var description=$.trim($("#description").val());
	ifemail = check_email(email);  
	telphone = isjsMobile(telphone);  
	if(name==""){layermsg('����д������');return false;}
	if(birthday==""){layermsg('����д�������£�');return false;}
	if(telphone==false){layermsg('����ȷ��д�ֻ����룡');return false;}
	if(ifemail==false){layermsg('����д�����ʼ���');return false;}
	if(living==""){layermsg('����д�־�ס�أ�');return false;}
	if(description==""){layermsg("����д�������ۣ�");return false;}

	$.post(weburl+'/member/index.php?c=info',convertFormToJson("resumeInfo"),function(data){
		var jsonData=eval("("+data+")"); 
		if(jsonData.url){
			layermsg(jsonData.msg,2,function(){window.location.href=jsonData.url;}); 
		}else{
			layermsg(jsonData.msg);
		}
	});
	return false;
}
function convertFormToJson(formid){
	var elements=$("#"+formid).find("*");	
	var str = '';
	for(var i=0;i<elements.length;i++){
		if($(elements).eq(i).attr("name")){ 
			str=str+","+$(elements).eq(i)[0].name+':"'+$(elements).eq(i)[0].value+'"';
		}
	}
	if(str.length>0){
		str=str.substring(1);
	}
	var cToObj=eval("({"+str+"})");
	return cToObj;
}
function check_email(strEmail) {
	 var emailReg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/;
	 if (emailReg.test(strEmail))
	 return true;
	 else
	 return false;
 }
function isjsMobile(obj){
	if(obj.length!=11) return false;
	else if(obj.substring(0,2)!="13" && obj.substring(0,2)!="15" && obj.substring(0,2)!="18") return false;
	else if(isNaN(obj)) return false;
	else  return true;
}
function checkshow(id){
	if(id=="expect"){
		$("#infobutton").show();
		$("#info").hide();
	}else if(id=="info"){
		$("#expectbutton").show();
		$("#expect").hide();
	}
	$("#"+id+"button").hide();
	$("#"+id).show();
} 
function saveexpect(){
	var name=$.trim($("#expect_name").val());
	var hy=$.trim($("#hy").val());
	var job_classid=$.trim($("#job_classid").val());
	var provinceid=$.trim($("#provinceid").val());
	var cityid=$.trim($("#cityid").val());
	var three_cityid=$.trim($("#three_cityid").val());
	var salary=$.trim($("#salary").val());
	var report=$.trim($("#report").val());
	var eid=$.trim($("#eid").val());
	if(name==""){
		layermsg('����д�������ƣ�');return false;
	}
	if(job_classid==""){
		layermsg('��ѡ����������ְλ��');return false;
	}
	if(cityid==""){
		layermsg('��ѡ�����������ص㣡');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: 'Ŭ��������'
	});
	$.post(weburl+"/member/index.php?c=expect",{name:name,hy:hy,job_classid:job_classid,provinceid:provinceid,cityid:cityid,three_cityid:three_cityid,salary:salary,report:report,eid:eid},function(data){
		layer.close(layerIndex);
		if(data>0){
			layermsg('����ɹ���',2,function(){window.location.href='index.php?c=addresume&eid='+data;}); 
		}else{
			layermsg('����ʧ�ܣ�');
		}
	})
}

function checkskill(){
	var name=$.trim($("input[name='name']").val());
	var longtime=$.trim($("input[name='longtime']").val());
	if(name==""){
		layermsg('����д�������ƣ�');return false;
	}
	if(longtime==""){
		layermsg('����д����ʱ�䣡');return false;
	}	
	var layerIndex=layer.open({
		type: 2,
		content: 'Ŭ��������'
	});
	$.post(weburl+'/member/'+$("#skillInfo").attr("action"),convertFormToJson("skillInfo"),function(data){
		layer.close(layerIndex);
		var jsonData=eval("("+data+")"); 
		if(jsonData.url){
			layermsg(jsonData.msg,2,function(){window.location.href=jsonData.url;}); 
		}else{
			layermsg(jsonData.msg);
		}
	});
	return false;
}
function checkwork(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("select[name='syear']").val());
	var smouth=$.trim($("select[name='smouth']").val());
	var sday=$.trim($("select[name='sday']").val());
	var eyear=$.trim($("select[name='eyear']").val());
	var emouth=$.trim($("select[name='emouth']").val());
	var eday=$.trim($("select[name='eday']").val());
	var department=$.trim($("input[name='department']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		layermsg('����д��λ���ƣ�');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('����ȷ��д����ʱ�䣡');return false;
	}
	if(department==""){
		layermsg('����д���ڲ��ţ�');return false;
	}
	if(title==""){
		layermsg('����д����ְλ��');return false;
	}
	if(content==""){
		layermsg('����д�������ݣ�');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: 'Ŭ��������'
	});
	$.post(weburl+'/member/'+$("#workInfo").attr("action"),convertFormToJson("workInfo"),function(data){
		layer.close(layerIndex);
		var jsonData=eval("("+data+")"); 
		if(jsonData.url){
			layermsg(jsonData.msg,2,function(){window.location.href=jsonData.url;}); 
		}else{
			layermsg(jsonData.msg);
		}
	});
	return false;
}
function checkproject(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("select[name='syear']").val());
	var smouth=$.trim($("select[name='smouth']").val());
	var sday=$.trim($("select[name='sday']").val());
	var eyear=$.trim($("select[name='eyear']").val());
	var emouth=$.trim($("select[name='emouth']").val());
	var eday=$.trim($("select[name='eday']").val());
	var sys=$.trim($("input[name='sys']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		layermsg('����д��Ŀ���ƣ�');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('����ȷ��д��Ŀʱ�䣡');return false;
	}
	if(sys==""){
		layermsg('����д��Ŀ������');return false;
	}
	if(title==""){
		layermsg('����д����ְλ��');return false;
	}
	if(content==""){
		layermsg('����д��Ŀ���ݣ�');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: 'Ŭ��������'
	});
	$.post(weburl+'/member/'+$("#projectInfo").attr("action"),convertFormToJson("projectInfo"),function(data){
		layer.close(layerIndex);
		var jsonData=eval("("+data+")"); 
		if(jsonData.url){
			layermsg(jsonData.msg,2,function(){window.location.href=jsonData.url;}); 
		}else{
			layermsg(jsonData.msg);
		}
	});
	return false;
}
function checkedu(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("select[name='syear']").val());
	var smouth=$.trim($("select[name='smouth']").val());
	var sday=$.trim($("select[name='sday']").val());
	var eyear=$.trim($("select[name='eyear']").val());
	var emouth=$.trim($("select[name='emouth']").val());
	var eday=$.trim($("select[name='eday']").val());
	var specialty=$.trim($("input[name='specialty']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		layermsg('����дѧУ���ƣ�');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('����ȷ��д��Уʱ�䣡');return false;
	}
	if(specialty==""){
		layermsg('����д��ѧרҵ��');return false;
	}
	if(title==""){
		layermsg('����д����ְλ��');return false;
	}
	if(content==""){
		layermsg('����дרҵ������');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: 'Ŭ��������'
	});
	$.post(weburl+'/member/'+$("#eduInfo").attr("action"),convertFormToJson("eduInfo"),function(data){
		layer.close(layerIndex);
		var jsonData=eval("("+data+")"); 
		if(jsonData.url){
			layermsg(jsonData.msg,2,function(){window.location.href=jsonData.url;}); 
		}else{
			layermsg(jsonData.msg);
		}
	});
	return false;
}
function checktraining(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("select[name='syear']").val());
	var smouth=$.trim($("select[name='smouth']").val());
	var sday=$.trim($("select[name='sday']").val());
	var eyear=$.trim($("select[name='eyear']").val());
	var emouth=$.trim($("select[name='emouth']").val());
	var eday=$.trim($("select[name='eday']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		layermsg('����д��ѵ���ģ�');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('����ȷ��д��ѵʱ�䣡');return false;
	}
	if(title==""){
		layermsg('����д��ѵ����');return false;
	}
	if(content==""){
		layermsg('����д��ѵ������');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: 'Ŭ��������'
	});
	$.post(weburl+'/member/'+$("#trainingInfo").attr("action"),convertFormToJson("trainingInfo"),function(data){
		layer.close(layerIndex);
		var jsonData=eval("("+data+")"); 
		if(jsonData.url){
			layermsg(jsonData.msg,2,function(){window.location.href=jsonData.url;}); 
		}else{
			layermsg(jsonData.msg);
		}
	});
	return false;
}
function checkcert(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("select[name='syear']").val());
	var smouth=$.trim($("select[name='smouth']").val());
	var sday=$.trim($("select[name='sday']").val());
	var eyear=$.trim($("select[name='eyear']").val());
	var emouth=$.trim($("select[name='emouth']").val());
	var eday=$.trim($("select[name='eday']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		layermsg('����д֤��ȫ�ƣ�');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('����ȷ��д��Чʱ�䣡');return false;
	}
	if(title==""){
		layermsg('����д�䷢��λ��');return false;
	}
	if(content==""){
		layermsg('����д֤��������');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: 'Ŭ��������'
	});
	$.post(weburl+'/member/'+$("#certInfo").attr("action"),convertFormToJson("certInfo"),function(data){
		layer.close(layerIndex);
		var jsonData=eval("("+data+")"); 
		if(jsonData.url){
			layermsg(jsonData.msg,2,function(){window.location.href=jsonData.url;}); 
		}else{
			layermsg(jsonData.msg);
		}
	});
	return false;
}
function checkother(){
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(title==""){
		layermsg('����д�������⣡');return false;
	}
	if(content==""){
		layermsg('����д����������');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: 'Ŭ��������'
	});
	$.post(weburl+'/member/'+$("#otherInfo").attr("action"),convertFormToJson("otherInfo"),function(data){
		layer.close(layerIndex);
		var jsonData=eval("("+data+")"); 
		if(jsonData.url){
			layermsg(jsonData.msg,2,function(){window.location.href=jsonData.url;}); 
		}else{
			layermsg(jsonData.msg);
		}
	});
	return false;
}