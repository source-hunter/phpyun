
/*��֤�뿴����,��¼��*/
function check_code(){
	document.getElementById("vcode_img").src=weburl+"/include/authcode.inc.php?"+Math.random();
}
/*��֤�뿴����,ע��*/
function check_codes(){
	document.getElementById("vcode_imgs").src=weburl+"/include/authcode.inc.php?"+Math.random();
}
/*��֤�뿴����,��¼��*/
function checkcode(){
	document.getElementById("vcode_img").src=weburl+"/include/authcode.inc.php?"+Math.random();
}
//���ڸ�ѡ��һ��ѡ�е��Ƕ��,���Կ���ѭ�����
function get_comindes_jobid(){
	var codewebarr="";
	$("input[name=checkbox_job]:checked").each(function(){ //���ڸ�ѡ��һ��ѡ�е��Ƕ��,���Կ���ѭ�����
		if(codewebarr==""){codewebarr=$(this).val();}else{codewebarr=codewebarr+","+$(this).val();}
	});
	return codewebarr;
}
function search_keyword(myform){
	var keyword=myform.keyword.value;
	var placeholder=myform.keyword.placeholder;
	if(placeholder==keyword&&keyword){
		myform.keyword.value='';
	}
}
function check_keyword(name){
	var keyword=$("#keyword").val();
	if(keyword&&keyword==name){$("#keyword").val('');}
}
function search_show(id){$(".cus-sel-opt-panel").hide();$("#"+id).show();}
function search_hide(id){$("#"+id).hide();}
function logout(url){
	$.get(url,function(msg){
		if(msg==1 || msg.indexOf('script')){
			if(msg.indexOf('script')){
				$('#uclogin').html(msg);
			}
			layer.msg('���ѳɹ��˳���', 2, 9,function(){window.location.href =weburl;});
		}else{
			layer.msg('�˳�ʧ�ܣ�', 2, 8);
		}
	});
}

