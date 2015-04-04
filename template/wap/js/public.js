function showMoreNav(){
	$(".subnav").toggle();
}
function checkshowjob(type){
	if(type=='once'||type=='tiny'){
		layer.open({
			type:1,
			content: $("#"+type+"list").html(),
			shadeClose: false
		});return;
	}else{
		$("#"+type+"list").show();
		checkhide('info'); 
	}
}
function checkhide(id){ 
	$("#"+id+"button").show();
	$("#"+id).hide();
}
function checkjob1(id,type){
	var style=$("#"+type+"list"+id).attr("style");
	$(".onelist").addClass("lookshow");
	$(".lookhide").attr("style","display: none;");
	if(style=="display: none;"){
		$("#"+type+"list"+id).show();
		$("#"+type+id).removeClass("lookshow");
	}
}
function checkjob2(id,type){
	if($("#citylevel").length>0){
		if(parseInt($("#citylevel").val())==2){
			$("#cityclassbutton").val($(event.target).html());
			$("#cityclassbutton").html($(event.target).html());
			$("#three_cityid").val(id);
			$("#cityid").val(id);
			Close('city');
			return;
		}
	}
	var style=$("#"+type+"post"+id).attr("style");
	$(".post_show_three").attr("style","display: none;");
	if(style=="display: none;"){
		$("#"+type+"post"+id).show();
	}
} 
function checkedcity(id,name){
	$("#cityclassbutton").val(name);
	$("#cityclassbutton").html(name);
	$("#three_cityid").val(id);
	Close('city');
}
function checked_input(id){
	var check_length = $("input[name='jobclass']:checked").length;
	if(check_length>5){ 
		$("#r"+id).attr("checked",false);
		layermsg('您最多只能选择五个！');  
	}
}
function realy(){
	var info="";
	var value=""; 
	$("input[name='jobclass']:checked").each(function(){
		var obj = $(this).val();
		var name = $(this).attr("data");
		if(info==""){
			info=obj;
			value=name;
		}else{
			info=info+","+obj;
			value=value+","+name;
		}
	})
	if(info==""){
		layermsg("请选择职位类别！");return false;
	}else{
		$("#job_classid").val(info);
		$("#jobclassbutton").val(value);
		$("#jobclassbutton").html(value);
		Close("job");
	}
}
function removes(){
	$("#jobclassbutton").val("请选择职位类别");
	$("#job_classid").val(""); 
	$(".onelist").attr("class","onelist lookshow");
	$(".onelist>.lookhide").hide();
	$(".post_show_three").hide();
	$("input[name='jobclass']").removeAttr("checked");
}
function Close(type){  
	$("#"+type+"list>.onelist").attr("class","onelist lookshow");
	$("#"+type+"list>.onelist>.lookhide").hide();
	$("#"+type+"list>.post_show_three").hide();
	$("#"+type+"list").hide(); 
}
function checkfrom(){ 
	var username=$.trim($("#username").val());
	if(username==""){ 
		layermsg("用户名不能为空！");return false;
	}else if(username.length<2||username.length>16){
		layermsg("用户名长度应在2-16位！");return false;
	} 
	var email=$.trim($("#email").val()); 
    var myreg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    if(!myreg.test(email)){
		layermsg("邮箱格式错误！");return false;
	} 
	var password=$.trim($("#password").val());
	var password2=$.trim($("#password2").val());
	if(password==""){
		layermsg("密码不能为空！");return false;
	}else if(password.length<6||password.length>20){
		layermsg("密码长度应在6-20位！");return false;
	}
	if(password!=password2){
		layermsg("两次密码不一致！");return false;
	}
}
function ckpwd(){
	var oldpassword=$.trim($("input[name='oldpassword']").val());
	var password1=$.trim($("input[name='password1']").val());
	var password2=$.trim($("input[name='password2']").val());
	if(oldpassword==''||password1==''||password2==''){
		layermsg("旧密码、新密码、确认密码均不能为空！");return false;
	}
	if(password1!=password2){
		layermsg("两次密码不一致！");return false;
	}
}
function isdel(url){
	layer.open({
		content: '是否删除该数据？',
		btn: ['确认', '取消'],
		shadeClose: false,
		yes: function(){
			location.href =url;
		} 
	});
}
function comjob(id){
	if(id>0){
		$.post(weburl+"/index.php?m=ajax&c=wap_job",{id:id,type:1},function(data){  
			$("select[name='job1_son']").html(data);
		})
	}
}
function comcity(id,name){
	if(id>0){
		$.post(weburl+"/index.php?m=ajax&c=wap_city",{id:id,type:1},function(data){  
			$("select[name='"+name+"']").html(data); 
		})
	} 
	if(name=='cityid'){$("select[name='three_cityid']").html("<option value=\"\">--请选择--</option>");} 
}
function mlogin(){
	var username=$.trim($("#username").val());
	var password=$.trim($("#password").val()); 
	if(username==''||password==''){
		layermsg('用户名或密码均不能为空！');return false; 
	} 
}  

