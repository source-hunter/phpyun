//���ؼ��� 
$(document).ready(function(){
	$(".down_resume").click(function(){
		var eid=$("#eid").val();
		var uid=$("#uid").val();
		var username=$("#username").val();
		$.post(weburl+"/index.php?m=ajax&c=down_resume",{eid:eid,uid:uid,username:username},function(data){
 			var data=eval('('+data+')');
			var status=data.status; 
			var integral=data.integral; 
			if(!status || status==0){ 
				layer.msg('��������ҵ�û���', 2, 8);return false; 
			}else if(status==1){
				layer.confirm("���ؼ������۳�"+integral+integral_pricename+"���Ƿ����أ�", function(){down_integral(eid,uid);}); 
			}else if(status==2){
				layer.confirm("���ĵȼ���Ȩ�Ѿ�����,���۳�"+integral+integral_pricename+"���Ƿ����أ�", function(){down_integral(eid,uid);});  
			}else if(status==6){
				window.open(weburl+"/index.php?m=ajax&c=resume_word&id="+eid); 
			}else if(status==3){
				window.open(weburl+"/index.php?m=ajax&c=resume_word&id="+eid);
				window.location.href=document.URL;
			}else if(status==4){ 
				layer.msg('��Ա���ؼ��������꣡', 2, 8);return false; 
			}else if(status==5){
				layer.alert(integral_pricename+'���㣬���ȳ�ֵ��',8, '��ʾ',function(){location.href =weburl+"/member/index.php?c=pay" });return false;
			}else if(status==7){ 
				layer.msg('���û��ѱ��������������', 2, 8);return false; 
			}else if(status==8){ 
				layer.msg('���ѱ����û������������', 2, 8);return false; 
			}
		});
	})	

	$("input[name='background_type']").click(function(){
		if($(this).val()=='1'){
			$(".resume_background_color").hide();
			$(".resume_background_image").show();
		}else if($(this).val()=='2'){
			$(".resume_background_image").hide();
			$(".resume_background_color").show();
		}else{
			$(".resume_background_color").show();
			$(".resume_background_image").show();
		}
	});
})
//��������ҳ�鿴��ϵ��ʽ�����ؼ���
function for_link(eid){
	var i=layer.load('ִ���У����Ժ�...',0);
	$.post(weburl+"/index.php?m=ajax&c=for_link",{eid:eid},function(data){  
		layer.close(i);
		var data=eval('('+data+')');
		var status=data.status;
		if(status==1){
			layer.msg(data.msg, 2,8);
		}else if(status==2){
			if(data.usertype=='2'){
				layer.confirm(data.msg, function(){down_integral(eid,data.uid);});
			}else{
				layer.confirm(data.msg, function(){lt_down_integral(eid,data.uid);});
			} 
		}else if(status==3){
			$("#for_link .city_1").html(data.html);
			$.layer({
				type : 1,
				title : "�鿴��ϵ��ʽ", 
				offset: [($(window).height() - 150)/2 + 'px', ''],
				closeBtn : [0 , true],
				border : [10 , 0.3 , '#000', true],
				area : ['350px','auto'],
				page : {dom :"#for_link"}
			});
		} 
	});
}
//���ؼ�������ģʽ
function down_integral(eid,uid){
	$.post(weburl+"/index.php?m=ajax&c=down_resume",{type:"integral",eid:eid,uid:uid},function(data){ 
		var data=eval('('+data+')');
		var status=data.status;
		var integral=data.integral;
		if(status==5){
			layer.confirm('������'+integral+integral_pricename+'���������ؼ������Ƿ��ֵ��', function(){window.location.href =weburl+"/member/index.php?c=pay";window.event.returnValue = false;return false; }); 
		}else if(status==3){
			window.open(weburl+"/index.php?m=ajax&c=resume_word&id="+eid);
			window.location.href=document.URL;
		}else{
			layer.msg(data.msg, 2, 8);return false;
		}
	})
}
//��ͷ���ؼ�������ģʽ
function lt_down_integral(eid,uid){
	$.post(weburl+"/index.php?m=ajax&c=lt_down_resume",{type:"integral",eid:eid,uid:uid},function(data){
		var data=eval('('+data+')');
		var status=data.status;
		var integral=data.integral;
		if(status==5){ 
			layer.confirm('������'+integral+integral_pricename+'���������ؼ������Ƿ��ֵ��', function(){window.location.href =weburl+"/member/index.php?c=pay"; window.event.returnValue = false;return false;}); 
		}else if(status==3){
			window.open(weburl+"/index.php?m=ajax&c=resume_word&id="+eid);
			location.href=document.URL;
		}
	})
}
//����layer��
function layer_div(divid,name,width,height){
	$.layer({
		type : 1,
		title :name, 
		offset: [($(window).height() - height)/2 + 'px', ''],
		closeBtn : [0 , true],
		border : [10 , 0.3 , '#000', true],
		area : [width,height],
		page : {dom :"#"+divid}
	});
}
function loadset(){
	$("#background_size_width").val($(".container").css("background-size").split(' ')[0]);
	$("#background_size_height").val($(".container").css("background-size").split(' ')[1]);
	var background_color=pnq_colorHex($(".container").css("background-color"));
	if(background_color!='transparent'){
		background_color=background_color.substring(1);
		$('input[name="background_type"]').eq(1)[0].checked=true;
		$(".resume_background_image").hide();
		$(".resume_background_color").show();
	}else{
		$('input[name="background_repeat"]').each(function(){
			if($(this).val()==$(".container").css("background-repeat")){
				$(this)[0].checked=true;
			}
		});
		$('input[name="background_type"]').eq(0)[0].checked=true;
		$(".resume_background_image").show();
		$(".resume_background_color").hide();
	}
	$("#background_color").val(background_color);
}
function pnq_colorHex( rgb ) {
  	var _this = rgb;
  	var reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/;
  
  	if ( /^(rgb|RGB)/.test( _this ) ) {
   		var aColor = _this.replace( /(?:\(|\)|rgb|RGB)*/g, '' ).split( ',' );
   		var strHex = '#';   
   		for ( var i = 0; i < aColor.length; i ++ ) {
    		var hex = Number( aColor[i] ).toString( 16 );
    		hex = hex < 10 ? 0 +''+ hex :hex;
    		if ( hex === '0' ) {
     			hex += hex;
    		}
    		strHex += hex;
   		}    
   		if ( strHex.length !== 7 ){
    		strHex = _this;
   		}   
		return strHex;
  	} else if ( reg.test( _this ) ) {
   		var aNum = _this.replace( /#/,'').split('');
   		if ( aNum.length === 6 ) {
    		return _this;
   		} else if ( aNum.length === 3 ) {
    		var numHex = '#';    
    		for ( var i = 0; i < aNum.length; i +=1 ) {
     			numHex += ( aNum[i] + aNum[i] );
    		}
    		return numHex;
   		}
  	} else {
   		return _this;
  	}
}
//Ӧ�ñ�������
function applyBackground(){
	if($('input[name="background_type"]:checked').val()=='1'){
		//������ͼƬ
		var background="";
		if($("#background_url").val()!=''){			
			background+="url("+$("#background_url").val()+")";			
			if($('input[name="background_repeat"]:checked').length>0){
				background+=" "+$('input[name="background_repeat"]:checked').val();
			}
			if($("#background_size_width").val()!=''&&$("#background_size_height").val()!=''&&$("#background_size_width").val()!='auto'&&$("#background_size_height").val()!='auto'){
				background+=" 0 0 / "+$("#background_size_width").val()+' '+$("#background_size_height").val();
			}
		}
		$(".container").css("background",background);
	}else if($('input[name="background_type"]:checked').val()=='2'){
		//������ɫ
		$(".container").css("background","");
		if($("#background_color").val()!=''){
			$(".container").removeAttr("style");
			$(".container").css("background-color","#"+$("#background_color").val());	
		}
	}/*else{
		//����ͼƬ�ͱ���ɫ
		var background="";
		if($("#background_url").val()!=''){			
			background+="url("+$("#background_url").val()+")";			
			if($('input[name="background_repeat"]:checked').length>0){
				background+=" "+$('input[name="background_repeat"]:checked').val();
			}
			if($("#background_size_width").val()!=''&&$("#background_size_height").val()!=''&&$("#background_size_width").val()!='auto'&&$("#background_size_height").val()!='auto'){
				background+=" 0 0 / "+$("#background_size_width").val()+' '+$("#background_size_height").val();
			}
		}
		$(".container").css("background",background);
	}*/
}
function saveBackground(){
	var eid=$("#eid").val();
	var background_type=$('input[name="background_type"]:checked').val();
	var background="";
	
	if($('input[name="background_type"]:checked').val()=='1'){
		//������ͼƬ
		$(".container").find("div").css("background-color","transparent");
		if($("#background_url").val()!=''){			
			background+="url("+$("#background_url").val()+")";			
			if($('input[name="background_repeat"]:checked').length>0){
				background+=" "+$('input[name="background_repeat"]:checked').val();
			}
			if($("#background_size_width").val()!=''&&$("#background_size_height").val()!=''&&$("#background_size_width").val()!='auto'&&$("#background_size_height").val()!='auto'){
				background+=" 0 0 / "+$("#background_size_width").val()+' '+$("#background_size_height").val();
			}
		}
	}else if($('input[name="background_type"]:checked').val()=='2'){
		//������ɫ
		$(".container").css("background","");
		if($("#background_color").val()!=''){
			$(".container").removeAttr("style");
			$(".container").css("background-color","#"+$("#background_color").val());	
			background="#"+$("#background_color").val();
		}
	}/*else{
		//����ͼƬ�ͱ���ɫ
		if($("#background_color").val()!=''){
			background+="#"+$("#background_color").val();
		}
		if($("#background_url").val()!=''){			
			background+=" url("+$("#background_url").val()+")";			
			if($('input[name="background_repeat"]:checked').length>0){
				background+=" "+$('input[name="background_repeat"]:checked').val();
			}
			if($("#background_size_width").val()!=''&&$("#background_size_height").val()!=''&&$("#background_size_width").val()!='auto'&&$("#background_size_height").val()!='auto'){
				background+=" 0 0 / "+$("#background_size_width").val()+' '+$("#background_size_height").val();
			}
		}
	}*/
	$.post(weburl+"/index.php?m=ajax&c=saveresumebackground",{eid:eid,background:background,background_image:$("#background_url").val()},function(data){
		if(data=='0'){
			layer.msg('�Բ�������Ȩ������',2,8);
		}else if(data=='1'){
			layer.msg('��������ʧ�ܣ�',2,9);
		}else if(data=='2'){
			layer.msg('�������óɹ���',2,8);
		}else{
			layer.msg('�Բ�������Ȩ������',2,8);
		}
	});
}
function settemplate(tmpid){
	var eid=$("#eid").val();
	$.post(weburl+"/index.php?m=ajax&c=saveresumetemplate",{eid:eid,tmpid:tmpid},function(data){
		var data=eval('('+data+')');
		if(data.url==''){
			layer.msg(data.msg,2,data.status);return false;
		}else{
			layer.msg(data.msg,2,data.status,function(){location.href=data.url;});return false;
		}
	});
}
function talent_pool(uid,eid){
	$.post(weburl+"/index.php?m=ajax&c=talent_pool",{eid:eid,uid:uid},function(data){
		if(data=='0'){
			layer.msg('ֻ����ҵ�û����ſ��Բ�����',2,8);
		}else if(data=='1'){
			layer.msg('����ɹ���',2,9);
		}else if(data=='2'){
			layer.msg('�ü����Ѽ��뵽�˲ſ⣡',2,8);
		}else{
			layer.msg('�Բ��𣬲�������',2,8);
		}
	});
}