$(document).ready(function(){
	
	$("#sq_job").click(function(){
		var jobid=$("#jobid").val();
		$.post("index.php?m=ajax&c=index_ajaxjob",{jobid:jobid},function(data){
			if(data==4){
				layer.msg('�������ϸù�˾Ҫ���޷��ύ���룡', 2, 8);
			}else if(!data || data==0){
				showcomlogin();
			}else if(data==2){
				layer.alert('����û�м������Ƿ�����Ӽ�����', 0, '��ʾ',function(){window.location.href ="member/index.php?c=resume";window.event.returnValue = false;return false; });
			}else if(data==3){
				layer.msg('�������������ְλ��', 2, 3);
			}else{
				$(".POp_up_r").html('');
				$(".POp_up_r").append(data);
				$.layer({
					type : 1,
					title :'����ְλ',
					offset: [($(window).height() - 380)/2 + 'px', ''],
					closeBtn : [0 , true],
					border : [10 , 0.3 , '#000', true],
					area : ['380px','auto'],
					page : {dom :"#sqjob_show"}
				});
			}
		});
		
	});
	$(".yun_topLogin").hover(function(){
		$(this).find(".yun_More").attr("class","yun_More yun_Morecurrent");
		$(this).find("ul").show();
	},function(){
		$(this).find(".yun_More").attr("class","yun_More");
		$(this).find("ul").hide();
	});
	$(".yun_topNav").hover(function(){
		$(this).find(".yun_navMore").attr("class","yun_navMore yun_webMorecurrent");
		$(this).find(".yun_webMoredown").show();
	},function(){
		$(this).find(".yun_navMore").attr("class","yun_navMore");
		$(this).find(".yun_webMoredown").hide();
	});
	$("#click_sq").click(function(){
		var companyname=$("#companyname").val();
		var jobname=$("#jobname").val();
		var companyuid=$("#companyuid").val();
		var jobid=$("#jobid").val();
		var eid=$("input[name=resume]:checked").val();
		$('#sqjob_show').hide();
		$('#bg').hide();
		layer.closeAll();
		var loadi = layer.load('ִ���У����Ժ�...',0);
		$.post("index.php?m=ajax&c=sq_job",{companyname:companyname,jobname:jobname,companyuid:companyuid,jobid:jobid,eid:eid},function(data){
			layer.close(loadi);
			if(data==4){
				layer.msg('�������ϸù�˾Ҫ���޷��ύ���룡', 2, 8);
			}else if(data==1){
				var i = $.layer({
					shade:[0],
					area:['auto','auto'],
					dialog:{
						msg:'����ɹ����Ƿ񷵻ظ������ģ�',
						btns:2,
						type:4,
						btn:['��������','�ر�'],
						yes:function(){window.location.href="member/index.php?c=job";window.event.returnValue = false;return false;},
						no:function(){layer.close(i);}
					}
				});
			}else if(data==2){
				layer.msg('ϵͳ�������Ժ����ԣ�', 2, 3);return false;
			}else if(data==3){
				layer.msg('�����������ְλ��', 2, 0);return false;
			}else if(data==5){
				layer.msg('��ְλ�ѹ��ڣ����������ְλ��', 2, 3);return false;
			}else if(data==6){
				layer.msg('��ְλ�����ڣ�', 2, 8);return false;
			}else{
				layer.alert('���ȵ�¼��',0,'��ʾ',function(){window.location.href="index.php?m=login&usertype=1";window.event.returnValue=false;return false;});
			}
		});
	})
	$(".sq_resume").click(function(){
		if($(this).attr("uid")){$("#uid").val($(this).attr("uid"));}
		if($(this).attr("username")){$("#username").val($(this).attr("username"));}
		$.post(weburl+"/index.php?m=ajax&c=index_ajaxresume",{show_job:1},function(data){
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
			    layer.msg('���ȵ�¼��', 2, 3);return false;
			}
			if(!status || status == 0){
				layer.alert('��������ҵ�û������ȵ�¼��', 0, '��ʾ',function(){
					window.location.href =weburl+"/index.php?m=login&usertype=2&type=out"; window.event.returnValue = false;return false;
				});

			}else if(status==1){
				layer.confirm("�������Խ��۳�"+integral+integral_pricename+"���Ƿ������",function(){
					layer.closeAll();
					$.layer({
						type : 1,
						title :'��������',
						offset: [($(window).height() - 280)/2 + 'px', ''],
						closeBtn : [0 , true],
						border : [10 , 0.3 , '#000', true],
						area : ['380px','auto'],
						page : {dom :"#job_box"}
					});
				});
			}else if(status==2){
				layer.confirm("��ĵȼ���Ȩ�Ѿ�����,���۳�"+integral+integral_pricename,function(){
					layer.closeAll();
					$.layer({
						type : 1,
						title :'��������',
						offset: [($(window).height() -380)/2 + 'px', ''],
						closeBtn : [0 , true],
						border : [10 , 0.3 , '#000', true],
						area : ['380px','auto'],
						page : {dom :"#job_box"}
					});
				});
			}else if(status==3){ 
				$.layer({
					type : 1,
					title :'��������',
					offset: [($(window).height() - 380)/2 + 'px', ''],
					closeBtn : [0 , true],
					border : [10 , 0.3 , '#000', true],
					area : ['380px','auto'],
					page : {dom :"#job_box"}
				});
			}else if(status==4){
				layer.msg('��Ա������������꣡', 2, 8);return false;
			}else if(status==5){
				layer.msg('�����޷����е�ְλ��', 2, 8);return false;
			}
		});
	})

	$("#click_invite").click(function(){
		layer.closeAll();
		var uid=$("#uid").val();
		var content=$("#content").val();
		var username=$("#username").val();
		var job=$("#jobname").val();
		var intertime=$("#intertime").val();
		var linkman=$("#linkman").val();
		var linktel=$("#linktel").val();
		var address=$("#address").val();
		job=job.split("+");
		var jobname=job[0];
		var jobid=job[1];
		if($("#update_yq").attr("checked")=='checked'){
			var update_yq=1;
		}else{
			var update_yq=0;
		}
	
		loadi = layer.load('ִ���У����Ժ�...',0);
		$.post(weburl+"/index.php?m=ajax&c=sava_ajaxresume",{uid:uid,content:content,username:username,jobname:jobname,update_yq:update_yq,address:address,linkman:linkman,linktel:linktel,intertime:intertime,jobid:jobid},function(data){
			layer.close(loadi);
			var data=eval('('+data+')');
			var status=data.status;
			var integral=data.integral;
			if(status==8){
				layer.msg('�������ϸ��û���ְ�����޷����룡', 2, 8);return false;
			}else if(status==9){
				layer.msg('���û��ѱ��������������', 2, 8);return false;
			}else if(!status || status==0){
				layer.alert('���ȵ�¼��', 0, '��ʾ',function(){window.location.href ="index.php?m=login&usertype=2&type=out";window.event.returnValue = false;return false;  });
			}else if(status==5){
				layer.confirm('������'+integral+integral_pricename+'�������������ԣ��Ƿ��ֵ��', function(){window.location.href =weburl+"/member/index.php?c=pay";window.event.returnValue = false;return false;  });
			}else if(status==3){
				layer.msg('���ѳɹ����룡', 2, 9,function(){location.reload();}); 
			}else if(status==7){
				layer.msg('���Ѿ���������˲ţ��벻Ҫ�ظ����룡', 2, 8); 
			}
		});
	})

	$("input[name=city]").click(function(){
		$('.city_box').show();
	})
	$(".p_t_right").click(function(){
		$("#bg").hide(1000);
		$('.city_box').hide(1000);
	})
	$("#colse_box").click(function(){
		$('.job_box').hide();
	})
	$("#close_job").click(function(){
		var check_val="0";
		var name_val = "����";
		$("input[type='checkbox'][name='job_box']:checked").each(function(){
		  var info = $(this).val().split("+");
			  check_val+=","+info[0];
			  name_val+="+"+info[1];
		  });
		  check_val = check_val.replace("0,","");
		  $("#qw_job").val(check_val);
		  name_val = name_val.replace("����+","");
		  $("#qw_show_job").html(name_val);
		  $("#bg").hide(1000);
		  $('#pannel_job').hide(1000);
	})
	$("#click").click(function(){
		var info = $("input[@type=radio][name=cityid][checked]").val();
		var info_arr = info.split("+");
		var name = info_arr[0];
		var id = info_arr[1];
		$("#sea_place").val(name);
		$("#cityid").val(id);
		$("#bg").attr("style","display:none");
		$('.city_box').hide(1000);
	});
	$("#click_head").click(function(){
		var info = $("input[@type=radio][name=cityid][checked]").val();
		var info_arr = info.split("+");
		var name = info_arr[0];
		var id = info_arr[1];
		$("#sea_place_head").val(name);
		$("#cityid_head").val(id);
		$("#bg").hide(1000);
		$('#city_box_head').hide(1000);
	});
	$(".header_seach_find").mouseover(function(){
		$(".header_seach_find_list").show();
	}).mouseout(function(){
		$(".header_seach_find_list").hide();
	});
	$(".header_seach_find_list").mouseover(function(){
		$(".header_seach_find_list").show();
	});

	$(".index_search_place").mouseover(function(){
		$(".index_place_position").show();
	}).mouseout(function(){
		$(".index_place_position").hide();
	});
	$(".index_place_position").mouseover(function(){
		$(".index_place_position").show();
	});
	$(".Company_post_ms span").click(function(){
		$(".Company_post_ms span").attr("class","");
		$(this).attr("class","Company_post_cur");
		$(".Company_toggle").hide();
		var name=$(this).attr("name");
		$("#Company_job_"+name).show();
	});
	
	$(".header_Remind_hover").hover(function(){
		$(".header_Remind_list").show();
		$(".header_Remind_em").addClass("header_Remind_em_hover");
	},function(){
		$(".header_Remind_list").hide();
		$(".header_Remind_em_hover").removeClass("header_Remind_em_hover");
	}); 
	
	
	$(".header_fixed_login_after").hover(function(){
		$(".header_fixed_reg_box").show();
	},function(){
		$(".header_fixed_reg_box").hide();
	});
	
	
	if(!isPlaceholder()){
		$("input").not("input[type='password']").each(
		function(){
			if($(this).val()=="" && $(this).attr("placeholder")!=""){
				$(this).val($(this).attr("placeholder"));
				$(this).focus(function(){
					if($(this).val()==$(this).attr("placeholder")) $(this).val("");
				});
				$(this).blur(function(){
					if($(this).val()=="") $(this).val($(this).attr("placeholder"));
				});
			}
		});
	};
	
})
function isPlaceholder(){
    var input = document.createElement('input');
    return 'placeholder' in input;
}
function fav_job(id,type){
	$.post("index.php?m=ajax&c=favjobuser",{id:id},function(data){
		if(data==1){
			var i = $.layer({
				shade : [0.5 , '#000' , true],
				area : ['auto','auto'],
				dialog : {
					msg:'�ղسɹ����Ƿ񷵻ظ������ģ�',
					btns : 2,
					type : 4,
					btn : ['�鿴�ղ�','�ر�'],
					yes : function(){
						window.location.href ="member/index.php?c=favorite";window.event.returnValue = false;return false;
					},
					no : function(){
						layer.close(i);
					}
				}
			});
		}else if(data==2){
			layer.msg('ϵͳ�������Ժ����ԣ�', 2, 3);return false;
		}else if(data==3){
			layer.msg('�����ղع���ְλ��', 2, 0);return false;
		}else if(data==0){
			if(type==2){
				$("#touch_lo").hide();
				$("#tologoin").show("1000");
			}else{
				layer.msg('���ȵ�¼��', 2, 3);return false;
			}
		}else if(data==4){
			if(type==2){
				$("#touch_lo").hide();
				$("#tologoin").show("1000");
			}else{
				layer.msg('�Բ��������Ǹ����û����޷�����ְλ��', 2, 8);return false;
			}
		}
	});
}

