function loadlayer(){
	parent.layer.load('执行中，请稍候...',0);
}
function wait_result(){
	layer.closeAll();
	parent.layer.load('执行中，请稍候...',0);
} 
function toDate(str){
    var sd=str.split("-");
    return new Date(sd[0],sd[1],sd[2]);
}
function check_username(){
	var username=$.trim($("#username").val());
	var pytoken=$.trim($("#pytoken").val());
	if(username){
		$.post("index.php?m=admin_resume&c=check_username",{username:username,pytoken:pytoken},function(msg){
			if(msg){
				layer.tips('已存在该用户！',"#username" , {guide: 1,style: ['background-color:#F26C4F; color:#fff;top:-7px', '#F26C4F']});
				$("#username").attr("vtype",'1');
			}else if($("#username").attr('vtype')=='1'){layer.closeTips();$("#username").attr("vtype",'0');}
		});
	}
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
			parent.layer.msg(message, layer_time, Number(layer_st),function(){ location.reload();});
		}else if(url==''){
			parent.layer.msg(message, layer_time, Number(layer_st));
		}else{
			parent.layer.msg(message, layer_time, Number(layer_st),function(){location.href = url;});
		}
	}
}
function config_msg(data){
	$("body").append(data);
	var message = $("#layer_msg").val();
	var url=$("#layer_url").val();
	var layer_time=$("#layer_time").val();
	var layer_st=$("#layer_st").val();
	if(url=='1'){
		parent.layer.msg(message, layer_time, Number(layer_st),function(){
			location.reload();
		});
	}else if(url==''){
		parent.layer.msg(message, layer_time, Number(layer_st));
	}else{
		parent.layer.msg(message, layer_time, Number(layer_st),function(){
			top.location.href =url;
		});
	}return false;
}
function resetpw(uname,uid){
	var pytoken = $('#pytoken').val();
	parent.layer.confirm("确定要重置密码吗？",function(){
		$.get("index.php?m=user_member&c=reset_pw&uid="+uid+"&pytoken="+pytoken,function(data){
			parent.layer.closeAll();
			parent.layer.alert("用户："+uname+" 密码已经重置为123456！", 9);return false;
		});
	});
}
function really(name){
	var chk_value =[];
	$('input[name="'+name+'"]:checked').each(function(){
		chk_value.push($(this).val());
	});
	if(chk_value.length==0){
		parent.layer.msg("请选择要删除的数据！",2,8);return false;
	}else{
		parent.layer.confirm("确定删除吗？",function(){
 			setTimeout(function(){$('#myform').submit()},0);
		});
	}
}
function layer_logout(url){
		$.get(url,function(data){
			var data=eval('('+data+')');
			if(data.url=='1'){
				parent.layer.msg(data.msg, Number(data.tm), Number(data.st),function(){top.location.reload();});return false;
			}else{
				parent.layer.msg(data.msg, Number(data.tm), Number(data.st),function(){top.location.href=data.url;});return false;
			}
		});
}
function layer_del(msg,url){
	if(msg==''){
		loadlayer();
		$.get(url,function(data){
			var data=eval('('+data+')');
			if(data.url=='1'){
				parent.layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.reload();});return false;
			}else{
				parent.layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.href=data.url;});return false;
			}
		});
	}else{
		var pytoken = $('#pytoken').val();
		parent.layer.confirm(msg, function(){
			loadlayer();
			$.get(url+'&pytoken='+pytoken,function(data){
				var data=eval('('+data+')');
				if(data.url=='1'){
					parent.layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.reload();});return false;
				}else{
					parent.layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.href=data.url;});return false;
				}
			});
		});
	}
}
function unselectall(){
	if(document.getElementById('chkAll').checked){
		document.getElementById('chkAll').checked = document.getElementById('chkAll').checked&0;
	}
	if(document.getElementById('chkAll2').checked){
		document.getElementById('chkAll2').checked = document.getElementById('chkAll2').checked&0;
	}
	getbg();
}
function CheckAll(form){
	for (var i=0;i<form.elements.length;i++){
		var e = form.elements[i];
		if (e.Name != 'chkAll'&&e.disabled==false){
			e.checked = form.chkAll.checked;
		}
	}
	getbg();
}
function CheckAll2(form){
	for (var i=0;i<form.elements.length;i++){
		var e = form.elements[i];
		if (e.Name != 'chkAll2'&&e.disabled==false){
			e.checked = form.chkAll2.checked;
		}
	}
	getbg();
}
function getbg(){
	$("tr").attr("style","");
	var id;
	$("input[type=checkbox]:checked").each(function(){
		id=$(this).val();
		$("#list"+id).attr("style","background:#d0e3ef;");
	});
}
function check_domain(){
	var domain;
	var name="";
	var id="";
	$('input[class="domain"]:checked').each(function(){
		domain=$(this).val();
		domain=domain.split("+");
		if(domain[0]==0){
			$("#domain_name").val('全站');
			$("#domainid").val('0');
			$("#domain_list").hide();
			return false;
		}
		if(id==""){
			id=domain[0];
		}else{
			id=id+","+domain[0];
		}
		if(name==""){
			name=domain[1];
		}else{
			name=name+"+"+domain[1];
		}
		$("#domain_name").val(name);
		$("#domainid").val(id);
		$("#domain_list").hide();
	});
}
function Close(id){
	$("#"+id).hide();
}

