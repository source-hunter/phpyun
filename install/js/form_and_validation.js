function checkweb(){
	document.getElementById('weburl_msg').innerHTML='վ���url';
	document.getElementById('dbhost_msg').innerHTML='���ݿ��������ַ��һ��Ϊlocalhost';
	document.getElementById('dbuser_msg').innerHTML='';
	document.getElementById('dbname_msg').innerHTML='';
	document.getElementById('username_msg').innerHTML='';
	document.getElementById('password_msg').innerHTML='Ĭ�����룺admin';
	document.getElementById('password2_msg').innerHTML='Ĭ�����룺admin';
	if(document.frm_sql.weburl.value==""){
		document.getElementById('weburl_msg').innerHTML="<font color=red>��ַ����Ϊ��</font>";
		document.frm_sql.weburl.focus();
		return false;
	}
	if(document.frm_sql.dbhost.value==""){
		document.getElementById('dbhost_msg').innerHTML="<font color=red>����������Ϊ��</font>";
		document.frm_sql.dbhost.focus();
		return false;
	}
	if(document.frm_sql.dbuser.value==""){
		document.getElementById('dbuser_msg').innerHTML="<font color=red>�û�������Ϊ��</font>";
		document.frm_sql.dbuser.focus();
		return false;
	}
    if(document.frm_sql.dbname.value==""){
		document.getElementById('dbname_msg').innerHTML="<font color=red>����������Ϊ��</font>";
		document.frm_sql.dbname.focus();
		return false;
	}	
	if(/[~#^.��,��$@%&!*]/gi.test(document.frm_sql.dbname.value)){
		document.getElementById('dbname_msg').innerHTML="<font color=red>���ݿⲻ�ܰ��������ַ�</font>";
		document.frm_sql.dbname.focus();
		return false;
	}
   	if(document.frm_sql.username.value==""){
		document.getElementById('username_msg').innerHTML="<font color=red>����Ա�ʻ�����Ϊ��</font>";
		document.frm_sql.username.focus();
		return false;
		}
	if(document.frm_sql.password.value==""){
		document.getElementById('password_msg').innerHTML="<font color=red>����Ա���벻��Ϊ��</font>";
		document.frm_sql.password.focus();
		return false;
		}		
	if(document.frm_sql.password2.value==""){
		document.getElementById('password2_msg').innerHTML="<font color=red>����Ա���벻��Ϊ��</font>";
		document.frm_sql.password2.focus();
		return false;
		}		
	if(document.frm_sql.password2.value!=document.frm_sql.password.value){
		document.getElementById('password2_msg').innerHTML="<font color=red>�������벻һ��</font>";
		document.frm_sql.password2.focus();
		return false;
		}		
	if(document.frm_sql.dbpwd.value==""){
       return confirm('���ݿ�����ȷ��Ϊ�գ����Ѱ�װǰ�뱸�ݺ����ݿ⣬�������ݶ�ʧ');
	}else{
	   return confirm('���Ѱ�װǰ�뱸�ݺ����ݿ⣬�������ݶ�ʧ');
	}
}
