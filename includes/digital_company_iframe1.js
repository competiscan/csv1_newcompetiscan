var parent_object_field = 'company';
function getParentObj(pof){
        var parent_text_obj = false;
	if(window.parent && window.parent.document && window.parent.document.forms && window.parent.document.forms.dashboardForm && window.parent.document.forms.dashboardForm[pof]){
		parent_text_obj = window.parent.document.forms.dashboardForm[pof];
	}
	return parent_text_obj;
}
function showCoSel(showall){
	var wholeval = document.forms.companyForm.companylook.value;
	var wholeval2 = '';
	var parent_text_obj = getParentObj(parent_object_field);
	if(parent_text_obj){
		wholeval2 = parent_text_obj.value;
	}
        if(showall==1 || wholeval.length>0 || wholeval2.length>0){
           var satext = '';
		if(showall==1){
			satext = '&showall=1';
		}
		/*if(parent_object_field=='productName'){
			var parent_text_objc = getParentObj('digital_company');
			if(parent_text_objc){
				satext = satext + '&cos='+encodeURIComponent(parent_text_objc.value);
			}
		}*/
		document.getElementById('waitimg').style.visibility = 'visible';
		
		processajax('digital_lookup_company_list1.php', true, 'POST', 'companytext='+encodeURIComponent(wholeval)+'&companysel='+encodeURIComponent(wholeval2)+'&parent_field='+encodeURIComponent(parent_object_field)+satext, document.getElementById('seltext'), 'doResponseCoSel');	
	}
	else{
              hideCoSel();
	}
}		
function hideCoSel(){
	document.getElementById('seltext').style.display = 'none';
}
function doResponseCoSel(response, obj){
	if(response!=''){
		obj.innerHTML = response;
		//my_innerHTML_text(obj,response);
		obj.style.display = 'block';
	}
	else{
		hideCoSel();
	}
	document.getElementById('waitimg').style.visibility = 'hidden';
}
function doCompany(){
	var compobj = document.forms.companyForm['companysel[]'];
        if(compobj){
		var comptext = '';
		
		if(compobj.length){
			for(var i=0;i<compobj.length;i++){
				if(compobj[i].checked){
					if(comptext!=''){
						comptext = comptext + ' or ';
					}
					comptext = comptext + '"' + compobj[i].value + '"';
				}
			}
		}
		else{
			if(compobj.checked){
				if(comptext!=''){
					comptext = comptext + ' or ';
				}
				comptext = comptext + '"' + compobj.value + '"';
			}
		}
		var parent_text_obj = getParentObj(parent_object_field);
		if(parent_text_obj){
			parent_text_obj.value = comptext;
		}
	}
}
function doSel(){
	document.forms.companyForm.companylook.value = '';
	showCoSel(0);
}
function doClear(){
	document.forms.companyForm.companylook.value = '';
	var parent_text_obj = getParentObj(parent_object_field);
	if(parent_text_obj){
		parent_text_obj.value = '';
	}
	showCoSel(0);
}
function doAll(){
	document.forms.companyForm.companylook.value = '';
	showCoSel(1);
}
function doSelAll(){
	if(document.getElementById('seltext').style.display != 'none'){
		var compobj = document.forms.companyForm['companysel[]'];
		if(compobj.length){
			for(var i=0;i<compobj.length;i++){
				compobj[i].checked = true;
			}
		}
		else{
			compobj.checked = true;
		}
		doCompany();
	}
}