$(document).ready(function(){
	$("#domain_name").click(function(){
		$("#domain_list").show();
	})
	$(".admin_Operating_c").hover(function(){
		var aid=$(this).attr("aid");
		$("#list"+aid).show();
		$("#list_"+aid).attr("class","admin_Operating_c admin_Operating_hover");
		goTopEx("list"+aid);
	},function(){
		var aid=$(this).attr("aid");
		$("#list"+aid).hide();
		$("#list_"+aid).attr("class","admin_Operating_c");
		goTopEx("list"+aid);
	});
	$("input[type='submit']").click(function(){
		var type=$("form[name='myform']").attr('target'); 
		if(type=='supportiframe'){
			wait_result();
		} 
	}); 
	
	$(".formselect").hover(function(){
		var did=$(this).attr("did");
		$("#"+did).show();
	},function(){
		var did=$(this).attr("did");
		$("#"+did).hide();

	}); 
	
	$(".admin_Prompt_close").click(function(){
		$(".admin_Prompt").hide();
	});
	
	/*高级搜索滑动效果*/ 
	if($(".admin_Filter").length > 0){ 
		var height=$(".admin_adv_search_box").height();  
		var admin_Filter=$(".admin_Filter").offset().top; 
		height=Math.abs(parseInt(height)-parseInt(admin_Filter));	 
		$(".admin_adv_search_box").css('top','-'+height+'px');
		$(".admin_search_div,.admin_adv_search_box").hover(function(){
			var top=parseInt(35)+parseInt(admin_Filter);
			$(".admin_search_div .admin_adv_search_bth").addClass('admin_adv_search_bth_hover'); 
			$(".admin_adv_search_box").stop().animate({top:top+'px'});
		},function(){     
			$(".admin_adv_search_box").stop().animate({top:'-'+height+'px'});
			$(".admin_search_div .admin_adv_search_bth").removeClass('admin_adv_search_bth_hover');		
		});
	};
	/*高级搜索结束*/
}) 

function formselect(val,id,name){ 
	$("#b"+id).val(name);
	$("#"+id).val(val);
	$("#d"+id).hide();
}
function goTopEx(id){
	var top=document.getElementById(id).getBoundingClientRect().top;
	var height=$(window).height();
	var height=height-5;
	$(".infoboxp").attr("style","min-height:"+height+"px;");
	var ttop=height-top;
	if(ttop<80){
		$("#"+id).attr("class","admin_Operating_list admin_Operating_up");
	}else{
		$("#"+id).attr("class","admin_Operating_list admin_Operating_down");
	}
}
function add_class(name,width,height,divid,url){
	if(url){$(divid).append("<input id='surl' value='"+url+"' type='hidden'/>");}
	$.layer({
		type : 1,
		title : name,
		offset: [($(window).height() - height)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : [width+'px',height+'px'],
		page : {dom :divid}
	});
}
function status_div(name,width,height){
	$.layer({
		type : 1,
		title :name,
		offset: [($(window).height() - height)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : [width+'px',height+'px'],
		page : {dom :"#status_div"}
	});
}
function copy_url(name,url){
	$("#copy_url").val(url);
	$.layer({
		type : 1,
		title : name,
		offset: [($(window).height() - 110)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['300px','110px'],
		page : {dom :'#wname'}
	});
}
function copy_adclass(name,url){
	$("#copy_url").val(url);
	$.layer({
		type : 1,
		title : name,
		offset: [($(window).height() - 110)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['400px','150px'],
		page : {dom :'#wname'}
	});
}
function adminmap(){
	$.layer({
		type : 2,
		title : '后台地图',
		offset: [($(window).height() - 500)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['700px','500px'],
		iframe: {src: 'index.php?c=map'}
	});
}
function rec_up(url,id,rec,type){
	//parent.layer.confirm("确定执行操作？",function(){
		var pytoken=$("#pytoken").val();
		$.get(url+"&id="+id+"&rec="+rec+"&type="+type+"&pytoken="+pytoken,function(data){
		//	parent.layer.closeAll();
			if(data==1){
			//	parent.layer.msg("操作成功！",2,9);
				if(rec=="1"){
					$("#"+type+id).html("<a href=\"javascript:void(0);\" onClick=\"rec_up('"+url+"','"+id+"','0','"+type+"');\"><img src=\"../data/ajax_img/doneico.gif\"></a>");
				}else{
					$("#"+type+id).html("<a href=\"javascript:void(0);\" onClick=\"rec_up('"+url+"','"+id+"','1','"+type+"');\"><img src=\"../data/ajax_img/errorico.gif\"></a>");
				}
			//}else{
			//	parent.layer.msg("操作失败！",2,8,function(){location.reload();});return false;
			}
		});
//	});

}
function rec_news (url,id,rec,type){
		var pytoken=$("#pytoken").val();
		$.get(url+"&id="+id+"&rec_news="+rec+"&type="+type+"&pytoken="+pytoken,function(data){
			if(data==1){
				if(rec=="1"){
					$("#"+type+id).html("<a href=\"javascript:void(0);\" onClick=\"rec_news('"+url+"','"+id+"','0','"+type+"');\"><img src=\"../data/ajax_img/doneico.gif\"></a>");
				}else{
					$("#"+type+id).html("<a href=\"javascript:void(0);\" onClick=\"rec_news('"+url+"','"+id+"','1','"+type+"');\"><img src=\"../data/ajax_img/errorico.gif\"></a>");
				}
			}
		});
}
function appendData(frame_id){
	var message = $(window.frames[frame_id].document).find("#layer_msg").html();
	$("#jobsynch").before(message);
	$("#viewMore").parent().parent().parent().find("tr:gt(10)").hide();
	$("#viewMore").parent().parent().show();
	$("#viewMore").click(function(){
		if($(this).html()=="查看详细"){
			$("#viewMore").parent().parent().parent().find("tr:gt(10)").show();
			$(this).html("收起详细");
		}
		else{
			$("#viewMore").parent().parent().parent().find("tr:gt(10)").hide();
			$(this).html("查看详细");
		}
	});
	$("#jobsynchFrom").show();
}