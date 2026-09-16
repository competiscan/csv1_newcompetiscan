<script type="text/javascript" src="js_calendar/calendar.js"></script>
<!-- ###########  Communication Type Implementation ############ -->
<script type="text/javascript" src="https://www.competiscan.com/admin/jquery.min.js"></script>
<!-- ###########  Communication Type Implementation ############ -->
<script type="text/javascript">
<!--
<?php echo $javascript; ?>
function doPDFText(pid) {
	var winy = window.open("<?php if(!$fromtemp) echo '../'; ?>pdfContentDetail.php?id="+pid,null,"scrollbars=yes, resizable=yes, height=450,width=650,status=yes,toolbar=no,menubar=no,location=no,addressbar=yes");
	winy.focus();
}
function doPDFSample(pid) {
	var winy = window.open("<?php if($fromtemp) echo 'admin/'; ?>managepdfSample.php?productID="+pid,null,"scrollbars=yes, resizable=yes, height=450,width=650,status=yes,toolbar=no,menubar=no,location=no,addressbar=yes");
	winy.focus();
}
var cpanchanged = false;
function doPCopy(pid,dodel) {
	var deltxt = '&del=';
	if(dodel){
		deltxt = deltxt + '1';
	}
	else{
		deltxt = deltxt + '0';
	}
	if(cpanchanged){
		deltxt = deltxt + '&change=1';
	}
	var loc = "<?php if($fromtemp) echo 'admin/'; ?>panelist_copy.php?productID="+pid+deltxt;
	if(cpanchanged){
		document.prodForm.pcopy_pop.value = loc;
		document.prodForm.productStatus.value = document.prodForm.old_productStatus.value;
		document.prodForm.submit();
	}
	else{
		var winy = window.open(loc,null,"scrollbars=yes, resizable=yes, height=160,width=450,status=yes,toolbar=no,menubar=no,location=no,addressbar=yes");
		winy.focus();
	}
}
function showHideImage() {
	var imgControl = MM_findObj("prod_img_div");
	var showHideLabel = MM_findObj("showHide");
	
	if(showHideLabel.innerHTML=="Hide Image") {
		showHideLabel.innerHTML = "Show Image";
		imgControl.style.display = "none";
	}
	else {
		showHideLabel.innerHTML = "Hide Image";
		imgControl.style.display = "block";
	}
}
function doProductImg(cid){ 
	var imgImg = document.images["prod_img"];
	var imgControl = MM_findObj("prod_img_div");
	var showHideLabel = MM_findObj("showHide");
	
	imgImg.src = '<?php if(!$fromtemp) echo '../'; ?>productImg.php?cid='+cid;
        
	document.prodForm.img_companyID.value = cid;
	showHideLabel.innerHTML = "Hide Image";
	imgControl.style.display = "block";
}

function checkHeadline(pid) {
	var winy = window.open("<?php if($fromtemp) echo 'admin/'; ?>check_headline.php?hl="+encodeURIComponent(document.prodForm.productHeadline.value)+'&pid='+pid,null,"height=350,width=650,status=yes,toolbar=no,menubar=no,location=no,addressbar=yes");
	winy.focus();
}
function doSaved(choice){
	if(choice==4 && document.prodForm.productComment.value==''){
		alert('Please add a Product Comment');
		document.prodForm.productComment.focus();
		return false;
	}
	document.prodForm.productStatus.value = choice;
	return true;
}
var companyWV = new Array();
var companyWV_id = new Array();
<?php
	$sql = "SELECT companyName,companyID FROM cscan_company WHERE isWorksiteVoluntary=1";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_array($rs)) {
		echo "companyWV[companyWV.length] = '".singleQuoteSafe($row[0])."';\n";
		echo "companyWV_id[companyWV_id.length] = $row[1];\n";
	}
?>
function checkCompanyWV(){
	var companyval = document.prodForm.company.value = trimspace(document.prodForm.company.value);
	if(in_array(companyval,companyWV)!=-1){
		document.prodForm.worksiteVoluntary.checked = true;
	}
}
function checkCompanyWV_id(companyid){
	if(in_array(companyid,companyWV_id)!=-1){
		document.prodForm.worksiteVoluntary.checked = true;
	}
}
var sectorWV = new Array();
<?php
	$sql = "SELECT sectorID FROM cscan_sector WHERE sectorWorksiteVoluntary=1";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_array($rs)) {
		echo "sectorWV[sectorWV.length] = '".$row[0]."';\n";
	}
