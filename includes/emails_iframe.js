var send_types = new Array('to','cc','bcc');
function showEmSel(showall){
	var wholeval = document.forms.emailForm.emaillook.value;
	var wholeval2 = '';
	var satext = '';
	var tid = '1';
	if(window.parent && window.parent.document && window.parent.document.forms && window.parent.document.forms.form1){
		if(window.parent.document.forms.form1.tid){
			tid = window.parent.document.forms.form1.tid.value;
		}
		for(var j=0;j<send_types.length;j++){
			if(window.parent.document.forms.form1[send_types[j]]){
				satext = satext + '&emailsel'+send_types[j]+'='+encodeURIComponent(window.parent.document.forms.form1[send_types[j]].value)
			}
		}
	}
	if(showall==1 || wholeval.length>0 || satext.length>0 || showall==2){
		if(showall==1){
			satext = satext + '&showall=1';
		}
		else if(showall==2){
			satext = satext + '&showlist=1';
		}
		
		document.getElementById('waitimg').style.visibility = 'visible';
		
		processajax('email_list.php', true, 'POST', 'tid='+tid+'&emailtext='+encodeURIComponent(wholeval)+satext, document.getElementById('seltext'), 'doResponseEmSel');	
	}
	else{
		hideEmSel();
	}
}
function hideEmSel(){
	document.getElementById('seltext').style.display = 'none';
}
function doResponseEmSel(response, obj){
	if(response!=''){
		obj.innerHTML = response;
		//my_innerHTML_text(obj,response);
		obj.style.display = 'block';
	}
	else{
		hideEmSel();
	}
	document.getElementById('waitimg').style.visibility = 'hidden';
}
function doEmails(){
	for(var j=0;j<send_types.length;j++){
		var emobj = document.forms.emailForm['emailsel'+send_types[j]+'[]'];
		if(emobj){
			var emtext = '';
			
			if(emobj.length){
				for(var i=0;i<emobj.length;i++){
					if(emobj[i].checked){
						if(emtext!=''){
							emtext = emtext + ',';
						}
						emtext = emtext + emobj[i].value;
					}
				}
			}
			else{
				if(emobj.checked){
					if(emtext!=''){
						emtext = emtext + ',';
					}
					emtext = emtext + emobj.value;
				}
			}
			
			if(window.parent && window.parent.document && window.parent.document.forms && window.parent.document.forms.form1 && window.parent.document.forms.form1[send_types[j]]){
				window.parent.document.forms.form1[send_types[j]].value = emtext;
				window.parent.checkSaveLink();
			}
		}
	}
}
function doSavedEmails(to,cc,bcc){
	var send_types_vals = new Array();
	send_types_vals['to'] = to;
	send_types_vals['cc'] = cc;
	send_types_vals['bcc'] = bcc;
	for(var j=0;j<send_types.length;j++){
		if(window.parent && window.parent.document && window.parent.document.forms && window.parent.document.forms.form1 && window.parent.document.forms.form1[send_types[j]]){
			window.parent.document.forms.form1[send_types[j]].value = send_types_vals[send_types[j]];
			window.parent.checkSaveLink();
		}
	}
}
function doSel(){
	document.forms.emailForm.emaillook.value = '';
	showEmSel(0);
}
function doClear(){
	document.forms.emailForm.emaillook.value = '';
	for(var j=0;j<send_types.length;j++){
		if(window.parent && window.parent.document && window.parent.document.forms && window.parent.document.forms.form1 && window.parent.document.forms.form1[send_types[j]]){
			window.parent.document.forms.form1[send_types[j]].value = '';
			window.parent.checkSaveLink();
		}
	}
	showEmSel(0);
}
function doAll(){
	document.forms.emailForm.emaillook.value = '';
	showEmSel(1);
}
function showList(){
	document.forms.emailForm.emaillook.value = '';
	showEmSel(2);
}
function doSelAll(){
	if(document.getElementById('seltext').style.display != 'none'){
		var emobj = document.forms.emailForm['emailselto[]'];
		if(emobj.length){
			for(var i=0;i<emobj.length;i++){
				emobj[i].checked = true;
			}
		}
		else{
			emobj.checked = true;
		}
		doEmails();
	}
}
function deleteName(emailto_id){
	document.getElementById('waitimg').style.visibility = 'visible';
	var done = processajax('email_list_del.php', false, 'POST', 'emailto_id='+emailto_id, '', '');
	if(done){
		showList();
	}
}
function iframe_saveList(emailto_idval,savenameval,to,cc,bcc){
	if(window.parent && window.parent.document){
		window.parent.saveList(emailto_idval,savenameval,to,cc,bcc);
	}
}