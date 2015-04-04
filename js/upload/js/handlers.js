function fileQueued(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("�ȴ��ϴ�...");
		progress.toggleCancel(true, this);

	} catch (ex) {
		this.debug(ex);
	}
}
function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
 			layer.msg('�Բ���ÿ���������ѡ��"+message+"���ļ�', 2, 8);
		}
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);
		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("�ļ�̫��.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("�������ϴ�0�ֽڵ��ļ�.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("δ֪�ļ�����.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("δ֪����");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesSelected > 0) {
			document.getElementById('btnUpload').disabled = false;
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}
	} catch (ex)  {
        this.debug(ex);
	}
}
function uploadStart(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("�ϴ���...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {}
	return true;
}
function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("�ϴ���...");
	} catch (ex) {
		this.debug(ex);
	}
}
function uploadSuccess(file, serverData) {
	try {
		if (serverData.indexOf("suc")!=-1) {
			var fileinfo = serverData.substring(5).split(",");
			fileinfo[0]=fileinfo[0].replace("./","/");
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setComplete();
			progress.setText("<a href=\""+fileinfo[0]+fileinfo[1]+"\" target=\"_blank\">"+fileinfo[1]+"</a>");
			var status = "��ϲ�㣬�ļ��ϴ��ɹ��� <br />";
			var u_value=parseFloat(parseInt(fileinfo[2])/1024).toFixed(2);
			status += "��С��"+u_value+"K <br />";
			status += "��ַ:"+fileinfo[0]+fileinfo[1];
			progress.setStatus(status);
			progress.toggleCancel(false);
			var name=document.getElementById("uploadname").value;
			var name2="";
			if(name!=""){
				name2=name+"###";
			}
			name=document.getElementById("uploadname").value=name2+fileinfo[0].replace("../","/")+fileinfo[1];
			document.getElementById('imglist').innerHTML += '[img]'+fileinfo[0]+fileinfo[1]+'[/img]<br/>';
			getimgtip();
		}else{
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setError();
			progress.setStatus("�ϴ�ʧ�ܣ�"+serverData.substring(5));
			progress.toggleCancel(false);
		}
	} catch (ex) {
		this.debug(ex);
	}
}
function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("�ļ��ϴ�ʧ��: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("�ļ��ϴ�ʧ��");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("������IO����");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("��ȫ����");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("�ļ���С��������");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("��֤ʧ�ܣ��ϴ��ѱ�����");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			if (this.getStats().files_queued === 0) {
				document.getElementById('btnUpload').disabled = true;
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus("��ȡ���ϴ�");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("��ֹͣ�ϴ�");
			break;
		default:
			progress.setStatus("δ֪����: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}
function uploadComplete(file) {
	if (this.getStats().files_queued === 0) {
		document.getElementById('btnUpload').disabled = true;
		document.getElementById(this.customSettings.cancelButtonId).disabled = true;
	}
}
function queueComplete(numFilesUploaded) {
	var status = document.getElementById("divStatus");
	status.innerHTML = numFilesUploaded + " ���ļ����ϴ�.";
}