function cktiny(){
	var name=$.trim($("input[name='username']").val()); 
	var job=$.trim($("input[name='job']").val());
	var mobile=$.trim($("input[name='mobile']").val());
	var production=$.trim($("#production").val());
	var password=$.trim($("input[name='password']").val()); 
	if(name==''){layermsg('姓名不能为空！');return false; }
	if(job==''){layermsg('意向职位不能为空！');return false; }
	if(mobile==''){
		layermsg('手机号不能为空！');
		return false; 
	}else{
		var reg= /^[1][3458]\d{9}$/;   
		if(!reg.test(mobile)){ 
			layermsg('手机格式错误！');
			return false;
		}
	}
	if(production==''){layermsg('自我介绍不能为空！');return false; } 
	if(password==''){layermsg('密码不能为空！');return false; } 
}
function ckonce(){
	var title=$.trim($("input[name='title']").val()); 
	var mans=$.trim($("input[name='mans']").val()); 
	var companyname=$.trim($("input[name='companyname']").val()); 
	var linkman=$.trim($("input[name='linkman']").val()); 
	var phone=$.trim($("input[name='phone']").val()); 
	var address=$.trim($("input[name='address']").val()); 
	var password=$.trim($("input[name='password']").val()); 
	var require=$.trim($("textarea[name='require']").val()); 
	if(title==''){layermsg('招聘名称不能为空！');return false; } 
	if(mans==''){layermsg('招聘人数不能为空！');return false; } 
	if(companyname==''){layermsg('店面名称不能为空！');return false; } 
	if(linkman==''){layermsg('联系人不能为空！');return false; } 
	if(phone==''){layermsg('联系电话不能为空！');return false; } 
	if(address==''){layermsg('联系地址不能为空！');return false; } 
	if(require==''){layermsg('要求不能为空！');return false; } 
	if(password==''){layermsg('密码不能为空！');return false; } 
}

function islayer(){
	if($.trim($("#layermsg").val())){
		var msg=$.trim($("#layermsg").val());
		var url=$.trim($("#layerurl").val());
		if(url){
			if(url=='1'){url=location.href;}
			layermsg(msg,2,function(){location.href = url;});	
		}else{
			//layermsg(msg,2,function(){location.href = url;});	
			layermsg(msg,2);	
		}
	} 
}
function layermsg(content,time,end){ 
	layer.open({
		content: content, 
		time: time === undefined ? 2 : time,
		end:end
	});
	return false;
}
function layeralert(title,content,time,end){ 
	layer.open({
		title: [title,'background-color:#0099CC; color:#fff;'],
		content: content, 
		time: time === undefined ? 2 : time,
		end:end===undefined?'':function(){location.href = end;}
	});
}
function really(name){
	var chk_value =[];    
	$('input[name="'+name+'"]:checked').each(function(){    
		chk_value.push($(this).val());   
	});   
	if(chk_value.length==0){
		layermsg("请选择要删除的数据！",2,8);return false;
	}else{
		layer.open({
			content: '确定删除吗？',
			btn: ['确认', '取消'],
			shadeClose: false,
			yes: function(){
				setTimeout(function(){$('#myform').submit()},0); 
			} 
		});
	} 
}
//全选
function m_checkAll(form){
	var elements=$("input[name='"+"delid[]"+"']");
	for (var i=0;i<elements.length;i++){
		var e = elements.eq(i)[0];
		e.checked = $("#checkAll")[0].checked; 
	}
} 
function checkAll(name){
	$("input[name="+name+"]").attr("checked",true);
} 
function getDaysHtml(year,month){
	var days=30;
	if((month==1)||(month==3)||(month==4)||(month==7)||(month==8)||(month==10)||(month==12)){
		days=31;
	}else if((month==4)||(month==6)||(month==9)||(month==11)){
		days=30;
	}else{
		if((year%4)==0){
			days=29;
		}else{
			days=28;
		}
	}
	var daysHtml='';
	for(var i=1;i<=days;i++){
		daysHtml+="<option value='"+i+"'>"+i+"</option>"
	}
	return daysHtml;
}
function selectMonth(yearid,monthid,dayid){
	$("#"+dayid).html(getDaysHtml(parseInt($("#"+yearid).val()),parseInt($("#"+monthid).val())));
}
function setSelectDay(dayid,day){
	$("#"+dayid).val(day);
}
$(document).ready(function(){
	$("#price_int").blur(function(){
		var value=$(this).val();
		var proportion=$(this).attr("int");
		$("#com_vip_price").val(value/proportion);
		$("#span_com_vip_price").html(value/proportion);
	})
});
function checkOncePassword(id){
	if($(".layermmain #once_password").val()==''){
		layermsg('请输入密码');
		return;
	}
	$.post(weburl+"/index.php?m=ajax&c=checkOncePassword",{id:id,password:$(".layermmain #once_password").val()},function(data){  
		if(data=='1'){
			var url=weburl+'/index.php?m=once&c=add&id='+id;
			layermsg('验证通过！',2,function(){location.href = url;});
		}else{
			layermsg('验证失败！',2,function(){});
		}
	});
}
function checkTinyPassword(id){
	if($(".layermmain #tiny_password").val()==''){
		layermsg('请输入密码');
		return;
	}
	$.post(weburl+"/index.php?m=ajax&c=checkTinyPassword",{id:id,password:$(".layermmain #tiny_password").val()},function(data){  
		if(data=='1'){
			var url=weburl+'/index.php?m=tiny&c=add&id='+id;
			layermsg('验证通过！',2,function(){location.href = url;});
		}else{
			layermsg('验证失败！',2,function(){});
		}
	});
}

