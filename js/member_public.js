var timestamp=Math.round(new Date().getTime()/1000) ;
/*验证码看不清*/
function check_code(){
	document.getElementById("vcode_img").src="include/authcode.inc.php?"+Math.random();
} 
function loadlayer(){
	layer.load('执行中，请稍候...',0);
}
function wait_result(){
	layer.closeAll();
	layer.load('执行中，请稍候...',0);
}
function addblack(){
	$.layer({
		type : 1,
		title : '搜索企业',
		closeBtn : [0 , true],
		offset: [($(window).height() - 320)/2 + 'px', ''],
		border : [10 , 0.3 , '#000', true],
		area : ['400px','320px'],
		page : {dom : '#blackdiv'}
	});
}
function quxiaoshenqin(id){
	$("#qsid").val(id);
	$.layer({
		type : 1,
		title : '填写取消原因',
		closeBtn : [0 , true],
		offset: [($(window).height() - 320)/2 + 'px', ''],
		border : [10 , 0.3 , '#000', true],
		area : ['300px','180px'],
		page : {dom : '#blackdiv'}
	});
}
function searchcom(){
	var name=$.trim($("#name").val());
	if(name==''){
		layer.closeAll();
		layer.msg('请输入搜索的公司名称！', 2, 8,function(){addblack();});return false;
	}
	$.post("index.php?c=blacklist&act=searchcom",{name:name},function(data){
		$(".Blacklist_box>form>ul").html(data);		
	});
}
function ckaddblack(){
	var chk_value=[];
	$('input[name="buid[]"]:checked').each(function(){
		chk_value.push($(this).val());
	});
	layer.closeAll();
	if(chk_value.length==0){ 
		layer.msg("请选择要屏蔽的公司！",2,8,function(){addblack()});return false;
	} 
	layer.load('执行中，请稍候...',0);
} 
function addfriend(id,type){
	$.post(weburl+"/index.php?m=ajax&c=makefriends",{id:id,type:type},function(data){
		if(data=="5"){
			layer.alert('请先登录！', 0, '提示',function(){window.location.href ="index.php?m=login&usertype=1"; window.event.returnValue = false;return false;});
		}else if(data=="4"){
			layer.msg('不能添加自己为好友！', 2, 0);return false;  
		}else if(data=="3"){ 
			layer.msg('您未通过身份验证不能添加好友！', 2, 8);return false;  
		}else if(data=="2"){ 
			layer.msg('对方未通过身份审核，不能加其为好友！', 2, 8);return false;  
		}else if(data=="1"){ 
			layer.msg('已提交好友申请，等待对方同意！', 2, 1);return false; 
		}else if(data=="6"){ 
			layer.msg('你们已经好友！', 2, 9);return false; 
		}else if(data=="7"){ 
			layer.msg('已提交好友申请，请耐心等待！', 2, 1);return false;
		}
	});
} 
function buyad(){
	if($.trim($('#ad_name').val())==''){
		layer.msg('请输入广告名称！', 2, 2);return false;
	}
	if($.trim($('#pic_url').val())==''){
		layer.msg('请选择广告图片！', 2, 2);return false;
	}
	if($.trim($('#pic_src').val())==''){
		layer.msg('请输入广告链接！', 2, 2);return false;
	}
	if($.trim($('#buy_time').val())==''){
		layer.msg('请输入购买时间！', 2, 2);return false;
	}
	buy_vip_ad();
}
function buy_vip_ad(){
	var value=$("input[name=buytype]:checked").val();
	var service_price=$("input[name=service_price]").val();
	var integral_buy=$("input[name=integral_buy]").val();
	
	var unit_buy=$("input[name=unit_buy]").val();

	var name="",price;
	if(value==1){
		var coupon=$("input[name=coupon]:checked").attr("date");
		name=parseInt(service_price)-parseInt(coupon);
		price=parseInt(service_price);
		if(coupon>0){
			var msg="购买此项服使用优惠券，还需扣除"+name+"元，是否继续？";
		}else{
			var msg="购买此项服将扣除"+price+"元，是否继续？";
		}
	}else{
		name=integral_buy+unit_buy;
		var msg="购买此项服将扣除"+name+"，是否继续？";
	}  
	layer.confirm(msg,function(){ 
		setTimeout(function(){$('#myform').submit()},0);
	});
}
$(document).ready(function(){
	$("#dingdan_submit").click(function(){
		var paytype=$("input[name=p1]:checked").val();
		var order=$("input[name=dingdan]").val();
		$.post(weburl+"/index.php?m=ajax&c=order_type",{order:order,paytype:paytype},function(data){return false;})
	})
	$("input[name=default_resume],.default_resume").click(function(){
		var value=$(this).val();
		if(value==''){value=$(this).attr('value');} 
		$.post(weburl+"/index.php?m=ajax&c=default_resume",{eid:value},function(data){
			if(data==0){
				layer.alert('请先登录！', 0, '提示',function(){window.location.href ="index.php?m=login&usertype=1";window.event.returnValue = false;return false;});
			}else if(data==1){ 
				layer.msg('设置成功！', 2, 9,function(){ location.reload();});return false; 
			}else{ 
				layer.msg('系统出错，请稍后再试！', 2, 8,function(){ location.reload();});return false; 
			}
		}) 
	}) 
	$("input[name=buytype]").click(function(){
		var value=$("input[name=buytype]:checked").val();
		if(value==1){
			$("#span_service_price").show();//人民币购买
			$("#span_integral_buy").hide();	
			$("#span_yh_price").show();//人民币购买
			$("#span_yh_integral").hide();	
			$("#coupon_buy").show();	
		}else{
			$("#span_integral_buy").show();//积分购买
			$("#span_service_price").hide(); 
			$("#span_yh_integral").show();//人民币购买
			$("#span_yh_price").hide();	
			$("#coupon_buy").hide();
		}
	})
	$(".seemsg").click(function(){
		var id=$(this).attr("id");
		$.post("index.php?c=up_msg",{id:id},function(msg){
			if(msg==1){
				$(".msgcontent").hide();
				$("#msg"+id).show();
			}else{
				layer.msg('非法操作！', 2, 0);return false; 
			}
		});
	})
	 
	$("#colse_box").click(function(){$('.job_box').hide();})
	$("#price_int").blur(function(){
		var value=$(this).val();
		var proportion=$(this).attr("int");
		$("#com_vip_price").val(value/proportion);
		$("#span_com_vip_price").html(value/proportion);
	})
	$("#comvip").change(function(){
		var value=$("#comvip option:selected").attr("price");
		if(value!=""){
			$("#com_vip_price").val(value);
			$("#span_com_vip_price").html(value);
		}else{
			$("#com_vip_price").val('0');
			$("#span_com_vip_price").html('0');
		}
	})
	$(".province").change(function(){
		var province=$(this).val();
		var lid=$(this).attr("lid");
		if(province==""){
			$("#"+lid+" option").remove()
			$("<option value='0'>请选择城市</option>").appendTo("#"+lid);
			lid2=$("#"+lid).attr("lid");
			if(lid2){
				$("#"+lid2+" option").remove();
				$("<option value='0'>请选择城市</option>").appendTo("#"+lid2);
				$("#"+lid2).hide();
			}
		}
		 
		$.post(weburl+"/index.php?m=ajax&c=ajax&"+timestamp, {"str":province},function(data) {  
			if(lid!="" && data!=""){
				$('#'+lid+' option').remove();
				$(data).appendTo("#"+lid);
				city_type(lid); 
			}
		})
	})
	$(".job1").change(function(){
		var province=$(this).val();
		var lid=$(this).attr("lid");
		$.post(weburl+"/index.php?m=ajax&c=ajax_job&"+timestamp, {"str":province},function(data) {
			if(lid!="" && data!=""){
				$('#'+lid+' option').remove();
				$(data).appendTo("#"+lid);
				job_type(lid);
			}
		})
	})
	$(".jobone").change(function(){
		var province=$(this).val();
		var lid=$(this).attr("lid");
		$.post(weburl+"/index.php?m=ajax&c=ajax_ltjob&"+timestamp, {"str":province},function(data) {
			if(lid!="" && data!=""){
				$('#'+lid+' option').remove();
				$(data).appendTo("#"+lid);
			}
		})
	})
	$("#details-ul li").click(function(){
		$("#details-ul li").attr("class","");
		$(this).attr("class","hover");
		$(".xinxi-guanli-box").hide();
		var name=$(this).attr("name");
		$("#details-con-"+name).show();
	})
})
 
