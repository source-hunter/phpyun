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
			$("#cityid").html('<option value="">请选择</option>');
		}
		$("#three_cityid").html('<option value="">请选择</option>');
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
	if(name==""){layermsg('请填写姓名！');return false;}
	if(birthday==""){layermsg('请填写出生年月！');return false;}
	if(telphone==false){layermsg('请正确填写手机号码！');return false;}
	if(ifemail==false){layermsg('请填写电子邮件！');return false;}
	if(living==""){layermsg('请填写现居住地！');return false;}
	if(description==""){layermsg("请填写自我评价！");return false;}

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
		layermsg('请填写简历名称！');return false;
	}
	if(job_classid==""){
		layermsg('请选择期望从事职位！');return false;
	}
	if(cityid==""){
		layermsg('请选择期望工作地点！');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: '努力保存中'
	});
	$.post(weburl+"/member/index.php?c=expect",{name:name,hy:hy,job_classid:job_classid,provinceid:provinceid,cityid:cityid,three_cityid:three_cityid,salary:salary,report:report,eid:eid},function(data){
		layer.close(layerIndex);
		if(data>0){
			layermsg('保存成功！',2,function(){window.location.href='index.php?c=addresume&eid='+data;}); 
		}else{
			layermsg('保存失败！');
		}
	})
}

function checkskill(){
	var name=$.trim($("input[name='name']").val());
	var longtime=$.trim($("input[name='longtime']").val());
	if(name==""){
		layermsg('请填写技能名称！');return false;
	}
	if(longtime==""){
		layermsg('请填写掌握时间！');return false;
	}	
	var layerIndex=layer.open({
		type: 2,
		content: '努力保存中'
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
		layermsg('请填写单位名称！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('请正确填写工作时间！');return false;
	}
	if(department==""){
		layermsg('请填写所在部门！');return false;
	}
	if(title==""){
		layermsg('请填写担任职位！');return false;
	}
	if(content==""){
		layermsg('请填写工作内容！');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: '努力保存中'
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
		layermsg('请填写项目名称！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('请正确填写项目时间！');return false;
	}
	if(sys==""){
		layermsg('请填写项目环境！');return false;
	}
	if(title==""){
		layermsg('请填写担任职位！');return false;
	}
	if(content==""){
		layermsg('请填写项目内容！');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: '努力保存中'
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
		layermsg('请填写学校名称！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('请正确填写在校时间！');return false;
	}
	if(specialty==""){
		layermsg('请填写所学专业！');return false;
	}
	if(title==""){
		layermsg('请填写担任职位！');return false;
	}
	if(content==""){
		layermsg('请填写专业描述！');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: '努力保存中'
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
		layermsg('请填写培训中心！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('请正确填写培训时间！');return false;
	}
	if(title==""){
		layermsg('请填写培训方向！');return false;
	}
	if(content==""){
		layermsg('请填写培训描述！');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: '努力保存中'
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
		layermsg('请填写证书全称！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		layermsg('请正确填写有效时间！');return false;
	}
	if(title==""){
		layermsg('请填写颁发单位！');return false;
	}
	if(content==""){
		layermsg('请填写证书描述！');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: '努力保存中'
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
		layermsg('请填写其他标题！');return false;
	}
	if(content==""){
		layermsg('请填写其他描述！');return false;
	}
	var layerIndex=layer.open({
		type: 2,
		content: '努力保存中'
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