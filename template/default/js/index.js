//JS -- 前台首页

function show_job(id,showhtml){
	if(showhtml=="1"){
		$.post("index.php?m=ajax&c=show_leftjob",{},function(data){	
			$("#menuLst").html(data);	
			$(".lst"+id).attr("class","lst"+id+" hov");			
		});
	}else{
		var num=$(".lstCon").length/3;
		if(id<num){
			var height=id*35;
			var heightdiv=$(".lst"+id+" .lstCon").height();
			if(heightdiv-height<35){
				height=heightdiv=$(".lst"+id+" .lstCon").height()/2;
			}
			$(".lst"+id+" .lstCon").attr("style","top:-"+height+"px");
		}else if(id<num*2){
			var height=id*35;
			var heightdiv=$(".lst"+id+" .lstCon").height()/2;
			$(".lst"+id+" .lstCon").attr("style","top:-"+heightdiv+"px");
		}else{
			var height=($(".lstCon").length-id)*35;
			var heightdiv=$(".lst"+id+" .lstCon").height();
			if(heightdiv>height){
				heightdiv=heightdiv-height;
			}else{
				heightdiv=0;
			}
			$(".lst"+id+" .lstCon").attr("style","top:-"+heightdiv+"px");
		}
		$(".lst"+id).attr("class","lst"+id+" hov");	
	}
}
function selects(id,type,name){
	$("#job_"+type).hide();
	$("#"+type).val(name);
	$("#"+type+"id").val(id);
} 
function hide_job(id){
	$("#menuLst li").removeClass("hov"); 
}
function showDiv2(obj){
	if($(obj).attr("class")=="current1"){
		$(obj).removeClass();
	}
	else{
		$(obj).addClass("current1");
		$(obj).find(".shade").height($(obj).find(".area").height()+60)
	}
}
function clean(){
	$("#edu").val("请选择");
	$("#eduid").val("");
	$("#exp").val("请选择");
	$("#expid").val("");
	$("#mun").val("请选择")
	$("#munid").val("");;
	$("#salary").val("请选择");
	$("#salaryid").val("");
	$("#index_job_class_val").val("请选择职位类别");
	$("#job_class").val("");
	$("#city").val("请选择工作地点");
	$("#cityid").val("");
	$("#hy").val("请选择行业类别");
	$("#hyid").val("");
}
$(function(){
	$('body').click(function(evt) {
		if($(evt.target).parents("#job_hy").length==0 && evt.target.id != "hy") {
			$('#job_hy').hide();
		}
		if($(evt.target).parents("#job_exp").length==0 && evt.target.id != "exp") {
			$('#job_exp').hide();
		}
		if($(evt.target).parents("#job_edu").length==0 && evt.target.id != "edu") {
			$('#job_edu').hide();
		}
		if($(evt.target).parents("#job_salary").length==0 && evt.target.id != "salary") {
			$('#job_salary').hide();
		}
		if($(evt.target).parents("#job_mun").length==0 && evt.target.id != "mun") {
			$('#job_mun').hide();
		} 
	});  
	$.post(weburl+"/index.php?m=includejs&c=DefaultLoginIndex",{},function(data){
		$("#index_logoin").html(data);
	});
	
	/*首页右侧新闻、公告切换*/
	$(".yun_index_h1_list li").hover(function(){
		var num=$(this).index(); 
		$(".yun_index_h1_list li").removeClass("yun_index_h1_cur");
		$(this).addClass("yun_index_h1_cur");
		$(".yuin_index_r>.yun_index_cont").hide();
		$(".yun_index_cont:eq("+num+")").show(); 
	}); 
})

/*首页广告*/
$(document).ready(function(){
	$('#bottom_ad_is_show').val('1');
	var duilian = $("div.duilian");
	var duilian_close = $(".btn_close");
	var scroll_Top = $(window).scrollTop();
	var window_w = $(window).width();
	if(window_w>1000){duilian.show();}
	buttom_ad();
	$("div .duilian").css("top",scroll_Top+200);
	$(window).scroll(function(){
		buttom_ad();
		var scroll_Top = $(window).scrollTop();
		duilian.stop().animate({top:scroll_Top+200});
	});
	duilian_close.click(function(){
		$(this).parents('.duilian').hide();
		return false;
	});
});
function colse_bottom(){
	$("#bottom_ad_fl").parent().hide();
	$('#bottom_ad_is_show').val('0');
}
function buttom_ad(){
	if($("#bottom_ad").length>0&&$("#bottom_ad_is_show").length>0){
		var scrollTop = $(window).scrollTop();
		var w_height=$(document).height();
		var bottom_ad=$("#bottom_ad").offset().top;
		var bottom_ad_fl=$("#bottom_ad_fl").offset().top;
		var poor_height=parseInt(w_height)-parseInt(scrollTop);
		var bottom_ad_is_show=$('#bottom_ad_is_show').val();
		if(window.attachEvent){
			poor_height=parseInt(poor_height)-parseInt(22);
		}
		if(poor_height<=880){
			$("#bottom_ad_fl").parent().hide();
		}else if(bottom_ad_is_show=='1'){
			$("#bottom_ad_fl").parent().show();
		}
	}
}
/*首页广告结束*/