function city_type(id){
	var id;
	var province=$("#"+id).val();
	var lid=$("#"+id).attr("lid");
	$.post(weburl+"/index.php?m=ajax&c=ajax&"+timestamp, {"str":province},function(data) {
		if(lid!=""){
			if(lid!="three_cityid" && lid!="three_city" && data!=""){
				$('#'+lid+' option').remove();
				$(data).appendTo("#"+lid);
			}else{
				if(data!=""){
					$('#'+lid+' option').remove();
					$(data).appendTo("#"+lid);
					$('#'+lid).show();
				}else{
					$('#'+lid+' option').remove();
					$("<option value='0'>请选择城市</option").appendTo("#"+lid);
					$('#'+lid).hide();
				}
			}
		}
	})
}
function job_type(id){
	var id;
	var province=$("#"+id).val();
	var lid=$("#"+id).attr("lid");
	$.post(weburl+"/index.php?m=ajax&c=ajax_job&"+timestamp, {"str":province},function(data) {
		if(lid!="" && data!=""){
			$('#'+lid+' option').remove();
			$(data).appendTo("#"+lid);
		}
	})
}
function check_form(ifidname,byidname){
	var ifidname;
	var byidname;
	if (ifidname){ 
		var msg=$("#"+byidname).html(); 
		layer.msg(msg, 2, 8);return false;
	}else{
		$("#"+byidname).hide(); 
		return true;
	}
}

