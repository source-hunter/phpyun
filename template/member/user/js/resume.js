function CheckPost(){
	var type=0;
	var name=$.trim($("input[name='name']").val());
	var num=0;
	$("input[type='radio'][name='sex']:checked").each(function(){
		num=1;
	})
	if(num==0){
		$("#by_sex").show();type=1;
	}
	var idcard=$.trim($("#idcard").val());
	var telphone=$.trim($("input[name='telphone']").val());
	var email=$.trim($("input[name='email']").val());
	var birthday=$.trim($("#birthday").val()); 
	var edu=$.trim($("#educid").val()); 
	var exp=$.trim($("#expid").val()); 
	var living=$.trim($("#living").val()); 
	var description=$.trim($("#description").val()); 
	if($.trim(name)==''){layer.msg($("#by_name").html(), 2, 8);return false;}
	if(idcard==''&&$("#idcard").length > 0){layer.msg($("#by_idcard").html(), 2, 8);return false;}
	if(birthday==''){layer.msg($("#by_birthday").html(), 2, 8);return false;}
	ifemail = check_email(email); 
	ifmoblie = isjsMobile(telphone);
	if(telphone==''){
		layer.msg($("#by_telphone").html(), 2, 8);return false;
	}else{
		if(ifmoblie==false){layer.msg("�ֻ���ʽ����ȷ", 2, 8);return false;}
	}
	if(email==''){
		layer.msg($("#by_email").html(), 2, 8);return false;
	}else{
		if(ifemail==false){layer.msg("�����ʼ���ʽ����ȷ", 2, 8);return false;}
	}
	if(living==''){layer.msg($("#by_living").html(), 2, 8);return false;}
	if(edu==''){layer.msg($("#by_educid").html(), 2, 8);return false;} 
	if(exp==''){layer.msg($("#by_expid").html(), 2, 8);return false;} 
	if(description==''){layer.msg($("#by_description").html(), 2, 8);return false;} 
	layer.load('ִ���У����Ժ�...',0);
}
function checkmore(type){
	var getinfoid=$.trim($("#getinfoid").val());
	if(getinfoid!=1){
		layer.msg('�������ƻ������ϣ�', 2, 2);return false;
	}
	$("#save"+type).show();
	$("#get"+type).hide();
	ScrollTo(type+"_botton")
}
function checkClose(type){
	$("#save"+type).hide();
	$("#get"+type).show();
}
function ScrollTo(id){
	$("#"+id).ScrollTo(700);
}
function checkmore2(type){
	var getinfoid=$.trim($("#getinfoid").val());
	if(getinfoid!=1){
		layer.msg('�������ƻ������ϣ�', 2, 2);return false;
	}
	var eid=$.trim($("#eid").val());
	if(eid==""){
		layer.msg('����������ְ����', 2, 2);return false;
	}
	ScrollTo(type+"_add");
	$("#"+type).show();
	$("#"+type+"_botton").attr("class","jianli_list_noadd");
	$("#"+type+"_botton").html('<em>�ݲ���д</em>');
	$("#"+type+"_botton").attr("onclick","checkClose2('"+type+"');");
	$("#Add"+type).hide();
	if(type=="skill"){
		$("#skillcid").val('');
		$("#levelid").val('');
		$("#skill_name").val('');
		$("#skill_longtime").val('');
		$("#skillid").val('');
		$("#skillc").val('��ѡ�������');
		$("#level").val('��ѡ�������̶�');
	}
	if(type=="work"){
		$("#totoday").attr("checked",false);
		$("#work_name").val('');
		$("#work_sdate").val('');
		$("#work_edate").val('');
		$("#work_department").val('');
		$("#work_title").val('');
		$("#work_content").val('');
		$("#workid").val('');
	}
	if(type=="project"){
		$("#project_name").val('');
		$("#project_sdate").val('');
		$("#project_edate").val('');
		$("#project_sys").val('');
		$("#project_title").val('');
		$("#project_content").val('');
		$("#projectid").val('');
	}
	if(type=="edu"){
		$("#edu_name").val('');
		$("#edu_sdate").val('');
		$("#edu_edate").val('');
		$("#edu_specialty").val('');
		$("#edu_title").val('');
		$("#edu_content").val('');
		$("#eduid").val('');
	}
	if(type=="training"){
		$("#training_name").val('');
		$("#training_sdate").val('');
		$("#training_edate").val('');
		$("#training_title").val('');
		$("#training_content").val('');
		$("#trainingid").val('');
	}
	if(type=="cert"){
		$("#cert_name").val('');
		$("#cert_sdate").val('');
		 
		$("#cert_title").val('');
		$("#cert_content").val('');
		$("#certid").val('');
	}
	if(type=="other"){
		$("#other_title").val('');
		$("#other_content").val('');
		$("#otherid").val('');
	}
}
function checkClose2(type){
	$("#"+type).hide();
	$("#"+type+"_botton").attr("class","jianli_list_add");
	$("#"+type+"_botton").html('<em>���</em>');
	$("#"+type+"_botton").attr("onclick","checkmore2('"+type+"');");
	$(".resume_"+type).addClass('state_done');
	$("#Add"+type).show();
}
function movelook(type,id){
	$("#"+type+id).addClass("expect_tj_list_hov");
}
function outlook(type,id){
	$("#"+type+id).bind("mouseleave", function(){
		$("#"+type+id).removeClass("expect_tj_list_hov");
	});
}
function getresume(type,id){
	ScrollTo(type+"_add");
	$("#"+type).show();
	$("#Add"+type).hide();
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=resume&act=resume_ajax",{type:type,id:id},function(data){
		layer.closeAll();
		data=eval('('+data+')');
		if(type=="skill"){
			$("#skillcid").val(data.skill);
			$("#levelid").val(data.ing);
			$("#skill_name").val(data.name);
			$("#skill_longtime").val(data.longtime);
			$("#skillid").val(data.id);
			$("#skillc").val(data.skillval);
			$("#level").val(data.ingval);
		}
		if(type=="work"){
			$("#work_name").val(data.name);
			$("#work_sdate").val(data.sdate);
			if(data.totoday=='1'){ 
				$("#totoday").attr("checked",true);
				$("#work_edate").hide();
				$("#work_edate").val('');
			}else{
				$("#work_edate").val(data.edate);
			} 
			$("#work_department").val(data.department);
			$("#work_title").val(data.title);
			$("#work_content").val(data.content);
			$("#workid").val(data.id);
		}
		if(type=="project"){
			$("#project_name").val(data.name);
			$("#project_sdate").val(data.sdate);
			$("#project_edate").val(data.edate);
			$("#project_sys").val(data.sys);
			$("#project_title").val(data.title);
			$("#project_content").val(data.content);
			$("#projectid").val(data.id);
		}
		if(type=="edu"){
			$("#edu_name").val(data.name);
			$("#edu_sdate").val(data.sdate);
			$("#edu_edate").val(data.edate);
			$("#edu_specialty").val(data.specialty);
			$("#edu_title").val(data.title);
			$("#edu_content").val(data.content);
			$("#eduid").val(data.id);
		}
		if(type=="training"){
			$("#training_name").val(data.name);
			$("#training_sdate").val(data.sdate);
			$("#training_edate").val(data.edate);
			$("#training_title").val(data.title);
			$("#training_content").val(data.content);
			$("#trainingid").val(data.id);
		}
		if(type=="cert"){
			$("#cert_name").val(data.name);
			$("#cert_sdate").val(data.sdate); 
			$("#cert_title").val(data.title);
			$("#cert_content").val(data.content);
			$("#certid").val(data.id);
		}
		if(type=="other"){
			$("#other_title").val(data.title);
			$("#other_content").val(data.content);
			$("#otherid").val(data.id);
		}
	})
}

