function check(){
	var obj=document.form1.to;
	if(obj.value!=''){
		return true;
	}
	alert("Please enter at least one email address");
	return false;
}
var send_types = new Array('to','cc','bcc');
var is_show = false;
function showLook(){
	var obj = document.getElementById('emtext');
	var obj2 = document.getElementById('showhide');
	if(obj){
		var ltext = '';
		if(is_show){
			obj.style.display = 'none';
			is_show = false;
			ltext = 'Show Address Lookup';
			if(top.frames && top.frames['elist'] && top.frames['elist'].document && top.frames['elist'].document.forms && top.frames['elist'].document.forms.emailForm && top.frames['elist'].document.forms.emailForm.emaillook){
				top.frames['elist'].document.forms.emailForm.emaillook.value = '';
			}
			document.forms.form1.to.focus();
		}
		else{
			obj.style.display = 'block';
			is_show = true;
			ltext = 'Hide Address Lookup';
			
			if(top.frames && top.frames['elist'] && top.frames['elist'].document){
				top.frames['elist'].doSel();
			}
			
			window.setTimeout('doFocus()', 500);	
		}
		my_innerHTML_text(obj2,ltext);
	}
}
function doFocus(){
	if(top.frames && top.frames['elist'] && top.frames['elist'].document && top.frames['elist'].document.forms && top.frames['elist'].document.forms.emailForm && top.frames['elist'].document.forms.emailForm.emaillook){
		top.frames['elist'].document.forms.emailForm.emaillook.focus();
	}
}
function checkLookup(){
	if(is_show && top.frames && top.frames['elist'] && top.frames['elist'].document){
		top.frames['elist'].doSel();
	}
	checkSaveLink();
}
function saveList(emailto_idval,savenameval,to,cc,bcc){
	var send_types_vals = new Array();
	if(to=='' && cc=='' && bcc=='') {
		for(var j=0;j<send_types.length;j++){
			send_types_vals[send_types[j]] = document.forms.form1[send_types[j]].value;
		}
	}
	else{
		send_types_vals['to'] = to;
		send_types_vals['cc'] = cc;
		send_types_vals['bcc'] = bcc;
	}
	for(var j=0;j<send_types.length;j++){
		document.forms.saveform[send_types[j]].value = send_types_vals[send_types[j]];
	}
	document.forms.saveform.emailto_id.value = emailto_idval;
	document.forms.saveform.savename.value = savenameval;
	var posobj = document.getElementById('showhide');//save_list_id
	var obj = document.getElementById('showbox_save');
	obj.style.left = (findPosX(posobj))+'px';
	obj.style.top = (findPosY(posobj))+'px';
	obj.style.display = 'block';
	document.saveform.savename.focus();
}
function cancelForm(){
	hideBlock(document.getElementById('showbox_save'));
	document.saveform.reset();
}
function checkSaveLink(){
	if(document.forms.form1.to.value.length>0){
		document.getElementById('show_save').style.visibility = 'visible';
	}
	else {
		document.getElementById('show_save').style.visibility = 'hidden';
	}
}
function saveName(){
	var toval = '';
	for(var j=0;j<send_types.length;j++){
		toval = toval + '&email'+send_types[j]+'_list=' + encodeURIComponent(document.forms.saveform[send_types[j]].value);
	}
	var nameval = document.forms.saveform.savename.value;
	var emailto_idval = document.forms.saveform.emailto_id.value;
	cancelForm();
	var done = processajax('email_list_save.php', false, 'POST', 'emailto_name='+encodeURIComponent(nameval)+'&emailto_id='+escape(emailto_idval)+toval, '', '');
	if(done){
		if(is_show && top.frames && top.frames['elist'] && top.frames['elist'].document){
			top.frames['elist'].showList();
		}
	}
}