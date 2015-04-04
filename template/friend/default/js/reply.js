 
function CheckPost1(){
	var content = $("#content").val();
	var touid = $("#touid").val();
	var myuid = $("#myuid").val();
	if(myuid==touid){ 
		layer.msg('不能给自己留言！', 2, 3);return false;
	}
	if(content=="有多久没给好友留言了？"||content=='回复:'){
		content='';
	}
	if(content==""){ 
		layer.msg('内容不能为空！', 2, 2);return false;
	}
}

function submitmessage(friend){
	if(friend=='1'){
		layer.msg('你们还不是好友，不能给对方留言！', 2,8);
		$("#content").val("");	
		return false;
	}
	var content = $("#content").val();
	var ownid = $("#ownid").val();
	var touid = $("#touid").val();
	var nid = $("#nid").val();
	if(ownid==touid){
		layer.msg('不能给自己留言！', 2, 3);return false;
	}
	if($.trim(content)==""){ 
		layer.msg('留言不能为空！', 2, 2);return false;
	}
	$.post(weburl+"/index.php?m=ajax&c=mymessage",{nid:nid,touid:touid,content:content},function(data){ 
		if(data==1){ 
			layer.msg('请先登录！', 2, 3);return false;
		}else{
			var data = eval("("+data+")");
			var content = "";		
			content = '<div class="Personals_cont_dy"><div class="Personals_cont_tx"><img src="'+data.pic+'" width="50" height="50"/></div><div class="Personals_cont_dy_r"><div class="Personals_cont_dy_name"><a href="'+data.url+'" style="float:left">'+data.nickname+'</a><span style="float:right;padding-right:16px; color:#336699"><a href="javascript:void(0)" onclick="layer_del(\'确定要删除该留言？\',\''+weburl+'/friend/index.php?c=del&t=message&id='+data.mid+'\')">删除</a></span></div><div class="Personals_cont_dy_ss" style="width:100%; float:left">'+data.content+'</div><div class="Personals_cont_dy_cz"><span class="messagetime" style="color:#999; float:left">'+data.ctime+'</span></div></div></div>';
			
			$("#message").prepend(content);
			$("#content").val("");						
			$("#message").show();
			$("#state").hide();
			$("#loadover").hide();
			$("#cssstate").attr("class","");
			$("#cssmessage").attr("class","answers_news_title_atc");
		}
	});
}
//发布动态
function submitstate(){
	var html = editor.text();
	html=$.trim(html);
	if(html=="你正在干嘛？"){
		html='';
	}
	if(html==""){ 
		layer.msg('内容不能为空！', 2, 2);return false;
	}
}
//回复动态
function submitreply(id,fid){
	var content = $("#reply_"+id).val();
	content=$.trim(content);
	if($.trim(content)==""){
		$("#reply_"+id).val("");
		layer.msg('请输入回复内容！', 2, 2);return false; 
	}
	$.post(weburl+"/index.php?m=ajax&c=friendreply",{nid:id,reply:content,fid:fid},function(data){
		if(data==1){ 
			layer.msg('请先登录！', 2, 3);return false;
		}else{
			var data = eval("("+data+")");
			var content = "";
			content = '<div class="Personals_cont_dy_pl"><div class="Personals_cont_dy_pl_tx"><img src="'+data[0].pic+'" width="30" height="30"></div><div class="Personals_cont_dy_pl_user"><div class="Personals_cont_dy_pl_user_n"><a href="'+data[0].url+'">'+data[0].nickname+'</a>: '+data[0].reply+'</div><div class="Personals_cont_dy_pl_user_m">'+data[0].ctime+'</div></div></div>';
			$("#commentlist_"+id).append(content);
			$("#comment_"+id).hide();
			$("#reply_"+id).val("");
			$("#comment"+id).show();	
		}
	});
}
/*
	2013-7-27  回复他人 lgl
*/
function reply_msg(pid,fid,f_name){ 
	$("#comment_"+pid).show();
	$("#fid").val(fid);
	$("#f_name").val(f_name);
	$("#replys_"+pid).attr("placeholder","@"+f_name+": ")
}
function reply_dynamic(id,nid,my_pic,uid,u_name){
	var r_contetn=$("#reply_"+id).val();
	if(r_contetn==""){
		layer.msg('请输入回复内容！', 2, 2);return false; 
	}else{
		var fid=$('#fid').val();
		var f_name=$('#f_name').val(); 
		$.post(weburl+"/friend/index.php?c=reply_dynamic",{pid:id,content:r_contetn,fid:fid,f_name:f_name,nid:nid},function(data){
			var result_r=data.split("||");
			if(result_r[0]=='1'){
				var html="";
				html="<div id=\"commentlist_"+id+"\"><div class=\"Personals_cont_dy_pl\"><div class=\"Personals_cont_dy_pl_tx\"><img src=\""+my_pic+"\" width=\"30\" height=\"30\"></div><div class=\"Personals_cont_dy_pl_user\"><div class=\"Personals_cont_dy_pl_user_n\"><a href=\"/friend/index.php?c=profile&id="+uid+"\">"+u_name+"</a> 回复 "+f_name+": "+r_contetn+"</div><div class=\"Personals_cont_dy_pl_user_m\">"+result_r[1]+"<span style=\"float:right\"><a href=\"javascript:void(0);\" onclick=\"reply_msg('"+id+"','"+uid+"','"+u_name+"','"+uid+"','"+u_name+"');\">回复</a></span></div></div></div></div>";
				$("#msg_"+id).append(html);
				$("#reply_"+id).val("");
			}else{ 
				layer.msg('回复失败！', 2, 8);return false;
			}
		});
		onblur_reply(id);
	} 
}
function onblur_reply(id){
	$("#comment_"+id).hide();
	$("#reply_"+id).val("");
	$("#colornum_"+id).html("0");
}
function get_allmsg(id){
	//$("div[name='hide_"+id+"']").toggle();
	var display=$("div[name='hide_"+id+"']").css("display");
	if(display=='none'){
		$("div[name='hide_"+id+"']").show();
		$("#click_"+id).html("收起评论");
	}else{
		$("div[name='hide_"+id+"']").hide();
		$("#click_"+id).html("查看全部评论");
	} 
}
/*
	2013-7-27	结束
*/
function clicktext(id){ 
	$("#comment_"+id).show();
	$("#comment"+id).hide();
	$("#reply_"+id).focus(); 	
}
function onblurtext(id){
	var content = $("#reply_"+id).val();
	content=$.trim(content);
	if(content==""){
		$("#reply_"+id).val("");
		$("#comment_"+id).hide();
		$("#comment"+id).show();
	}
}
function texthide(id){
	$("#submit_"+id).hide();
}
function showit(sul,hul){
	$("#"+sul).show();
	$("#"+hul).hide();
	if(sul=="message"){
		$(".loadover").attr("class","load");
	}else{
		$(".load").attr("class","loadover");
	}
	$(".load").hide();
	$("#css"+sul).attr("class","");
	$("#css"+hul).attr("class","");
	$("#css"+sul).attr("class","answers_news_title_atc");
}
function reply(name,id){
	$("#content").val("");
	$("#content").val('回复'+name+':');
	$("#touid").val(id);
	myform.content.focus();
}