function resume_del(table,id){
	var eid = $.trim($("#eid").val());
	layer.confirm('ȷ��Ҫɾ���������ݣ�', function(){
		layer.load('ִ���У����Ժ�...',0);
		$.post("index.php?c=resume&act=publicdel",{table:table,id:id,eid:eid},function(data){
			layer.closeAll();
			if(data!="0"){
				//data=data.split("##");
				data=eval('('+data+')');
				$("#"+table+id).remove();
				if(parseInt(data.integrity)<60){
					 var showhtml="�����ڵļ���������̫�ͣ������ܹ�ʹ�ô˼���ӦƸ!"
				}else{
					var showhtml="���ļ����ѷ���Ҫ��"
				}
				//�����Ҳ�������
				if(data.num<1){
					changeRightIntegrityState("m_right_"+table,"remove");
				}
				$("#_ctl0_UserManage_LeftTree1_msnInfo").html(showhtml);
				$("#numresume").html(data.integrity+"%");
				$(".play").attr("style","width:"+data.integrity+"%");
				if(data.num=="0"){
					$(".resume_"+table).removeClass('state_done');
				} 
				layer.msg('ɾ���ɹ���', 2,9);				
			}else{ 
				layer.msg('���緱æ�����Ժ�', 2,3);	
			}
		});
	}); 
}
function saveskill(){
	shell();
	var eid = $.trim($("#eid").val());
	var id = $.trim($("#skillid").val());
	var skill = $.trim($("#skillcid").val());
	var ing = $.trim($("#levelid").val());
	var name = $.trim($("#skill_name").val());
	var longtime = $.trim($("#skill_longtime").val());
	if(eid==""){ 
		layer.msg('����������ְ����', 2, 2);
		return false;
	}
	if(name==""){ 
		layer.msg('����д�������ƣ�', 2, 2);
		return false;
	} 
	if(longtime==""){ 
		layer.msg('����д����ʱ�䣡', 2, 2);
		return false;
	}
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=expect&act=skill",{skill:skill,ing:ing,name:name,longtime:longtime,eid:eid,id:id,table:"resume_skill",submit:"1",dom_sort:getDomSort()},function(data){
		layer.closeAll();
		if(data!=0){
			data=eval('('+data+')');
			$("#skill").hide();
			$("#Addskill").show();
			if(id>0){ 
				var html='<li><span>�������ƣ�</span>'+data.name+'</li><li><span>����ʱ�䣺</span>'+data.longtime+'��</li><li><span>�������</span>'+data.skillval+'</li><li><span>�����̶ȣ�</span>'+data.ingval+'</li>';
				$("#skill_"+id).html(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('skill');ScrollTo("skill_botton");}); 
			}else{
				numresume(data.numresume,'skill');
				var html='<div class="expect_tj_list" id="skill'+data.id+'" onmousemove="movelook(\'skill\',\''+data.id+'\');" onmouseout="outlook(\'skill\',\''+data.id+'\');"><div class="expect_modify"><a href="javascript:getresume(\'skill\',\''+data.id+'\');">�޸�</a><a href="javascript:resume_del(\'skill\',\''+data.id+'\');">ɾ��</a></div><ul class="expect_amend" id="skill_'+data.id+'"><li><span>�������ƣ�</span>'+data.name+'</li><li><span>����ʱ�䣺</span>'+data.longtime+'��</li><li><span>�������</span>'+data.skillval+'</li><li><span>�����̶ȣ�</span>'+data.ingval+'</li></ul></div>';
				$("#skillList").append(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('skill');ScrollTo("skill_botton");}); 
			}
			changeRightIntegrityState("m_right2","add");
		}else{ 
			layer.msg('����ʧ�ܣ�', 2,8);
		}
	});
}
function changeRightIntegrityState(id,state){
	if(state=="add"){
		$("#"+id).find(".dom_m_right_state").removeClass("state");
		$("#"+id).find(".dom_m_right_state").addClass("state_done");
		$("."+id).removeClass("state");
		$("."+id).addClass("state_done");		
	}else{
		$("#"+id).find(".dom_m_right_state").removeClass("state_done");
		$("#"+id).find(".dom_m_right_state").addClass("state");	
		$("."+id).removeClass("state_done");
		$("."+id).addClass("state");		
	}
}
function savework(){
	shell();
	var eid = $.trim($("#eid").val());
	var id = $.trim($("#workid").val());
	var sdate = $.trim($("#work_sdate").val());
	var edate = $.trim($("#work_edate").val());
	var name = $.trim($("#work_name").val());
	var department = $.trim($("#work_department").val());
	var title = $.trim($("#work_title").val());
	var content = $.trim($("#work_content").val());
	if(eid==""){ 
		layer.msg('����������ְ����', 2,2);
		return false;
	}
	if(name==""){ 
		layer.msg('����д��λ���ƣ�', 2,2);
		return false;
	}
	if(sdate==""){
		layer.msg('��ʼʱ�䲻��Ϊ�գ�', 2,3); return false
	}else if(edate){
		var st=toDate(sdate);
		var ed=toDate(edate);
		if(st>ed){ 	
			layer.msg('��ʼʱ�䲻�ô��ڽ���ʱ��', 2,3);return false
		}
	}	
	if(department==""){ 
		layer.msg('����д���ڲ��ţ�', 2,2);
		return false;
	} 
	if(content==""){ 
		layer.msg('����д�������ݣ�', 2,2);
		return false;
	}
	layer.load('ִ���У����Ժ�...',0); 
	$.post("index.php?c=expect&act=work",{sdate:sdate,edate:edate,name:name,department:department,eid:eid,title:title,content:content,id:id,table:"resume_work",submit:"1",dom_sort:getDomSort()},function(data){	
		layer.closeAll();
		if(data!=0){
			data=eval('('+data+')');
			$("#work").hide();
			$("#Addwork").show();
			if(id>0){ 
				var html='<li><span>��λ���ƣ�</span>'+data.name+'</li><li><span>����ʱ�䣺</span>'+data.sdate+'��  '+data.edate+'</li><li><span>���ڲ��ţ�</span>'+data.department+'</li><li><span>����ְλ��</span>'+data.title+'</li><li class="expect_amend_end"><span>�������ݣ�</span><em>'+data.content+'</em></li>';
				$("#work_"+id).html(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('work');ScrollTo("work_botton");}); 
			}else{
				numresume(data.numresume,'work');
				var html='<div class="expect_tj_list" id="work'+data.id+'" onmousemove="movelook(\'work\',\''+data.id+'\');" onmouseout="outlook(\'work\',\''+data.id+'\');"><div class="expect_modify"><a href="javascript:getresume(\'work\',\''+data.id+'\');">�޸�</a><a href="javascript:resume_del(\'work\',\''+data.id+'\');">ɾ��</a></div><ul class="expect_amend" id="work_'+data.id+'"><li><span>��λ���ƣ�</span>'+data.name+'</li><li><span>����ʱ�䣺</span>'+data.sdate+'�� '+data.edate+'</li><li><span>���ڲ��ţ�</span>'+data.department+'</li><li><span>����ְλ��</span>'+data.title+'</li><li class="expect_amend_end"><span>�������ݣ�</span><em>'+data.content+'</em></li></ul></div>';
				$("#workList").append(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('work');ScrollTo("work_botton");}); 
			}
			changeRightIntegrityState("m_right3","add");
		}else{ 
			layer.msg('����ʧ�ܣ�', 2,8);
		}
	});
}
function saveproject(){
	shell();
	var eid = $.trim($("#eid").val());
	var id = $.trim($("#projectid").val());
	var sdate = $.trim($("#project_sdate").val());
	var edate = $.trim($("#project_edate").val());
	var name = $.trim($("#project_name").val());
	var sys = $.trim($("#project_sys").val());
	var title = $.trim($("#project_title").val());
	var content = $.trim($("#project_content").val());
	if(eid==""){ 
		layer.msg('����������ְ����', 2,2);
		return false;
	}
	if(name==""){ 
		layer.msg('����д��Ŀ���ƣ�', 2,2);
		return false;
	}
	if(sdate==""||edate=="")
	{ 
		layer.msg('��ʼʱ�䣬����ʱ�䲻��Ϊ�գ�', 2,3);
		return false
	}else{
		var st=toDate(sdate);
		var ed=toDate(edate);
		if(st>ed){ 
			layer.msg('��ʼʱ�䲻�ô��ڽ���ʱ�䣡', 2,3);			
			return false
		}
	}	
	if(sys==""){ 
		layer.msg('����д��Ŀ������', 2,2);
		return false;
	} 
	if(content==""){
		layer.msg('����д��Ŀ���ݣ�', 2,2); 
		return false;
	}
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=expect&act=project",{sdate:sdate,edate:edate,name:name,sys:sys,eid:eid,title:title,content:content,id:id,table:"resume_project",submit:"1",dom_sort:getDomSort()},function(data){
		layer.closeAll();
		if(data!=0){
			data=eval('('+data+')');
			$("#project").hide();
			$("#Addproject").show();
			if(id>0){ 
				var html='<li><span>��Ŀ���ƣ�</span>'+data.name+'</li><li><span>��Ŀʱ�䣺</span>'+data.sdate+'��'+data.edate+'</li><li><span>��Ŀ������</span>'+data.sys+'</li><li><span> ����ְλ��</span>'+data.title+'</li><li class="expect_amend_end"><span>��Ŀ���ݣ�</span><em>'+data.content+'</em></li>';
				$("#project_"+id).html(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('project');ScrollTo("project_botton");});	 
			}else{
				numresume(data.numresume,'project');
				var html='<div class="expect_tj_list" id="project'+data.id+'" onmousemove="movelook(\'project\',\''+data.id+'\');" onmouseout="outlook(\'project\',\''+data.id+'\');"><div class="expect_modify"><a href="javascript:getresume(\'project\',\''+data.id+'\');">�޸�</a><a href="javascript:resume_del(\'project\',\''+data.id+'\');">ɾ��</a></div><ul class="expect_amend" id="project_'+data.id+'"><li><span>��Ŀ���ƣ�</span>'+data.name+'</li><li><span>��Ŀʱ�䣺</span>'+data.sdate+'��'+data.edate+'</li><li><span>��Ŀ������</span>'+data.sys+'</li><li><span> ����ְλ��</span>'+data.title+'</li><li class="expect_amend_end"><span>��Ŀ���ݣ�</span><em>'+data.content+'</em></li></ul></div>';
				$("#projectList").append(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('project');ScrollTo("project_botton");}); 
			}
			changeRightIntegrityState("m_right4","add");
		}else{ 
			layer.msg('����ʧ�ܣ�', 2,8); 
		}
	});
}
function saveedu(){
	shell();
	var eid = $.trim($("#eid").val());
	var id = $.trim($("#eduid").val());
	var sdate = $.trim($("#edu_sdate").val());
	var edate = $.trim($("#edu_edate").val());
	var name = $.trim($("#edu_name").val());
	var specialty = $.trim($("#edu_specialty").val());
	var title = $.trim($("#edu_title").val());
	var content = $.trim($("#edu_content").val());
	if(eid==""){ 
		layer.msg('����������ְ����', 2,2);
		return false;
	}
	if(name==""){ 
		layer.msg('����дѧУ���ƣ�', 2,2);
		return false;
	}
	if(sdate==""||edate=="")
	{
		layer.msg('��ʼʱ�䣬����ʱ�䲻��Ϊ�գ�', 2,3); 
		return false
	}else{
		var st=toDate(sdate);
		var ed=toDate(edate);
		if(st>ed){
			layer.msg('��ʼʱ�䲻�ô��ڽ���ʱ��', 2,3); 
			return false
		}
	} 
	if(content==""){ 
		layer.msg('����дרҵ������', 2,2); 
		return false;
	}	
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=expect&act=edu",{sdate:sdate,edate:edate,name:name,specialty:specialty,eid:eid,title:title,content:content,id:id,table:"resume_edu",submit:"1",dom_sort:getDomSort()},function(data){
		layer.closeAll();
		if(data!=0){
			data=eval('('+data+')');
			$("#edu").hide();
			$("#Addedu").show();
			if(id>0){ 
				var html='<li><span>ѧУ���ƣ�</span>'+data.name+'</li><li><span>��Уʱ�䣺</span>'+data.sdate+'��'+data.edate+'</li><li><span>��ѧרҵ��</span>'+data.specialty+'</li><li><span>����ְλ��</span>'+data.title+'</li><li class="expect_amend_end"><span>רҵ������</span><em>'+data.content+'</em></li>';
				$("#edu_"+id).html(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('edu');ScrollTo("edu_botton");}); 
			}else{
				numresume(data.numresume,'edu');
				var html='<div class="expect_tj_list" id="edu'+data.id+'" onmousemove="movelook(\'edu\',\''+data.id+'\');" onmouseout="outlook(\'edu\',\''+data.id+'\');"><div class="expect_modify"><a href="javascript:getresume(\'edu\',\''+data.id+'\');">�޸�</a><a href="javascript:resume_del(\'edu\',\''+data.id+'\');">ɾ��</a></div><ul class="expect_amend" id="edu_'+data.id+'"><li><span>ѧУ���ƣ�</span>'+data.name+'</li><li><span>��Уʱ�䣺</span>'+data.sdate+'��'+data.edate+'</li><li><span>��ѧרҵ��</span>'+data.specialty+'</li><li><span>����ְλ��</span>'+data.title+'</li><li class="expect_amend_end"><span>רҵ������</span><em>'+data.content+'</em></li></ul></div>';
				$("#eduList").append(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('edu');ScrollTo("edu_botton");}); 
			}
			changeRightIntegrityState("m_right0","add");
		}else{ 
			layer.msg('����ʧ�ܣ�', 2,8);
		}
	});
}
function savetraining(){
	shell();
	var eid = $.trim($("#eid").val());
	var id = $.trim($("#trainingid").val());
	var sdate = $.trim($("#training_sdate").val());
	var edate = $.trim($("#training_edate").val());
	var name = $.trim($("#training_name").val());
	var title = $.trim($("#training_title").val());
	var content = $.trim($("#training_content").val());
	if(eid==""){ 
		layer.msg('����������ְ����', 2,3);
		return false;
	}
	if(name==""){ 
		layer.msg('����д��ѵ���ģ�', 2,3);
		return false;
	}
	if(sdate==""||edate=="")
	{ 
		layer.msg('��ʼʱ�䣬����ʱ�䲻��Ϊ�գ�', 2,3);		
		return false
	}else{
		var st=toDate(sdate);
		var ed=toDate(edate);
		if(st>ed){ 
			layer.msg('��ʼʱ�䲻�ô��ڽ���ʱ��', 2,3);			
			return false
		}
	}	
	if(title==""){ 
		layer.msg('����д��ѵ����', 2,2);
		return false;
	}
	if(content==""){ 
		layer.msg('����д��ѵ������', 2,2);
		return false;
	}	
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=expect&act=training",{sdate:sdate,edate:edate,name:name,eid:eid,title:title,content:content,id:id,table:"resume_training",submit:"1",dom_sort:getDomSort()},function(data){
		layer.closeAll();
		if(data!=0){
			data=eval('('+data+')');
			$("#training").hide();
			$("#Addtraining").show();
			if(id>0){ 
				var html='<li><span>��ѵ���ģ�</span>'+data.name+'</li><li><span>��ѵʱ�䣺</span>'+data.sdate+'��'+data.edate+'</li><li><span>��ѵ����</span>'+data.title+'</li><li class="expect_amend_end"><span>��ѵ������</span><em>'+data.content+'</em></li>';
				$("#training_"+id).html(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('training');ScrollTo("training_botton");}); 
			}else{
				numresume(data.numresume,'training');
				var html='<div class="expect_tj_list" id="training'+data.id+'" onmousemove="movelook(\'training\',\''+data.id+'\');" onmouseout="outlook(\'training\',\''+data.id+'\');"><div class="expect_modify"><a href="javascript:getresume(\'training\',\''+data.id+'\');">�޸�</a><a href="javascript:resume_del(\'training\',\''+data.id+'\');">ɾ��</a></div><ul class="expect_amend" id="training_'+data.id+'"><li><span>��ѵ���ģ�</span>'+data.name+'</li><li><span>��ѵʱ�䣺</span>'+data.sdate+'��'+data.edate+'</li><li><span>��ѵ����</span>'+data.title+'</li><li class="expect_amend_end"><span>��ѵ������</span><em>'+data.content+'</em></li></ul></div>';
				$("#trainingList").append(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('training');ScrollTo("training_botton");}); 
			}
			changeRightIntegrityState("m_right1","add");
		}else{ 
			layer.msg('����ʧ�ܣ�', 2,8);
		}
	});
}
function savecert(){
	shell();
	var eid = $.trim($("#eid").val());
	var id = $.trim($("#certid").val());
	var sdate = $.trim($("#cert_sdate").val());
	var name = $.trim($("#cert_name").val());
	var title = $.trim($("#cert_title").val());
	var content = $.trim($("#cert_content").val());
	if(eid==""){ 
		layer.msg('����������ְ����', 2,2);
		return false;
	}
	if(name==""){ 
		layer.msg('����д֤�����ƣ�', 2,2);
		return false;
	}
	if(sdate==""){ 
		layer.msg('��ʼʱ�䣬����ʱ�䲻��Ϊ�գ�', 2,3);return false
	}
	if(title==""){ 
		layer.msg('����д�䷢��λ��', 2,2);
		return false;
	}
	if(content==""){ 
		layer.msg('����д֤��������', 2,2);
		return false;
	}	
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=expect&act=cert",{sdate:sdate,name:name,eid:eid,title:title,content:content,id:id,table:"resume_cert",submit:"1",dom_sort:getDomSort()},function(data){
		layer.closeAll();
		if(data!=0){
			data=eval('('+data+')');
			$("#cert").hide();
			$("#Addcert").show();
			if(id>0){ 
				var html='<li><span>֤��ȫ�ƣ�</span>'+data.name+'</li><li><span>�䷢ʱ�䣺</span>'+data.sdate+'</li><li><span>�䷢��λ��</span>'+data.title+'</li><li class="expect_amend_end"><span>֤��������</span><em>'+data.content+'</em></li>';
				$("#cert_"+id).html(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('cert');ScrollTo("cert_botton");}); 
			}else{
				numresume(data.numresume,'cert');
				var html='<div class="expect_tj_list" id="cert'+data.id+'" onmousemove="movelook(\'cert\',\''+data.id+'\');" onmouseout="outlook(\'cert\',\''+data.id+'\');"><div class="expect_modify"><a href="javascript:getresume(\'cert\',\''+data.id+'\');">�޸�</a><a href="javascript:resume_del(\'cert\',\''+data.id+'\');">ɾ��</a></div><ul class="expect_amend" id="cert_'+data.id+'"><li><span>֤��ȫ�ƣ�</span>'+data.name+'</li><li><span>�䷢ʱ�䣺</span>'+data.sdate+'</li><li><span>�䷢��λ��</span>'+data.title+'</li><li class="expect_amend_end"><span>֤��������</span><em>'+data.content+'</em></li></ul></div>';
				$("#certList").append(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('cert');ScrollTo("cert_botton");}); 
			}
			changeRightIntegrityState("m_right5","add");
		}else{ 
			layer.msg('����ʧ�ܣ�', 2,8);
		}
	});
}
function saveother(){
	shell();
	var eid = $.trim($("#eid").val());
	var id = $.trim($("#otherid").val());
	var title = $.trim($("#other_title").val());
	var content = $.trim($("#other_content").val());
	if(eid==""){ 
		layer.msg('����������ְ����', 2,2);
		return false;
	}
	if(title==""){ 
		layer.msg('����д�������⣡', 2,2);
		return false;
	}
	if(content==""){ 
		layer.msg('����д����������', 2,2);
		return false;
	}	
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=expect&act=other",{eid:eid,title:title,content:content,id:id,table:"resume_other",submit:"1",dom_sort:getDomSort()},function(data){
		layer.closeAll();
		if(data!=0){
			data=eval('('+data+')');
			$("#other").hide();
			$("#Addother").show();
			if(id>0){ 
				var html='<li><span>�������⣺</span>'+data.title+'</li><li class="expect_amend_end"><span>����������</span><em>'+data.content+'</em></li>';
				$("#other_"+id).html(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('other');ScrollTo("other_botton");}); 
			}else{
				numresume(data.numresume,'other');
				var html='<div class="expect_tj_list" id="other'+data.id+'" onmousemove="movelook(\'other\',\''+data.id+'\');" onmouseout="outlook(\'other\',\''+data.id+'\');"><div class="expect_modify"><a href="javascript:getresume(\'other\',\''+data.id+'\');">�޸�</a><a href="javascript:resume_del(\'other\',\''+data.id+'\');">ɾ��</a></div><ul class="expect_amend" id="other_'+data.id+'"><li><span>�������⣺</span>'+data.title+'</li><li class="expect_amend_end"><span>����������</span><em>'+data.content+'</em></li></ul></div>';
				$("#otherList").append(html);
				layer.msg('�����ɹ���', 2,9,function(){checkClose2('other');ScrollTo("other_botton");}); 				
			}
			changeRightIntegrityState("m_right6","add");
		}else{ 
			layer.msg('����ʧ�ܣ�', 2, 8);
		}	
	});
}
function toDate(str){
    var sd=str.split("-");
    return new Date(sd[0],sd[1],sd[2]);
}
function numresume(numresume,type){
	if(numresume<60){
		 var showhtml="�����ڵļ���������̫�ͣ������ܹ�ʹ�ô˼���ӦƸ!"
	}else{
		var showhtml="���ļ����ѷ���Ҫ��"
	}
	$("#_ctl0_UserManage_LeftTree1_msnInfo").html(showhtml);
	$("#numresume").html(numresume+"%");
	$(".resume_"+type).show();
	$(".play").attr("style","width:"+numresume+"px");
}