function addwebfav(url,title){
	var title,url;
	if(document.all){
		window.external.addFavorite(url,title);
	}else if(window.sidebar){
		window.sidebar.addPanel(title,url,"");
	}
}

function setHomepage(url){
   var url;
   if(document.all){
	  document.body.style.behavior='url(#default#homepage)';
	  document.body.setHomePage(url);
   }else if(window.sidebar){
		if(window.netscape){
			 try{
				 netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			 }
			 catch(e){
				 alert("���������δ����[��Ϊ��ҳ]���ܣ��������������ڵ�ַ��������about:config,Ȼ���� signed.applets.codebase_principal_support ֵ��Ϊtrue����");
			 }
		}
		var prefs=Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
		prefs.setCharPref('browser.startup.homepage',url);
   }
}
function marquee(time,id){
	$(function(){
		var _wrap=$(id);
		var _interval=time;
		var _moving;
		_wrap.hover(function(){
			clearInterval(_moving);
		},function(){
			_moving=setInterval(function(){
			var _field=_wrap.find('li:first');
			var _h=_field.height();
			_field.animate({marginTop:-_h+'px'},800,function(){
			_field.css('marginTop',0).appendTo(_wrap);
			})
		},_interval)
		}).trigger('mouseleave');
	});
}




function forget(){
	var aucode = $("#txt_CheckCode").val();
	var username =  $("#username").val();
	if(username==""){
		$("#msg_error").html("<font color='red'>����д��ע��ʱ���û�����</font>");
		return false;
	}
	if(aucode==""){
		$("#msg_error").html("<font color='red'>��֤�벻��Ϊ�գ�</font>");
		return false;
	}
	return true;
}
function unselectall(){
	if(document.getElementById('chkAll').checked){
		document.getElementById('chkAll').checked = document.getElementById('chkAll').checked&0;
	}
}
function CheckAll(form){
	for (var i=0;i<form.elements.length;i++){
		var e = form.elements[i];
		if (e.Name != 'chkAll'&&e.disabled==false)
		e.checked = form.chkAll.checked;
	}
}
function get_zph(id){
	var pid=id;
	var stime=$("#zph_stime_"+id).val();
	var etime=$("#zph_etime_"+id).val();
	if(stime<'0' && etime>'0'){
		layer.msg('��Ƹ���Ѿ���ʼ��', 2,8);return false;
	}else if(etime<'0'){
		layer.msg("��Ƹ���Ѿ�������", 2,8);return false;
	}
	$("#zph_name").html($(".title"+pid).html());
	$("input[name=pid]").val(pid);
	$.get("index.php?m=ajax&c=getzph",function(data){
		var data=eval('('+data+')');
		var status=data.status;
		var content=data.content;
		if(status==0){
			$("#error_zph").show();
			$("#TB_ajaxContent").hide();
			$("#error_zph").html(content);
		}else{
			$("#error_zph").hide();
			$("#TB_ajaxContent").show();
			$("#joblist").html(content);
			$("input[name=uid]").val(data.uid);
		}
		if(status==2){
			$(".Corporate_box_sub").hide();
		}
		$.layer({
			type : 1,
			title :'ԤԼ��Ƹ��',
			offset: [($(window).height() - 235)/2 + 'px', ''],
			shade: [0],
			closeBtn : [0 , true],
			border : [10 , 0.3 , '#000', true],
			area : ['380px','auto'],
			page : {dom :"#TB_window"}
		});
	});
};
function clickzph(){
	var uid=$("input[name=uid]").val();
	var pid=$("input[name=pid]").val();
	var jobid=get_comindes_jobid();
	$.get("index.php?m=ajax&c=zphcom&uid="+uid+"&pid="+pid+"&jobid="+jobid, function(data){
		var data=eval('('+data+')');
		var status=data.status;
		var content=data.content;
		layer.closeAll();
		if(status==0){
			layer.msg(content, 2,8);
		}else{
			layer.msg(content, 2,9);
		} return false;
	})
}
function showcomlogin(){
	$.layer({
		type : 1,
		title :'���ٵ�¼',
		offset: [($(window).height() - 380)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['380px','300px'],
		page : {dom :"#touch_lo"}
	});
}
function report_com(){
	$.layer({
		type : 1,
		title :'�ٱ���ְλ',
		offset: [($(window).height() - 380)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['380px','250px'],
		page : {dom :"#report"}
	});
}
function checkcomlogin(){
	var username = $.trim($("#username").val());
	var password = $.trim($("#password").val());
	var authcode = $.trim($("#authcode").val());
	if(username == "" || password=="" || authcode==""){
		layer.closeAll();
		layer.msg('��������д�û��������룬��֤�룡', 2, 8,function(){showcomlogin();});return false;
	}
	$.post("index.php?m=login&c=loginsave",{comid:1,username:username,password:password,authcode:authcode,usertype:"1"},function(data){
		if(data==1){
			location.reload();
		}else{
			layer.msg(data, 2, 8,function(){location.reload();});return false;
		}
	});
}
function checklogin(){
	var username = $("#username").val();
	var password = $("#password").val();
	var authcode = $("#authcode").val();
	if(username == "" || password=="" || authcode==""){
		$("#msg").html("<font color='red'>��������д�û��������룬��֤�룡</font>");
		return false;
	}
	$.post(weburl+"/index.php?m=login&c=loginsave",{comid:1,username:username,password:password,authcode:authcode,usertype:"1"},function(data){
		if(data==1){
			window.location.reload();
		}else{
			$("#msg").html("<font color='red'>"+data+"</font>");
		}
	});
}
function checklink(){
	var comid = $("#comid").val();
	var username = $("#username").val();
	var password = $("#password").val();
	var authcode = $("#authcode").val();
	if(username == "" || password=="" || authcode==""){
		$("#msg").html("<font color='red'>��������д�û��������룬��֤�룡</font>");
		return false;
	}
	$.post(weburl+"/index.php?m=login&c=loginsave",{comid:comid,username:username,password:password,authcode:authcode,usertype:"1"},function(data){
		if(data==1){
			window.location.reload();
		}else{
			$("#msg").html("<font color='red'>"+data+"</font>");
		}
	});
}

function check_skill(id){
	$(".pop-ul-ul").hide();
	$(".user_tck_box1").removeClass("tanchu");
	$("#showskill"+id).addClass("tanchu");
	$("#skill"+id).show();
}
function box_delete(id){
	$("#sk"+id).remove();
	$("#td_"+id).remove();
	 $("#zn"+id).removeAttr("checked");
}
function checked_input2(id,name,divid,fid){
	var check_length = $("input[type='checkbox'][name='"+name+"'][checked]").length;
	if(name=="job_classid"){
		if($("#zn"+id).attr("checked")=="checked"){
			if(check_length>=5){
				layer.msg('�����ֻ��ѡ�����', 2,8);
				$("#zn"+id).attr("checked",false);
			}else{
				var info = $("#zn"+id).val();
				var info_arr = info.split("+");
				if(id==fid){
					$("."+fid).remove();
				}else{
					$("#td_"+fid).remove();
				}
				$("#"+divid).append("<li id='td_"+id+"' class='show_type"+id+" "+fid+"' ><input id='chk_"+id+"' onclick='box_delete("+id+");' type='checkbox' checked value='"+info+"' name='"+name+"'>"+info_arr[1]+"</li>");
			}
		}else{
			$("#td_"+id).remove();
		}
	}
}
$(document).ready(function(){
	var jobarr=new Array();
	$("#close_skill").click(function(){
		$("#bg").hide();
		$('#skill_box').hide();
		var skill_val = "";
		var i=0;
		$("input[type='checkbox'][name='job_classid']:checked").each(function(){
		  var info = $(this).val().split("+");
			jobarr[i]=info[0];
			i++;
		  skill_val+="<li id=\"sk"+info[0]+"\" class=\"show_type"+info[0]+"\" onclick=\"box_delete('"+info[0]+"');\"><input type=\"checkbox\" name=\"job_classid[]\" checked=\"\" value="+info[0]+"><span>"+info[1]+"</span></li>";
		  });
		$("#job_classid").html(skill_val);
	})
})
function checkmore(type,div,size,msg){
	if(msg=="չ��"){
		var msg="����";
		$("#"+type+" a:gt("+size+")").show();
		$("#"+div).html("<a class=\"yun_close  icon\" href=\"javascript:;\" onclick=\"checkmore('"+type+"','"+div+"','"+size+"','"+msg+"');\">"+msg+"</a>");
	}else{
		var msg="չ��";
		$("#"+type+" a:gt("+size+")").hide();
		$("#"+div).show();
		$("#"+div).html("<a class=\"yun_open  icon\" href=\"javascript:;\" onclick=\"checkmore('"+type+"','"+div+"','"+size+"','"+msg+"');\">"+msg+"</a>");
	}
}

function checkrest(url){window.location.href="index.php?m="+url;}
function Close(id){
	$("#"+id).hide();
	$("#bg").hide();
}
function check_pl(){
	if($.trim($("#content").val())==""){
		layer.msg('�������ݲ���Ϊ�գ�', 2,2);return false;
	}
}
function huifu(id){
	$("#huifu"+id).show();
}
function check_huifu(id){
	if($("#reply"+id).val()==""){
		layer.msg('�ظ����ݲ���Ϊ�գ�', 2,2);return false;
	}
}

function addfriend(id,type){
	loadi = layer.load('ִ���У����Ժ�...',0);
	$.post(weburl+"/index.php?m=ajax&c=makefriends",{id:id,type:type},function(data){
		layer.close(loadi);
		if(data=="5"){
			layer.alert('���ȵ�¼��', 0, '��ʾ',function(){window.location.href =weburl+"/index.php?m=login&usertype=1";window.event.returnValue = false;return false;   });
		}else if(data=="4"){
			layer.msg('��������Լ�Ϊ���ѣ�', 2, 0);return false;
		}else if(data=="3"){
			layer.msg('��δͨ�������֤������Ӻ��ѣ�', 2, 8);return false;
		}else if(data=="2"){
			layer.msg('�Է�δͨ�������ˣ����ܼ���Ϊ���ѣ�', 2, 8);return false;
		}else if(data=="1"){
			layer.msg('���ύ�������룬�ȴ��Է�ͬ�⣡', 2, 1);return false;
		}else if(data=="6"){
			layer.msg('�����Ѿ����ѣ�', 2, 9);return false;
		}else if(data=="7"){
			layer.msg('���ύ�������룬�����ĵȴ���', 2, 1);return false;
		}
	});
}

function addfriend_im(id,type,status,username){

	$.post(weburl+"/index.php?m=ajax&c=makefriends",{id:id,type:type},function(data){
		if(data=="5"){
			layer.alert('���ȵ�¼��', 0, '��ʾ',function(){window.location.href =weburl+"/index.php?m=login&usertype=1";window.event.returnValue = false;return false;   });
		}else if(data=="4"){
			layer.msg('��������Լ�Ϊ���ѣ�', 2, 0);return false;
		}else if(data=="3"){
			layer.msg('��δͨ�������֤������Ӻ��ѣ�', 2, 8);return false;
		}else if(data=="2"){
			layer.msg('�Է�δͨ�������ˣ����ܼ���Ϊ���ѣ�', 2, 8);return false;
		}else if(data=="1"){
			$("#WB_webim").find(".wbim_min_friend").click();
			setTimeout("add_im('"+id+"','"+type+"','"+status+"','"+username+"')",200);
			
		}else if(data=="6"){
			$("#WB_webim").find(".wbim_min_friend").click();
			setTimeout("show_im('"+id+"')",200);
			
		}else if(data=="7"){
			$("#WB_webim").find(".wbim_min_friend").click();
			setTimeout("add_im('"+id+"','"+type+"','"+status+"','"+username+"')",200);
			
		}
	});
}
function show_im(id){
	$('#WB_webim').find('#im_'+id).click();
}
function add_im(id,type,status,username){
	$('#WB_webim').find('#im_'+id).click();
	var lis=$("#list_content4").find("ul").find("li");
	var ul=$("#list_content4").find("ul");
	var statusHtml='';
	if(status=="1"){
		statusHtml='<span class="W_chat_stat W_chat_stat_online"></span>';
	}else{
		statusHtml='<span class="W_chat_stat W_chat_stat_offline"></span>';
	}
	var typeName='';
	if(type=="2"){
		typeName='��ҵ';
	}else if(type=="1"){
		typeName='����';
	}
	var lihtml='<li class="clearfix" style="height:20px;line-height:20px;"><div class="webim_list_name" id="right_im_'+type+'"><div class="list_head_state" style="float:left;margin-top:5px; margin-right:5px;">'+statusHtml+'</div><span class="user_name" id="im_'+id+'" uid="'+id+'" usertype="'+type+'" style="float:left;">['+typeName+'] '+username+'</span></div></li>';

	if(lis.length==1){
		if(lis.text()=="���޺���"){
			ul.html(lihtml);
		}else if(lis.attr("uid")!=id){
			ul.append(lihtml);
		}
	}else{
		var flag=false;
		for(var i in lis){
			if(lis[i].attr("uid")==id){
				flag=true;break;
			}
		}
		if(!flag){
			ul.append(lihtml);
		}
	}
}
function layer_del(msg,url){ 
	if(msg==''){
		var i=layer.load('ִ���У����Ժ�...',0);
		$.get(url,function(data){
			layer.close(i);
			var data=eval('('+data+')');
			if(data.url=='1'){
				layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.reload();});return false;
			}else{
				layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.href=data.url;});return false;
			}
		});
	}else{
		layer.confirm(msg, function(){
			var i=layer.load('ִ���У����Ժ�...',0);
			$.get(url,function(data){
				layer.close(i);
				var data=eval('('+data+')');
				if(data.url=='1'){
					layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.reload();});return false;
				}else{
					layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.href=data.url;});return false;
				}
			});
		});
	}
}
function top_search(M,name){
	$("input[name='m']").val(M);
	$(".header_seach_find_list").hide();
	$('#search_name').html(name)
}
function top_searchs(M,name){
	$("input[name='m']").val(M);
	$(".index_place_position").hide();
	$('#search_name').html(name)
}