function checkLength(num,id) {
	var con = $("#reply_"+id).val();
	var content = con.length;
	
	if (con.length > num) {// if too long.... trim it!
		con = con.substring(0, num);
		$("#reply_"+id).val(con);
	// otherwise, update 'characters left' counter
	}
	
	if(con.length=="0"){
		$("#colornum_"+id).html("0");
	}else{
		$("#colornum_"+id).html(con.length);
	} 
}	
function morereply(id,num,more){
	$.post(weburl+"/index.php?m=ajax&c=morereply",{nid:id,more:more},function(data){
		if(data){
			var data=eval("("+data+")");		
			var content = "";
			for (var one in data){
				content += '<div class="Personals_cont_dy_pl"><div class="Personals_cont_dy_pl_tx"><img src="'+data[one].pic+'" width="30" height="30"></div><div class="Personals_cont_dy_pl_user"><div class="Personals_cont_dy_pl_user_n"><a href="'+data[one].url+'">'+data[one].nickname+'</a>: '+data[one].reply+'</div><div class="Personals_cont_dy_pl_user_m">'+data[one].ctime+'</div></div></div>';
			}
			$("#commentlist_"+id).html(content);
			if(more==2){
				$("#onlyreply"+id).html("<span class=\"morereply\" onclick=\"morereply("+id+","+num+",1);\">收起回复</span>");
			}else{
				$("#onlyreply"+id).html("<span class=\"morereply\" onclick=\"morereply("+id+","+num+",2);\">还有"+num+"条回复</span>");
			}
		}
	});
}
function layer_del(msg,url){
	layer.confirm(msg, function(){
		$.get(url,function(data){ 
			var data=eval('('+data+')');  
			if(data.url=='1'){
				layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.reload();});return false;
			}else{
				layer.msg(data.msg, Number(data.tm), Number(data.st),function(){location.href=data.url;});return false;
			} 
		});
	});
} 