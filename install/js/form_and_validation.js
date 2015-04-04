function checkweb(){
	document.getElementById('weburl_msg').innerHTML='站点的url';
	document.getElementById('dbhost_msg').innerHTML='数据库服务器地址，一般为localhost';
	document.getElementById('dbuser_msg').innerHTML='';
	document.getElementById('dbname_msg').innerHTML='';
	document.getElementById('username_msg').innerHTML='';
	document.getElementById('password_msg').innerHTML='默认密码：admin';
	document.getElementById('password2_msg').innerHTML='默认密码：admin';
	if(document.frm_sql.weburl.value==""){
		document.getElementById('weburl_msg').innerHTML="<font color=red>网址不能为空</font>";
		document.frm_sql.weburl.focus();
		return false;
	}
	if(document.frm_sql.dbhost.value==""){
		document.getElementById('dbhost_msg').innerHTML="<font color=red>主机名不能为空</font>";
		document.frm_sql.dbhost.focus();
		return false;
	}
	if(document.frm_sql.dbuser.value==""){
		document.getElementById('dbuser_msg').innerHTML="<font color=red>用户名不能为空</font>";
		document.frm_sql.dbuser.focus();
		return false;
	}
    if(document.frm_sql.dbname.value==""){
		document.getElementById('dbname_msg').innerHTML="<font color=red>数据名不能为空</font>";
		document.frm_sql.dbname.focus();
		return false;
	}	
	if(/[~#^.。,，$@%&!*]/gi.test(document.frm_sql.dbname.value)){
		document.getElementById('dbname_msg').innerHTML="<font color=red>数据库不能包含特殊字符</font>";
		document.frm_sql.dbname.focus();
		return false;
	}
   	if(document.frm_sql.username.value==""){
		document.getElementById('username_msg').innerHTML="<font color=red>管理员帐户不能为空</font>";
		document.frm_sql.username.focus();
		return false;
		}
	if(document.frm_sql.password.value==""){
		document.getElementById('password_msg').innerHTML="<font color=red>管理员密码不能为空</font>";
		document.frm_sql.password.focus();
		return false;
		}		
	if(document.frm_sql.password2.value==""){
		document.getElementById('password2_msg').innerHTML="<font color=red>管理员密码不能为空</font>";
		document.frm_sql.password2.focus();
		return false;
		}		
	if(document.frm_sql.password2.value!=document.frm_sql.password.value){
		document.getElementById('password2_msg').innerHTML="<font color=red>两次密码不一样</font>";
		document.frm_sql.password2.focus();
		return false;
		}		
	if(document.frm_sql.dbpwd.value==""){
       return confirm('数据库密码确定为空！提醒安装前请备份好数据库，以免数据丢失');
	}else{
	   return confirm('提醒安装前请备份好数据库，以免数据丢失');
	}
}