function returnmessage(frame_id){
	if(frame_id==''||frame_id==undefined){
		frame_id='supportiframe';
	}
	var message = $(window.frames[frame_id].document).find("#layer_msg").val();
	if(message != null){
		var url=$(window.frames[frame_id].document).find("#layer_url").val();
		var layer_time=$(window.frames[frame_id].document).find("#layer_time").val();
		var layer_st=$(window.frames[frame_id].document).find("#layer_st").val();
		if(url=='1'){
			layer.msg(message, layer_time, Number(layer_st),function(){ location.reload();});
		}else if(url==''){
			layer.msg(message, layer_time, Number(layer_st));
		}else{
			layer.msg(message, layer_time, Number(layer_st),function(){location.href = url;});
		}
	}
}
function com_msg(){
	var msg_content=$.trim($("#msg_content").val());
	if(msg_content==''){
		layer.msg('��ѯ���ݲ���Ϊ�գ�', 2,2);return false;
	}
}


function job_class(id,type,grade){
	if(type=='f'){
		var height=$("#dt_job_"+id).offset().top;
		$("#layout_job .dt_job_"+grade).removeClass('cur');
		$("#layout_job .dd_job_"+grade).hide();
		if(grade=='1'){
			var top=parseInt(height)-parseInt(615);
			$("#layout_job .dd_job_2").hide();
			$("#layout_job .dt_job_2").removeClass('cur');
		}else{
			
			var top=34;
		}
		$("#dt_job_"+id).addClass('cur');
		$("#dd_job_"+id).css("top",top);
		$("#dd_job_"+id).fadeIn("slow");
	}else{
		var check_length = $("input[type='checkbox'][name='job_class'][checked]").length;
		if($("#job_"+id).attr("checked")=="checked"){
			if(check_length>=5){
				layer.msg('�����ֻ��ѡ�����', 2,8);
				$("#job_"+id).attr("checked",false);
			}else{
				var value=$("#job_"+id).val();
				$("#job_choosed").append("<span id='span_job_"+id+"'><input id='ck_job_"+id+"'  value='"+id+"' onclick=\"del_ck('job_"+id+"')\" name='job_class' checked='checked' type='checkbox' target='"+value+"'>"+value+"</span>");
			}
		}else{
			$("#span_job_"+id).remove();
		}
	}
}
function job_city(id,type,grade){
	if(type=='province'){
		var height=$("#dt_"+id).offset().top;
		if(grade=='1'){
			var top=parseInt(height)-parseInt(570);
		}else{
			var top=34;
		}
		$("#layout_inner .dt_"+grade).removeClass('cur');
		$("#dt_"+id).addClass('cur');
		$("#layout_inner .dd_"+grade).hide();
		$("#dd_"+id).css("top",top);
		$("#dd_"+id).show();
	}else{
		var check_length = $("input[type='checkbox'][name='select_city'][checked]").length;
		if($("#"+id).attr("checked")=="checked"){
			if(check_length>=5){
				layer.msg('�����ֻ��ѡ��������У�', 2,8);
				$("#"+id).attr("checked",false);
			}else{
				var value=$("#"+id).val();
				$("#choosed").append("<span id='span_"+id+"'><input id='ck_"+id+"'  value='"+id+"' onclick=\"del_ck('"+id+"')\" name='select_city' checked='checked' type='checkbox' target='"+value+"'>"+value+"</span>");
			}
		}else{
			$("#span_"+id).remove();
		}
	}
}
function select_prop(name,id,div){
	var chk_value =[];
	var chk_ids =[];
	$('input[name="'+name+'"]:checked').each(function(){
		chk_value.push($(this).attr('target'));
		chk_ids.push($(this).val());
	});
	if(chk_value.length==0){
		layer.msg('��ѡ��ְλ���', 2,2);return false;
	}else{
		$("#"+id+" dt").removeClass("cur");
		$("#"+id+" dd").hide();
		$("#"+id).val(chk_value);
		$("#"+name).val(chk_ids);
		$("#"+id).removeClass("city_cur");
		$("#"+div).hide();
	}
}
function close_prop(div,id){
	$("#"+div).hide();
	$("#"+id).removeClass("city_cur");
}
function del_ck(id){
	$("#span_"+id).remove();
	$("#"+id).removeAttr("checked");
}