$(document).ready(function(){
	$(".sq_resume").click(function(){
		if($(this).attr("uid")){$("#uid").val($(this).attr("uid"));}
		if($(this).attr("username")){$("#username").val($(this).attr("username"));}
		$.post(weburl+"/index.php?m=ajax&c=index_ajaxresume",{show_job:'1'},function(data){
			var data=eval('('+data+')');
			var status=data.status;
			var integral=data.integral;
			if(data.html){
				$("#jobname").html(data.html);
			}
			if(data.linkman){
				$("#linkman").val(data.linkman);
			}
			if(data.linktel){
				$("#linktel").val(data.linktel);
			}
			if(data.address){
				$("#address").val(data.address);
			}
			if(data.intertime){
				$("#intertime").val(data.intertime);
			}
			if(data.content){
				$("#content").text(data.content);
				$("#update_yq").attr("checked",true);
			}
			if(status == 6){
			    layermsg('请先登录！');return false;
			}
			if(!status || status == 0){
				 layermsg('您不是企业用户，请先登录！');
				/*layer.alert('您不是企业用户，请先登录！', 0, '提示',function(){
					window.location.href =weburl+"/index.php?m=login&usertype=2&type=out"; window.event.returnValue = false;return false;
				});*/

			}else if(status==1){
				layer.open({
					content:"邀请面试将扣除"+integral+integral_pricename+"，是否继续？",
					btn: ['确认', '取消'],
					shadeClose: false,
					yes: function(){
						location.href=weburl+'/index.php?m=user&c=invite&jobname='+data.html+'&linkman='+data.linkman+'&linktel='+data.linktel+'&address='+data.address+'&intertime='+data.intertime+'&uid='+$("#uid").val();
				
						//$("#job_box").show();
					} 
				});
				/*layer.confirm("邀请面试将扣除"+integral+integral_pricename+"，是否继续？",function(){
					layer.closeAll();
					$.layer({
						type : 1,
						title :'邀请面试',
						offset: [($(window).height() - 280)/2 + 'px', ''],
						closeBtn : [0 , true],
						border : [10 , 0.3 , '#000', true],
						area : ['380px','auto'],
						page : {dom :"#job_box"}
					});
				});*/
			}else if(status==2){
				layer.open({
					content:"你的等级特权已经用完,将扣除"+integral+integral_pricename,
					btn: ['确认', '取消'],
					shadeClose: false,
					yes: function(){
						location.href=weburl+'/index.php?m=user&c=invite&jobname='+data.html+'&linkman='+data.linkman+'&linktel='+data.linktel+'&address='+data.address+'&intertime='+data.intertime+'&uid='+$("#uid").val();
				
						//$("#job_box").show();
					} 
				});
				/*layer.confirm("你的等级特权已经用完,将扣除"+integral+integral_pricename,function(){
					layer.closeAll();
					$.layer({
						type : 1,
						title :'邀请面试',
						offset: [($(window).height() -380)/2 + 'px', ''],
						closeBtn : [0 , true],
						border : [10 , 0.3 , '#000', true],
						area : ['380px','auto'],
						page : {dom :"#job_box"}
					});
				});*/
			}else if(status==3){ 
				/*$.layer({
					type : 1,
					title :'邀请面试',
					offset: [($(window).height() - 380)/2 + 'px', ''],
					closeBtn : [0 , true],
					border : [10 , 0.3 , '#000', true],
					area : ['380px','auto'],
					page : {dom :"#job_box"}
				});*/
						location.href=weburl+'/index.php?m=user&c=invite&jobname='+data.html+'&linkman='+data.linkman+'&linktel='+data.linktel+'&address='+data.address+'&intertime='+data.intertime+'&uid='+$("#uid").val();
				
						//$("#job_box").show();
			}else if(status==4){
				layermsg('会员邀请简历已用完！');return false;
			}else if(status==5){
				layermsg('您暂无发布中的职位！');return false;
			}
		});
	})
});
