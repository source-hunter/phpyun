//ְλ����ҳ JS
$(document).ready(function(){
	
//ְλ����ҳ��ѡ����ְλ ��һ��
	$("#comindex_sqjob").click(function(){
		var loadi = layer.load('���ڼ��ء���',0);
		var codewebarr=get_comindes_jobid();
		if(!codewebarr){layer.msg('����û��ѡ��ְλ��', 2, 8);return false;}
		$.post("index.php?m=ajax&c=index_ajaxjob",{jobid:codewebarr},function(data){
			layer.close(loadi);
			if(data==0){ 
				layer.msg('�Բ��������Ǹ����û����޷�����ְλ��', 2, 8);return false;
			}else if(data==4){ 
				layer.msg('�������ϸù�˾Ҫ���޷��ύ���룡', 2, 8);return false;
			}else if(data==2){ 
				layer.msg('����û�м���,������Ӽ�����', 2, 8);return false;
			}else if(data==3){ 
				layer.msg('�������������ְλ��', 2, 8);return false;
			}else{
				$(".POp_up_r").html('');	 	
				$(".POp_up_r").append(data); 
				$.layer({
					type : 1,
					title :'����ְλ',
					offset: [($(window).height() - 380)/2 + 'px', ''],
					closeBtn : [0 , true],
					border : [10 , 0.3 , '#000', true],
					area : ['380px','auto'],
					page : {dom :"#sqjob_show"}
				});
			}
		});
	})
	
//ְλ����ҳ����ְλ �ڶ���
	$("#click_comindex_sqjob").click(function(){
		var codewebarr=get_comindes_jobid();
		if(!codewebarr){layer.msg('����û��ѡ��ְλ��', 2, 8);return false;}
		var eid=$("input[name=resume][checked]").val();
		layer.closeAll();
		var loadi = layer.load('ִ���У����Ժ�...',0); 
		$.post("index.php?m=ajax&c=comindex_sqjob",{codewebarr:codewebarr,eid:eid},function(data){
			layer.close(loadi);
			if(data=='4'){ 
				layer.msg('�������ϸù�˾Ҫ���޷��ύ���룡', 2, 8);
			}else if(data=='1'){ 
				layer.msg('���ѳɹ������ְλ��', 2, 9);
			}else if(data=='2'){ 
				layer.msg('ϵͳ�������Ժ����ԣ�', 2, 0);
			}else if(data=='3'){ 
				layer.msg('�������������ְλ��', 2, 8);
			}else if(data='0'){
				layer.alert('���ȵ�¼��', 0, '��ʾ',function(){location.href ="index.php?m=login&usertype=1" });
			}else{
				layer.msg('ϵͳ��æ��', 2, 0);
			} 
		});	
	})
	
//ְλ����ҳ �ղ�ְλ type:��ְͨλ 1 ����˾��������ͷְλ 2����ͷ������ְλ 3
	$("#comindex_favjob").click(function(){
		var codewebarr=get_comindes_jobid();
		if(!codewebarr){layer.msg('����û��ѡ��ְλ��', 2, 8);return false;}
		$.post("index.php?m=ajax&c=comindex_favjob",{codewebarr:codewebarr,type:1},function(data){ 
			if(data==0){
				layer.msg('�Բ��������Ǹ����û����޷��ղ�ְλ��', 2, 8);return false;	
			}else if(data==1){ 
				layer.msg('���ѳɹ��ղظ�ְλ��', 2, 9);return false;
			}else if(data==2){ 
				layer.msg('ϵͳ�������Ժ����ԣ�', 2, 0);return false;
			}else if(data==3){ 
				layer.msg('�������ղع���ְλ��', 2, 8);return false;
			}else{
				layer.alert('���ȵ�¼��', 0, '��ʾ',function(){location.href ="index.php?m=login&usertype=1" });return false; 
			}
			$('.job_box').hide();
		});	
	})	
	$(".checkbox_job").click(function(data){//ȫѡ
		var val=$(this).attr("class");
		if(val=="checkbox_job"){
			$(this).addClass("iselect")
			var pid=$(this).attr("pid");
			$("#checkbox"+pid).attr("checked","checked");
		}else{
			$(this).removeClass("iselect")
			var pid=$(this).attr("pid");
			$("#checkbox"+pid).attr("checked",false);
			$(".checkbox_all").removeClass("iselect")
		}
	})
	$('body').mouseout(function(evt){
		if($(evt.target).parents('.com-list-wrapper').length==0){
		   $('.ks-popup').hide();
		}
	})
})
function checkAll(){//ȫѡ
	var val=$(".checkbox_all").attr("class");
	if(val=="checkbox_all"){
		$("input[name=checkbox_job]").attr("checked","checked");
		$(".checkbox_job").addClass("iselect")
		$(".checkbox_all").addClass("iselect")
	}else{
		$("input[name=checkbox_job]").attr("checked",false);
		$(".checkbox_job").removeClass("iselect")
		$(".checkbox_all").removeClass("iselect")
	}
}
function exchange(){
	var exchangep=$("#exchangep").val();
	$.get("index.php?m=ajax&c=exchange&page="+exchangep,function(data){
		
		$(".job_right_box_list").html(data);
	});
}
$(document).ready(function(){
	$(".yun_Looking_work_name").hover(function(){
		var aid=$(this).attr("aid");
		$("#i"+aid).addClass("All_post_seach_lbg");
	},function(){
		var aid=$(this).attr("aid");
		$("#i"+aid).removeClass("All_post_seach_lbg");
	}); 
}); 