function check_email(strEmail) {
	 var emailReg = /^([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9\-]+@([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	 if (emailReg.test(strEmail))
	 return true;
	 else
	 return false;
 }
function isjsMobile(obj){
	if(obj.length!=11) return false;
	else if(obj.substring(0,2)!="13" && obj.substring(0,2)!="14" && obj.substring(0,2)!="15" && obj.substring(0,2)!="18") return false;
	else if(isNaN(obj)) return false;
	else  return true;
}

function isIdCardNo(idcard) {
        var Errors=new Array( "0,验证通过!", "1,身份证号码位数不对!", "2,身份证号码出生日期超出范围或含有非法字符!", "3,身份证号码校验错误!", "4,身份证地区非法!");
        var area={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"}
        var idcard,Y,JYM;
        var S,M;
        var idcard_array = new Array();
        idcard_array = idcard.split("");
        if(area[parseInt(idcard.substr(0,2))]==null) return false;
        switch(idcard.length){
              case 18:{
                  if( parseInt(idcard.substr(6,4)) % 4 == 0 || (parseInt(idcard.substr(6,4)) % 100 == 0 && parseInt(idcard.substr(6,4))%4 == 0 )){
                       ereg=/^[1-9][0-9]{5}(19|20|21|22)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;
                  } else {
                        ereg=/^[1-9][0-9]{5}(19|20|21|22)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;
                  }
                  if(ereg.test(idcard)){
                        S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7
                        + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9
                        + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10
                        + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5
                        + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8
                        + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4
                        + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2
                        + parseInt(idcard_array[7]) * 1
                        + parseInt(idcard_array[8]) * 6
                        + parseInt(idcard_array[9]) * 3 ;
                        Y = S % 11;
                        M = "F";
                        JYM = "10X98765432";
                        M = JYM.substr(Y,1);
                        if(M == idcard_array[17]) return true;
                        else return false;
                  }else return false;
                  break;
              }
              default:{
                    return false;
                    break;
              }
        }
		return false;
    }
function checkDate(date){return true;}
$(document).ready(function(){
	$("#wysc").click(function(){
		$("#uploadname").val('');
		$("#upload_img").css("top","10%");
		$("#upload_img").show();
		$("#bg").show();
	})
	$("#qd").click(function(){
		var name=$("#uploadname").val();
		if(name==""){$("#close").click();return false;}
		var namearr=name.split("###");
		var i,upload="";
		for(i=0;i<namearr.length;i++){
			//alert(namearr[i]);
			var num=i+1;
			upload+='<dd style="height:auto;" id="list'+i+'"><div style="float:left;"><img src="..'+namearr[i]+'" width="100" height="100"/></div><div style="float:left; margin-left:10px; line-height:30px;"><div><input class="imgshow" type="hidden" value="'+namearr[i]+'" />名称：<input class="titleshow" type="text" size="28" maxlength="10" value="环境展示'+num+'" /></div><div style="text-align:left;"><a href="javascript:del_upload(\''+namearr[i]+'\',\''+i+'\');">取消删除</a></div></div><div style="clear:both; height:5px;"></div></dd>';
		}
		$("#trlistone dl").html(upload);
		$("#trlistone").show();
		$("#trlisttwo").hide();
		$("#close").click();
	})
	$("#close").click(function(){
		$("#upload_img").hide();
		$("#bg").hide();
	})
})
function del_upload(dir,list){
	$.post(weburl+"/index.php?m=ajax&c=delupload&"+timestamp, {"str[]":[dir]},function(data) {
		if(data){
			$("#list"+list).remove();
			var upload=$("#trlistone dl").html();
			if(upload==""){
				$("#trlistone").hide();
				$("#trlisttwo").show();
			}
		}
	})
}

function checkshare(){
	var re = /^-?[0-9]*(\.\d*)?$|^-?d^(\.\d*)?$/;
	var smallday = $.trim($("#smallday").val());
	if(smallday!=""){
		if (!re.test(smallday)){
			layer.msg('购买天数填写不正确！', 2, 8);return false;  
		}
	}else{
		layer.msg('购买天数不能为空！', 2, 8);return false;   
	}
	return true;
}
 
$(function(){
	$(".zphstatus").click(function(){
		var zid=$(this).attr("zid");
		var pid=$(this).attr("pid");
		$.get(weburl+"/index.php?m=ajax&c=getzphcom&jobid="+pid+"&zid="+zid, function(data){
		    var data=eval('('+data+')'); 
			$("#title").html(data.title); 
			$("#stime").html(data.starttime); 
			$("#etime").html(data.endtime); 
			$("#address").html(data.address); 
			$("#cname").html(data.content); 
			$.layer({
				type : 1,
				title :'我的职位', 
				offset: [($(window).height() - 380)/2 + 'px', ''],
				closeBtn : [0 , true],
				border : [10 , 0.3 , '#000', true],
				area : ['380px','auto'],
				page : {dom :"#infobox"}
			}); 
		});
	}); 
	
}); 
//全选
function m_checkAll(form){
	for (var i=0;i<form.elements.length;i++){
		var e = form.elements[i];
		if (e.Name != 'checkAll'&&e.disabled==false)
		e.checked = form.checkAll.checked; 
	}
} 
function really(name){
	var chk_value =[];    
	$('input[name="'+name+'"]:checked').each(function(){    
		chk_value.push($(this).val());   
	});   
	if(chk_value.length==0){
		layer.msg("请选择要删除的数据！",2,8);return false;
	}else{
		layer.confirm("确定删除吗？",function(){
			setTimeout(function(){$('#myform').submit()},0); 
		});
	} 
} 
function CheckForm(){
	var chk_value =[];    
	$('input[name="usertype"]:checked').each(function(){    
		chk_value.push($(this).val());   
	});
	if(chk_value.length==0){
		layer.msg("请选择购买类型！",2,8);return false;
	}
}
function pay_form(name){
	if($("#comvip").length!=0&&$("#comvip").val()==''){ 
		layer.msg("请选择购买类型！",2,8);return false;
	}
	if($("#price_int").length!=0&&$("#price_int").val()<1){ 
		layer.msg(name,2,8);return false; 
	} 
}
function Showsub1(){
	var oldpass = $("#oldpassword").val();
	var pass = $("#password").val();
	var repass = $("#repassword").val();
	oldpass=$.trim(oldpass);
	pass=$.trim(pass);
	repass=$.trim(repass);
	var flag = true;
	if(oldpass==""){
		$("#msg_oldpassword").html("<font color='red'>原始密码不能为空!</font>");
		flag = false;
	} else if(oldpass.length<6 || oldpass.length>20){
		$("#msg_oldpassword").html("<font color='red'>密码需在 6-20个字符之内!</font>");
		flag = false;
	} else{
		$("#msg_oldpassword").html("<font color='#008000'>输入成功!</font>");
	}
	if(pass==""){
		$("#msg_password").html("<font color='red'>新密码不能为空!</font>");
		flag = false;
	}else if(pass.length<6 || pass.length>20){
		$("#msg_password").html("<font color='red'>新密码需在 6-20个字符之内!</font>");
		flag = false;
	}else{
		$("#msg_password").html("<font color='#008000'>输入成功!</font>");
	}
	if(repass==""){
		$("#msg_repassword").html("<font color='red'>请再次确认新密码!</font>");
		flag = false;
	}else if(repass.length<6 || repass.length>20){
		$("#msg_repassword").html("<font color='red'>新密码需在 6-20个字符之内!</font>");
		flag = false;
	} if(repass!=pass){
		$("#msg_repassword").html("<font color='red'>两次密码输入不一致，请重新输入!</font>");
		flag = false;
	}else if(repass==pass && repass!=""){
		$("#msg_repassword").html("<font color='#008000'>输入成功!</font>");
	}
	return flag;
}


function reply_xin(id,name,content){
	$("#fid").val(id);
	$("#wnc").html("<div class='Reply_cont_name'><font color='#0066FF'>"+name+"</font> 给您留言：</div>"+content);
	//$("#reply").show();
	$.layer({
		type : 1,
		title : '回复',
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['450px','auto'],
		page : {dom : '#reply'}
	});
} 
function check_xin(){
	if($("#content").val()==""){
		layer.msg('回复内容不能为空！', 2, 2);return false; 
	}	
}
function Showsub(){ 
	 var title = $("#title").val();
	 var con = $("#content").val();
 	 con=$.trim(con);
	 if(title==""){layer.msg('请填写留言标题！', 2, 2);return false;}
	 if(con==""){layer.msg('请填写留言内容！', 2, 2);return false;}			
}
function zhankaiShouqi(control){
	$(control).parent().find('.job_add_y_list:gt(3)').slideToggle();
	if($(control).html()=='更多'){
		$(control).html('收起');
	}else{
		$(control).html('更多');
	}
}

$(function(){
  $('body').click(function(evt) {
	if($(evt.target).parents("#job_hy").length==0 && evt.target.id != "hy") {
	   $('#job_hy').hide();
	}
	if($(evt.target).parents("#job_qyhy").length==0 && evt.target.id != "qyhy") {
	   $('#job_qyhy').hide();
	}
	if($(evt.target).parents("#job_pr").length==0 && evt.target.id != "pr") {
	   $('#job_pr').hide();
	}
	if($(evt.target).parents("#job_lt_salary").length==0 && evt.target.id !="lt_salary"){
	    $('#job_lt_salary').hide();
	}
	if($(evt.target).parents("#job_lt_full").length==0 && evt.target.id !="lt_full"){
	    $('#job_lt_full').hide();
	}	
	if($(evt.target).parents("#job_salary").length==0 && evt.target.id != "salary") {
	   $('#job_salary').hide();
	}
	if($(evt.target).parents("#job_report").length==0 && evt.target.id != "report") {
	   $('#job_report').hide();
	}	
	if($(evt.target).parents("#job_province").length==0 && evt.target.id != "province") {
	   $('#job_province').hide();
	}	
	if($(evt.target).parents("#job_twocity").length==0 && evt.target.id != "twocity") {
	   $('#job_twocity').hide();
	}	
	if($(evt.target).parents("#job_threecity").length==0 && evt.target.id != "threecity") {
	   $('#job_threecity').hide();
	}	
	if($(evt.target).parents("#job_skillc").length==0 && evt.target.id != "skillc") {
	   $('#job_skillc').hide();
	}
	if($(evt.target).parents("#job_level").length==0 && evt.target.id != "level") {
	   $('#job_level').hide();
	}	
	if($(evt.target).parents("#job_marriage").length==0 && evt.target.id != "marriage") {
	   $('#job_marriage').hide();
	}
	if($(evt.target).parents("#job_educ").length==0 && evt.target.id != "educ") {
	   $('#job_educ').hide();
	}
	if($(evt.target).parents("#job_edu").length==0 && evt.target.id != "edu") {
	   $('#job_edu').hide();
	}	
	if($(evt.target).parents("#job_type").length==0 && evt.target.id != "type") {
	   $("#job_type").hide();
	}
	if($(evt.target).parents("#job_edu").length==0 && evt.target.id != "edu") {
	   $('#job_edu').hide();
	}
	if($(evt.target).parents("#job_mun").length==0 && evt.target.id != "mun") {
	   $("#job_mun").hide();
	}
	if($(evt.target).parents("#job_exp").length==0 && evt.target.id != "exp") {
	   $("#job_exp").hide();
	}
	if($(evt.target).parents("#job_qypr").length==0 && evt.target.id != "qypr") {
	   $("#job_qypr").hide();
	}	
	if($(evt.target).parents("#job_mun").length==0 && evt.target.id != "mun") {
	   $("#job_mun").hide();
	}	
	if($(evt.target).parents("#job_qyprovince").length==0 && evt.target.id != "qyprovince") {
	   $("#job_qyprovince").hide();
	}
	if($(evt.target).parents("#job_ltage").length==0 && evt.target.id != "ltage") {
	   $("#job_ltage").hide();
	}
	if($(evt.target).parents("#job_ltsex").length==0 && evt.target.id != "ltsex") {
	   $("#job_ltsex").hide();
	}
	if($(evt.target).parents("#job_type1").length==0 && evt.target.id != "jobone_name") {
	   $("#job_type1").hide();
	}

	if($(evt.target).parents("#job_ltexp").length==0 && evt.target.id != "ltexp") {
	   $("#job_ltexp").hide();
	}
	if($(evt.target).parents("#job_citys").length==0 && evt.target.id != "citys") {
	   $("#job_citys").hide();
	}	
	if($(evt.target).parents("#job_three_city").length==0 && evt.target.id != "three_city") {
	   $("#job_three_city").hide();
	}	
	if($(evt.target).parents("#job_ltedu").length==0 && evt.target.id != "ltedu") {
	   $("#job_ltedu").hide();
	} 
	if($(evt.target).parents("#job_basic_info").length==0 && evt.target.id != "basic_info") {
	   $("#job_basic_info").hide();
	} 
	if($(evt.target).parents("#job_job1").length==0 && evt.target.id != "job1") {
	   $("#job_job1").hide();
	}
	if($(evt.target).parents("#job_job1_son").length==0 && evt.target.id != "job1_son") {
	   $("#job_job1_son").hide();
	}
	if($(evt.target).parents("#job_job_post").length==0 && evt.target.id != "job_post") {
	   $("#job_job_post").hide();
	} 
	if($(evt.target).parents("#job_number").length==0 && evt.target.id !="number"){
	    $('#job_number').hide();
	}
    if($(evt.target).parents("#job_age").length==0 && evt.target.id !="age"){
	    $('#job_age').hide();
	}	
	if($(evt.target).parents("#job_sex").length==0 && evt.target.id !="sex"){
	    $("#job_sex").hide();
	}
   });
})
function selects(id,type,name){
	$("#job_"+type).hide();
	$("#"+type).val(name);
	$("#"+type+"id").val(id);
} 


function select_city(id,type,name,gettype,ptype){
	$("#job_"+type).hide();
	$("#"+type).val(name);
	$("#"+type+"id").val(id);
	if(type=='qyprovince'){
		$("#citysid").val('');
	} 
	if(type=='province'){
		$("#citys").val('请选择城市');
		$("#three_city").val('请选择城市');
		$("#citysid").val('');
		$("#three_cityid").val('');
	} 
	if(type=='job1'){
		$("#job1_son").val('请选择职位');
		$("#job1_sonid").val('');
		$("#job_post").val('请选择职位'); 
		$("#job_postid").val('');
	}
	$.post(weburl+"/index.php?m=ajax&c=ajax_city",{id:id,gettype:gettype,ptype:ptype},function(data){
		if(ptype=='job'){ 
			$("#job_"+gettype).html(data);
			if(gettype=="job1_son"){
				if(data==""){
					$("#job_"+gettype).hide();
				}else{
					$("#job_"+gettype).show();
				}
			}else if(gettype=="job_post"){
				$("#job_post").parents().show();
				$("#job_"+gettype).show();
			}
		}else{
			if(gettype=="citys"){$("#"+gettype).val("请选择城市");} 
			$("#job_"+gettype).html(data);
			if(type=='citys'){
				$("#cityshowth").show();
			}
		} 
	})
}  
function selectjobone(id,type,name,gettype){
	$("#"+type).val(id);
	$("#"+type+"_name").val(name);
	$("#jobtwo").val("");
	$("#jobtwo_name").val("请选择");
	$.post(weburl+"/index.php?m=ajax&c=ajax_ltjobone&"+timestamp, {"str":id},function(data) {
		if(data!=""){
			$('#job_type2').find("ul").html(data); 
		}
	});
	$("#job_type1").hide();
}
function selectjobtwo(id,type,name,gettype){
	$("#"+type).val(id);
	$("#"+type+"_name").val(name);
	$("#job_type2").hide();
}
function checktpl(id){

	var	buytpl=$("#buytpl_"+id).val();
	var name=$("input[name=tplname"+id+"]").val();
	var msg;
	var p=$("#list_tpl_"+id).html().replace("模板价格：","");
	$('#tplid').val(id);
	
	if(buytpl==1){
		msg="确定使用该模板？";
	}else{
		msg="确定使用"+name+",扣除"+p+"？";
	}
	layer.confirm(msg,function(){ 
		setTimeout(function(){$('#myform').submit()},0);
	}); 
}
function job_refresh(){
	layer.confirm("刷新次数已用完，是否先购买特权？",function(){
		window.location.href =weburl+"/member/index.php?c=pay";window.event.returnValue = false;return false; 
	});
}
function invoice_link(type){
	if(type=='2'){$(".payment_fp_touch_in").show();}else{$(".payment_fp_touch_in").hide();}	
}
function really_read(name){ 
	var chk_value =[];    
	$('input[name="'+name+'"]:checked').each(function(){    
		chk_value.push($(this).val());   
	});   
	if(chk_value.length==0){
		layer.msg("请选择要阅读的数据！",2,8);return false;
	}else{
		layer.confirm("确定阅读吗？",function(){
			$.post("index.php?c=hr&act=hrset",{ids:chk_value,ajax:1},function(data){ 
				var data=eval('('+data+')');
				if(data.url=='1'){
					parent.layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.reload();});return false;
				}else{
					parent.layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.href=data.url;});return false;
				}
			})
		});
	} 
}
function del_pay(oid){ 
	layer.confirm('是否取消该订单？', function(){
		$.get("index.php?c=paylog&act=del&id="+oid,function(msg){
			if(msg=='0'){
				layer.msg('非法操作！', 2, 0);return false;  
			}else{
				layer.msg('取消成功！', 2, 9,function(){location.reload();});return false;  
			}
		});
	});  
} 
function paylog_remark(){ 
	var id=$("#paylog_id").val();
	var content=$.trim($("#alertcontent").val());
	if(id<1){
		layer.msg('非法操作！', 2, 8);return false; 
	}
	if(content==''){
		layer.msg('备注内容不能为空！', 2, 8);return false; 
	} 
}
function paylog_invoice(){
	var title=$.trim($("#title").val());
	var link_man=$.trim($("#link_man").val());
	var link_moblie=$.trim($("#link_moblie").val());
	var address=$.trim($("#address").val());
	if(title==''||link_man==''||link_moblie==''||address==''){
		layer.msg('发票抬头、联系人、联系电话、邮寄地址均不能为空！', 2, 8);return false;
	}
	
} 
function switchJob(num,element,classname,itemCommonclassname,itemclassname){
	$("."+classname).removeClass(classname+"_on");
	$(element).addClass(classname+"_on");
	$("."+itemCommonclassname).hide();
	$("."+itemclassname).show();
}
function resumetop(eid,date){
	$("input[name='eid']").val(eid);
	$("#topdate").html(date);
	$.layer({
		type : 1,
		title :'简历置顶',
		offset: [($(window).height() - 200)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : ['400px','200px'],
		page : {dom :"#resumetop"}
	});
}