?>
function checkSectorWV(fieldname){
	var selectedSubCatArray = returnSCSC(fieldname);
	for(var j=0;j<selectedSubCatArray.length;j++){
		if(in_array(selectedSubCatArray[j],sectorWV)!=-1){
			if(!document.prodForm.worksiteVoluntary.checked && confirm('Select Worksite/Voluntary?')){
				document.prodForm.worksiteVoluntary.checked = true;
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
function check_insurance() {
	var id = document.prodForm['agentCommunicationID[]'];
	var unselected = false;
	if(document.prodForm.mPanelID.value!="4" && document.prodForm.mPanelID.value!="0") {
		for(var i=0;i<id.options.length;i++) {
			if(id.options[i].selected){
				id.options[i].selected = false;
				unselected = true;
			}
		}
		if(unselected){
			alert("Agent Communication Type is only available for Insurance Producer mailing panel type");
		}
	}
}
function check_BA() {
	var is_brand = false;
	var selectedSubCatArray = returnSCSC(3);
	if(in_array('67',selectedSubCatArray)!=-1 || in_array('78',selectedSubCatArray)!=-1){
		is_brand = true;
	}
	if(is_brand){
		var id = document.prodForm['agentCommunicationID[]'];
		for(var i=0;i<id.options.length;i++) {
			if(id.options[i].value=='27'){
				id.options[i].selected = is_brand;
				break;
			}
		}
	}
}
function getBlock(sid,dis){
	var obj = false;
	obj = MM_findObj("div_"+sid);
	if(obj){
		if(dis){
			obj.style.display = 'block';
			document.prodForm['part_'+sid].value = 1;
		}
		else{
			obj.style.display = 'none';
			document.prodForm['part_'+sid].value = 0;
		}
	}
}
function getCat(){
	var sectordoc = document.forms.prodForm['sectorID[]'];
	if(document.forms.prodForm['categoryID[]']){
		var catdoc = document.forms.prodForm['categoryID[]'];
		var sid = 0;//sectordoc.options[sectordoc.selectedIndex].value;
		var selectedArray = new Array();
		for(var j=0;j<sectordoc.options.length;j++){
			if(sectordoc.options[j].selected){
				selectedArray[selectedArray.length] = sectordoc.options[j].value;
				getBlock(sectordoc.options[j].value,true);
			}
			else{
				getBlock(sectordoc.options[j].value,false);
			}
		}
		
		var selectedCatArray = new Array();
		for(var j=0;j<catdoc.options.length;j++){
			if(catdoc.options[j].selected){
				selectedCatArray[selectedCatArray.length] = catdoc.options[j].value;
			}
			getBlock(catdoc.options[j].value,false);
		}
		
		catdoc.options.length = 0;
		var dummySort = new Array();
		var dummyData = new Array();
		var optiontext = 'Select';
		var isSel = true;
		for(var k=0;k<selectedArray.length;k++){
			sid = selectedArray[k];
			if(sectorArray[sid]){
				for(var i in sectorArray[sid]){
					optiontext = sectorArray[sid][i];
					if(in_array(i,selectedCatArray)!=-1){
						isSel = true;
						getBlock(i,true);
					}
					else {
						isSel = false;
					}
					dummySort[dummySort.length] = optiontext;
					dummyData[dummyData.length] = new Array(optiontext, i, false, isSel);
				}
			}
		}
		dummySort.sort();
		for(var j=0;j<dummySort.length;j++){
			for(var n=0;n<dummyData.length;n++){
				if(dummySort[j]==dummyData[n][0]){
					catdoc.options[j] = new Option(dummyData[n][0], dummyData[n][1], dummyData[n][2], dummyData[n][3]);
					break;
				}
			}
		}		
		getSubCat();
		dependsSector();
		checkDeps_s();
		checkProductName();
	}
}
function getSubCat(){
	var catdoc = document.forms.prodForm['categoryID[]'];
	var subcatdoc = document.forms.prodForm['subCategoryID[]'];
	var cid = 0;//catdoc.options[catdoc.selectedIndex].value;
	var selectedArray = new Array();
	for(var j=0;j<catdoc.options.length;j++){
		if(catdoc.options[j].selected){
			selectedArray[selectedArray.length] = catdoc.options[j].value;
			getBlock(catdoc.options[j].value,true);
		}
		else{
			getBlock(catdoc.options[j].value,false);
		}
	}
	
	var selectedSubCatArray = new Array();
	for(var j=0;j<subcatdoc.options.length;j++){
		if(subcatdoc.options[j].selected){
			selectedSubCatArray[selectedSubCatArray.length] = subcatdoc.options[j].value;
		}
	}
	
	subcatdoc.options.length = 0;
	var dummySort = new Array();
	var dummyData = new Array();
	var optiontext = 'Select';
	var isSel = true;
	for(var k=0;k<selectedArray.length;k++){
		cid = selectedArray[k];
		if(categoryArray[cid]){
			for(var i in categoryArray[cid]){
				optiontext = categoryArray[cid][i];
				if(in_array(i,selectedSubCatArray)!=-1){
					isSel = true;
				}
				else {
					isSel = false;
				}
				dummySort[dummySort.length] = optiontext;
				dummyData[dummyData.length] = new Array(optiontext, i, false, isSel);
			}
		}
	}
	dummySort.sort();
	for(var j=0;j<dummySort.length;j++){
		for(var n=0;n<dummyData.length;n++){
			if(dummySort[j]==dummyData[n][0]){
				subcatdoc.options[j] = new Option(dummyData[n][0], dummyData[n][1], dummyData[n][2], dummyData[n][3]);
				break;
			}
		}
	}
	doscsc_sort();
}
if(checkIE6()){
	document.write('<iframe id="ieframe" src="javascript:\'<html><head><title><\/title><\/head><body>&nbsp;<\/body><\/html>\';" scrolling="no" frameborder="0" style="display:none;position:absolute;border:solid 1px #ffffff;background:#0055E3;padding:4px;color:#ffffff;z-index:99;"><\/iframe>');
}
function showPiece(wholeobj,idsobj,idsname,filename,divname){
	var wholeval = wholeobj.value;
	if(wholeval.length>0){
		var idsval = idsobj.value;
		processajax(filename, true, 'POST', idsname+'='+escape(idsval)+'&findval='+encodeURIComponent(wholeval), document.getElementById(divname), 'doResponsePs');
	}
	else{
		hideDiv_inner(divname);
	}
}
function showPans(){
	showPiece(document.forms.selform.pan_id,document.forms.prodForm.competi_ids,'competi_ids','<?php if($fromtemp) echo 'admin/'; ?>panelists.php','showbox_pans');
}
function showPubs(){
	showPiece(document.forms.pub_selform.pub_id,document.forms.prodForm.pub_ids,'pub_ids','<?php if($fromtemp) echo 'admin/'; ?>piece.php','showbox_pubs');
}
function showCmps(){
	showPiece(document.forms.cmp_selform.cmp_id,document.forms.prodForm.cmp_ids,'cmp_ids','<?php if($fromtemp) echo 'admin/'; ?>piece.php','showbox_cmps');
}
function showAffs(){
	showPiece(document.forms.aff_selform.aff_id,document.forms.prodForm.aff_ids,'aff_ids','<?php if($fromtemp) echo 'admin/'; ?>piece.php','showbox_affs');
}
function showCPNs(){
	showPiece(document.forms.prodForm.co_productName,document.forms.prodForm.cmp_ids,'cpn_ids','<?php if($fromtemp) echo 'admin/'; ?>piece.php','showbox_cpns');
}
function hideDiv_inner(ename){
	document.getElementById(ename).style.display = 'none';
}
function doResponsePs(response, obj){
	if(response!=''){
		obj.innerHTML = response;
		//my_innerHTML_text(obj,response);
		obj.style.display = 'block';
	}
	else{
		obj.style.display = 'none';
	}
}
function hideCpan(){
	<?php 
	if($fromtemp) {
		echo 'return true;';
	}
	else {
	?>
	var obj = document.getElementById('cpan');
	if(obj){
		obj.style.display = 'none';
	}
	<?php 
	}
	?>
}
function removePiece(obj,obj2,idval){
	obj.parentNode.removeChild(obj);
	
	var newval = '';
	var idsval = obj2.value;
	var valArray = idsval.split(',');
	for(var i=0;i<valArray.length;i++){
		if(valArray[i]!=idval){
			if(newval.length>0){
				newval = newval + ',';
			}
			newval = newval + valArray[i];
		}
	}
	obj2.value = newval;
}
function sortCmp(idval,sort){
	var newval = '';
	var obj = document.getElementById('cmps');
	var obj2 = document.forms.prodForm.cmp_ids;
	var idsval = obj2.value;
	var valArray = idsval.split(',');
	for(var i=0;i<valArray.length;i++){
		if(valArray[i]==idval){
			if(valArray[i+sort]){
				var tmpidval = valArray[i];
				valArray[i] = valArray[i+sort];
				valArray[i+sort] = tmpidval;
				var movenode = obj.removeChild(obj.childNodes[i]);
				if(i+sort>=obj.childNodes.length){
					obj.appendChild(movenode);
				}
				else{
					obj.insertBefore(movenode,obj.childNodes[i+sort]);
				}
				break;
			}
		}
	}
	for(var i=0;i<valArray.length;i++){
		if(newval.length>0){
			newval = newval + ',';
		}
		newval = newval + valArray[i];
	}
	obj2.value = newval;
}
function removeCmp(cmp_id){
	var obj = document.getElementById('cmp'+cmp_id);
	var obj2 = document.forms.prodForm.cmp_ids;
	removePiece(obj,obj2,cmp_id);
	checkProductName();
	removeCompanyFields(cmp_id);
}
function removeAff(aff_id){
	var obj = document.getElementById('aff'+aff_id);
	var obj2 = document.forms.prodForm.aff_ids;
	removePiece(obj,obj2,aff_id);
}
function removePub(pub_id,pub_date){
	var obj = document.getElementById('pub'+pub_id+'_'+pub_date);
	var obj2 = document.forms.prodForm.pub_ids;
	removePiece(obj,obj2,pub_id+'|'+pub_date);
}
function closePanEdit(){
	var panelist_id = document.forms.ivform_edit.panelist_id.value;
	var ppdate = document.forms.ivform_edit.ppdate.value;
	var pdid = document.forms.ivform_edit.pdid.value;
	var competi_id = document.forms.ivform_edit.competi_id.value;
	var old_invitationID = document.forms.ivform_edit.old_invitationID.value;
	var old_trackingID = document.forms.tform_edit.old_trackingID.value;
	var old_pproductFICO = document.forms.fform_edit.old_pproductFICO.value;
	var objd = document.selform_date_edit;
	var dateid = objd.ymd.value.substring(0,4)+objd.ymd.value.substring(5,7)+objd.ymd.value.substring(8,10);
	var invitationID = document.ivform_edit.invitationID.value;
	var trackingID = document.tform_edit.trackingID.value;
	var pproductFICO = document.fform_edit.pproductFICO.value;
	if(old_invitationID!=invitationID || pdid.substring(0,8)!=dateid || old_trackingID!=trackingID || old_pproductFICO!=pproductFICO){
		removePan(panelist_id,ppdate,pdid,false);
		addPanOnly(panelist_id,competi_id,true);
	}
	
	document.getElementById('showbox_pans_edit').style.display = 'none';
	document.forms.ivform_edit.panelist_id.value = '';
	document.forms.ivform_edit.ppdate.value = '';
	document.forms.ivform_edit.pdid.value = '';
	document.forms.ivform_edit.competi_id.value = '';
	document.forms.ivform_edit.old_invitationID.value = '';
	document.forms.ivform_edit.invitationID.value = '';
	document.forms.tform_edit.old_trackingID.value = '';
	document.forms.tform_edit.trackingID.value = '';
	document.forms.fform_edit.old_pproductFICO.value = '';
	document.forms.fform_edit.pproductFICO.value = '';
}

function editPan(panelist_id,ppdate,pdid,competi_id)
{
	document.forms.ivform_edit.panelist_id.value = panelist_id;
	document.forms.ivform_edit.ppdate.value = ppdate;
	document.forms.ivform_edit.pdid.value = pdid;
	document.forms.ivform_edit.competi_id.value = competi_id;
	
    var panel_edit_popup = document.getElementById('showbox_pans_edit');
    var add_panelist_link = document.getElementById('addpan');
    var panelist_box_width = document.getElementById('pans_scroll').offsetWidth;
    panel_edit_popup.style.left = (findPosX(add_panelist_link)+panelist_box_width/1.2)+'px';
    panel_edit_popup.style.top = (findPosY(add_panelist_link))+'px';
    panel_edit_popup.style.display = 'block';

	var invtext = '';
	var tratext = '';
	var fictext = '';
	var competi_idsval = document.forms.prodForm.competi_ids.value;
	var invitation_idsval = document.forms.prodForm.invitation_ids.value;
	var tracking_idsval = document.forms.prodForm.tracking_ids.value;
	var fico_idsval = document.forms.prodForm.fico_ids.value;
	
	var valArray = competi_idsval.split(',');
	var valArray3 = invitation_idsval.split('|');
	var valArray4 = tracking_idsval.split('|');
	var valArray5 = fico_idsval.split('|');
	for(var i in valArray){
		var valArray2 = valArray[i].split('|');
		if(valArray2[0]==panelist_id && valArray2[1]==ppdate){
			invtext = valArray3[i];
			tratext = valArray4[i];
			fictext = valArray5[i];
			break;
		}
	}
	document.forms.ivform_edit.invitationID.value = invtext;
	document.forms.ivform_edit.old_invitationID.value = invtext;
	document.forms.tform_edit.trackingID.value = tratext;
	document.forms.tform_edit.old_trackingID.value = tratext;
	document.forms.fform_edit.pproductFICO.value = fictext;
	document.forms.fform_edit.old_pproductFICO.value = fictext;
	
	var m = pdid.substring(4,6);
	var d = pdid.substring(6,8);
	var y = pdid.substring(0,4);
	var obj4 = document.selform_date_edit;
	obj4.ymd.value = y+'-'+m+'-'+d;
}

function removePan(panelist_id,ppdate,pdid,dopanifo){
	var obj = document.getElementById('pan'+panelist_id+'_'+pdid);
	obj.parentNode.removeChild(obj);
	
	var newval = '';
	var newval3 = '';
	var newval4 = '';
	var newval5 = '';
	var competi_idsval = document.forms.prodForm.competi_ids.value;
	var invitation_idsval = document.forms.prodForm.invitation_ids.value;
	var tracking_idsval = document.forms.prodForm.tracking_ids.value;
	var fico_idsval = document.forms.prodForm.fico_ids.value;
	var valArray = competi_idsval.split(',');
	var valArray3 = invitation_idsval.split('|');
	var valArray4 = tracking_idsval.split('|');
	var valArray5 = fico_idsval.split('|');
	for(var i in valArray){
		var valArray2 = valArray[i].split('|');
		if(valArray2[0]!=panelist_id || valArray2[1]!=ppdate){
			if(newval.length>0){
				newval = newval + ',';
			}
			newval = newval + valArray[i];
			newval3 = newval3 + valArray3[i] + '|';
			newval4 = newval4 + valArray4[i] + '|';
			newval5 = newval5 + valArray5[i] + '|';
		}
	}
	document.forms.prodForm.competi_ids.value = newval;
	document.forms.prodForm.invitation_ids.value = newval3;
	document.forms.prodForm.tracking_ids.value = newval4;
	document.forms.prodForm.fico_ids.value = newval5;
	cpanchanged = true;//hideCpan();
	
	if(dopanifo && panelistInfoArray[panelist_id]){
		var pgender = panelistInfoArray[panelist_id][0];
		var page = panelistInfoArray[panelist_id][1];
		var pincome = panelistInfoArray[panelist_id][2];
		var pstate = panelistInfoArray[panelist_id][3];
		
		if(pgender=='M' || pgender=='F'){
			genderArray[pgender] = genderArray[pgender] - 1;
			var newgender = 'N';
			if(genderArray['M']>0 && genderArray['F']>0){
				newgender = 'B';
			}
			else if(genderArray['M']>0){
				newgender = 'M';
			}
			else if(genderArray['F']>0){
				newgender = 'F';
			}
			for(i=0;i<document.forms.prodForm.gender.length;i++){
				if(document.forms.prodForm.gender[i].value==newgender){
					document.forms.prodForm.gender[i].checked = true;
					break;
				}
			}
		}
		for(i=0;i<document.forms.prodForm['age[]'].length;i++){
			if(page==document.forms.prodForm['age[]'].options[i].value){
				if(checkpanelistInfoArray(panelist_id,1,page)){
					document.forms.prodForm['age[]'].options[i].selected = false;
				}
				break;
			}
		}
		for(i=0;i<document.forms.prodForm['incomeID[]'].length;i++){
			if(pincome==document.forms.prodForm['incomeID[]'].options[i].value){
				if(checkpanelistInfoArray(panelist_id,2,pincome)){
					document.forms.prodForm['incomeID[]'].options[i].selected = false;
				}
				break;
			}
		}
		for(i=0;i<document.forms.prodForm['state[]'].length;i++){
			if(pstate==document.forms.prodForm['state[]'].options[i].value){
				if(checkpanelistInfoArray(panelist_id,3,pstate)){
					document.forms.prodForm['state[]'].options[i].selected = false;
				}
				break;
			}
		}
		panelistInfoArray[panelist_id] = new Array('N',-1,-1,-1);
	}
}
function checkpanelistInfoArray(panelist_id,ind,val){
	for(var p in panelistInfoArray){
		if(p!=panelist_id && panelistInfoArray[p][ind]==val){
			return false;
		}
	}
	return true;
}
function markInsuranceexchange(){
	if(document.forms.prodForm.is_insuranceexchange.value==1){
		for(var i=0;i<document.forms.prodForm.offerOrigin.length;i++){
			if(document.forms.prodForm.offerOrigin.options[i].value==4){
				document.forms.prodForm.offerOrigin.options[i].selected = true;
				document.forms.prodForm.is_insuranceexchange.value = 2;
				break;
			}
		}
	}
}
function checkInsuranceexchange(){
	var oo = '';
	if(document.forms.prodForm.offerOrigin.selectedIndex>0){
		oo = document.forms.prodForm.offerOrigin.options[document.forms.prodForm.offerOrigin.selectedIndex].value;
	}
	if(oo==4 && document.forms.prodForm.is_insuranceexchange.value==0){
		if(!confirm('Insurance Exchange Company?')){
			document.forms.prodForm.offerOrigin.selectedIndex = 0;
		}
	}
}
function addPiece(idval,nameval,typeval,obj2,obj3,flag1){
	var obj = document.getElementById(typeval+'s');
	var insert = true;
	var obj4 = false;
	
	if(idval==0){
		nameval = obj2.value;
		idval = processajax('<?php if($fromtemp) echo 'admin/'; ?>piece.php', false, 'POST', typeval+'_name='+encodeURIComponent(nameval), false, '');
	}
	var newval = idval;
	
	if(typeval=='pub'){
		obj4 = document.pform;
		nameval = nameval + ' [' + obj4.ymd.value + ']';
		newval = newval+'|'+obj4.ymd.value.substring(0,4)+obj4.ymd.value.substring(5,7)+obj4.ymd.value.substring(8,10);
	}
	var checkval = newval;
	
	var oldval = '';
	var idsval = obj3.value;
	var valArray = idsval.split(',');
	for(var i=0;i<valArray.length;i++){
		if(valArray[i]==checkval){
			insert = false;
		}
		else if(valArray[i]!=''){
			if(oldval.length>0){
				oldval = oldval + ',';
			}
			oldval = oldval + valArray[i];
		}
	}
	
	if(insert){
		if(oldval.length>0){
			newval = oldval + ',' + newval;
		}
		var newnode = document.createElement('div');
		newnode.id = typeval+idval;
		if(typeval=='pub'){
			newnode.id = newnode.id+'_'+obj4.ymd.value.substring(0,4)+obj4.ymd.value.substring(5,7)+obj4.ymd.value.substring(8,10);
			
			var e_val = '';
			var e_obj = false;
			if(document.prodForm.entryID){
				var old_entryID = document.forms.prodForm.old_entryID.value;
				if(old_entryID==''){
					e_obj = document.prodForm.entryID;
					e_val = document.prodForm.entryID.value.substring(0,10);
				}
			}
			else if(document.prodForm.firstSeen){
				e_obj = document.prodForm.firstSeen;
				e_val = document.prodForm.firstSeen.value;
			}
			if(e_val!=''){
				var dateval = obj4.ymd.value.substring(0,4)+'-'+obj4.ymd.value.substring(5,7)+'-'+obj4.ymd.value.substring(8,10);
				if(dateval<e_val){
					e_obj.value = dateval;
				}
			}
		}
		newnode.appendChild(document.createTextNode(nameval));
		<?php //if (!$fromtemp) { ?>

		if (typeval == 'cmp' && flag1[0] && window.location.href.indexOf('manageproduct') == -1) {
			newnode.appendChild(document.createTextNode(' ('));
			var newnode3 = document.createElement('a');
			newnode3.href = '#';
			newnode3.onclick = new Function("doProductImg("+idval+"); return false;");
			newnode3.appendChild(document.createTextNode('Product Image'));
			newnode.appendChild(newnode3);
			newnode.appendChild(document.createTextNode(')'));
		}

		<?php // } ?>
		if(typeval=='cmp' && flag1[1]){
			//document.forms.prodForm.is_military.checked = true;
			for(var i=0;i<document.forms.prodForm['multiculturalmarkets[]'].length;i++){
				if(document.forms.prodForm['multiculturalmarkets[]'].options[i].value==13){
					document.forms.prodForm['multiculturalmarkets[]'].options[i].selected = true;
					break;
				}
			}
		}
		if(typeval=='cmp' && flag1[2]){
			document.forms.prodForm.is_insuranceexchange.value = 1;
			markInsuranceexchange();
		}
		newnode.appendChild(document.createTextNode(' '));
		var newnode2 = document.createElement('a');
		newnode2.href = '#';
		<?php if($fromtemp) echo "newnode2.className = 'bluelink';"; ?>
		
		if(typeval=='pub'){
			newnode2.onclick = new Function("removePub("+idval+","+obj4.ymd.value.substring(0,4)+obj4.ymd.value.substring(5,7)+obj4.ymd.value.substring(8,10)+"); return false;");
		}
		else if(typeval=='cmp'){
			newnode2.onclick = new Function("sortCmp("+idval+",-1); return false;");
			newnode2.appendChild(document.createTextNode('Up'));
			newnode.appendChild(newnode2);
			newnode.appendChild(document.createTextNode(' '));
			newnode2 = document.createElement('a');
			newnode2.href = '#';
			<?php if($fromtemp) echo "newnode2.className = 'bluelink';"; ?>
			newnode2.onclick = new Function("sortCmp("+idval+",1); return false;");
			newnode2.appendChild(document.createTextNode('Down'));
			newnode.appendChild(newnode2);
			newnode.appendChild(document.createTextNode(' '));
			newnode2 = document.createElement('a');
			newnode2.href = '#';
			<?php if($fromtemp) echo "newnode2.className = 'bluelink';"; ?>
			newnode2.onclick = new Function("removeCmp("+idval+"); return false;");
		}
		else if(typeval=='aff'){
			newnode2.onclick = new Function("removeAff("+idval+"); return false;");
			if(flag1){
				for(var i=0;i<document.forms.prodForm['multiculturalmarkets[]'].length;i++){
					if(document.forms.prodForm['multiculturalmarkets[]'].options[i].value==13){
						document.forms.prodForm['multiculturalmarkets[]'].options[i].selected = true;
						break;
					}
				}
			}
		}
		
		newnode2.appendChild(document.createTextNode('Remove'));
		newnode.appendChild(newnode2);
		obj.appendChild(newnode);
		
		obj3.value = newval;
	}
	
	hideDiv_outer('showbox_'+typeval+'s_outer','showbox_'+typeval+'s');
	obj2.value='';
	
	if (insert) {
		if (typeval=='cmp') {
			checkCompanyWV_id(checkval);
		}
	}
}
function addCmp(idval,nameval,has_img,military,co_states,co_comboIDs,insuranceexchange){
	var flag = new Array(has_img,military,insuranceexchange);
	addPiece(idval,nameval,'cmp',document.forms.cmp_selform.cmp_id,document.forms.prodForm.cmp_ids,flag);
	checkProductName();
	updateCompanyFields(idval,co_states,co_comboIDs);
        doPromotionRetailCompany('<?php if($updID!='' && $updID!='0'){echo $updID;}elseif($muid!='' && $muid!='0'){ echo $muid;} ?>'<?php if($fromtemp!=''){ echo ",isTmp=1";} ?>);
}
function updateCompanyFields(idval,co_states,co_comboIDs){
	var co_statesval = document.forms.prodForm.co_states.value;
	if(co_statesval.length>0){
		co_statesval = co_statesval + '|';
	}
	co_statesval = co_statesval + idval+':'+co_states;
	document.forms.prodForm.co_states.value = co_statesval;
	var co_comboIDsval = document.forms.prodForm.co_comboIDs.value;
	if(co_comboIDsval.length>0){
		co_comboIDsval = co_comboIDsval + '|';
	}
	co_comboIDsval = co_comboIDsval + idval+':'+co_comboIDs;
	document.forms.prodForm.co_comboIDs.value = co_comboIDsval;
	checkCompanyFields('');
}
function removeCompanyFields(idval){
	var coa = document.forms.prodForm.co_states.value.split('|');
	var co = '';
	for(var m=0;m<coa.length;m++){
		var check = coa[m].split(':');
		if(check[0]!=idval){
			if(co.length>0){
				co = co + '|';
			}
			co = co + coa[m];
		}
	}
	document.forms.prodForm.co_states.value = co;
	var coa = document.forms.prodForm.co_comboIDs.value.split('|');
	var co = '';
	for(var m=0;m<coa.length;m++){
		var check = coa[m].split(':');
		if(check[0]!=idval){
			if(co.length>0){
				co = co + '|';
			}
			co = co + coa[m];
		}
	}
	document.forms.prodForm.co_comboIDs.value = co;
        doPromotionRetailCompany('<?php if($updID!='' && $updID!='0'){echo $updID;}elseif($muid!='' && $muid!='0'){ echo $muid;} ?>'<?php if($fromtemp!=''){ echo ",isTmp=1";} ?>);
}
function checkStateSpecific(check_state){
	if(!document.forms.prodForm.is_state_specific.checked){
		return true;
	}
	var valArray = new Array();
	var first = true;
	for(var i=0;i<document.forms.prodForm['state[]'].length;i++){
		if(document.forms.prodForm['state[]'].options[i].selected){
			if(document.forms.prodForm['state[]'].options[i].value!='' && document.forms.prodForm['state[]'].options[i].value!='0'){
				first = false;
			}
			if(document.forms.prodForm['state[]'].options[i].value==check_state){
				return true;
			}
		}
	}
	if(first){
		return true;
	}
	return confirm('Correct Panelist State?');
}
function checkCompanyFields(only){
	if(only=='state' || only==''){
		var coa = document.forms.prodForm.co_states.value.split('|');
		if(document.forms.prodForm.co_states.value.length>0 && coa.length>0){
			var valArray = new Array();
			for(var m=0;m<coa.length;m++){
				var check = coa[m].split(':');
				if(check[1]!='' && check[1]!='0'){
					valArray[valArray.length] = check[1];
				}
			}
			if(valArray.length>0){
				for(var i=0;i<document.forms.prodForm['state[]'].length;i++){
					if(document.forms.prodForm['state[]'].options[i].selected){
						if(document.forms.prodForm['state[]'].options[i].value!='0' && in_array(document.forms.prodForm['state[]'].options[i].value,valArray)==-1){
							alert('Correct Company State?');
							break;
						}
					}
				}
			}
		}
	}
	if(only=='scsc_comboIDs' || only==''){
		var coa = document.forms.prodForm.co_comboIDs.value.split('|');
		if(document.forms.prodForm.co_comboIDs.value.length>0 && coa.length>0){
			var valArray = new Array();
			for(var m=0;m<coa.length;m++){
				var check = coa[m].split(':');
				if(check[1]!='' && check[1]!='0'){
					valArray[valArray.length] = check[1];
				}
			}
			if(valArray.length>0){
				var valArray2 = document.prodForm.scsc_comboIDs.value.split('|');
				for(var m=0;m<valArray2.length;m++){
					if(valArray2[m]!='' && in_array(valArray2[m],valArray)==-1){
						alert('Correct Company Sector/Category/Sub Category?');
						break;
					}
				}
			}
		}
	}
}
function addAff(idval,nameval,military){
	addPiece(idval,nameval,'aff',document.forms.aff_selform.aff_id,document.forms.prodForm.aff_ids,military);
}
function addCPN(idval,nameval,newval){
	if(newval){
		nameval = document.forms.prodForm.co_productName.value;
		idval = processajax('<?php if($fromtemp) echo 'admin/'; ?>piece.php', false, 'POST', 'cpn_name='+encodeURIComponent(nameval)+'&cpn_id='+idval, false, '');
	}
	document.forms.prodForm.productName.value = nameval;
	document.forms.prodForm.co_productName.value = '';
	showCPNs();
}
function addPub(idval,nameval,panelval,stateIDval,countryval){
	if(!checkPubDate()){
		return false;
	}
	addPiece(idval,nameval,'pub',document.forms.pub_selform.pub_id,document.forms.prodForm.pub_ids,0);
	for(var i=0;i<document.forms.prodForm['state[]'].length;i++){
		if(stateIDval==document.forms.prodForm['state[]'].options[i].value){
			document.forms.prodForm['state[]'].options[i].selected = true;
			break;
		}
	}
	if(document.forms.prodForm['primary_country'].selectedIndex<=0){
		for(var i=0;i<document.forms.prodForm['primary_country'].length;i++){
			if(countryval==document.forms.prodForm['primary_country'].options[i].value){
				document.forms.prodForm['primary_country'].options[i].selected = true;
				break;
			}
		}
	}
	if(panelval){
		alert('Audience: '+panelval);
	}
	return true;
}
function addPanOnly(panelist_id,competi_id,edit){
	var obj = document.getElementById('pans');
	
	var datedisplay = '00/00/0000';
	var dateval = '0000-00-00';
	var dateid = '00000000';
	var timeval = '00:00:00';
	var timeid = '000000';
	var currdate = new Date();
	var hh = ''+currdate.getHours();
	var mm = ''+currdate.getMinutes();
	var ss = ''+currdate.getSeconds();
	if(hh.length==1){
		hh = '0'+hh;
	}
	if(mm.length==1){
		mm = '0'+mm;
	}
	if(ss.length==1){
		ss = '0'+ss;
	}
	var timevalc = hh+':'+mm+':'+ss;
	var timeidc = ''+hh+mm+ss;
	
	var objd = document.selform_date;
	if(edit){
		objd = document.selform_date_edit;
	}
	if(objd){
		datedisplay = dateval = objd.ymd.value;
		dateid = objd.ymd.value.substring(0,4)+objd.ymd.value.substring(5,7)+objd.ymd.value.substring(8,10);
	}
	
	var obji = document.ivform;
	var objt = document.tform;
	var objf = document.fform;
	if(edit){
		obji = document.ivform_edit;
		objt = document.tform_edit;
		objf = document.fform_edit;
	}
	
	var isDupe = false;
	var tracking_idsval = document.forms.prodForm.tracking_ids.value;
	var tvalArray = tracking_idsval.split('|');
	var competi_idsval = document.forms.prodForm.competi_ids.value;
	var valArray = competi_idsval.split(',');
	for(var i in valArray){
		var valArray2 = valArray[i].split('|');
		if(valArray2[0]==panelist_id && valArray2[1]==dateval+' '+timeval && tvalArray[i]!=objt.trackingID.value){
			timeval = timevalc;
			timeid = timeidc;
		}
		if(valArray2[0]==panelist_id && valArray2[1]==dateval+' '+timeval){
			alert('Duplicate');
			isDupe = true;
			break;
		}
	}
	
	if(!isDupe){
		var newnode = document.createElement('div');
		newnode.id = 'pan'+panelist_id+'_'+dateid+timeid;
		
		newnode.appendChild(document.createTextNode(competi_id+' '));
		
		var newnode1 = document.createElement('span');
		newnode1.id = 'panInv'+panelist_id+'_'+dateid+timeid;
		var temptext = '';
		if(obji.invitationID.value!=''){
			temptext = temptext + '['+obji.invitationID.value+'] ';
		}
		if(objt.trackingID.value!=''){
			temptext = temptext + '{'+objt.trackingID.value+'} ';
		}
		if(objf.pproductFICO.value!=''){
			temptext = temptext + '#'+objf.pproductFICO.value+' ';
		}
		temptext = temptext + '('+datedisplay+') ';
		newnode1.appendChild(document.createTextNode(temptext));
		newnode.appendChild(newnode1);
		
		var newnode2 = document.createElement('a');
		newnode2.href = '#';
		<?php if($fromtemp) echo "newnode2.className = 'bluelink';"; ?>;
		
		newnode2.onclick = new Function("removePan('"+panelist_id+"','"+dateval+" "+timeval+"','"+dateid+timeid+"',true); return false;");
		newnode2.appendChild(document.createTextNode('Remove'));
		newnode.appendChild(newnode2);
		
		newnode.appendChild(document.createTextNode(' '));
		var newnode3 = document.createElement('a');
		newnode3.href = '#';
		newnode3.style.paddingLeft = '6px';
		<?php if($fromtemp) echo "newnode3.className = 'bluelink';"; ?>;
		newnode3.onclick = new Function("editPan('"+panelist_id+"','"+dateval+" "+timeval+"','"+dateid+timeid+"','"+competi_id+"'); return false;");
		newnode3.appendChild(document.createTextNode('Edit'));
		newnode.appendChild(newnode3);
		
		obj.appendChild(newnode);
		
		if(competi_idsval.length>0){
			competi_idsval = competi_idsval + ',';
		}
		competi_idsval = competi_idsval + panelist_id+'|'+dateval+' '+timeval;
		
		document.forms.prodForm.invitation_ids.value = document.forms.prodForm.invitation_ids.value + obji.invitationID.value + '|';
		document.forms.prodForm.tracking_ids.value = document.forms.prodForm.tracking_ids.value + objt.trackingID.value + '|';
		document.forms.prodForm.fico_ids.value = document.forms.prodForm.fico_ids.value + objf.pproductFICO.value + '|';
		
		document.forms.prodForm.competi_ids.value = competi_idsval;
		hideDiv_outer('showbox_pans_outer','showbox_pans');
		document.forms.selform.pan_id.value='';
		obji.invitationID.value = '';
		objt.trackingID.value = '';
		objf.pproductFICO.value = '';
		
		var objscroll = document.getElementById("pans_scroll");
		if(objscroll && objscroll.scrollHeight){
			objscroll.scrollTop = objscroll.scrollHeight;
		}

		var e_val = '';
		var e_obj = false;
		if(document.prodForm.entryID){
			var old_entryID = document.forms.prodForm.old_entryID.value;
			if(old_entryID==''){
				e_obj = document.prodForm.entryID;
				e_val = document.prodForm.entryID.value.substring(0,10);
			}
		}
		else if(document.prodForm.firstSeen){
			e_obj = document.prodForm.firstSeen;
			e_val = document.prodForm.firstSeen.value;
		}
		if(e_val!=''){
			if(dateval<e_val){
				e_obj.value = dateval;
			}
		}
		
		cpanchanged = true;
	}
}
function addPan(panelist_id,competi_id,mChannelIDval,mPanelIDval,genderval,ageIDval,incomeIDval,stateIDval,ownbizval){
	if(!checkPanDate() || !checkStateSpecific(stateIDval)){
		return false;
	}
	addPanOnly(panelist_id,competi_id,false);
	
	if(mPanelIDval==1 || mPanelIDval==2){
		panelistInfoArray[panelist_id] = new Array(genderval,ageIDval,incomeIDval,stateIDval);
		if(genderval=='M' || genderval=='F'){
			genderArray[genderval] = genderArray[genderval] + 1;
		}
		var i = 0;
		if(document.forms.prodForm.mChannelID.selectedIndex<1){
			for(i=0;i<document.forms.prodForm.mChannelID.length;i++){
				if(mChannelIDval==document.forms.prodForm.mChannelID.options[i].value){
					document.forms.prodForm.mChannelID.selectedIndex = i;
					checkChannel();
					doDelMeth();
					checkDeps_mc();
					break;
				}
			}
		}
		if(document.forms.prodForm.mPanelID.selectedIndex<1){
			for(i=0;i<document.forms.prodForm.mPanelID.length;i++){
				if(mPanelIDval==document.forms.prodForm.mPanelID.options[i].value){
					document.forms.prodForm.mPanelID.selectedIndex = i;
					/*###########  Communication Type Implementation ############*/
					//getDeps('agentCommunicationID[]');
					/*###########  Communication Type Implementation ############*/
					doDelMeth();
					checkDeps_mp();
					check_BA();
					break;
				}
			}
		}
		
		var currgender = 3;
		var newgender = 3;
		for(i=0;i<document.forms.prodForm.gender.length;i++){
			if(document.forms.prodForm.gender[i].checked){
				currgender = i;
			}
			if(genderval==document.forms.prodForm.gender[i].value){
				newgender = i;
			}
		}
		if(newgender!=3 && currgender!=2){
			if(currgender==3){//none
				document.forms.prodForm.gender[newgender].checked = true;
			}
			else if(newgender!=currgender){//both
				document.forms.prodForm.gender[2].checked = true;
			}
		}

		// The ageIDval will come either as a string ("1,2") or an integer (2). Either way, place it in an array. - Tyler
		if (typeof ageIDval === 'string' || ageIDval instanceof String) {
			ageIdArray = ageIDval.split(',');
		} else {
			ageIdArray = [ageIDval];
		}

		// Iterate through each age group, then through each UI option. - Tyler
		for(ai=0;ai<ageIdArray.length;ai++) {
			for(i=0;i<document.forms.prodForm['age[]'].length;i++){
				if(ageIdArray[ai]==document.forms.prodForm['age[]'].options[i].value){
					document.forms.prodForm['age[]'].options[i].selected = true;
					document.forms.prodForm['age[]'].options[0].selected = false;
					break;
				}
			}
		}

		for(i=0;i<document.forms.prodForm['incomeID[]'].length;i++){
			if(incomeIDval==document.forms.prodForm['incomeID[]'].options[i].value){
				document.forms.prodForm['incomeID[]'].options[i].selected = true;
				document.forms.prodForm['incomeID[]'].options[0].selected = false;
				break;
			}
		}
	}
	else{
		panelistInfoArray[panelist_id] = new Array('N',-1,-1,stateIDval);
	}
	for(i=0;i<document.forms.prodForm['state[]'].length;i++){
		if(stateIDval==document.forms.prodForm['state[]'].options[i].value){
			document.forms.prodForm['state[]'].options[i].selected = true;
			if(document.forms.prodForm['state[]'].options[0].value=='0'){
				document.forms.prodForm['state[]'].options[0].selected = false;
			}
			checkCompanyFields('state');
			break;
		}
	}
	if(ownbizval){
		alert('This panelist is a Business Owner');
	}
	
	return true;
}
function hideDiv_outer(objname,obj2name){
	ie6Show();
	document.getElementById(objname).style.display = 'none';
	hideDiv_inner(obj2name);
}
function showDiv_outer(objname,obj2name){
	var obj = document.getElementById(objname);
	var obj2 = document.getElementById(obj2name);
	obj.style.left = findPosX(obj2)+'px';
	obj.style.top = findPosY(obj2)+'px';
	obj.style.display = 'block';
	ie6Hide(obj);
}
function checkChannel(){
	var chan = 0;
	if(document.forms.prodForm.mChannelID.selectedIndex>0){
		chan = document.forms.prodForm.mChannelID.options[document.forms.prodForm.mChannelID.selectedIndex].value;
	}
	if(chan==1 || chan==2){
		document.forms.prodForm.DMSource.disabled = false;
                var dmtm=document.forms.prodForm.dmtmsource.value; 
                document.forms.prodForm.DMSource.value =dmtm; 
	}
	else {
		document.forms.prodForm.DMSource.disabled = true;
		document.forms.prodForm.DMSource.value = '';
	}
	if(chan==3){
		document.forms.prodForm['electronicID[]'].disabled = false;
	}
	else{
		document.forms.prodForm['electronicID[]'].disabled = true;
		document.forms.prodForm['electronicID[]'].selectedIndex = -1;
	}
	if(chan==6){
            
		document.forms.prodForm.external_link.disabled = false;
		document.forms.prodForm.external_updates.disabled = false;
		document.forms.prodForm.external_fans.disabled = false;
                if(typeof document.forms.prodForm.socialmedia_adtype!='undefined'){
                    document.forms.prodForm.socialmedia_adtype.disabled = false;
                    document.forms.prodForm.socialmedia_adtype.selectedIndex = -1;
                }                
	}
	else {
        

		document.forms.prodForm.external_link.disabled = true;
		document.forms.prodForm.external_link.value = '';
		document.forms.prodForm.external_updates.disabled = true;
		document.forms.prodForm.external_fans.disabled = true;
               if(typeof document.forms.prodForm.socialmedia_adtype!='undefined'){
                    document.forms.prodForm.socialmedia_adtype.disabled = true;
                }
	}
	if (chan == 5 || chan == 7) {
		document.forms.prodForm.traffic_sources.disabled = false;
	}
	else {
		document.forms.prodForm.traffic_sources.disabled = true;
		document.forms.prodForm.traffic_sources.value = '';
	}

	document.forms.prodForm.external_updates.value = '';
	document.forms.prodForm.external_fans.value = '';
}
function logPop(mid,pid,istmp) {
	var wind = window.open('<?php if($fromtemp) echo 'admin/'; ?>admin_log.php?mid='+mid+'&pid='+pid+'&istmp='+istmp,"winpop","left=0, top=0, scrollbars=yes, resizable=yes, width=400, height=300");
	wind.focus();
}
function getDeps(fieldname){
	var ac = document.forms.prodForm[fieldname];
	if(ac){
		var mp = document.forms.prodForm.mPanelID;
		var mpsel = 0;
		if(mp.selectedIndex>0){
			mpsel = mp.selectedIndex;
		}
		var mpid = mp.options[mpsel].value;
		
		var mc = document.forms.prodForm.mChannelID;
		var mcsel = 0;
		if(mc.selectedIndex>0){
			mcsel = mc.selectedIndex;
		}
		var mcid = mc.options[mcsel].value;
		
		var selectedMPArray = new Array();
		for(var j=0;j<ac.options.length;j++){
			if(ac.options[j].selected){
				selectedMPArray[selectedMPArray.length] = ac.options[j].value;
			}
		}
		var selectedSArray = returnSCSC(0);
		ac.options.length = 0;
		var counter = 0;
		var optiontext = 'Any';
		var isSel = false;
		var anySel = true;
		var show1 = false;
		var show2 = false;
		var show3 = false;

		for(var n=0;n<depsArrayID[fieldname].length;n++){
			show1 = false;
			show2 = false;
			show3 = false;
                        showRates=false;
			if(!depsArray[fieldname]){
				show1 = true;
			}
			else {
				if(depsArray[fieldname][mpid] && depsArray[fieldname][mpid][depsArrayID[fieldname][n]]){
					show1 = true;
				}
			}
			if(!depsArrayS[fieldname]){
				show2 = true;
			}
			else{
				for(var m=0;m<selectedSArray.length;m++){
					if(depsArrayS[fieldname][selectedSArray[m]] && depsArrayS[fieldname][selectedSArray[m]][depsArrayID[fieldname][n]]){
						show2 = true;
						break;
					}
				}
			}
			if(!depsArrayM[fieldname]){
				show3 = true;
			}
			else {
				if(depsArrayM[fieldname][mcid] && depsArrayM[fieldname][mcid][depsArrayID[fieldname][n]]){
					show3 = true;
				}
			}
                        // Changes for 'Rates' communication type                        
                        if((depsArrayName[fieldname][n]=='Rates') && ((selectedSArray.indexOf('4')>=0) || (selectedSArray.indexOf('5')>=0) || (selectedSArray.indexOf('315')>=0) || (mpid=='6'))){
                            showRates = true;
                            
                        }
                        if(!showRates){
                            if((show1 && show2 && show3) || depsArrayID[fieldname][n]=='0'){
                                    optiontext = depsArrayName[fieldname][n];
                                    if(in_array(depsArrayID[fieldname][n],selectedMPArray)!=-1){
                                            isSel = true;
                                            anySel = false;
                                    }
                                    else {
                                            isSel = false;
                                    }
                                    ac.options[counter++] = new Option(optiontext, depsArrayID[fieldname][n], false, isSel);
                            }
                        }else{
                            if(((show1 || show2) && show3) || depsArrayID[fieldname][n]=='0'){
                                optiontext = depsArrayName[fieldname][n];
                                    if(in_array(depsArrayID[fieldname][n],selectedMPArray)!=-1){
                                            isSel = true;
                                            anySel = false;
                                    }
                                    else {
                                            isSel = false;
                                    }
                                    ac.options[counter++] = new Option(optiontext, depsArrayID[fieldname][n], false, isSel);
                            }
                                
                            
                        }
		
		}
	}
}
function doDelMeth(){
	var chan = 0;
	if(document.forms.prodForm.mChannelID.selectedIndex>0){
		chan = document.forms.prodForm.mChannelID.options[document.forms.prodForm.mChannelID.selectedIndex].value;
	}
	var mpid = 0;
	if(document.forms.prodForm.mPanelID.selectedIndex>0){
		mpid = document.forms.prodForm.mPanelID.options[document.forms.prodForm.mPanelID.selectedIndex].value;
	}
	if(chan==1 && (mpid==1 || mpid==2)){
		document.forms.prodForm.delmethid.disabled = false;
	}
	else{
		document.forms.prodForm.delmethid.disabled = true;
	}
	document.forms.prodForm.delmethid.selectedIndex= 0;
}
/*Start Envelope/Postage Data Fields*/
function doEnvelopePostageData(){
	var chan = 0;
	if(document.forms.prodForm.mChannelID.selectedIndex>0){
		chan = document.forms.prodForm.mChannelID.options[document.forms.prodForm.mChannelID.selectedIndex].value;
	}
	var mpid = 0;
	if(document.forms.prodForm.mPanelID.selectedIndex>0){
		mpid = document.forms.prodForm.mPanelID.options[document.forms.prodForm.mPanelID.selectedIndex].value;
	}
        var delid = 0;
	if(document.forms.prodForm.delmethid.selectedIndex>0){
		delid = document.forms.prodForm.delmethid.options[document.forms.prodForm.delmethid.selectedIndex].value;
	}
        //alert(delid +"chn=="+chan+"mpnl=="+mpid);
	if((chan==1) && ((mpid==1) || (mpid==2)) && ((delid==1) || (delid==3) || (delid==7))){
         
		document.forms.prodForm.deliveryTypeId.disabled = false;
                                    
                document.forms.prodForm.postageId.disabled = false;
                  
                document.forms.prodForm.presortedId.disabled = false;
                
                 
                document.forms.prodForm.packageTypeId.disabled = false;
                
	}
	else{
		document.forms.prodForm.deliveryTypeId.disabled = true;
                document.forms.prodForm.postageId.disabled = true;
                document.forms.prodForm.presortedId.disabled = true;
                document.forms.prodForm.packageTypeId.disabled = true;
	}
	document.forms.prodForm.deliveryTypeId.selectedIndex= 0;
        document.forms.prodForm.postageId.selectedIndex = 0;
        document.forms.prodForm.presortedId.selectedIndex = 0;
        document.forms.prodForm.packageTypeId.selectedIndex = 0;
    } 
    
    function checkDeps_EnvelopePostageData(){ 
    checkDependencies('dm_deliveryTypeId')
    checkDependencies('dm_postageId')
    checkDependencies('dm_presortedId')
    checkDependencies('dm_packageTypeId')
    }
/*End Envelope/Postage Data Fields*/

function getVari(){
	var va = document.forms.prodForm['vid[]'];
	if(va){
		var selectedVArray = new Array();
		for(var j=0;j<va.options.length;j++){
			if(va.options[j].selected){
				selectedVArray[selectedVArray.length] = va.options[j].value;
			}
		}
		
		var selectedArray = returnSCSC(1);
		va.options.length = 0;
		var counter = 0;
		var optiontext = '';
		var isSel = true;
		var show = false;
		for(var n=0;n<variArrayID.length;n++){
			show = false;
			for(var j=0;j<selectedArray.length;j++){
				if(variArray[selectedArray[j]] && variArray[selectedArray[j]][variArrayID[n]]){
					show = true;
					break;
				}
			}
			if(show){
				if(in_array(variArrayID[n],selectedVArray)!=-1){
					isSel = true;
				}
				else {
					isSel = false;
				}
				va.options[counter++] = new Option(variArrayName[n], variArrayID[n], false, isSel);
			}
		}
	}
}
function doIncentive(){
	document.forms.prodForm.incentive_ongoing.disabled = true;
	var obj = document.getElementById('so_incentive');
	obj.style.display = 'none';
	
	var selectedArray = returnSCSC(1);
	for(var j=0;j<selectedArray.length;j++){
		if(in_array(selectedArray[j],coreArray)==-1){
			document.forms.prodForm.incentive_ongoing.disabled = false;
			obj.style.display = 'inline';
		}
	}
}
function checkAA(){
    document.prodForm.affinityAssociation.checked = true;
}
function depends(fields,eval_bool){
	var fieldsArray = fields.split(',');
	for(var field in fieldsArray){
		var obj = document.prodForm[fieldsArray[field]];
		if(obj){
			obj.disabled = eval(eval_bool);
		}
	}
}
function dependsSector(){
	var selectedArray = returnSCSC(2);
	var selectedSubCatArray = returnSCSC(3);
	
	var eval_bool = '';
	if(in_array(88,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('FreeChecking,Checking_APR,Checking_APY',eval_bool);
	
	if(in_array(89,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('Savings_APR,Savings_APY',eval_bool);//SavingsInterestRate
	
	if(in_array(100,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('MoneyMarket_APR,MoneyMarket_APY',eval_bool);
	
	if(in_array(189,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('CD_APR,CD_APY',eval_bool);
	
	if(in_array(186,selectedArray)!=-1 || in_array(94,selectedArray)!=-1 || in_array(187,selectedArray)!=-1 || in_array(185,selectedArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('InstallationCharge',eval_bool);
	
	if(in_array(94,selectedArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('LocalCallingMonthlyCost,LongDistanceMonthlyCost',eval_bool);
	
	if(in_array(103,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('Reloadable',eval_bool);
}
function doServicePlanType(){
	for(var i=0;i<document.prodForm.ServicePlanType.options.length;i++){
		if(document.prodForm.ServicePlanType.options[i].value==1 && document.prodForm.InternetMbps){
			if(document.prodForm.ServicePlanType.options[i].selected){
				document.prodForm.InternetMbps.disabled = false;
			}
			else{
				document.prodForm.InternetMbps.disabled = true;
			}
		}
		if(document.prodForm.ServicePlanType.options[i].value==2 && document.prodForm.HDTV){
			if(document.prodForm.ServicePlanType.options[i].selected){
				document.prodForm.HDTV.disabled = false;
			}
			else{
				document.prodForm.HDTV.disabled = true;
			}
		}
	}
				
}
function checkPubDate(){
	if (document.pform.ymd.value == '') {
		alert('Date?');
		return false;
	}

	return true;
}
function checkPanDate(){
	if (document.selform_date.ymd.value == '') {
		alert('Date?');
		return false;
	}

	return true;
}
function doProdDelete(pid){
	if(confirm("Are you sure you want to delete?") ) {
		document.location.href = 'manageproduct.php?delID='+pid;
	}
}
<?php 
if($fromtemp){
?>
function validate() {
	<?php 
	if(!empty($nopermission)){
		echo "return false;";
	}
	?>
	var needtext = '';
	var productName = document.prodForm.productName.value = trimspace(document.prodForm.productName.value);
	var sectorID = '';
	var sectorIDA = returnSCSC(1);
	for(var j=0;j<sectorIDA.length;j++){
		if(sectorIDA[j]>0){
			sectorID = sectorIDA[j];
			break;
		}
	}
	var headline = document.prodForm.productHeadline.value = trimspace(document.prodForm.productHeadline.value);
	var chan = 0;
	if(document.forms.prodForm.mChannelID.selectedIndex>0){
		chan = document.forms.prodForm.mChannelID.options[document.forms.prodForm.mChannelID.selectedIndex].value;
	}
	var mpid = 0;
	if(document.forms.prodForm.mPanelID.selectedIndex>0){
		mpid = document.forms.prodForm.mPanelID.options[document.forms.prodForm.mPanelID.selectedIndex].value;
	}
	if( headline == '' && chan!=5 && chan!=7) {
		needtext = needtext + "\n  Product Headline";
	}
	if( productName == '' ) {
		needtext = needtext + "\n  Product Name";
	}
	if(document.forms.prodForm.cmp_ids.value==''){
		needtext = needtext + "\n  Company";
	}
	if( sectorID == '' ) {
		needtext = needtext + "\n  Sector";
	}
	var categoryID = '';
	var categoryIDA = returnSCSC(2);
	for(var j=0;j<categoryIDA.length;j++){
		if(categoryIDA[j]>0){
			categoryID = categoryIDA[j];
			break;
		}
	}
	if (categoryID == ''){
		needtext = needtext + "\n  Category";
	}
	if(document.forms.prodForm.mChannelID.selectedIndex<1){
		needtext = needtext + "\n  Media Channel";
	}
	if(chan==1 || chan==2){
		if(document.prodForm.DMSource.value==''){
			needtext = needtext + "\n  DM/TM Source";
		}
		if(chan==2 && document.forms.prodForm.pub_ids.value==''){
			needtext = needtext + "\n  Publication";
		}
	}
	else if(chan==3){
		var electronic_sel = false;
		for(var i=0;i<document.forms.prodForm['electronicID[]'].length;i++){
			if(document.forms.prodForm['electronicID[]'].options[i].selected){
				if(document.forms.prodForm['electronicID[]'].options[i].value!='0'){
					electronic_sel = true;
					break;
				}
			}
		}
		if(!electronic_sel) {
			needtext = needtext + "\n  Electronic Type";
		}
	}
	else if(chan==6){
		if(document.prodForm.external_link.value==''){
			needtext = needtext + "\n  External Link";
		}
	}
	if(document.forms.prodForm.mPanelID.selectedIndex<1){
		needtext = needtext + "\n  Audience";
	}
	if(document.forms.prodForm.mTypeID.selectedIndex<1 && chan!=5 && chan!=6 && chan!=7){
		var doneedtext = true;
		if(document.forms.prodForm.mPanelID.selectedIndex>0){
			if(mpid==4  || mpid==5  || mpid==6){
				doneedtext = false;
			}
		}
		if(doneedtext){
			needtext = needtext + "\n  Mailing Type";
		}
	}
	if(chan==1 && (mpid==1 || mpid==2) && document.forms.prodForm.delmethid.selectedIndex<=0){
		needtext = needtext + "\n  Delivery Method";
	}
	if(document.prodForm.offerOrigin.selectedIndex<1 && document.prodForm.offerOrigin.length>1 && chan!=5 && chan!=6 && chan!=7){
		needtext = needtext + "\n  Offer Origin";
	}
	var state_sel = false;
	for(var i=0;i<document.forms.prodForm['state[]'].length;i++){
		if(document.forms.prodForm['state[]'].options[i].selected){
			if(document.forms.prodForm['state[]'].options[i].value!='0' || chan==2){
				state_sel = true;
				break;
			}
		}
	}
	if(!state_sel && chan!=5 && chan!=6 && chan!=7) {
		needtext = needtext + "\n  State";
	}
	
	if(needtext!=''){
		return confirm("These fields are required for approval:"+needtext+"\n\nWould you like to save anyway?");
	}
	if(!checkAllDates()){
		return false;
	}
	
	if(!validate_variant()){
		return false;
	}
	
	return true;
}
<?php 
}
else {
?>
function validate() {
	<?php 
	if(!empty($nopermission)){
		echo "return false;";
	}
	?>
	var needtext = '';
	var productName = document.prodForm.productName.value = trimspace(document.prodForm.productName.value);
	var sectorID = '';
	var sectorIDA = returnSCSC(1);
	for(var j=0;j<sectorIDA.length;j++){
		if(sectorIDA[j]>0){
			sectorID = sectorIDA[j];
			break;
		}
	}
	var headline = document.prodForm.productHeadline.value = trimspace(document.prodForm.productHeadline.value);
	var PDFContent_obj = document.prodForm.PDFContent;
	var PDFFile_obj = document.prodForm.PDFFILE;
	var mediafile_obj = document.prodForm.mediafile;
	var choice = document.prodForm.productStatus.value;
	var chan = 0;
    var mpid = 0;
    var pdf_is_required = true;

	if (document.forms.prodForm.mChannelID.selectedIndex > 0) {
		chan = document.forms.prodForm.mChannelID.options[document.forms.prodForm.mChannelID.selectedIndex].value;
	}

	if (document.forms.prodForm.mPanelID.selectedIndex > 0) {
		mpid = document.forms.prodForm.mPanelID.options[document.forms.prodForm.mPanelID.selectedIndex].value;
	}

    if (chan == 5 || chan == 7) { // 5: Online Display advertising; 7: Mobile
        pdf_is_required = false;
    }
    //Start for process pdf
    var process_pdf_require='';
    var element_processpdf=document.getElementById('process_pdf_chkbox');
    if (element_processpdf.checked == true) {
        process_pdf_require=1;
    }    
    //curpdf==a if there is no pdffile already uploaded
    if (pdf_is_required && document.prodForm.curpdf.value == 'a' && PDFFile_obj.value == '' && mediafile_obj.value == '' && process_pdf_require=='') {
        alert("Please select Product PDF");
        PDFFile_obj.focus();

        return false;
    }
	
	if(document.prodForm.curimg.value=='a') { //curimg==a if there is no image already uploaded
            //####################### end for display the product image under temp ###############   
          // if(document.prodForm.imgFile.value=="" && document.prodForm.img_companyID.value==0) {
           
//           if(document.prodForm.imgtempcompanyfile.lenght!='undefined'){
//                if(document.prodForm.imgFile.value=="" && document.prodForm.imgtempcompanyfile.value=="") {    
//			needtext = needtext + "\n  Company Image";
//		}
//            }else if(document.prodForm.imgFile.value=="") {//    
//			needtext = needtext + "\n  Company Image";
//		} 

        
        if(document.prodForm.imgFile.value=="" && document.prodForm.img_companyID.value==0) {
                needtext = needtext + "\n  Company Image";
                }
        
        
        
           
	}
        
        
        
	
	if (headline == '' && chan!=5 && chan!=7 ) {
		needtext = needtext + "\n  Product Headline";
	}
	if (productName == '' ) {
		needtext = needtext + "\n  Product Name";
	}
	if (document.forms.prodForm.cmp_ids.value=='') {
		needtext = needtext + "\n  Company";
	}
	if( sectorID == '' ) {
		needtext = needtext + "\n  Sector";
	}
	var categoryID = '';
	var categoryIDA = returnSCSC(2);
	for(var j=0;j<categoryIDA.length;j++){
		if(categoryIDA[j]>0){
			categoryID = categoryIDA[j];
			break;
		}
	}
	if (categoryID == ''){
		needtext = needtext + "\n  Category";
	}
	if(document.forms.prodForm.mChannelID.selectedIndex<1){
		alert("Please enter Media Channel");
		if(!document.forms.prodForm.mChannelID.disabled){
			document.forms.prodForm.mChannelID.focus();
		}
		return false;
	}
	
	/*#################   For Primary country restriction under Mobile, Online display, Print and Social Media sector ################# */ 
   
   if((document.forms.prodForm.mChannelID.value=='7' || document.forms.prodForm.mChannelID.value=='5'  || document.forms.prodForm.mChannelID.value=='6') && document.forms.prodForm.competi_ids.value=='' && document.forms.prodForm.primary_country.value=='' ){ 
	   alert("Please select Primary Country");
	   if(!document.forms.prodForm.primary_country.disabled){
			document.forms.prodForm.primary_country.focus();
		}
	   return false;
	   
   }
   
  /* #################   End For Primary country restriction #################  */  
	
	
	
	
	if(chan==1 || chan==2){
		if(document.prodForm.DMSource.value==''){
			alert("Please enter DM/TM Source");
			if(!document.forms.prodForm.DMSource.disabled){
				document.forms.prodForm.DMSource.focus();
			}
			return false;
		}
		if(chan==2 && document.forms.prodForm.pub_ids.value==''){
			needtext = needtext + "\n  Publication";
		}
	}
	else if(chan==3){
		var electronic_sel = false;
		for(var i=0;i<document.forms.prodForm['electronicID[]'].length;i++){
			if(document.forms.prodForm['electronicID[]'].options[i].selected){
				if(document.forms.prodForm['electronicID[]'].options[i].value!='0'){
					electronic_sel = true;
					break;
				}
			}
		}
		if(!electronic_sel) {
			alert("Please enter Electronic Type");
			if(!document.forms.prodForm['electronicID[]'].disabled){
				document.forms.prodForm['electronicID[]'].focus();
			}
			return false;
		}
	}
	else if(chan==6){
		if(document.prodForm.external_link.value==''){
			alert("Please enter External Link");
			if(!document.forms.prodForm.external_link.disabled){
				document.forms.prodForm.external_link.focus();
			}
			return false;
		}
                
                if(document.forms.prodForm.socialmedia_adtype.selectedIndex<1){
                    alert("Please select Social Media Ad Type");
                    if(!document.forms.prodForm.socialmedia_adtype.disabled){
                            document.forms.prodForm.socialmedia_adtype.focus();
                    }
                    return false;
                }
	}
	if(document.forms.prodForm.mPanelID.selectedIndex<1){
		alert("Please enter Audience");
		if(!document.forms.prodForm.mPanelID.disabled){
			document.forms.prodForm.mPanelID.focus();
		}
		return false;
	}
	if(document.forms.prodForm.mTypeID.selectedIndex<1 && chan!=5 && chan!=6 && chan!=7){
		var doneedtext = true;
		if(document.forms.prodForm.mPanelID.selectedIndex>0){
			if(mpid==4  || mpid==5  || mpid==6){
				doneedtext = false;
			}
		}
		if(doneedtext){
			needtext = needtext + "\n  Mailing Type";
		}
	}
	if(chan==1 && (mpid==1 || mpid==2) && document.forms.prodForm.delmethid.selectedIndex<=0){
		alert("Please enter Delivery Method");
		if(!document.forms.prodForm.delmethid.disabled){
			document.forms.prodForm.delmethid.focus();
		}
		return false;
	}
	if(document.prodForm.offerOrigin.selectedIndex<1 && document.prodForm.offerOrigin.length>1 && chan!=5 && chan!=6 && chan!=7){
		needtext = needtext + "\n  Offer Origin";
	}
	var state_sel = false;
	for(var i=0;i<document.forms.prodForm['state[]'].length;i++){
		if(document.forms.prodForm['state[]'].options[i].selected){
			if(document.forms.prodForm['state[]'].options[i].value!='0' || chan==2){
				state_sel = true;
				break;
			}
		}
	}
	if(!state_sel && chan!=5 && chan!=6 && chan!=7) {
		needtext = needtext + "\n  State";
	}
	
	if(choice==1){
		if(needtext!=''){
			if(confirm("These fields are required for approval:"+needtext+"\n\nWould you like to save as Unapproved?")){
				if(!doSaved(2)){
					return false;
				}
			}
			else {
				return false;
			}
		}
		else if(!checkAllDates()){
			return false;
		}
	}
	
	if(!validate_variant()){
		return false;
	}
	
	<?php if(isset($_GET['muid'])){ ?>	
		var disfields = new Array('<?php echo implode("','",$disfields); ?>');
		for (var j=0;j<disfields.length;j++){
			var obj = disfields[j];
			if(document.forms.prodForm[obj]){
				if(document.forms.prodForm[obj].disabled){
					document.forms.prodForm[obj].disabled = false;
				}
				else if(document.forms.prodForm[obj].length){
					for(var i=0;i<document.forms.prodForm[obj].length;i++){
						if(document.forms.prodForm[obj][i] && document.forms.prodForm[obj][i].disabled){
							document.forms.prodForm[obj][i].disabled = false;
						}
					}
				}
			}
		}
	<?php } ?>	
	
	return true;
}
<?php 
}
?>
var checkDepsEvalArray = new Array();
var checkDepsDispArray = new Array();
<?php 
$all_parts = array();
if(isset($saveDIArray)){
	foreach($saveDIArray as $k=>$a){
		list($part1) = explode('_',$k);
		$eval = array_shift($a);
		echo " checkDepsEvalArray['$k'] = \"".addslashes($eval)."\"; checkDepsDispArray['$k'] = new Array(); ";
		foreach($a as $v){
			echo " checkDepsDispArray['$k'][checkDepsDispArray['$k'].length] = '$v'; ";
		}
		if(!isset($all_parts[$part1])){
			$all_parts[$part1] = array();
		}
		$all_parts[$part1][] = 'checkDependencies(\''.$k.'\')';
	}
}
else{
	echo "\nfunction checkDeps_s(){ return true; }";
}
$doonload = '';

foreach($all_parts as $k=>$a){
	$doonload .= " checkDeps_".$k."(); ";
	echo "\nfunction checkDeps_".$k.'(){ ';
	echo "\n".implode(' || ',$a)."\n";
	echo "}\n";
}
if(!isset($_GET['muid']) || $fromtemp) {
	$doonload .= " checkProductName(); ";
}

$comboIDs_split = explode('|',$comboIDs);
foreach($comboIDs_split as $scsc_combo){
	if(!empty($scsc_combo)){
		list($s,$c,$sc,$ssc) = explode('_',$scsc_combo);
		if(!empty($s) || !empty($c) || !empty($sc) || !empty($ssc)){
			$scsc_combo_text = sectorName($s).' / '.sectorName($c).' / '.sectorName($sc).' / '.sectorName($ssc);
			$doonload .= " displaySCSC('$scsc_combo','".singleQuoteSafe($scsc_combo_text)."'); ";
		}
	}
}

?>
function checkAllDeps() {
	<?php echo $doonload; ?>
	showButtons();
}

function checkDependencies(lookup) {
    var show = 'none';
    var showMintelFields = showIncentiveFields(lookup);

    if (checkDepsEvalArray[lookup]) {
        if (eval(checkDepsEvalArray[lookup]) && showMintelFields) {
            show = 'block';
        }

        for (var i = 0; i < checkDepsDispArray[lookup].length; i++) {
            var obj = document.getElementById('div_' + checkDepsDispArray[lookup][i]);
            var cssDisplay = show;

            if (!showMintelFields && showBaseIncentiveFields(lookup, i)) {
                cssDisplay = 'block';
            }

            if (obj) {
                obj.style.display = cssDisplay;
            }
        }
    }

    setupIncentiveExtras();
}

/**
 * Check if we can show the basic incentive fields, "Sign-on" and "Ongoing",
 * based on if the initial dependency is met. But since it is combined with the
 * rest of those mintel fields, we want to check just the first two of that bloc.
 * Mintel fields must meet the sector dependency criteria, while the first two incentive
 * fields only need to match that "Bonuses/Contests/Incentives" of Communication Type
 * was chosen.
 *
 * @param {string} Reference to the group of fields
 * @param {integer} Loop counter
 * @return {boolean}
 */
function showBaseIncentiveFields(lookup, i) {
    return (lookup == 'ct_incentive' && eval(checkDepsEvalArray[lookup]) && i < 2);
}

/**
 * Determine if we need to display or hide the incentive fields.
 * If chosen sectors are either Banking or Credit Card, then show, hide otherwise.
 *
 * @return {boolean}
 */
function showIncentiveFields(lookup) {
    if (lookup !== 'ct_incentive') {
        return true;
    } else {
        var chosenSectors = getCurrentSectors();

        // 87 and 90 are Banking and Credit Card ids from cscan_sector respectively
        return (chosenSectors.indexOf('87') !== -1 || chosenSectors.indexOf('90') !== -1);
    }
}

/**
 * Toggle the action that allows admin to show or hide the extra incentive fields.
 *
 * @param {string}
 */
function showIncentiveExtras(setId) {
    var incentiveSetId = document.getElementById('incentive_set_' + setId);

    if (incentiveSetId.innerText.substring(0, 3) === 'Add') {
        disableIncentiveExtras(setId, 'enabled');
        if (secondaryIncentivesNotYetOpen(setId)) {
            return;
        }

        incentiveSetId.innerText = 'Hide sign-on incentive #' + setId + ' details';
        displayIncentiveExtras('block', setId);
    } else {
        incentiveSetId.innerText = 'Add sign-on incentive #' + setId + ' details';
        displayIncentiveExtras('none', setId);
        //resetIncentiveExtras(setId);
        disableIncentiveExtras(setId, 'disabled');

        // Close #3 set if we are closing the #2 set
        if (setId == 2) {
            showIncentiveExtras(3);
        }
    }
}

/**
 * Check that the second set of incentive fields are visible
 *
 * @param {string} setId ID to set of extra incentive fields to show
 */
function secondaryIncentivesNotYetOpen(setId) {
    var incentiveSetId_2 = document.getElementById('incentive_set_2');

    if (setId == 3 && incentiveSetId_2.innerText.substring(0, 3) === 'Add') {
        return true;
    }

    return false;
}

/**
 * Show/hide the extra incentive lines, should only also be visible if the first set of
 * incentive fields are themselves qualified to show (alignment of category, communication type, etc)
 *
 * @param {string} display CSS value of block/none
 * @param {string} setId ID to set of extra incentive fields to show
 */
function displayIncentiveExtras(display = 'none', setId) {
    var incentiveSetId = document.getElementById('incentive_set_' + setId);
    if (incentiveSetId) {
        var incentiveParentDiv = incentiveSetId.parentNode.parentNode.parentNode.parentNode.parentNode.getAttribute('id');
        var divId = parseInt(incentiveParentDiv.substring(8)) + 1; // 8 because of "div_top_x" used in class name
        var divSet = divId + 11; // 11 == number of fields in the group set

        for (var i = divId; i < divSet; i++) {
            document.getElementById('div_top_' + i).style.display = display;
        }
    }
}

/**
 * Blank out fields, usually when asked to be hidden
 *
 * @param {string} setId ID to set of extra incentive fields to show
 */
function resetIncentiveExtras(setId) {
    document.getElementsByName('incentive_signon_' + setId)[0].value = '';
    document.getElementsByName('incentive_type_' + setId)[0].value = '';
    document.getElementsByName('incentive_value_' + setId)[0].value = '';
    document.getElementsByName('accelerator_per_' + setId)[0].value = '';
    document.getElementsByName('accelerator_type_' + setId)[0].value = '';
    document.getElementsByName('max_award_' + setId)[0].value = '';
    document.getElementsByName('max_spend_' + setId)[0].value = '';
    document.getElementsByName('min_spend_' + setId)[0].value = '';
    document.getElementsByName('window_' + setId)[0].value = '';
    document.getElementsByName('category_limited_' + setId)[0].checked = false;
    document.getElementsByName('window_fixed_date_' + setId)[0].checked = false;
    document.getElementsByName('category_limited_' + setId)[1].checked = false;
    document.getElementsByName('window_fixed_date_' + setId)[1].checked = false;
}
function disableIncentiveExtras(setId, attr = '') {
    if(attr == 'disabled'){//alert(setId+'disabled');
        document.getElementsByName('incentive_signon_' + setId)[0].disabled = true;
        document.getElementsByName('incentive_type_' + setId)[0].disabled = true;
        document.getElementsByName('incentive_value_' + setId)[0].disabled = true;
        document.getElementsByName('accelerator_per_' + setId)[0].disabled = true;
        document.getElementsByName('accelerator_type_' + setId)[0].disabled = true;
        document.getElementsByName('max_award_' + setId)[0].disabled = true;
        document.getElementsByName('max_spend_' + setId)[0].disabled = true;
        document.getElementsByName('min_spend_' + setId)[0].disabled = true;
        document.getElementsByName('window_' + setId)[0].disabled = true;
        document.getElementsByName('category_limited_' + setId)[0].disabled = true;
        document.getElementsByName('window_fixed_date_' + setId)[0].disabled = true;
        document.getElementsByName('category_limited_' + setId)[1].disabled = true;
        document.getElementsByName('window_fixed_date_' + setId)[1].disabled = true;
    }
    if(attr == 'enabled'){//alert(setId+'enabled');
        document.getElementsByName('incentive_signon_' + setId)[0].disabled = false;
        document.getElementsByName('incentive_type_' + setId)[0].disabled = false;
        document.getElementsByName('incentive_value_' + setId)[0].disabled = false;
        document.getElementsByName('accelerator_per_' + setId)[0].disabled = false;
        document.getElementsByName('accelerator_type_' + setId)[0].disabled = false;
        document.getElementsByName('max_award_' + setId)[0].disabled = false;
        document.getElementsByName('max_spend_' + setId)[0].disabled = false;
        document.getElementsByName('min_spend_' + setId)[0].disabled = false;
        document.getElementsByName('window_' + setId)[0].disabled = false;
        document.getElementsByName('category_limited_' + setId)[0].disabled = false;
        document.getElementsByName('window_fixed_date_' + setId)[0].disabled = false;
        document.getElementsByName('category_limited_' + setId)[1].disabled = false;
        document.getElementsByName('window_fixed_date_' + setId)[1].disabled = false;
    }
}
/**
 * Check that dependencies required to show extra incentive fields are a go
 */
function validIncentiveDependencies() {
    return (showIncentiveFields('ct_incentive') && validIncentiveCommunicationType() &&
        validIncentiveAudience() && validIncentiveMediaChannel());
}

/**
 * Check Audience matches those that will trigger Incentive fields to show
 */
function validIncentiveAudience() {
    var audience = document.getElementsByName('mPanelID')[0].value;

    // 1 == Consumer; 2 == Employer/Business Owner
    return (audience == 1 || audience == 2);
}

/**
 * Check Communication Type matches those that will trigger Incentive fields to show
 */
function validIncentiveCommunicationType() {
    var communicationType = document.getElementById('agentCommunicationID').value;

    // 1 == Bonuses/Contests/Incentives
    return (communicationType == 1);
}

/**
 * Check Media Channel matches those that will trigger Incentive fields to show
 */
function validIncentiveMediaChannel() {
    var mediaChannel = document.getElementsByName('mChannelID')[0].value;

    // 1 == Direct Mail
    return (mediaChannel == 1);
}

/**
 * Show/hide the "Add sign-on #" links from the admin view, at least allowing for them to be toggled back
 * when the criteria is met. But default is to not show until ready.
 */
function setupIncentiveExtras() {
    var displayCSS = (validIncentiveDependencies()) ? 'block' : 'none';
    var incentiveExtraMarkers = document.getElementsByClassName('incentive_extra_set');

    for (var i = 0; i < incentiveExtraMarkers.length; i++) {
        if (incentiveExtraMarkers[i]) {
            incentiveExtraMarkers[i].parentNode.parentNode.parentNode.parentNode.parentNode.style.display = displayCSS;

            if (displayCSS == 'block') {
                displayIncentiveExtras('none', 2);
                displayIncentiveExtras('none', 3);
            }
        }
    }
}

/**
 * Expand the extra set of incentives if some of the fields have been filled from before
 */
function displayExistingIncentiveExtras() {

    if (document.getElementsByName('incentive_signon_2').length>0){
        if (document.getElementsByName('incentive_signon_2')[0].value != '' ||
            document.getElementsByName('incentive_type_2')[0].value != '') {
            showIncentiveExtras(2);
        }
    }
    if (document.getElementsByName('incentive_signon_3').length>0){
        if (document.getElementsByName('incentive_signon_3')[0].value != '' ||
            document.getElementsByName('incentive_type_3')[0].value != '') {
            showIncentiveExtras(3);
        }
    }
}

setupIncentiveExtras();
setTimeout(displayExistingIncentiveExtras, 2000);

/**
 * Look at the hidden input field containing current set of Sector/Category and return them
 *
 * @return {array} IDs of currently chosen sectors
 */
function getCurrentSectors() {
    var chosenSectors = document.getElementById('scsc_comboIDs').value.split('|');
    var sectors = Array();

    for (var i = 0, n = chosenSectors.length; i < n; i++) {
        var sectorCategoryCombo = chosenSectors[i].split('_');
        sectors.push(sectorCategoryCombo[0]);
    }

    return sectors;
}

function checkSector(ids){
	var valArray = ids.split(',');
	for(var i=0;i<valArray.length;i++){
		if(checkSCSC(valArray[i],0)>0){
			return true;
		}
	}
	return false;
}
function checkCategory(ids){
	var valArray = ids.split(',');
	for(var i=0;i<valArray.length;i++){
		if(checkSCSC(valArray[i],0)>0){
			return true;
		}
	}
	return false;
}
function checkSubCategory(ids){
	var valArray = ids.split(',');
	for(var i=0;i<valArray.length;i++){
		if(checkSCSC(valArray[i],0)>0){
			return true;
		}
	}
	return false;
}
function checkMailingType(ids){
	return checkMultiSelect(document.prodForm.mTypeID,ids);
}
function checkMediaChannel(ids){
	return checkMultiSelect(document.prodForm.mChannelID,ids);
}
function checkCommunicationType(ids){
	return checkMultiSelect(document.prodForm['agentCommunicationID[]'],ids);
}
function checkMediaPanel(ids){
	return checkMultiSelect(document.forms.prodForm.mPanelID,ids);
}
function checkDeliveryMethod(ids){
	return checkMultiSelect(document.forms.prodForm.delmethid,ids);
}
function checkMultiSelect(obj,ids){
	var valArray = ids.split(',');
	for(var i=0;i<obj.length;i++){
		if(obj.options[i].selected && in_array(obj.options[i].value,valArray)!=-1){
			return true;
		}
	}
	return false;
}
function sendColleagueA(ID,type) {
	var wind = window.open('../sendLink.php?id='+ID+'&send_mode'+type,"coll"+ID,"left=20, top=20, scrollbars=yes, resizable=yes, width=625, height=475");
	wind.focus();
}
function checkAllDates(){
	var oe_date = document.prodForm.OfferExpiryDate.value;
	var ei_date = '0000-00-00';
	if(document.prodForm.entryID){
		ei_date = document.prodForm.entryID.value.substring(0,10);
	}
	else if(document.prodForm.firstSeen){
		ei_date = document.prodForm.firstSeen.value;
	}
	var firstseen = '0000-00-00';
	var lastseen = '0000-00-00';
	var competi_idsval = document.forms.prodForm.competi_ids.value;
	var valArray = competi_idsval.split(',');
	for(var i in valArray){
		var valArray2 = valArray[i].split('|');
		if(valArray2.length>1){
			var pdate = valArray2[1].substring(0,10);
			
			if(pdate<firstseen || firstseen=='0000-00-00'){
				firstseen = pdate;
			}
			if(pdate>lastseen || lastseen=='0000-00-00'){
				lastseen = pdate;
			}
		}
	}
	var pid_idsval = document.forms.prodForm.pub_ids.value;
	valArray = pid_idsval.split(',');
	for(var i in valArray){
		var valArray2 = valArray[i].split('|');
		if(valArray2.length>1){
			var pdate = valArray2[1].substring(0,4)+'-'+valArray2[1].substring(4,6)+'-'+valArray2[1].substring(6,8);
			if(pdate<firstseen || firstseen=='0000-00-00'){
				firstseen = pdate;
			}
			if(pdate>lastseen || lastseen=='0000-00-00'){
				lastseen = pdate;
			}
		}
	}
	if(firstseen=='0000-00-00'){
		firstseen = ei_date;
		lastseen = ei_date;
	}
	if(ei_date!='0000-00-00' && firstseen!=ei_date){
		alert('Entry ID ('+ei_date+') First Seen ('+firstseen+') error!');
		return false;
	}
	if(oe_date!=''){
		var y = parseInt(oe_date.substring(0,4),10);
		var m = parseInt(oe_date.substring(5,7),10)-1;
		var d = parseInt(oe_date.substring(8,10),10);
		var dat = new Date(y, m, d, 0, 0, 0, 0);
		var ts = dat.getTime();
		var off = 259200000;//86400*3;
		if(document.forms.prodForm.mChannelID.selectedIndex>0){
			chan = document.forms.prodForm.mChannelID.options[document.forms.prodForm.mChannelID.selectedIndex].value;
			if(chan==2 || chan==3){
				off = 0;
			}
		}
		ts = ts - off;
		dat = new Date(ts);
		var y2 = parseInt(lastseen.substring(0,4),10);
		var m2 = parseInt(lastseen.substring(5,7),10)-1;
		var d2 = parseInt(lastseen.substring(8,10),10);
		var dat2 = new Date(y2, m2, d2, 0, 0, 0, 0);
		if(dat2.getTime()>dat.getTime()){
			alert('Offer Expiry ('+oe_date+') Last Seen ('+lastseen+') error!');
			return false;
		}
	}
	return true;
}
function doscsc_sort(){
	var new_scsc_sortArray = new Array();
	var sectorCatSub = new Array('sectorID[]','categoryID[]','subCategoryID[]');
	var selectedSArray = new Array();
	for(var n=0;n<sectorCatSub.length;n++){
		var sectordoc = document.forms.prodForm[sectorCatSub[n]];
		for(var j=0;j<sectordoc.options.length;j++){
			if(sectordoc.options[j].selected){
				selectedSArray[selectedSArray.length] = sectordoc.options[j].value;
			}
		}
	}
	var checkArray = new Array('212','92','91','93','103','102'); //178
	var checkArrays = new Array(scsc_sortArray,checkArray);
	for(var a=0;a<checkArrays.length;a++){
		for(var b=0;b<checkArrays[a].length;b++){
			if(in_array(checkArrays[a][b],selectedSArray)!=-1 && in_array(checkArrays[a][b],new_scsc_sortArray)==-1){
				new_scsc_sortArray[new_scsc_sortArray.length] = checkArrays[a][b];
			}
		}
	}
	var obj = document.getElementById('div_scsc_sort');
	if(obj){
		while(obj.childNodes && obj.childNodes.length>0){
			var kid = obj.childNodes;
			obj.removeChild(kid[0]);
		}
		for(var i=0;i<new_scsc_sortArray.length;i++){
			scsc_sortArray[scsc_sortArray.length] = new_scsc_sortArray[i];
			var newdiv = document.createElement('div');
			newdiv.id = 'div_scsc_sort_'+new_scsc_sortArray[i];
			newdiv.style.margin = '4px';
			var ins = document.createElement('input');
			ins.type = 'text';
			ins.value = i+1;
			ins.name = 'div_scsc_sort_'+new_scsc_sortArray[i]+'_val';
			ins.id = 'div_scsc_sort_'+new_scsc_sortArray[i]+'_val';
			ins.size = '3';
			ins.maxlength = '255';
			ins.className = 'input_box';
			ins.style.textAlign = 'center';
			newdiv.appendChild(ins);
			var newnode = document.createTextNode(' '+subcategoryArray[new_scsc_sortArray[i]]);
			newdiv.appendChild(newnode);
			obj.appendChild(newdiv);
		}
	}
	scsc_sortArray = new_scsc_sortArray;
}
function checkProductName(){
	var payment_cards = false;
	var check = 0;
	if(checkSCSC('90',1)==1){
		check = processajax('<?php if($fromtemp) echo 'admin/'; ?>piece.php', false, 'POST', 'check=1&cpn_ids='+escape(document.forms.prodForm.cmp_ids.value), false, '');
	}
	if(check==1){
		document.forms.prodForm.productName.readOnly = true;
		showDiv_outer('showbox_cpns_outer','showbox_cpns');
	}
	else{
		document.forms.prodForm.productName.readOnly = false;
		hideDiv_outer('showbox_cpns_outer','showbox_cpns');
	}
}
function do_SCSC(obj,type,obj_to,asy){
	var tid = 0;
	for(var j=0;j<obj.options.length;j++){
		if(obj.options[j].selected){
			tid = obj.options[j].value;
			break;
		}
	}
	obj_to.selectedIndex = 0;
	obj_to.options.length = 1;
	obj_to.style.display = 'none';
	processajax('<?php if($fromtemp) echo 'admin/'; ?>scsc_info.php', asy, 'POST', type+'='+tid, obj_to, 'doInnerSelect');
}
function doInnerSelect(response, obj){
	if(response.length>0){
		//obj.innerHTML = response;
		var opt = 1;
		var lines = response.split("\n");
		for(var i=0;i<lines.length;i++){
			var line = lines[i].split("\t");
			if(line.length==2){
				obj.options[opt] = new Option(line[1], line[0], false, false);
				opt = opt + 1;
			}
		}
		obj.style.display = 'block';
	}
}
function add_SCSC(){
	var scsc_names = new Array('combo_sid','combo_cid','combo_scid','combo_sscid');
	var scsc_values = new Array();
	var scsc_text = new Array();
        var category_is_selected = false;
        var is_hospital_cat=false;
	for (var j = 0; j < scsc_names.length; j++) {
		scsc_values[j] = '0';
		scsc_text[j] = '';
		var obj = document.prodForm[scsc_names[j]];

		for (var k = 0; k < obj.options.length; k++) {
                    if (obj.options[k].selected && obj.options[k].value.length > 0) {
                            scsc_values[j] = obj.options[k].value;
                            scsc_text[j] = obj.options[k].text;
                            break;
                    }
		}
                // Added for hospital category
                var ids_string = scsc_values.toString();
                if(ids_string=='4,454,0' && is_hospital_cat===false ){
                    is_hospital_cat=true;                    
                    break;
                    //return false;
                }
                // End Added for hospital category
                
                // Added if condition for hospital category                
                if (ids_string!=4 && ids_string!='4,454') {   
                    obj.selectedIndex = 0;

                    if (scsc_names[j]!='combo_sid') {
                            obj.options.length = 1;
                            obj.style.display = 'none';
                    }
                }
                
	}
        // Added for hospital category
        if(is_hospital_cat){
            alert('Sub Category is required');
            return false;
        }else{
          document.prodForm.combo_cid.options.length = 1;
          document.prodForm.combo_cid.style.display = 'none';
          document.prodForm.combo_sid.selectedIndex = 0;
          is_hospital_cat=false;
        }
        // End Added for hospital category

    if (scsc_values[1] != '0' || window.location.href.indexOf('manageproduct') != -1) {
        category_is_selected = true; // Skip category requirement on mass update tool
    }

	if (scsc_values[0] != '0' && category_is_selected) {
		var scsc_combo = '';
		var scsc_combo_text = '';
		for(var j=0;j<scsc_values.length;j++){
			if(scsc_combo.length>0){
				scsc_combo = scsc_combo + '_';
				scsc_combo_text = scsc_combo_text + ' / ';
			}
			scsc_combo = scsc_combo + scsc_values[j];
			scsc_combo_text = scsc_combo_text + scsc_text[j];
			getBlock(scsc_values[j],true);
		}
		var exists = 0;
		var scsc_comboIDs_val = document.prodForm.scsc_comboIDs.value;
		var valArray = scsc_comboIDs_val.split('|');
		var sortorder = valArray.length;
		for(var i=0;i<sortorder;i++){
			if(valArray[i]==scsc_combo){
				exists = 1;
			}
		}
		if(!exists){
			if(scsc_comboIDs_val.length>0){
				scsc_comboIDs_val = scsc_comboIDs_val + '|';
			}
			scsc_comboIDs_val = scsc_comboIDs_val + scsc_combo;
			document.prodForm.scsc_comboIDs.value = scsc_comboIDs_val;
			/* ###########  Communication Type Implementation ############ */
			//if (window.location.host == "demo.competiscan.com" || window.location.host == "localhost") {
				getSectorByIDs(scsc_comboIDs_val);
			//}
			/* ###########  Communication Type Implementation ############ */
			displaySCSC(scsc_combo,scsc_combo_text);
                         
		}
		var scsc_combo_edit = document.prodForm.scsc_combo_edit.value;
		document.prodForm.scsc_combo_edit.value = '';
		add_SCSC_link_text();
		if(scsc_combo_edit!='' && scsc_combo_edit!=scsc_combo){
			var sortorder_edit = removeSCSC(scsc_combo_edit);
			var newsort = sortorder_edit - (sortorder - 1);
			if(newsort<0){
				sortSCSC(scsc_combo,newsort);
			}
		}
		else{
			afterSCSC();
		}
                doPromotionRetailCompany('<?php if($updID!='' && $updID!='0'){echo $updID;}elseif($muid!='' && $muid!='0'){ echo $muid;} ?>'<?php if($fromtemp!=''){ echo ",isTmp=1";} ?>);
		checkCompanyFields('scsc_comboIDs');
		return true;
	}
	else{
		clearSCSC();
                //doPromotionRetailCompany();
		alert('Sector and Category are required');
		return false;
	}
        
}
function displaySCSC(scsc_combo,scsc_combo_text){
	var newobj = document.getElementById('scsc_combos');
	if(newobj){
		var newnode = document.createElement('div');
		newnode.id = 'combo'+scsc_combo;
		newnode.style.fontWeight = 'bold';
		newnode.style.marginBottom = '2px';
		newnode.appendChild(document.createTextNode(scsc_combo_text+' '));
		<?php if(!isset($_GET['muid']) || $fromtemp){ ?>
			var newnode2 = document.createElement('a');
			newnode2.href = '#';
			<?php if($fromtemp) echo "newnode2.className = 'bluelink';"; ?>;
			newnode2.onclick = new Function("sortSCSC('"+scsc_combo+"',-1); return false;");
			newnode2.appendChild(document.createTextNode('Up'));
			newnode.appendChild(newnode2);
			newnode.appendChild(document.createTextNode(' '));
			newnode2 = document.createElement('a');
			newnode2.href = '#';
			<?php if($fromtemp) echo "newnode2.className = 'bluelink';"; ?>;
			newnode2.onclick = new Function("sortSCSC('"+scsc_combo+"',1); return false;");
			newnode2.appendChild(document.createTextNode('Down'));
			newnode.appendChild(newnode2);
			newnode.appendChild(document.createTextNode(' '));
			newnode2 = document.createElement('a');
			newnode2.href = '#';
			<?php if($fromtemp) echo "newnode2.className = 'bluelink';"; ?>;
			newnode2.onclick = new Function("editSCSC('"+scsc_combo+"'); return false;");
			newnode2.appendChild(document.createTextNode('Edit'));
			newnode.appendChild(newnode2);
			newnode.appendChild(document.createTextNode(' '));
			newnode2 = document.createElement('a');
			newnode2.href = '#';
			<?php if($fromtemp) echo "newnode2.className = 'bluelink';"; ?>;
			newnode2.onclick = new Function("removeSCSC('"+scsc_combo+"'); return false;");
			newnode2.appendChild(document.createTextNode('Remove'));
			newnode.appendChild(newnode2);
		<?php } ?>
		newobj.appendChild(newnode);
	}
}
function clearSCSC(){
	do_SCSC(document.prodForm.combo_sid,'sid',document.prodForm.combo_cid,true);
	do_SCSC(document.prodForm.combo_cid,'cid',document.prodForm.combo_scid,true);
	do_SCSC(document.prodForm.combo_scid,'scid',document.prodForm.combo_sscid,true);
	document.prodForm.scsc_combo_edit.value = '';
	add_SCSC_link_text();
        doPromotionRetailCompany('<?php if($updID!='' && $updID!='0'){echo $updID;}elseif($muid!='' && $muid!='0'){ echo $muid;} ?>'<?php if($fromtemp!=''){ echo ",isTmp=1";} ?>);
}
function add_SCSC_link_text(){
	var lobj = document.getElementById('add_SCSC_link');
	if(lobj){
		if(document.prodForm.scsc_combo_edit.value==''){
			my_innerHTML_text(lobj,'Add');
		}
		else{
			my_innerHTML_text(lobj,'Update');
		}
	}
}
function editSCSC(scsc_combo){
	var valArray = scsc_combo.split('_');
	var scsc_names = new Array('combo_sid','combo_cid','combo_scid','combo_sscid');
	for(var j=0;j<scsc_names.length;j++){
		var obj = document.prodForm[scsc_names[j]];
		obj.selectedIndex = 0;
		for(var k=0;k<obj.options.length;k++){
			if(obj.options[k].value==valArray[j]){
				obj.options[k].selected = true;
				if(scsc_names[j]=='combo_sid'){
					do_SCSC(document.prodForm.combo_sid,'sid',document.prodForm.combo_cid,false);
				}
				if(scsc_names[j]=='combo_sid' || scsc_names[j]=='combo_cid'){
					do_SCSC(document.prodForm.combo_cid,'cid',document.prodForm.combo_scid,false);
				}
				if(scsc_names[j]=='combo_sid' || scsc_names[j]=='combo_cid' || scsc_names[j]=='combo_scid'){
					do_SCSC(document.prodForm.combo_scid,'scid',document.prodForm.combo_sscid,false);
				}
				break;
			}
		}
	}
	document.prodForm.scsc_combo_edit.value = scsc_combo;
	add_SCSC_link_text();
        doPromotionRetailCompany('<?php if($updID!='' && $updID!='0'){echo $updID;}elseif($muid!='' && $muid!='0'){ echo $muid;} ?>'<?php if($fromtemp!=''){ echo ",isTmp=1";} ?>);
}
function afterSCSC(){
	dependsSector();
	checkDeps_s();
	checkProductName();
	getDeps('offerOrigin');
	markInsuranceexchange();
	/*###########  Communication Type Implementation ############*/
	//getDeps('agentCommunicationID[]');
	/*###########  Communication Type Implementation ############*/
	getDeps('responseMechID[]');
	getDeps('riders[]');
	getDeps('multiculturalmarkets[]');
	getVari();
	doIncentive();
	check_BA();
	checkSectorWV(3);
}
function removeSCSC(scsc_combo){
	var scsc_comboIDs_val = document.prodForm.scsc_comboIDs.value;
	var valArray = scsc_comboIDs_val.split('|');
	if(valArray.length==1){
		if(!confirm('This will clear all Sector/Category/Sub Category dependent fields.\nContinue?')){
			return -1;
		}
	}
	var sortorder = 0;
	var obj = document.getElementById('combo'+scsc_combo);
	if(obj){
		obj.parentNode.removeChild(obj);
		var scsc_comboIDs_val_new = '';
		for(var i=0;i<valArray.length;i++){
			if(valArray[i]!=scsc_combo){
				if(scsc_comboIDs_val_new.length>0){
					scsc_comboIDs_val_new = scsc_comboIDs_val_new + '|';
				}
				scsc_comboIDs_val_new = scsc_comboIDs_val_new + valArray[i];
			}
			else{
				sortorder = i;
				var valArray2 = valArray[i].split('_');
				getBlock(valArray2[0],false);
				getBlock(valArray2[1],false);
				getBlock(valArray2[2],false);
			}
		}
		document.prodForm.scsc_comboIDs.value = scsc_comboIDs_val_new;
		/* ###########  Communication Type Implementation ############ */
		//if (window.location.host == "demo.competiscan.com" || window.location.host == "localhost") {
			getSectorByIDs(scsc_comboIDs_val_new);
		//}
		/* ###########  Communication Type Implementation ############ */
		var selectedSArray = returnSCSC(0);
		for(var m=0;m<selectedSArray.length;m++){
			getBlock(selectedSArray[m],true);
		}
	}
	afterSCSC();
        doPromotionRetailCompany('<?php if($updID!='' && $updID!='0'){echo $updID;}elseif($muid!='' && $muid!='0'){ echo $muid;} ?>'<?php if($fromtemp!=''){ echo ",isTmp=1";} ?>);
	return sortorder;
}
function sortSCSC(idval,sort){
	var newval = '';
	var obj = document.getElementById('scsc_combos');
	var obj2 = document.prodForm.scsc_comboIDs;
	var idsval = obj2.value;
	var valArray = idsval.split('|');
	for(var i=0;i<valArray.length;i++){
		if(valArray[i]==idval){
			if(valArray[i+sort]){
				var tmpidval = valArray[i];
				valArray[i] = valArray[i+sort];
				valArray[i+sort] = tmpidval;
				var movenode = obj.removeChild(obj.childNodes[i]);
				if(i+sort>=obj.childNodes.length){
					obj.appendChild(movenode);
				}
				else{
					obj.insertBefore(movenode,obj.childNodes[i+sort]);
				}
				break;
			}
		}
	}
	for(var i=0;i<valArray.length;i++){
		if(newval.length>0){
			newval = newval + '|';
		}
		newval = newval + valArray[i];
	}
	obj2.value = newval;
	checkProductName();
}

function checkSCSC(idval,typei){ //0,1,2,3,4
	var valArray = document.prodForm.scsc_comboIDs.value.split('|');
	for(var i=0;i<valArray.length;i++){
		var valArray2 = valArray[i].split('_');
		if(typei==0){
			for(var j=0;j<valArray2.length;j++){
				if(valArray2[j]==idval){
					return i+1;
				}
			}
		}
		else{
			if(valArray2[typei-1]==idval){
				return i+1;
			}
		}
	}
	return 0;
}
function returnSCSC(typei){ //0,1,2,3,4
	var valArray = document.prodForm.scsc_comboIDs.value.split('|');
	var outArray = new Array();
	for(var i=0;i<valArray.length;i++){
		var valArray2 = valArray[i].split('_');
		if(typei==0){
			for(var j=0;j<valArray2.length;j++){
				outArray[outArray.length] = valArray2[j];
			}
		}
		else{
			outArray[i] = valArray2[typei-1];
		}
	}
	return outArray;
}
function compare_variant(){
	var divname = 'compare_variant_div';
	var variant = document.prodForm.variant.value;
	if(variant.length>0){
		processajax('<?php if($fromtemp) echo 'admin/'; ?>checkvar.php', true, 'POST', 'eid='+encodeURIComponent(variant), document.getElementById(divname), 'doResponsePs');
	}
	else{
		hideDiv_inner(divname);
	}
}
function validate_variant(){
	var variant = document.prodForm.variant.value;
	if(variant.length>0 && document.prodForm.sectorID_v){
		var sectorID_v = document.prodForm.sectorID_v.value;
		if(sectorID_v!='' && sectorID_v!='0'){
			sectorID_v = sectorID_v.split(',');
			var selectedArray = returnSCSC(1);
			for(var j=0;j<selectedArray.length;j++){
				if(in_array(selectedArray[j],sectorID_v)==-1){
					return confirm('Sectors do not match Variant.\nContinue?');
				}
			}
			var categoryID_v = document.prodForm.categoryID_v.value;
			if(categoryID_v!='' && categoryID_v!='0'){
				categoryID_v = categoryID_v.split(',');
				selectedArray = returnSCSC(2);
				for(j=0;j<selectedArray.length;j++){
					if(in_array(selectedArray[j],categoryID_v)==-1){
						return confirm('Categories do not match Variant.\nContinue?');
					}
				}
				var subCategoryID_v = document.prodForm.subCategoryID_v.value;
				if(subCategoryID_v!='' && subCategoryID_v!='0'){
					subCategoryID_v = subCategoryID_v.split(',');
					selectedArray = returnSCSC(3);
					for(j=0;j<selectedArray.length;j++){
						if(in_array(selectedArray[j],subCategoryID_v)==-1){
							return confirm('Sub Categories do not match Variant.\nContinue?');
						}
					}
					var subSubCategoryID_v = document.prodForm.subSubCategoryID_v.value;
					if(subSubCategoryID_v!='' && subSubCategoryID_v!='0'){
						subSubCategoryID_v = subSubCategoryID_v.split(',');
						selectedArray = returnSCSC(4);
						for(j=0;j<selectedArray.length;j++){
							if(in_array(selectedArray[j],subSubCategoryID_v)==-1){
								return confirm('Sub Sub Categories do not match Variant.\nContinue?');
							}
						}
					}
				}
			}
		}
		var companyID_v = document.prodForm.companyID_v.value;
		var cmp_ids = document.prodForm.cmp_ids.value.split(',');
		if(cmp_ids[0] && companyID_v!='' && companyID_v!='0' && companyID_v!=cmp_ids[0]){
			return confirm('Companies do not match Variant.\nContinue?');
		}
	}
	return true;
}
function showButtons(){
	var obj = document.getElementById('show_buttons');
	var obj2 = document.getElementById('no_buttons');
	if(obj && obj2){
		<?php 
		if(!empty($nopermission)){
			echo "obj2.innerHTML = 'You do not have Permission';";
		}
		else{
		?>
			obj.style.display = 'block';
			obj2.style.display = 'none';
		<?php 
		}
		?>
	}
}


function showDigitalSource(){ 
    mchid = document.forms.prodForm['mChannelID'].value;
   // var fld = document.getElementById('mChannelID');
   var fld =document.forms.prodForm['mChannelID'];
    var chvalues = [];
    for (var i = 0; i < fld.options.length; i++) {
      if (fld.options[i].selected) {
        chvalues.push(fld.options[i].value);
      }
    }     
      if ( (chvalues.indexOf( '5' ) > -1) || (chvalues.indexOf( '9' ) > -1) || (chvalues.indexOf( '10' ) > -1)  ){           
           
            //document.getElementById('div_digital_device').style.display = 'none';
            document.getElementById('div_digital_device').style.display = 'block';
      }else{
      
          document.getElementById('div_digital_device').style.display = 'none';
          //document.getElementById('div_digital_device').parentNode.style.display='none';
      }
    
}


function MM_findObj(n, d) { //v4.01
var p,i,x; if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[n];
for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers.document);
if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function SortByName(x,y) {
  return ((x.type == y.type) ? 0 : ((x.type > y.type) ? 1 : -1 ));
}

/* ###########  Communication Type Implementation ############ */
//if (window.location.host == "demo.competiscan.com" || window.location.host == "localhost") {
	$('.audience').on('change', function(){
		var audience = $(this).val();
		var sector = $("#scsc_comboIDs").val();
		var productID = $("#hiddenProductID").val();
		var productTempID = $("#hiddenProductTempID").val();
		var selectedCommunicationID = $("#agentCommunicationID option:selected").map(function () {
									        return $(this).val();
									    }).get().join(',');
		if (window.location.host == "localhost") {
	        var origin = window.location.origin+'/competiscan.com/admin/get-communication-type.php';
                } else {
                    var origin = window.location.origin+'/admin/get-communication-type.php';
                }

		$.ajax({
			type: 'POST',
	        url: origin,
	        data: {audienceValue: audience, sectorValue: sector, productID: productID, productTempID: productTempID, selectedCommunicationID: selectedCommunicationID},
	        success: function (response) {
	        	var json = $.parseJSON(response);
	        	var commTypeAsc = json.communicationID.sort(SortByName);
	        	$('#agentCommunicationID >').remove();  
	        	var Anyselected = '';
	        	if(json.agentCommunicationID != ''){
	        		Anyselected = '';
	        	}else{
	        		Anyselected = 'selected';
	        	}
	        	$('#agentCommunicationID').append('<option value="" '+Anyselected +'>Any</option>');
	        	$.each(commTypeAsc, function(index, value){
	        		var selected = '';
	        		if($.inArray(value.ID, json.agentCommunicationID) != -1){
	        			selected = 'selected="'+selected+'"';
	        		}else{
	        			selected = '';
	        		}

	                var communicatioType = '<option value="'+value.ID+'" '+selected+'>'+value.type+'</option>';
	                $('#agentCommunicationID').append(communicatioType);
	            });  
	        },
	        error: function (data) {
	        	console.log("Something went wrong!");
	        },
		});
	});

	function getSectorByIDs(sectorIds,audienceIds=''){
		var audience;
		if(audienceIds == ''){
			audience = $('.audience').children("option:selected").val();
		}else{
			audience = audienceIds;
		}

		var selectedCommunicationID = $("#agentCommunicationID option:selected").map(function () {
									        return $(this).val();
									    }).get().join(',');
		var sector = sectorIds;
		var productID = $("#hiddenProductID").val();
		var productTempID = $("#hiddenProductTempID").val();
		if (window.location.host == "localhost") {
	        var origin = window.location.origin+'/competiscan.com/admin/get-communication-type.php';
                } else {
                    var origin = window.location.origin+'/admin/get-communication-type.php';
                }
		$.ajax({
			type: 'POST',
	        url: origin,
	        async: false,
	        data: {audienceValue: audience, sectorValue: sector, productID: productID, productTempID: productTempID, selectedCommunicationID: selectedCommunicationID},
	        success: function (response) {
	        	if($.trim(response)){
		        	var json = $.parseJSON(response);
		        	var Anyselected = '';
		        	if(json.agentCommunicationID != ''){
		        		Anyselected = '';
		        	}else{
		        		Anyselected = 'selected';
		        	}
		        	$('#agentCommunicationID').append('<option value="" '+Anyselected +'>Any</option>');
		        	if(json.communicationID != undefined){
		        		var commTypeAsc = json.communicationID.sort(SortByName);
		        		$('#agentCommunicationID >').remove();  
		        		if(json.agentCommunicationID != ''){
			        		Anyselected = '';
			        	}else{
			        		Anyselected = 'selected';
			        	}
			        	$('#agentCommunicationID').append('<option value="" '+Anyselected +'>Any</option>');
			        	$.each(commTypeAsc, function(index, value){
			        		var selected = '';
			        		if($.inArray(value.ID, json.agentCommunicationID) != -1){
			        			selected = 'selected="'+selected+'"';
			        		}else{
			        			selected = '';
			        		}

			                var communicatioType = '<option value="'+value.ID+'"'+selected +'>'+value.type+'</option>';
			                $('#agentCommunicationID').append(communicatioType);
			            });
		        	}
		        }
	        },
	        error: function (data) {
	        	console.log("Something went wrong!");
	        },
		});
	}

	var audience = $('.audience').children("option:selected").val();
	var sector = $("#scsc_comboIDs").val();

	if(audience != '' || sector != ''){
		getSectorByIDs(sector,audience);
	}
//}
/* ###########  Communication Type Implementation ############ */
/*Start permotion add new field*/
 function doPromotionRetailCompany(pid,formtemp=0){
     var disable='<?php echo $disabled; ?>';
     //alert(disable);
     var cmp_ids = document.prodForm.cmp_ids.value.split(',');
     var scsc_comboIDs_val = document.prodForm.scsc_comboIDs.value;
     var valArray = scsc_comboIDs_val.split('|');
     //alert(valArray);
     outArray=new Array();
     lastSubCatArray=new Array();
     for(var i=0;i<valArray.length;i++){
	valArray2 = valArray[i].split('_');
        lastSubCatValue='';
        for(var j=0;j<valArray2.length;j++){
                   //alert(valArray2[j]);
                   outArray[outArray.length] = valArray2[j];
                   if(valArray2[j]>0){
                       var lastSubCatValue=valArray2[j];
                   }
           }
           lastSubCatArray.push(lastSubCatValue); 
		
        }
       //alert(lastSubCatArray); 
       //return false;
      if(cmp_ids!='' && cmp_ids.length>0 && valArray.length>0 && outArray.indexOf("266") !== -1){
        if (window.location.host == "localhost") {
	        var origin = window.location.origin+'/competiscan.com/admin/promotion_companies_ajax.php';
	    } else {
	        var origin = window.location.origin+'/admin/promotion_companies_ajax.php';
	    }

        $.ajax({          
            type: "POST",
            url: origin,
            data: {lastSubCatArray:lastSubCatArray,companyid:cmp_ids,pid:pid,formtemp:formtemp,disable:disable,action:'getprocompany'},
            success: function(data){
            //alert(data);
                if(outArray.indexOf("266") !== -1){
                $("#div_promotion").html(data);	
                }else{
                 $("#div_promotion").html('');   
                } 
           }           
       });
    }else{
        $("#div_promotion").html('');
    }
        
}

function addcompanypromotionfield(lstsubcatid,tempid='',countnum='',isallreadysaved='',istemp='',formtemp){
   var cmpelement = document.getElementById('selected_promotion_company');
    var cmpid =cmpelement.options[cmpelement.selectedIndex].value;
    if(lstsubcatid=='0'){
     var ptypeelement = document.getElementById('selected_product_type');
     var lstsubcatid =ptypeelement.options[ptypeelement.selectedIndex].value;   
    }
    //alert(cmpid);
   var remove_id = document.getElementById("remove_promotion_link").value; 
    if (window.location.host == "localhost") {
	    var origin = window.location.origin+'/competiscan.com/admin/promotion_companies_ajax.php';
        } else {
            var origin = window.location.origin+'/admin/promotion_companies_ajax.php';
        }
    if(cmpid>0 && lstsubcatid>0){
    $.ajax({          
            type: "POST",
            url: origin,
            data: {lstsubcatid:lstsubcatid,companyid:cmpid,tempid:tempid,countnum:countnum,isallreadysaved:isallreadysaved,istemp:istemp,remove_id:remove_id,formtemp:formtemp,action:'getaddpromotionfield'},
            success: function(data){
                if(data==10){
                $("#div_promotion_append").html("<div style='float:center;'>You have already added 10 promtion of selected company.</div>");
                }else{
                $("#div_promotion_append").html(data);
                
            }
           }           
       });
   }else{
        $("#div_promotion_append").html('');
   }
}
function checkPromotionBogo(bogo){
    if(bogo=='1'){
        $(".buy_bogo").show();
        $(".get_bogo").show();
    }else{
        $(".buy_bogo").hide();
        $(".get_bogo").hide();
    } 
}

function RemovePromotion(id,isallreadysaved='',istemp='',formtemp){
    //alert(id+"PD"+isallreadysaved+"ISTEMP"+istemp+"formtemp"+formtemp)
    $(".div_promotion_hide").hide();
    $(".save_promotion_hide").hide();
    $(".add_more_promotion_hide").show();
    if(id>0){
        if(confirm('Are you sure you want to delete this promotion?'))
        {
        if((isallreadysaved!='' && istemp==1 && formtemp==0) || (isallreadysaved!='' && istemp==1 && formtemp==1)){
         var remove_id = document.getElementById("remove_promotion_link").value; 
         if(remove_id==''){
            remove_id=id; 
         }else{
          remove_id=remove_id+','+id;
         }
          $('#remove_promotion_link').val(remove_id);
          $("#remove_"+id).hide();
          $("#remove_comma_"+id).hide();
        }
        else{
            if (window.location.host == "localhost") {
	    var origin = window.location.origin+'/competiscan.com/admin/promotion_companies_ajax.php';
            } else {
            var origin = window.location.origin+'/admin/promotion_companies_ajax.php';
            }
            $.ajax({          
                    type: "POST",
                    url: origin,
                    data: {tempid:id,formtemp:formtemp,action:'removepromotion'},
                    success: function(data){
                        //alert(id);    
                        //alert(data);
                        if(data==5){
                        $("#remove_"+id).hide();
                        $("#remove_comma_"+id).hide();
                        }
                   }           
               });
            }
        }
     } 
}
function SavePromotionField(lstsubcatid,updateid='',isallreadysaved='',istemp='',formtemp){
    var compid = document.getElementById("selected_promotion_company").value;
    var promotion_type = document.getElementById("promotion_type").value; 
    var coupon_discount_value = document.getElementById("coupon_discount_value").value;
    var add_price = document.getElementById("add_price").value;
    var regular_price = document.getElementById("regular_price").value;
    var shipping_detail = document.getElementById("shipping_detail").value;
    var online_in_store = document.getElementById("online_in_store").value;
    var qualifier = document.getElementById("qualifier").value;
    var qualifier_minimum_purchase_value = document.getElementById("qualifier_minimum_purchase_value").value;
    var code_required = document.getElementById("code_required").value;
    var bogo = document.getElementById("bogo").value;
    var buy_x = document.getElementById("buy_x").value;
    var get_x = document.getElementById("get_x").value;
    if(bogo==2){
        buy_x='';
        get_x='';
    }
    var count_promotion_no = document.getElementById("count_promotion_no").value;
    if(compid>0){
        if(promotion_type=='0' || promotion_type==''){
            alert('Please select promotion type.');
            return false;
        }
        if (window.location.host == "localhost") {
	    var origin = window.location.origin+'/competiscan.com/admin/promotion_companies_ajax.php';
        } else {
        var origin = window.location.origin+'/admin/promotion_companies_ajax.php';
        }
        $.ajax({          
                type: "POST",
                url: origin,
                data: {lstsubcatid:lstsubcatid,formtemp:formtemp,isallreadysaved:isallreadysaved,istemp:istemp,compid:compid,updateid:updateid,count_promotion_no:count_promotion_no,promotion_type:promotion_type,coupon_discount_value:coupon_discount_value,add_price:add_price,regular_price:regular_price,shipping_detail:shipping_detail,online_in_store:online_in_store,qualifier:qualifier,qualifier_minimum_purchase_value:qualifier_minimum_purchase_value,code_required:code_required,bogo:bogo,buy_x:buy_x,get_x:get_x,action:'add_promotion'},
                success: function(data){
                    //alert(data);
                if(updateid!=''){
                   // $("#div_promotion_show_company").html(data);
                }else{
                     $("#div_promotion_show_company").append(data);
                 }
                $(".save_promotion_hide").hide();
                $(".div_promotion_hide").hide();
                if(count_promotion_no<10){
                 $(".add_more_promotion_hide").show();
                }else{
                   // $("#div_promotion_show_alert").html("<div style='float:center;'>You have already added 10 promtion of selected company.</div>");
                }
            }           
        });
   }
}
function displayPromotionselectdCompany(compid,product_type,productid,formtemp){
    $('.selected_promotion_company  option').each(function() {  
    var getselectedcmp = $(this).val(); 
    if(getselectedcmp){
        $("#selected_promotion_company option[value="+getselectedcmp+"]").attr('selected', false);    
    
        }
    }) 
    $("#selected_promotion_company option[value="+compid+"]").attr('selected', true);
    $("#selected_promotion_company option[value="+compid+"]").prop('selected', true);
   
    $('.selected_product_type  option').each(function() {  
    var getselected_producttype = $(this).val(); 
    if(getselected_producttype){
        $("#selected_promotion_company option[value="+getselected_producttype+"]").attr('selected', false);    
    
        }
    }) 
    $("#selected_product_type option[value="+product_type+"]").attr('selected', true);
    $("#selected_product_type option[value="+product_type+"]").prop('selected', true);
    
    addcompanypromotionfield(product_type,tempid='',countnum='',productid,istemp='',formtemp);
}

/*End permotion add new field*/

/* Start for processed pdf */
function htmlWin(pid) {
	setTimeout(function()
    {
        //alert('msg');
		$("#process_pdf_chkbox_field").show();
		
    }, 
    5000);
    var winy = window.open('https://api3.competiscan.com/html-pdf/v1/temppdf/'+pid,"pdf"+pid,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes, width=900, height=600");
    winy.focus();
}

function checkProcessPdf(muid){
	var chkbox_processpdf = document.getElementById('process_pdf_chkbox');
	if (window.location.host == "localhost") {
	    var ajax_page_url = window.location.origin+'/competiscan.com/admin/ajax_checkprocess_pdf.php';
	} else {
	    var ajax_page_url = window.location.origin+'/admin/ajax_checkprocess_pdf.php';
	}	
    if(chkbox_processpdf.checked){
		document.getElementById("checking_process_pdf").style.display='block';
		document.getElementById("process_pdf_chkbox").checked = false;
		document.getElementById("processpdf_status").innerHTML = "";	

		$.ajax({          
                    type: "POST",
                    url: ajax_page_url,
                    data: {muid:muid,action:'checkprocesspdf'},
                    success: function(data){                     
                        //alert(data);
                        if(data==1){
						 	document.getElementById("process_pdf_chkbox").checked = true;
							document.getElementById("checking_process_pdf").style.display='none';
							document.getElementById("processpdf_status").style.display='block';
							document.getElementById("processpdf_status").innerHTML = " Process PDF is available";
							                      
                        }else{
							document.getElementById("process_pdf_chkbox").checked = false;
							document.getElementById("checking_process_pdf").style.display='none'; 
							document.getElementById("processpdf_status").style.display='block';
							document.getElementById("processpdf_status").innerHTML = " Process PDF is not available";

						}
                  	}           
               });
        //alert('check'+muid);
    } else {
		document.getElementById("processpdf_status").innerHTML = "";	
      //alert('unchecked');
    }
}
    
/* End for processed pdf */
//-->
</script>
<?php 
if($fromtemp){
	$bcolor = '#0055E3';
    $secbcolor = '#E8E8FF';
}
else{
	$bcolor = '#14734F';
    $secbcolor = '#DDF9EE';
}
?>
<div id="showbox" style="display:none;position:absolute;border:solid 1px #ffffff;background:<?php echo $bcolor; ?>;padding:4px;color:#ffffff;z-index:100;"></div>

<div id="showbox_pans_outer" style="display:none;position:absolute;border:solid 1px #ffffff;background:<?php echo $bcolor; ?>;padding:4px;color:#ffffff;z-index:100">
<table border="0" cellpadding="0" cellspacing="2" class="bodytext">
<tr><td style="color:#ffffff;font-weight:bold;">Date</td><td><form name="selform_date" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:inline;"><input class="input_box" name="ymd" type="text" value="" size="15" readonly="readonly" /> <a href="#" onclick="displayCalendar(document.selform_date.ymd,'yyyy-mm-dd',this); return false;"><img name="popcalp" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></form></td></tr>
<tr><td style="color:#ffffff;font-style:italic;">Invitation ID</td><td><form name="ivform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:inline;"><input type="text" name="invitationID" class="input_box" size="25" maxlength="200" /></form></td></tr>
<tr><td style="color:#ffffff;font-style:italic;">Last 4 Digits</td><td><form name="tform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:inline;"><input type="text" name="trackingID" class="input_box" size="25" maxlength="200" /></form></td></tr>
<tr><td style="color:#ffffff;font-weight:bold;">Panelist</td><td><form name="selform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:inline;"><input type="text" name="pan_id" class="input_box" size="30" maxlength="200" autocomplete="off" onkeyup="startTimer('showPans()');" /></form>&nbsp;<a href="#" style="color: #FFFFFF;" onclick="showPans(); return false;">Go</a></td></tr>
<tr><td>&nbsp;</td><td><div id="showbox_pans" style="color:#000000;max-height:290px;overflow-y:auto;background-color:<?php echo $secbcolor; ?>;padding:3px;display:none"></div></td></tr>
<tr><td colspan="2"><a href="#" style="color: #FFFFFF;" onclick="hideDiv_outer('showbox_pans_outer','showbox_pans'); document.forms.selform.pan_id.value=''; return false;">close</a></td></tr>
</table>
<form name="fform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:none;"><input type="text" name="pproductFICO" class="input_box" size="25" maxlength="200" /></form>
</div>
<div id="showbox_pans_edit" style="display:none;position:absolute;border:solid 1px #ffffff;background:<?php echo $bcolor; ?>;padding:4px;color:#ffffff;z-index:101;">
<table border="0" cellpadding="0" cellspacing="2" class="bodytext">
<tr><td style="color:#ffffff;font-weight:bold;">Date</td><td><form name="selform_date_edit" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:inline;"><input class="input_box" name="ymd" type="text" value="" size="15" readonly="readonly" /> <a href="#" onclick="displayCalendar(document.selform_date_edit.ymd,'yyyy-mm-dd',this); return false;"><img name="popcalp2" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></form></td></tr>
<tr><td style="color:#ffffff;font-style:italic;">Invitation ID</td><td><form name="ivform_edit" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:inline;"><input type="text" name="invitationID" class="input_box" size="25" maxlength="200" /><input type="hidden" name="old_invitationID" value="" /><input type="hidden" name="panelist_id" value="" /><input type="hidden" name="ppdate" value="" /><input type="hidden" name="pdid" value="" /><input type="hidden" name="competi_id" value="" /></form></td></tr>
<tr><td style="color:#ffffff;font-style:italic;">Last 4 Digits</td><td><form name="tform_edit" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:inline;"><input type="text" name="trackingID" class="input_box" size="25" maxlength="200" /><input type="hidden" name="old_trackingID" value="" /></form></td></tr>
<tr><td><a href="#" style="color: #FFFFFF;" onclick="closePanEdit(); return false;">close</a></td></tr>
</table>
<form name="fform_edit" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;" style="display:none;"><input type="text" name="pproductFICO" class="input_box" size="25" maxlength="200" /><input type="hidden" name="old_pproductFICO" value="" /></form>
</div>

<div id="showbox_pubs_outer" style="display:none;position:absolute;border:solid 1px #ffffff;background:<?php echo $bcolor; ?>;padding:4px;color:#ffffff;z-index:100;">
<table border="0" cellpadding="0" cellspacing="2" class="bodytext">
<tr><td style="color:#ffffff;font-weight:bold;">Publication</td><td style="color:#ffffff;"><form name="pform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return false;"><input class="input_box" name="ymd" type="text" value="" size="15" readonly="readonly" /> <a href="#" onclick="displayCalendar(document.pform.ymd,'yyyy-mm-dd',this); return false;"><img name="popcalp3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></form></td></tr>
<tr><td>&nbsp;</td><td><form name="pub_selform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;" onsubmit="return false;"><input type="text" name="pub_id" class="input_box" size="30" maxlength="200" autocomplete="off" onkeyup="startTimer('showPubs()');" /></form>&nbsp;<a href="#" style="color: #FFFFFF;" onclick="showPubs(); return false;">Go</a></td></tr>
<tr><td>&nbsp;</td><td><div id="showbox_pubs" style="color:#ffffff;"></div></td></tr>
<tr><td colspan="2"><a href="#" style="color: #FFFFFF;" onclick="hideDiv_outer('showbox_pubs_outer','showbox_pubs'); document.forms.pub_selform.pub_id.value=''; return false;">close</a></td></tr>
</table>
</div>

<div id="showbox_cmps_outer" style="display:none;position:absolute;border:solid 1px #ffffff;background:<?php echo $bcolor; ?>;padding:4px;color:#ffffff;z-index:100;">
<table border="0" cellpadding="0" cellspacing="2" class="bodytext">
<tr><td style="color:#ffffff;font-weight:bold;">Company</td><td><form name="cmp_selform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;" onsubmit="return false;"><input type="text" name="cmp_id" class="input_box" size="30" maxlength="200" autocomplete="off" onkeyup="startTimer('showCmps()');" /></form>&nbsp;<a href="#" style="color: #FFFFFF;" onclick="showCmps(); return false;">Go</a></td></tr>
<tr><td>&nbsp;</td><td><div id="showbox_cmps" style="color:#ffffff;"></div></td></tr>
<tr><td colspan="2"><a href="#" style="color: #FFFFFF;" onclick="hideDiv_outer('showbox_cmps_outer','showbox_cmps'); document.forms.cmp_selform.cmp_id.value=''; return false;">close</a></td></tr>
</table>
</div>

<div id="showbox_affs_outer" style="display:none;position:absolute;border:solid 1px #ffffff;background:<?php echo $bcolor; ?>;padding:4px;color:#ffffff;z-index:100;">
<table border="0" cellpadding="0" cellspacing="2" class="bodytext">
<tr><td style="color:#ffffff;font-weight:bold;">Affinity/Association</td><td><form name="aff_selform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;" onsubmit="return false;"><input type="text" name="aff_id" class="input_box" size="30" maxlength="200" autocomplete="off" onkeyup="startTimer('showAffs()');" /></form>&nbsp;<a href="#" style="color: #FFFFFF;" onclick="showAffs(); return false;">Go</a></td></tr>
<tr><td>&nbsp;</td><td><div id="showbox_affs" style="color:#ffffff;"></div></td></tr>
<tr><td colspan="2"><a href="#" style="color: #FFFFFF;" onclick="hideDiv_outer('showbox_affs_outer','showbox_affs'); document.forms.aff_selform.aff_id.value=''; return false;">close</a></td></tr>
</table>
</div>
