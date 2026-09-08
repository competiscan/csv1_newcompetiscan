function chexStart(chexblock,chexstart){
	var chexend = document.form1['chexEnd'+chexblock].value;
	var obj = 0;
	var first_box = true;
	var obj_checked = true;
	for(var i=chexstart;i<chexend;i++){
		obj = document.getElementById('chex'+i);
		if(obj){
			if(first_box){
				if(chexstart==0){
					obj_checked = document.form1.allField.checked;
				}
				else{
					if(obj.checked){
						obj_checked = false;
					}
					else{
						obj_checked = true;
					}
				}
				first_box = false;
			}
			obj.checked = obj_checked;
		}
	}
}
function set_field() {
	var i = 0;
	if(document.form1.allField.checked == true) {
		for(i=1;i<document.form1.elements.length;i++) {
			if(document.form1.elements[i].type == 'checkbox'){
				document.form1.elements[i].checked = true;
			}
		}
	}
	else {
		for(i=1;i<document.form1.elements.length;i++) {
			if(document.form1.elements[i].type == 'checkbox'){
				document.form1.elements[i].checked = false;
			}
		}
	}
}
function unset_all() {
	for(var i=0; i<document.form1.elements.length; i++) {
		if(document.form1.elements[i].type == 'checkbox') {
			if(document.form1.elements[i].checked == false) {
				document.form1.allField.checked = false;
				break;
			}
		}
	}
}
function validate() {
	var field_flag = 0;
	for(var i=0; i<document.form1.elements.length;i++) {
		if(document.form1.elements[i].type == 'checkbox') {
			if(document.form1.elements[i].checked == true) {
				field_flag = 1;
				break;
			}
		}
	}
	if(field_flag == 0) {
		alert('Please select any field');
		return false;
	}
	return true;
}
function unset_all_comp() {
	var allComp = document.form1.getElementById('allCompany');
	allComp.checked = false;
}
function set_company(){
	var comp_chk_bx = document.form1.elements('id');
	var length = comp_chk_bx.length;
	if(document.form1.allCompany.checked == true) {
		for(i=0; i<length; i++) {
			comp_chk_bx[i].checked = true;
		}
	}
	else {
		for(i=0; i<length; i++) {
			comp_chk_bx[i].checked = false;
		}
	}
}
function submitPre(pageval,moreval){
	document.form1.page.value = pageval;
	document.form1.more.value = moreval;
}
function doAddlDiv(idname,linkname){
	var var1 = idname+'_div';
	var var2 = idname;
	
	var obj = document.getElementById(var1);
	if(obj){
		var obj2 = document.getElementById(var2);
		var cur_disp = obj.style.display;
		if(cur_disp!='block'){
			obj.style.display = 'block';
			if(obj2){
				my_innerHTML_text(obj2,'Hide '+linkname);
			}
		}
		else{
			obj.style.display = 'none';
			if(obj2){
				my_innerHTML_text(obj2,'Show '+linkname);
			}
		}
	}
}
function validate2() {
	var field_flag = 0;
	for(var i=0; i<document.form4['field[]'].length;i++) {
		if(document.form4['field[]'][i].checked == true) {
			field_flag = 1;
			break;
		}
	}
	var field_flag2 = 0;
	for(var i=0; i<document.form4['units[]'].length;i++) {
		if(document.form4['units[]'][i].checked == true) {
			field_flag2 = 1;
			break;
		}
	}
	if(field_flag == 0 || field_flag2 == 0) {
		alert('Please select any field');
		return false;
	}
	return true;
}
function doTrendTime(){
	if(document.form3.top_comp_rad[1].checked){
		document.form3.top_comp.disabled = false;
	}
	else{
		document.form3.top_comp.disabled = true;
	}
}
function doBasketSearchChange(formname){
	console.log(formname);
	var eb_date1 = '';
	var eb_date2 = '';
	var eb_date3 = '';
	var eb_gender = '';
	var eb_state = '';
	var eb_age = '';
	var eb_income = '';
	var eb_DMA_ID = '';
	
	if(document.formb){
		for(var i=0;i<document.formb.addedToDatabase.length;i++){
			if(document.formb.addedToDatabase[i].selected){
				eb_date1 = document.formb.addedToDatabase[i].value;
				break;
			}
		}
		for(var i=0;i<document.formb.month1.length;i++){
			if(document.formb.month1[i].selected){
				eb_date2 = document.formb.month1[i].value;
				break;
			}
		}
		for(var i=0;i<document.formb.month2.length;i++){
			if(document.formb.month2[i].selected){
				eb_date3 = document.formb.month2[i].value;
				break;
			}
		}
		for(var i=0;i<document.formb.gender.length;i++){
			if(document.formb.gender[i].checked){
				eb_gender = document.formb.gender[i].value;
				break;
			}
		}
		for(i=0;i<document.formb['state[]'].length;i++){
			if(document.formb['state[]'].options[i].selected){
				if(eb_state!=''){
					eb_state = eb_state + ',';
				}
				eb_state = eb_state + document.formb['state[]'].options[i].value;
			}
		}
		for(i=0;i<document.formb['age[]'].length;i++){
			if(document.formb['age[]'].options[i].selected){
				if(eb_age!=''){
					eb_age = eb_age + ',';
				}
				eb_age = eb_age + document.formb['age[]'].options[i].value;
			}
		}
		for(i=0;i<document.formb['income[]'].length;i++){
			if(document.formb['income[]'].options[i].selected){
				if(eb_income!=''){
					eb_income = eb_income + ',';
				}
				eb_income = eb_income + document.formb['income[]'].options[i].value;
			}
		}
		for(i=0;i<document.formb['DMA_ID[]'].length;i++){
			if(document.formb['DMA_ID[]'].options[i].selected){
				if(eb_DMA_ID!=''){
					eb_DMA_ID = eb_DMA_ID + ',';
				}
				eb_DMA_ID = eb_DMA_ID + document.formb['DMA_ID[]'].options[i].value;
			}
		}
		
		document[formname].eb_date1.value = eb_date1;
		document[formname].eb_date2.value = eb_date2;
		document[formname].eb_date3.value = eb_date3;
		document[formname].eb_gender.value = eb_gender;
		document[formname].eb_state.value = eb_state;
		document[formname].eb_age.value = eb_age;
		document[formname].eb_income.value = eb_income;
		document[formname].eb_DMA_ID.value = eb_DMA_ID;
	}
}
function validateMonth() {
	var month1 = document.forms.formb.month1;
	var month2 = document.forms.formb.month2;
	var addToDB = document.forms.formb.addedToDatabase;
	if(month1.selectedIndex != 0 || month2.selectedIndex != 0) {
		addToDB.disabled = true;
	}
	else{
		addToDB.disabled = false;
	}
	if(addToDB.selectedIndex != 0) {
		month1.disabled = true;
		month2.disabled = true;
	}
	else {
		month1.disabled = false;
		month2.disabled = false;
	}
	
	if(month1.disabled == false) {
		var month1indx = month1.selectedIndex;
		var month1val = month1.options[month1indx].value;
		var month2indx = month2.selectedIndex;
		var month2val = month2.options[month2indx].value;
		
		if(month1val!='' && month2val!=''){
			var month1_arr = month1val.split("-");
			var month2_arr = month2val.split("-");
			var month1comp1 = parseInt(month1_arr[0]+''+month1_arr[1]);
			var month1comp2 = parseInt(month2_arr[0]+''+month2_arr[1]);
			
			if(month1comp1 > month1comp2) {
				alert('Second month cannot be less than first month');
				month2.selectedIndex = month1.selectedIndex;
				document.formb.month2.focus();
			}
		}
	}
}
function in_array(val,ar){
	for(var i=0;i<ar.length;i++){
		if(val==ar[i]){
			return i;
		}
	}
	return -1;
}
function populateTemplate(){
	var template_id = document.tnameForm.tid.options[document.tnameForm.tid.selectedIndex].value;
	if(template_id!=0){
		document.tnameForm.deletet.style.display = 'inline';
	}
	else{
		document.tnameForm.deletet.style.display = 'none';
	}
	document.tnameForm.templateName.value = document.tnameForm.tid.options[document.tnameForm.tid.selectedIndex].text;
	var templateFileType_val = document.tnameForm['tft'+template_id].value;
	var templateCoices_array = document.tnameForm['tc'+template_id].value.split(',');
	
	for(var i=0;i<document.form1.file_choice.length;i++){
		if(document.form1.file_choice[i].value==templateFileType_val){
			document.form1.file_choice[i].checked = true;
			break;
		}
	}
	for(i=1;i<document.form1.elements.length;i++) {
		if(document.form1.elements[i].type == 'checkbox'){
			if(in_array(document.form1.elements[i].value,templateCoices_array)!=-1){
				document.form1.elements[i].checked = true;
			}
			else{
				document.form1.elements[i].checked = false;
			}
		}
	}
	
}
function getTemplateChoices(){
	var file_choice_val = 0;
	for(var i=0;i<document.form1.file_choice.length;i++){
		if(document.form1.file_choice[i].checked){
			file_choice_val = document.form1.file_choice[i].value;
			break;
		}
	}
	document.tnameForm.templateFileType.value = file_choice_val;
	
	var tchoices = '';
	for(i=1;i<document.form1.elements.length;i++) {
		if(document.form1.elements[i].type == 'checkbox' && document.form1.elements[i].checked){
			if(tchoices!=''){
				tchoices = tchoices + ',';
			}
			tchoices = tchoices + document.form1.elements[i].value;
		}
	}
	document.tnameForm.templateCoices.value = tchoices;
}