function atn(id){
	if(id){
		$.post(weburl+"/index.php?m=ajax&c=atn_company",{id:id},function(data){
			if(data==1){
				$("#atn_"+id).removeClass('zg-btn-unfollow');
				$("#atn_"+id).addClass('zg-btn-green');
				$("#atn_"+id).html("ȡ����ע");
				var antnum=$("#antnum"+id).html();
				$("#antnum"+id).html(parseInt(antnum)+1);
			}else if(data==2){
				$("#atn_"+id).removeClass('zg-btn-green');
				$("#atn_"+id).addClass('zg-btn-unfollow');
				$("#atn_"+id).html("��ע");
				var antnum=$("#antnum"+id).html();
				$("#antnum"+id).html(parseInt(antnum)-1);
			}else if(data==3){
				layer.msg('���ȵ�¼��', 2, 8);return false;
			}else if(data==4){
				layer.msg('ֻ�и����û��ſɹ�ע', 2, 8);return false;
			}
		});
	}
}
function jsmsg(id){
	var myuid = $("#myuid").val();
	if(myuid==""){
		layer.msg('�㻹û�е�¼��', 2, 8);
	}
	$("#msg"+id).show();
}
function showImgDelay(imgObj,imgSrc,maxErrorNum){
    if(maxErrorNum>0){
        imgObj.onerror=function(){
            showImgDelay(imgObj,imgSrc,maxErrorNum-1);
        };
        setTimeout(function(){
            imgObj.src=imgSrc;
        },500);
		maxErrorNum=parseInt(maxErrorNum)-parseInt(1);
    }
}
function reportSub(){
	var authcode=$("#report_authcode").val();
	var r_reason=$("#r_reason").val();
	var r_uid=$("#r_uid").val();
	var id=$("#id").val();
	var r_name=$("#r_name").val();
	if($.trim(r_reason)==""){
		layer.msg('�ٱ����ݲ���Ϊ�գ�', 2, 8);
		return false;
	}
	$.post("index.php?m=com&c=report",{authcode:authcode,r_reason:r_reason,id:id,r_name:r_name,r_uid:r_uid},function(data){
		layer.closeAll();
		if(data==1){
			layer.msg('��֤�벻��ȷ��', 2, 8);
		}else if(data==2){
			layer.msg('���Ѿ��ٱ������û���', 2, 8);
		}else if(data==3){
			layer.msg('�ٱ��ɹ���', 2,9);
		}else if(data==4){
			layer.msg('�ٱ�ʧ�ܣ�', 2, 8);
		}else if(data==5){
			layer.msg('��վ�ѹرվٱ����ܣ�', 2, 8);
		}
	})
}

