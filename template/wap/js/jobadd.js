function checkinfo(){
	var name=$.trim($("#name").val());
	var hy=$.trim($("#hy").val());
	var pr=$.trim($("#pr").val());
	var cityid=$.trim($("#cityid").val());
	var address=$.trim($("#address").val());
	var mun=$.trim($("#mun").val());
	var linkphone=$.trim($("#linkphone").val());
	var linkmail=$.trim($("#linkmail").val());
	if(name==''){layermsg("��������ҵ���ƣ�");return false;}
	if(hy==''){layermsg("��ѡ����ҵ��ҵ��");return false;}
	if(pr==''){layermsg("��ѡ����ҵ���ʣ�");return false;}
	if(cityid==''){layermsg("��ѡ����ҵ��ַ��");return false;}
	if(mun==''){layermsg("��ѡ����ҵ��ģ��");return false;}
	if(address==''){layermsg("����д��˾��ַ��");return false;}
	if(linkphone==''){layermsg("����д�̶��绰��");return false;}
	if(linkmail==''){layermsg("����д���䣡");return false;}
}
function checkjob(id,type){
	if(id>0){
		$.post(weburl+"/index.php?m=ajax&c=wap_job",{id:id,type:type},function(data){
			if(type==1){
				$("#job1_son").html(data);
			}else{
				$("#job_post").html(data);
			}
		})
	}else{
		if(type==1){
			$("#job1_son").html('<option value="">��ѡ��</option>');
		}
		$("#job_post").html('<option value="">��ѡ��</option>');
	}
}
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
function checkfrom(){
	if($.trim($("#name").val())==""){
		layermsg("��Ƹ���Ʋ���Ϊ�գ�");
		return false;
	}
	if($.trim($("#job_post").val())==""){
		layermsg("��ѡ��ְλ���");
		return false;
	}
	if($.trim($("#cityid").val())==""){
		layermsg("��ѡ�����ص㣡");
		return false;
	}
	if($.trim($("#days").val())<1){
		layermsg("����ȷ��д��Ƹ������");
		return false;
	}
	if($.trim($("#description").val())==""){
		layermsg("ְλ��������Ϊ�գ�");
		return false;
	}
}