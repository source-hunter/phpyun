$(document).ready(function(){
	$('.delete').live('click',function(){
		var id = $(this).attr('data-id');
		var pid = $(this).attr('data-pid');
		if(parseInt(pid)>0)
		{
			unsel(id,pid);
		}else{
			unsel(id)	
		}
	});	
	$('.search_job_list').hover(function(){
		$(".search_job_list").removeClass("search_job_list_cur_line");
		$(this).addClass('search_job_list_cur_line');  
		$(".search_job_list_cur_line>.search_job_list_box").show();
	},function(){
		var ltype=$('#ltype').val();
		if(ltype==''){
			$(".search_job_list_cur_line>.search_job_list_box").hide();
			$(".search_job_list").removeClass("search_job_list_cur_line");}
		} 
	);

	$(".com_admin_ask").hover(function(){  
		layer.tips("加入搜索器，方便下次直接搜索，无需点击众多条件！", this, {
			guide: 1,
			style: ['background-color:#F26C4F; color:#fff;top:-7px', '#F26C4F']
		});
	},function(){layer.closeTips();});  
});
function check_select_show(id){
	$("#list"+id).show();
}
function check_onselect(id,val,name){
	$("#"+id).val(val);
	$("#list"+id).hide();
	$("#button"+id).val(name);
}
function addfinder(para,usertype){
	$.post(weburl+"/index.php?m=com&c=addfinder",{para:para,usertype:usertype},function(data){
		var data=eval('('+data+')');
		layer.msg(data.msg, Number(data.tm), Number(data.st));return false;		
	});
}
function index_job(job_class){
	var html = $("#jobdiv").html();
	if(html.replace(" ","")==''){
		var i=layer.load('加载中，请稍候...',0);
		$.post(weburl+"/index.php?m=ajax&c=show_jobsearch",{job_class:job_class},function(data){
			layer.close(i);
			$("#jobdiv").html(data);
			$.layer({
				type : 1,
				title : '职位类别',
				offset : ['20px' , '50%'],
				closeBtn : [0 , true],
				fix : false,
				border : [10 , 0.3 , '#000', true],
				move : false,
				area : ['960px','auto'],
				page : {dom :'#jobdiv'}
			}); 
		});
	}else{
		$("#jobdiv").html(html);
		$.layer({
			type : 1,
			title : '职位类别',
			offset : ['100px' , '50%'],
			closeBtn : [0 , true],
			fix : false,
			border : [10 , 0.3 , '#000', true],
			move : false,
			area : ['960px','auto'],
			page : {dom :'#jobdiv'}
		}); 
	}
}
function index_city(){
	var html = $("#citydiv").html();
	if(html.replace(" ","")==''){
		$.post(weburl+"/index.php?m=ajax&c=show_citysearch",{},function(data){
			$("#citydiv").html(data);
			$.layer({
				type : 1,
				title : '选择工作地区',
				offset : ['100px' , '50%'],
				closeBtn : [0 , true],
				fix : false,
				border : [10 , 0.3 , '#000', true],
				move : false,
				area : ['650px','auto'],
				page : {dom :'#citydiv'}
			}); 
		});
	}else{
		$("#citydiv").html(html);
		$.layer({
			type : 1,
			title : '选择工作地区',
			offset : ['100px' , '50%'],
			closeBtn : [0 , true],
			fix : false,
			border : [10 , 0.3 , '#000', true],
			move :false,
			area : ['650px','auto'],
			page : {dom :'#citydiv'}
		}); 
	}
}
function showcity(id){
	$("#td_city"+id).attr("class","focusItemTop mOutItem");
	$("#span_city"+id).hide();
	$("#div_city"+id).show();
}
function guanbicity(id){
   $("#td_city"+id).bind("mouseleave", function(){
	$("#td_city"+id).attr("class","blurItem");
	$("#span_city"+id).show();
	$("#div_city"+id).hide();
   });
}
function checkcity(id,name){
	$("#city").val(name);
	$("#cityid").val(id);
	 layer.closeAll();
}
function showjob(id){
	$("#td"+id).attr("class","focusItemTop mOutItem");
	$("#span"+id).hide();
	$("#div"+id).show();
}
function determine(id){
	var check_val,name_val;
	$(".selall").each(function(){
		var info =$(this).attr("data-val").split("+");
		check_val+=","+info[0];
		name_val+="+"+info[1];
	}); 
	
	if(check_val){ 
		  check_val = check_val.replace("undefined,","");
		  $("#job_class").val(check_val); 
	}  
	if(name_val==''||name_val==undefined){
		name_val=$("#index_job_class_val").attr('placeholder'); 
		 $("#job_class").val(''); 
	}
 	if(name_val){
		name_val = name_val.replace("undefined+","");  
  		$("#index_job_class_val").val(name_val);
	}
	layer.closeAll();
}
function check_all(id){
	if($("#all"+id).attr("checked")!="checked"){
		$(".label"+id).removeAttr("disabled");
		$(".label"+id).removeAttr("checked");
		unsel(id);
	}else{
		$("#all"+id).attr("checked","true");
		$(".label"+id).attr("disabled","disabled");
		$(".label"+id).attr("checked","true");
		addsel(id);
	}
}
function addsel(id,pid){
	//判断数量
	var i=0;
	$(".selall").each(function(){
		i++;
	});
	if(parseInt(pid)>0){		
		if(i>5){
			unsel(id,pid);
			layer.msg('您最多只能选择五项！', 2,2);
			return false;
		}else{
			var name = $('#job_class_'+id).attr('data-name');
			html = '<li class="job_class_'+id+' parent_'+pid+'"><a class="clean g3 selall" href="javascript:void(0);" data-val="'+id+'+'+name+'"><span class="text">'+name+'</span><span class="delete" data-id="'+id+'" data-pid ="'+pid+'">移除</span></a></li>';
			$('.job_class_'+id).remove();
			$('.selected').first().append(html);
		}
	}else{
		if(i>4){
			unsel(id);
			layer.msg('您最多只能选择五项！', 2,2);
			return false;
		}else{
			var name = $('#all'+id).attr('data-name');
			html = '<li class="all'+id+'"><a class="clean g3 selall" href="javascript:void(0);"  data-val="'+id+'+'+name+'"><span class="text">'+name+'</span><span class="delete" data-id="'+id+'">移除</span></a></li>';
			$('.parent_'+id).remove();
			$('.all'+id).remove();
			$('.selected').first().append(html);
		}
	}
}
function unsel(id,pid){
	if(parseInt(pid)>0){
		$('.job_class_'+id).remove();
		$('#job_class_'+id).removeAttr("checked","");
	}else{
		$('.all'+id).remove();
		$('#all'+id).removeAttr("checked","");
		$('.label'+id).removeAttr("disabled");
		$('.label'+id).removeAttr("checked");
	}
}
function closelayer(){
	layer.closeAll();
}
function guanbiselect(id){
   $("#td"+id).bind("mouseleave", function(){
		$("#td"+id).attr("class","blurItem");
		$("#span"+id).show();
		$("#div"+id).hide();
   });
}
function check_this(id){
	if($("#job_class_"+id).attr("disabled") != 'disabled'){
		if($("#job_class_"+id).attr("checked")!="checked"){
		 	var pid = $("#job_class_"+id).attr('data-pid');
			 $("#job_class_"+id).removeAttr("checked");
			 unsel(id,pid);
		}else{
			 var pid = $("#job_class_"+id).attr('data-pid');
			 $("#job_class_"+id).attr("checked","true");
			 addsel(id,pid);
		}
	}
}