function forrecord(id,page){ 
	$.post(weburl+"/index.php?m=ajax&c=jobrecord",{id:id,page:page},function(data){
		$(".Company_job_record_div").html(data);
	});
} 
$(function(){
	$('body').click(function(evt) {
		if($(evt.target).parents("#listhy").length==0 && evt.target.id != "buttonhy") {
			$('#listhy').hide();
		}
	})
})

function checkform_redeem_show(){
	var num=$("#num").val();
	var stock=$("#stock").val();
	var uid=$("#uid").val();
	var restriction=$("#restriction").val();
	if(!uid){
		layer.msg('����û�е�¼�����ȵ�¼��', 2, 8);
		return false;
	}
	if(num==0){
		layer.msg('����ȷ��д�һ�������', 2, 8);
		return false;
	}
	if(Number(num)>Number(restriction) && restriction!="0"){
		layer.msg('�����޹�����,����ȷ��д��', 2, 8);
		return false;
	}
	if(Number(num)>Number(stock)){
		layer.msg('�����������,����ȷ��д��', 2, 8);
		return false;
	}
	return true;
}
function redeem_dh(){
	var linkman=$("input[name=linkman]").val();
	var linktel=$("input[name=linktel]").val();
	var password=$("input[name=password]").val();
	if(!linkman || !linktel){
		layer.msg('��ϵ�˻���ϵ�绰����Ϊ�գ�', 2, 8);
		return false;
	}
	if(!password){
		layer.msg('���������룡', 2, 8);
		return false;
	}
	return true;
}


function istrainlogin(){
	layer.confirm("ֻ��ע����ѵ�˻��ſɷ������Ƿ��˳���ע�᣿",function(){
		window.location.href ='index.php?c=register';
	});
}