function shell(){
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=expect",{shell:1},function(data){
 		if(data==1){
			layer.msg('�������ƻ������ϣ�', 2, 2);return false;
		}
	});
}



function saveexpect(){	
	shell();
	var name = $.trim($("#expect_name").val()); 
	var hy = $.trim($("#hyid").val());  
	var job_classid = $.trim($("#job_class").val());
	var provinceid = $.trim($("#provinceid").val());
	var cityid = $.trim($("#citysid").val());
	var three_cityid = $.trim($("#three_cityid").val());
	var salary = $.trim($("#salaryid").val()); 
	var type = $.trim($("#typeid").val()); 
	var report = $.trim($("#reportid").val());
	var eid = $.trim($("#eid").val());
	if(name==""||name=="����: ������Ա���� 2�깤������"){layer.msg('����д�������ƣ�', 2, 2);return false; }
	if(hy==""){layer.msg('��ѡ�������ҵ��', 2, 2);return false;}
	if(three_cityid==""&&cityid==''){layer.msg('��ѡ�����ص㣡', 2, 2);return false;}
	if(job_classid==""){layer.msg('��ѡ�����ְλ��', 2, 2);return false;}
	if(salary==""){layer.msg('��ѡ������нˮ��', 2, 2);return false;}
	if(type==""){layer.msg('��ѡ�������ʣ�', 2, 2);return false;}
	if(report==""){layer.msg('��ѡ�񵽸�ʱ�䣡', 2, 2);return false;}
	layer.load('ִ���У����Ժ�...',0);
	$.post("index.php?c=expect&act=saveexpect",{name:name,hy:hy,job_classid:job_classid,provinceid:provinceid,cityid:cityid,three_cityid:three_cityid,salary:salary,type:type,report:report,eid:eid,submit:"1",dom_sort:getDomSort()},function(data){
		layer.closeAll();
		if(data==0){
			layer.msg('����ʧ�ܣ�', 2, 8);
		}else if(data==1){
			layer.msg('��ļ������Ѿ�����ϵͳ���õļ������ˣ�', 2, 8);
		}else{
			data=eval('('+data+')');
			if(eid==""){
				 window.location.href="index.php?c=expect&e="+data.id;
			}else{
				$("#saveexpect").hide();
				$("#getexpect").show();
				numresume(data.numresume,'expect');
				$("#eid").val(data.id);
				var html='<li><span>�������ƣ�</span>'+data.name+'</li><li><span>����ʱ�䣺</span>'+data.report+'</li><li><span>����������ҵ��</span>'+data.hy+'</li><li><span>���������ص㣺</span>'+data.city+'</li><li><span>������нˮ��</span>'+data.salary+'</li><li><span>�����������ʣ�</span>'+data.type+'</li><li class="expect_amend_end"><span>��������ְλ��</span><em>'+data.job_classname+'</em></li>';
				$("#expect").html(html);
				layer.msg('�����ɹ���', 2,9,function(){ScrollTo("expect_botton");$(".resume_expect").addClass('state_done');}); 
			}
		}
	});
}
function totoday(){
	if($("#totoday").attr("checked")=='checked'){
		$('#work_edate').val('');
		$('#work_edate').hide();
	}else{
		$('#work_edate').show();
	}
}

