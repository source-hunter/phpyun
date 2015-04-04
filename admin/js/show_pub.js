function showdiv2(uid){ 
	$.layer({
		type: 2, 
		shadeClose: true,
		maxmin: false,
		title: '名企招聘', 
		shade: [0.1,'#fff'],
		border : [10 , 0.3 , '#000', true],
		offset: [($(window).height() - 500)/2 + 'px', ''],
		area: ['470px','500px'], 
		iframe: {src: 'index.php?m=admin_company&c=hotjobinfo&uid='+uid}
	}); 
} 
function showdiv3(id){  
	$.layer({
		type: 2,
		maxmin: false,
		shadeClose: true,
		title: '名企招聘', 
		shade: [0.1,'#fff'],
		offset: [($(window).height() - 500)/2 + 'px', ''],
		area: ['470px','500px'],
		iframe: {src: 'index.php?m=admin_company&c=hotjobinfo&id='+id}
	});  
}
function showdiv4(div,content,reply){ 
	$("#beizhu").html(content);
	$("#reply").html(reply);
	$.layer({
		type : 1,
		title :'回复评论', 
		offset: [($(window).height() - 210)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['410px','210px'],
		page : {dom :"#"+div}
	}); 
}
function showdiv5(class_name,orders,integral_buy,href,id,type){
	
	$("input[name='type']").attr("checked",false);
	$("#radio_"+type).attr("checked",true);
	if(type=='1'){
		$('.buy').show();
	}
	$("#id").val(id); 
	$("#class_name").val(class_name);
	$("#orders").val(orders);
	$("#integral_buy").val(integral_buy);
	$("#href").val(href);
	$(".admin_submit4").val("修改");   
	add_industry('0');
}
function showdiv6(div,key_name,type,color,size,bold,tuijian,num,id){
	$.post("index.php?m=admin_keyword&c=ajax",{type:type},function(date){
		$("#type").html(date);
	})
	if(bold=="1"){
		$("#bold").html("<input type=\"radio\" name=\"bold\" value=\"0\">&nbsp;否&nbsp;<input type=\"radio\" name=\"bold\" value=\"1\"checked>&nbsp;是");
	}
	if(tuijian=="1"){
		$("#tuijian").html("<input type=\"radio\" name=\"tuijian\" value=\"0\">&nbsp;否&nbsp;<input type=\"radio\" name=\"tuijian\" value=\"1\"checked>&nbsp;是");
	}
	$("#"+div).show('1000');
	$("#key_name").val(key_name);
	$("#color").val(color);
	$("#size").val(size);
	$("#num").val(num);
	$("#id").val(id);
	$("#bg").show('1000');
}
function showdiv7(div,title,type,content,reply,id){
	$("#"+div).show('1000');
	$("#title").html(title);
	$("#type").html(type);
	$("#content").html(content);
	$("#reply").html("<textarea rows=\"5\" cols=\"40\" name=\"reply\">"+reply+"</textarea>");
	$("#msgid").val(id);
	$("#bg").show('1000');
}
function guanbi(div){
	$("#"+div).hide('1000');
	$("#bg").hide('1000');
}
function guanbi_key(div){
	$.post("index.php?m=admin_keyword&c=ajax",{type:0},function(date){
		$("#type").html(date);
	})
	$("#bold").html("<input type=\"radio\" name=\"bold\" value=\"0\" checked>&nbsp;否&nbsp;<input type=\"radio\" name=\"bold\" value=\"1\">&nbsp;是");
	$("#tuijian").html("<input type=\"radio\" name=\"tuijian\" value=\"0\" checked>&nbsp;否&nbsp;<input type=\"radio\" name=\"tuijian\" value=\"1\">&nbsp;是");
	$("#key_name").val("");
	$("#color").val("");
	$("#size").val("");
	$("#num").val("");
	$("#"+div).hide('1000');
	$("#bg").hide('1000');
}