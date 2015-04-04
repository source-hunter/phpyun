function checkinfo(){
	var name=$.trim($("#name").val());
	var hy=$.trim($("#hy").val());
	var pr=$.trim($("#pr").val());
	var cityid=$.trim($("#cityid").val());
	var address=$.trim($("#address").val());
	var mun=$.trim($("#mun").val());
	var linkphone=$.trim($("#linkphone").val());
	var linkmail=$.trim($("#linkmail").val());
	if(name==''){layermsg("请输入企业名称！");return false;}
	if(hy==''){layermsg("请选择企业行业！");return false;}
	if(pr==''){layermsg("请选择企业性质！");return false;}
	if(cityid==''){layermsg("请选择企业地址！");return false;}
	if(mun==''){layermsg("请选择企业规模！");return false;}
	if(address==''){layermsg("请填写公司地址！");return false;}
	if(linkphone==''){layermsg("请填写固定电话！");return false;}
	if(linkmail==''){layermsg("请填写邮箱！");return false;}
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
			$("#job1_son").html('<option value="">请选择</option>');
		}
		$("#job_post").html('<option value="">请选择</option>');
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
			$("#cityid").html('<option value="">请选择</option>');
		}
		$("#three_cityid").html('<option value="">请选择</option>');
	}
}
function checkfrom(){
	if($.trim($("#name").val())==""){
		layermsg("招聘名称不能为空！");
		return false;
	}
	if($.trim($("#job_post").val())==""){
		layermsg("请选择职位类别！");
		return false;
	}
	if($.trim($("#cityid").val())==""){
		layermsg("请选择工作地点！");
		return false;
	}
	if($.trim($("#days").val())<1){
		layermsg("请正确填写招聘天数！");
		return false;
	}
	if($.trim($("#description").val())==""){
		layermsg("职位描述不能为空！");
		return false;
	}
}