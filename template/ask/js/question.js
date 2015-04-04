function returnmessage(){
	var message = $(window.frames["supportiframe"].document).find("#layer_msg").val();  
	if(message != null){  
		var url=$(window.frames["supportiframe"].document).find("#layer_url").val();
		var layer_time=$(window.frames["supportiframe"].document).find("#layer_time").val(); 
		var layer_st=$(window.frames["supportiframe"].document).find("#layer_st").val(); 
		if(url=='1'){
			layer.msg(message, layer_time, Number(layer_st),function(){ location.reload();}); 
		}else if(url==''){
			layer.msg(message, layer_time, Number(layer_st)); 
		}else{
			layer.msg(message, layer_time, Number(layer_st),function(){location.href = url;});
		}
	}
}
function position(id){
	var height=$(window).height();
	var width=$(window).width();
	var heights=$(id).height();
	var widths=$(id).width();
	var top=(parseInt(height)-(heights))/2;
	var left=(parseInt(width)-(widths))/2;
	if(window.attachEvent){
		var offset_top=$(id).offset().top;
		top=parseInt(top)+parseInt(offset_top);
	}   
	$(id).css("top",top);
	$(id).css("left",left); 
	$(id).show(); 
}
function clearForm(form) {
	$(':input', form).each(function() {
		var type = this.type;
		var tag = this.tagName.toLowerCase(); 
		if (type == 'text' || type == 'password' || tag == 'textarea'){
			this.value = "";
		}else if (type == 'checkbox' || type == 'radio'){
			this.checked = false;
		}else if (tag == 'select'){
			this.selectedIndex = -1;
		}
	});
};
function get_order(url){
	location=url;
}
function get_comment(aid,qid,is_show,is_answer){
	var style=$("#div_"+aid).css("display");
	if(style=="none" || is_show=='1'){
		$.post(weburl+"/ask/index.php?c=get_comment",{aid:aid},function(data){
			var html='';
			if(is_answer!='0'){
				var datas = Array();			
				datas = eval("("+data+")");
				$.each(datas,function(key,val){
					html +="<dl>";
					html +="<dt><img src=\""+val.pic+"\" width=\"25\" height=\"25\"></dt>";
					html +="<dd><div class=\"answers_discuss_reply\"><a href=\""+val.url+"\">"+val.nickname+"</a>";
					if(val.myself!='1'){
						html +="<a href=\"javascript:void(0)\" class=\"review_jb\" onclick=\"get_show('"+val.id+"','3');\">举报</a>";
					} 
					html +="<span>"+val.date+"</span></div><div class=\"answers_discuss_reply_cont\">"+val.content+"</div></dd></dl>";
				});	
			}
			//if(is_answer=='0'){
				html +="<div class=\"my_discuss\"><textarea id=\"comment_"+aid+"\" class=\"goog-textarea\" ></textarea><a href=\"javascript:void(0)\" class=\"my_discuss_aubmit\" onclick=\"for_comment('"+aid+"','"+qid+"');\">评论</a></div>";
			//}
			$("#review_"+aid).html(html);
			$("#div_"+aid).show();
		});
	}else{
		$("#div_"+aid).hide();
	} 
} 
function for_comment(aid,qid){
	var content=$("#comment_"+aid).val(); 
	if(content=="" || content=="undefined"){
		layer.msg('评论内容不能为空！', 2, 2);return false; 
	}else{
		$.post(weburl+"/ask/index.php?c=for_comment",{aid:aid,qid:qid,content:content},function(msg){
			if(msg=='1'){
				$("#comment_"+aid).val("");
				var com_num=$("#com_num_"+aid).html();  
				com_num=parseInt(com_num)+parseInt(1);
				$("#com_num_"+aid).html(com_num); 
				get_comment(aid,qid,is_show='1','');
			}else if(msg=='0'){
				layer.msg('评论失败！', 2, 8);return false; 
			}else if(msg=='no_login'){ 
				layer.msg('请先登录！', 2, 8);return false; 
			}
		});
	}
}
function support(aid){
	$.post(weburl+"/ask/index.php?c=for_support",{aid:aid},function(msg){
		if(msg=='0'){
			layer.msg('提交失败！', 2, 8);return false; 
		}else if(msg=='1'){
			var num=$("#support_num_"+aid).html(); 
			$("#support_num_"+aid).html(parseInt(num)+parseInt(1)); 
			layer.msg('投票成功！', 2, 9);return false; 
		}else if(msg=='2'){
			layer.msg('请勿重复投票！', 2, 3);return false; 
		}
	});
}