function checkbox_more(id){
	var codewebarr="";
	$("#"+id+" input[type=checkbox][checked]").each(function(){
		if(codewebarr==""){codewebarr=$(this).val();}else{codewebarr=codewebarr+","+$(this).val();}
	}); 
	return codewebarr;
}
function location_url(url){
	if($.trim($("#eid").val())==""){
		layer.msg('�������Ƽ�����', 2,8);
	}else{
		 window.location.href=url;
	}
}

function getDomSort(){
	var domsort="";
	var elements=$("#dom0 .dom_m");
	for(var i=0;i<elements.length;i++){
		domsort=domsort+","+$(elements[i]).attr("id");
	}
	return domsort=domsort.substring(1,domsort.length);
}
//�����༭���̵�Ԥ��
function reviewresume(resumeid){
	if(resumeid!=''){
		//�޸ļ���
		//������Ϣ
		var username = $.trim($("#expect_name").val()); 
		var sex = $.trim($("#sex").val()); 
		var idcard = $.trim($("#idcard").val()); 
		var birthday = $.trim($("#birthday").val()); 
		var living = $.trim($("#living").val()); 
		var educid = $.trim($("#educid").val()); 
		var expid = $.trim($("#expid").val()); 
		var telphone = $.trim($("#telphone").val()); 
		var email = $.trim($("#email").val()); 
		var height = $.trim($("#height").val()); 
		var nationality = $.trim($("#nationality").val()); 
		var weight = $.trim($("#weight").val()); 
		var marriageid = $.trim($("#marriageid").val()); 
		var telhome = $.trim($("#telhome").val()); 
		var domicile = $.trim($("#domicile").val()); 
		var address = $.trim($("#address").val()); 
		var homepage = $.trim($("#homepage").val()); 
		var description = $.trim($("#description").val()); 
		
		//����ְλ
		var expect_name = $.trim($("#expect_name").val()); 
		var hyid = $.trim($("#hyid").val());  
		var job_class = $.trim($("#job_class").val());
		var provinceid = $.trim($("#provinceid").val());
		var citysid = $.trim($("#citysid").val());
		var three_cityid = $.trim($("#three_cityid").val());
		var salaryid = $.trim($("#salaryid").val()); 
		var typeid = $.trim($("#typeid").val()); 
		var reportid = $.trim($("#reportid").val());
		var eid = $.trim($("#eid").val());
		//1.רҵ����
		var skillid = $.trim($("#skillid").val());
		var skillcid = $.trim($("#skillcid").val());
		var levelid = $.trim($("#levelid").val());
		var skill_name = $.trim($("#skill_name").val());
		var skill_longtime = $.trim($("#skill_longtime").val());
		//2.��������
		var workid = $.trim($("#workid").val());
		var work_sdate = $.trim($("#work_sdate").val());
		var work_edate = $.trim($("#work_edate").val());
		var work_name = $.trim($("#work_name").val());
		var work_department = $.trim($("#work_department").val());
		var work_title = $.trim($("#work_title").val());
		var work_content = $.trim($("#work_content").val());
		//3.��Ŀ����
		var projectid = $.trim($("#projectid").val());
		var project_sdate = $.trim($("#project_sdate").val());
		var project_edate = $.trim($("#project_edate").val());
		var project_name = $.trim($("#project_name").val());
		var project_sys = $.trim($("#project_sys").val());
		var project_title = $.trim($("#project_title").val());
		var project_content = $.trim($("#project_content").val());
		//4.��������
		var eid = $.trim($("#eid").val());
		var eduid = $.trim($("#eduid").val());
		var edu_sdate = $.trim($("#edu_sdate").val());
		var edu_edate = $.trim($("#edu_edate").val());
		var edu_name = $.trim($("#edu_name").val());
		var edu_specialty = $.trim($("#edu_specialty").val());
		var edu_title = $.trim($("#edu_title").val());
		var edu_content = $.trim($("#edu_content").val());
		//5.��ѵ����
		var trainingid = $.trim($("#trainingid").val());
		var training_sdate = $.trim($("#training_sdate").val());
		var training_edate = $.trim($("#training_edate").val());
		var training_name = $.trim($("#training_name").val());
		var training_title = $.trim($("#training_title").val());
		var training_content = $.trim($("#training_content").val());
		//6.֤��
		var certid = $.trim($("#certid").val());
		var cert_sdate = $.trim($("#cert_sdate").val()); 
		var cert_name = $.trim($("#cert_name").val());
		var cert_title = $.trim($("#cert_title").val());
		var cert_content = $.trim($("#cert_content").val());
		//7.������Ϣ
		var otherid = $.trim($("#otherid").val());
		var other_title = $.trim($("#other_title").val());
		var other_content = $.trim($("#other_content").val());
		$.post("index.php?c=expect&act=setreviewresume",{eid:eid,otherid:otherid,other_title:other_title,other_content:other_content,
		certid:certid,cert_sdate:cert_sdate,cert_name:cert_name,cert_title:cert_title,cert_content:cert_content,
		trainingid:trainingid,training_sdate:training_sdate,training_edate:training_edate,training_name:training_name,training_title:training_title,training_content:training_content,
		eduid:eduid,edu_sdate:edu_sdate,edu_edate:edu_edate,edu_name:edu_name,edu_specialty:edu_specialty,edu_title:edu_title,edu_content:edu_content,
		projectid:projectid,project_sdate:project_sdate,project_edate:project_edate,project_name:project_name,project_sys:project_sys,project_title:project_title,project_content:project_content,
		workid:workid,work_sdate:work_sdate,work_edate:work_edate,work_name:work_name,work_department:work_department,work_title:work_title,work_content:work_content,
		skillid:skillid,skillcid:skillcid,levelid:levelid,skill_name:skill_name,skill_longtime:skill_longtime,
		expect_name:expect_name,hyid:hyid,job_class:job_class,provinceid:provinceid,citysid:citysid,three_cityid:three_cityid,salaryid:salaryid,typeid:typeid,reportid:reportid,
		username:username,sex:sex,idcard:idcard,birthday:birthday,living:living,educid:educid,expid:expid,telphone:telphone,email:email,height:height,nationality:nationality,weight:weight,marriageid:marriageid,telhome:telhome,domicile:domicile,address:address,homepage:homepage,description:description},function(){
			window.open("index.php?c=expect&act=reviewresume","����Ԥ��");
		});
	}else{
		//��������
		//������Ϣ
		var username = $.trim($("#expect_name").val()); 
		var sex = $.trim($("#sex").val()); 
		var idcard = $.trim($("#idcard").val()); 
		var birthday = $.trim($("#birthday").val()); 
		var living = $.trim($("#living").val()); 
		var educid = $.trim($("#educid").val()); 
		var expid = $.trim($("#expid").val()); 
		var telphone = $.trim($("#telphone").val()); 
		var email = $.trim($("#email").val()); 
		var height = $.trim($("#height").val()); 
		var nationality = $.trim($("#nationality").val()); 
		var weight = $.trim($("#weight").val()); 
		var marriageid = $.trim($("#marriageid").val()); 
		var telhome = $.trim($("#telhome").val()); 
		var domicile = $.trim($("#domicile").val()); 
		var address = $.trim($("#address").val()); 
		var homepage = $.trim($("#homepage").val()); 
		var description = $.trim($("#description").val()); 
		
		//����ְλ
		var expect_name = $.trim($("#expect_name").val()); 
		var hyid = $.trim($("#hyid").val());  
		var job_class = $.trim($("#job_class").val());
		var provinceid = $.trim($("#provinceid").val());
		var citysid = $.trim($("#citysid").val());
		var three_cityid = $.trim($("#three_cityid").val());
		var salaryid = $.trim($("#salaryid").val()); 
		var typeid = $.trim($("#typeid").val()); 
		var reportid = $.trim($("#reportid").val());
		var eid = $.trim($("#eid").val());
		//1.רҵ����
		var skillid = $.trim($("#skillid").val());
		var skillcid = $.trim($("#skillcid").val());
		var levelid = $.trim($("#levelid").val());
		var skill_name = $.trim($("#skill_name").val());
		var skill_longtime = $.trim($("#skill_longtime").val());
		//2.��������
		var workid = $.trim($("#workid").val());
		var work_sdate = $.trim($("#work_sdate").val());
		var work_edate = $.trim($("#work_edate").val());
		var work_name = $.trim($("#work_name").val());
		var work_department = $.trim($("#work_department").val());
		var work_title = $.trim($("#work_title").val());
		var work_content = $.trim($("#work_content").val());
		//3.��Ŀ����
		var projectid = $.trim($("#projectid").val());
		var project_sdate = $.trim($("#project_sdate").val());
		var project_edate = $.trim($("#project_edate").val());
		var project_name = $.trim($("#project_name").val());
		var project_sys = $.trim($("#project_sys").val());
		var project_title = $.trim($("#project_title").val());
		var project_content = $.trim($("#project_content").val());
		//4.��������
		var eid = $.trim($("#eid").val());
		var eduid = $.trim($("#eduid").val());
		var edu_sdate = $.trim($("#edu_sdate").val());
		var edu_edate = $.trim($("#edu_edate").val());
		var edu_name = $.trim($("#edu_name").val());
		var edu_specialty = $.trim($("#edu_specialty").val());
		var edu_title = $.trim($("#edu_title").val());
		var edu_content = $.trim($("#edu_content").val());
		//5.��ѵ����
		var trainingid = $.trim($("#trainingid").val());
		var training_sdate = $.trim($("#training_sdate").val());
		var training_edate = $.trim($("#training_edate").val());
		var training_name = $.trim($("#training_name").val());
		var training_title = $.trim($("#training_title").val());
		var training_content = $.trim($("#training_content").val());
		//6.֤��
		var certid = $.trim($("#certid").val());
		var cert_sdate = $.trim($("#cert_sdate").val()); 
		var cert_name = $.trim($("#cert_name").val());
		var cert_title = $.trim($("#cert_title").val());
		var cert_content = $.trim($("#cert_content").val());
		//7.������Ϣ
		var otherid = $.trim($("#otherid").val());
		var other_title = $.trim($("#other_title").val());
		var other_content = $.trim($("#other_content").val());
		$.post("index.php?c=expect&act=setreviewresume",{eid:eid,otherid:otherid,other_title:other_title,other_content:other_content,
		certid:certid,cert_sdate:cert_sdate,cert_name:cert_name,cert_title:cert_title,cert_content:cert_content,
		trainingid:trainingid,training_sdate:training_sdate,training_edate:training_edate,training_name:training_name,training_title:training_title,training_content:training_content,
		eduid:eduid,edu_sdate:edu_sdate,edu_edate:edu_edate,edu_name:edu_name,edu_specialty:edu_specialty,edu_title:edu_title,edu_content:edu_content,
		projectid:projectid,project_sdate:project_sdate,project_edate:project_edate,project_name:project_name,project_sys:project_sys,project_title:project_title,project_content:project_content,
		workid:workid,work_sdate:work_sdate,work_edate:work_edate,work_name:work_name,work_department:work_department,work_title:work_title,work_content:work_content,
		skillid:skillid,skillcid:skillcid,levelid:levelid,skill_name:skill_name,skill_longtime:skill_longtime,
		expect_name:expect_name,hyid:hyid,job_class:job_class,provinceid:provinceid,citysid:citysid,three_cityid:three_cityid,salaryid:salaryid,typeid:typeid,reportid:reportid,
		username:username,sex:sex,idcard:idcard,birthday:birthday,living:living,educid:educid,expid:expid,telphone:telphone,email:email,height:height,nationality:nationality,weight:weight,marriageid:marriageid,telhome:telhome,domicile:domicile,address:address,homepage:homepage,description:description},function(){
			window.open("index.php?c=expect&act=reviewresume","����Ԥ��");
		});
	}
}