function del_attention(id,type){
	$.post(weburl+"/ask/index.php?c=del_attention",{id:id,type:type},function(msg){
		if(msg=='1'){
			location.reload();
		}else{
			layer.msg('操作失败！', 2, 8);return false; 
		}
	});
}
function attention(id,type){
	$.post(weburl+"/ask/index.php?c=attention",{id:id,type:type},function(msg){
		if(msg=='0'){
			layer.msg('关注失败！', 2, 8);return false;  
		}else if(msg=='1'){
			if(type=='1'){
				layer.msg('关注成功！', 2, 9,function(){location.reload();});return false;   
			}else{
				$("a[name='atn_"+id+"']").removeClass("answer_gz");
				$("a[name='atn_"+id+"']").addClass("answer_ygz");
				$("a[name='atn_"+id+"']").html("取消关注");
			} 
		}else if(msg=='2'){
			layer.msg('您已关注过该题目！', 2, 8);return false;  
		}else if(msg=='no_login'){
			layer.msg('请先登录！', 2, 8);return false;  
		}else if(msg=='3'){
			$("a[name='atn_"+id+"']").removeClass("answer_ygz");
			$("a[name='atn_"+id+"']").addClass("answer_gz");
			$("a[name='atn_"+id+"']").html("关注");
		}else if(msg=='4'){
			layer.msg('不能关注自己发布的问题！', 2, 8);return false; 
		}
	});
}
function attention_user(uid){
	$.post(weburl+"/index.php?m=ajax&c=Atn",{id:uid},function(msg){
		if(msg=='1'){
			$("a[name='atn_"+uid+"']").removeClass("zg-btn-green");
			$("a[name='atn_"+uid+"']").addClass("zg-btn-unfollow");
			$("a[name='atn_"+uid+"']").html("取消关注");
		}else if(msg=='2'){
			$("a[name='atn_"+uid+"']").removeClass("zg-btn-unfollow");
			$("a[name='atn_"+uid+"']").addClass("zg-btn-green");
			$("a[name='atn_"+uid+"']").html("关注");
		}else if(msg=='3'){
			layer.msg('请先登录！', 2, 8);return false; 
		}
	});
}
function checkform(type){
	if(type=='1'){
		var title=$("#title").val();
		if (title==""){
			layer.msg('请填写问答标题！', 2, 2); 
			return (false);
		} 
	} 
}
function keyup(){
	var name=$("#q_class").val();
	$.post(weburl+"index.php?c=get_q_class",{name:name},function(data){
		$("#result_class").html(data);
	});
}
function get_class(id){
	var name=$("#"+id).html();
	$("#q_class").val(name);
	$("#class").val(id);
	$("#result_class").html("");
}
function reason(){
	var reason=$("#reason").val(); 
	var eid=$("#eid").val();
	var type=$("#type").val();
	$.post(weburl+"/ask/index.php?c=q_repost",{reason:reason,eid:eid,type:type},function(data){ 
		layer.closeAll();
		if(data=='0'){
			layer.msg('举报失败！', 2, 8);
		}else if(data=='1'){
			layer.msg('举报成功！', 2, 9);
		}else if(data=='2'){
			layer.msg('您已举报过该错误！', 2, 3);
		}else if(data=='3'){
			layer.msg('该错误已被他人举报！', 2, 3);
		}else if(data=='no_login'){
			layer.msg('请先登录！', 2, 8);
		}
	});
}
function get_user_info(address,aid,uid,height){
	var info=$("#info_"+address+aid).html();  
	if(info=="" ||info==null){
		var pointY=$("#"+address+aid).offset().top; 
		var pointX = $("#"+address+aid).offset().left; 		
		if(height){
			var top=parseInt(pointY)+parseInt(height);
		}else{
			var top=parseInt(pointY)+parseInt(70);
		}  
		var left=parseInt(pointX)-parseInt(40);
		$.post(weburl+"/ask/index.php?c=get_user_info",{uid:uid},function(data){
			var datas = Array();			
			datas = eval("("+data+")");
			var html="";
			html="<div  class=\"yun_ask_tck\" style=\"top:"+top+"px;left:"+left+"px\" id=\"info_"+address+aid+"\"><div class=\"yun_ask_box\"><div><div class=\"yun_ask_triangle\"></div></div><div class=\"yun_ask_box_top\"></div><div class=\"yun_ask_box_cont\"><div class=\"yun_ask_box_img\">";
			if(datas.pic){
				html +="<img src=\""+datas.pic+"\" width=\"63\" height=\"63\" onerror=\"showImgDelay(this,'../"+datas.sy_friend_icon+"',2);\">";
			}
			html +="</div><div class=\"yun_ask_box_r\"><div class=\"yun_ask_box_r_name\">"+datas.nickname+"</div><font color=\"#0099FF\">"+datas.answer_num+"</font>次回答<font color=\"#0099FF\"> "+datas.ant_num+"</font>人关注他</div></div><div class=\"yun_ask_box_bottom\">签名："+datas.description+"</div><div class=\"yun_ask_box_bot\"></div></div></div>";
			$("#"+address+aid).append(html);			
		});
	}else{
		$("#info_"+address+aid).show();
	}
}
function hide_user_info(address,aid){
	$("#info_"+address+aid).hide();
	$(".yun_ask_tck").hide();
}
function forward_sinaweibo(id) {
    var title = $("#title_"+id).html();  
    var _url = encodeURIComponent(document.location);
    var _assname = encodeURI('name');
    var _appkey = encodeURI('appkey');
    var _pic = encodeURI('');
    var _site = '';
    var _u = 'http://v.t.sina.com.cn/share/share.php?url=' + _url + '&appkey=' + _appkey + '&site=' + _site + '&pic=' + _pic + '&title=' + title + '&assname=' + _assname;
    window.open(_u, '', 'width=700, height=680, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, location=yes, resizable=no, status=no');
}
function forward_tencentweibo(id) {
	var title = $("#title_"+id).html();  
    var _url = encodeURIComponent(document.location);
    var _assname = encodeURI('name');
    var _appkey = encodeURI('appkey');
    var _pic = encodeURI('');
    var _site = weburl;
    var _u = 'http://v.t.qq.com/share/share.php?url=' + _url + '&appkey=' + _appkey + '&site=' + _site + '&pic=' + _pic + '&title=' + title + '&assname=' + _assname;
    window.open(_u, '', 'width=700, height=680, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, location=yes, resizable=no, status=no');
}
$.fn.smartFloat = function(d_id) {
	var height=$(window).height();
	var heightdiv=$("#"+d_id).height();
	var position = function(element) {
		var top=(parseInt(height)-(heightdiv))/2;
		$(window).scroll(function() {
			var scrolls = $(this).scrollTop();
			if (window.XMLHttpRequest) {
				element.css({
					position: "fixed",
					top: top
				});	
			} else {
				element.css({
					top: top+scrolls
				});	
			}
		});
	};
	return $(this).each(function() {
		position($(this));						 
	});
};
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