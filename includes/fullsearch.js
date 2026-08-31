var categoryArray = new Array();
var sectorArray = new Array();
var subCategoryArray = new Array();
var affSubCategoryArray = new Array();
var faceArray = new Array();
var faceArrayID = new Array();
var termArray = new Array();
var termArrayID = new Array();
var issueArray = new Array();
var issueArrayID = new Array();
var enhArray = new Array();
enhArray[1] = new Array();//mchannel
enhArray[2] = new Array();//sector
enhArray[3] = new Array();//mpanel
enhArray[4] = new Array();//delmethod
var depsArrayID = new Array();
var depsArrayName = new Array();
var depsArray = new Array();
var depsArrayS = new Array();
var depsArrayM = new Array();
var affcat_id = '';
var reward_id = '';
var rewarde_id = '';
var multic_id = '';
var mortgage_id = '';
var mortgage_arr = new Array();
var dmaCs = new Array();
var dmaNs = new Array();
var dmaCodes = new Array();
var dmaNames = new Array();
var dmaswitch = 'Names';

function validateMonth() {
	var month1 = document.forms.searchForm.month1;
	var month2 = document.forms.searchForm.month2;
	var addToDB = document.forms.searchForm.addedToDatabase;
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
				document.searchForm.month2.focus();
			}
		}
	}
}
function setEnhance() {
	var obj = document.getElementById('div1');
	
	if(document.searchForm.refine_search.value=='Close Enhanced Search'){
		document.searchForm.enhance.value = 0;
		obj.style.display = 'none';
		document.searchForm.refine_search.value = 'Enhance Search';
	}
	else{
		document.searchForm.enhance.value = 1;
		obj.style.display = 'block';
		document.searchForm.refine_search.value = 'Close Enhanced Search';
		//doEnhance(1);
	}
        mortgageCheckList();
}
function unset(loc) {
	document.location.href = loc;
}
function validateSearch(alertext) {
	var channelFlag2 = 0;
	var panelFlag2 = 0;
	var sectorFlag2 = 0;
	
	if(trimspace(document.searchForm.key.value)=='' && trimspace(document.forms.searchForm.company.value)=='' && trimspace(document.searchForm.key2.value)=='') {
		for(var i=0; i <= document.forms.searchForm['mChannelID[]'].options.length-1; i++) {
			if(document.forms.searchForm['mChannelID[]'].options[i].selected == true) {
				channelFlag2 += 1;
			}
			if(channelFlag2 >= 1 ) {
				break;
			}
		}
		if(channelFlag2 == 0) {
			alert('Please '+alertext+'select at least one Media Channel');
			if(document.forms.searchForm.key){
				document.forms.searchForm.key.focus();
			}
			return false;
		}
		for(var i=0; i <= document.forms.searchForm['sectorID[]'].options.length-1; i++) {
			if(document.forms.searchForm['sectorID[]'].options[i].selected == true) {
				sectorFlag2 += 1;
			}
			if(sectorFlag2 >= 1 ) {
				break;
			}
		}
		if(sectorFlag2 == 0) {
			alert('Please '+alertext+'select at least one Sector');  
			if(document.forms.searchForm.key){
				document.forms.searchForm.key.focus();
			}
			return false;
		}
		for(var i=0; i <= document.forms.searchForm['mPanelID[]'].options.length-1; i++) {
			if(document.forms.searchForm['mPanelID[]'].options[i].selected == true) {
				panelFlag2 += 1;
			}
			if(panelFlag2 >= 1 ) {
				break;
			}
		}
		if(panelFlag2 == 0) {
			alert('Please '+alertext+'select at least one Audience');
			if(document.forms.searchForm.key){
				document.forms.searchForm.key.focus();
			}  
			return false;
		}
	}
	return true;
}
function showHelp(file){
	var win = window.open(file,'fulltext','top=0,left=0,height=650,width=600,resizable=1,scrollbars=yes');
	win.focus();  
}
function showHelp2(file){
	var win = window.open(file,'cosearch','top=0,left=0,height=650,width=600,resizable=1,scrollbars=yes');
	win.focus();  
}
function getEnh(part){
	var enhdoc = false;
	if(part==1){
		enhdoc = document.forms.searchForm['mChannelID[]'];
	}
	else if(part==2){
		enhdoc = document.forms.searchForm['sectorID[]'];
	}
	else if(part==3){
		enhdoc = document.forms.searchForm['mPanelID[]'];
	}
	else if(part==4){
		enhdoc = document.forms.searchForm['delmethid_mult[]'];
	}
        //alert(part);
        //alert(enhdoc);
	if(enhdoc){
		var selectedArray = new Array();
		for(var j=0;j<enhdoc.options.length;j++){
			if(enhdoc.options[j].selected){
				selectedArray[selectedArray.length] = enhdoc.options[j].value;
			}
		}
		for(var k in enhArray[part]){
			var obj = document.getElementById('div'+k);
			var vals = enhArray[part][k].split(",");
			obj.style.display = 'none';
			for(var v in vals){
				if(vals[v]=='A' && selectedArray.length>0){
					obj.style.display = 'block';
					break;
				}
				if(in_array(vals[v],selectedArray)!=-1){
					obj.style.display = 'block';
					break;
				}
			}
		}
	}
	checkRewards();
        mortgageCheckList();
}
function getCat(){
	var sectordoc = document.forms.searchForm['sectorID[]'];
	if(document.forms.searchForm['categoryID[]']){
		var catdoc = document.forms.searchForm['categoryID[]'];
		var sid = 0;//sectordoc.options[sectordoc.selectedIndex].value;
		var selectedArray = new Array();
		for(var j=0;j<sectordoc.options.length;j++){
			if(sectordoc.options[j].selected){
				selectedArray[selectedArray.length] = sectordoc.options[j].value;
			}
		}
		
		var selectedCatArray = new Array();
		for(var j=0;j<catdoc.options.length;j++){
			if(catdoc.options[j].selected){
				if(catdoc.options[j].value!=''){
					selectedCatArray[selectedCatArray.length] = catdoc.options[j].value;
				}
			}
		}
		
		catdoc.options.length = 0;
		var dummySort = new Array();
		var dummyData = new Array();
		var optiontext = 'Any';
		var isSel = false;
		var anySel = true;
		catdoc.options[0] = new Option(optiontext, '', false, isSel);
		for(var k=0;k<selectedArray.length;k++){
			sid = selectedArray[k];
			if(sectorArray[sid]){
				for(var i in sectorArray[sid]){
					optiontext = sectorArray[sid][i];
					if(in_array(i,selectedCatArray)!=-1){
						isSel = true;
						anySel = false;
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
		
		if(selectedArray.length==1 && selectedArray[0]=='90'){
			/*
			(90)
			178: Payment Cards
			179: Credit Access Checks
			231: Ancillary Products/Svc.
			*/
			var reordermap = new Array('178','179','231');
			dummySort = reorderSCSC(dummyData,dummySort,reordermap);
		}
		
		for(var j=0;j<dummySort.length;j++){
			for(var n=0;n<dummyData.length;n++){
				if(dummySort[j]==dummyData[n][0]){
					catdoc.options[j+1] = new Option(dummyData[n][0], dummyData[n][1], dummyData[n][2], dummyData[n][3]);
					break;
				}
			}
		}
		if(anySel){
			catdoc.selectedIndex = 0;
		}

		/* ###########  Communication Type Implementation ############ */
		//if (window.location.host == "demo.competiscan.com" || window.location.host == "localhost") {
			var sectorIDs = $("#sectorID").children("select option:selected").map(function () {
		        return $(this).val();
		    }).get().join(',');
		    $("#sectorIDs").attr('value',sectorIDs);
	    //}
	    /* ###########  Communication Type Implementation ############ */

		getSubCat();
	}
}
function getSubCat(){
	var catdoc = document.forms.searchForm['categoryID[]'];
	var subcatdoc = document.forms.searchForm['subCategoryID[]'];
	var cid = 0;//catdoc.options[catdoc.selectedIndex].value;
	var selectedArray = new Array();
	
	for(var j=0;j<catdoc.options.length;j++){
		if(catdoc.options[j].selected){
			selectedArray[selectedArray.length] = catdoc.options[j].value;
		}
	}
	
	if(in_array('13',selectedArray)!=-1){
		showFT();
	}
	else{
		hideFT();
	}
	
	var selectedSubCatArray = new Array();
	for(var j=0;j<subcatdoc.options.length;j++){
		if(subcatdoc.options[j].selected){
			if(subcatdoc.options[j].value!=''){
				selectedSubCatArray[selectedSubCatArray.length] = subcatdoc.options[j].value;
			}
		}
	}
	
	subcatdoc.options.length = 0;
	var dummySort = new Array();
	var dummyData = new Array();
	var optiontext = 'Any';
	var isSel = false;
	var anySel = true;
	subcatdoc.options[0] = new Option(optiontext, '', false, isSel);
	for(var k=0;k<selectedArray.length;k++){
		cid = selectedArray[k];
		if(categoryArray[cid]){
			for(var i in categoryArray[cid]){
				optiontext = categoryArray[cid][i];
				if(in_array(i,selectedSubCatArray)!=-1){
					isSel = true;
					anySel = false;
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
	
	if(selectedArray.length==1 && selectedArray[0]=='178'){
		/*
		(178)
		93: Payment Cards � Credit Cards
		92: Payment Cards � Charge Cards
		212: Payment Cards � Business Cards
		102: Payment Cards � Private Label Cards
		103: Payment Cards - Prepaid Cards
		91: Payment Cards � Corporate Cards
		*/
		var reordermap = new Array('93','92','212','102','103','91');
		dummySort = reorderSCSC(dummyData,dummySort,reordermap);
	}
	
	for(var j=0;j<dummySort.length;j++){
		for(var n=0;n<dummyData.length;n++){
			if(dummySort[j]==dummyData[n][0]){
				subcatdoc.options[j+1] = new Option(dummyData[n][0], dummyData[n][1], dummyData[n][2], dummyData[n][3]);
				break;
			}
		}
	}
	if(anySel){
		subcatdoc.selectedIndex = 0;
	}

	/* ###########  Communication Type Implementation ############ */
	//if (window.location.host == "demo.competiscan.com" || window.location.host == "localhost") {
		var categoryIDs = $("#categoryID").children("select option:selected").map(function () {
	        return $(this).val();
	    }).get().join(',');
	    categoryIDs = categoryIDs.replace(/^,/, '');
	    $("#categoryIDs").attr('value',categoryIDs);
    //}
    /* ###########  Communication Type Implementation ############ */

	getSubSubCat();
}
function getSubSubCat(){
	var catdoc = document.forms.searchForm['subCategoryID[]'];
	var subcatdoc = document.forms.searchForm['subSubCategoryID[]'];
	var cid = 0;//catdoc.options[catdoc.selectedIndex].value;
	var selectedArray = new Array();
	
	for(var j=0;j<catdoc.options.length;j++){
		if(catdoc.options[j].selected){
			selectedArray[selectedArray.length] = catdoc.options[j].value;
		}
	}
	
	var selectedSubCatArray = new Array();
	for(var j=0;j<subcatdoc.options.length;j++){
		if(subcatdoc.options[j].selected){
			if(subcatdoc.options[j].value!=''){
				selectedSubCatArray[selectedSubCatArray.length] = subcatdoc.options[j].value;
			}
		}
	}
	
	subcatdoc.options.length = 0;
	var dummySort = new Array();
	var dummyData = new Array();
	var optiontext = 'Any';
	var isSel = false;
	var anySel = true;
	subcatdoc.options[0] = new Option(optiontext, '', false, isSel);
	for(var k=0;k<selectedArray.length;k++){
		cid = selectedArray[k];
		if(subCategoryArray[cid]){
			for(var i in subCategoryArray[cid]){
				optiontext = subCategoryArray[cid][i];
				if(in_array(i,selectedSubCatArray)!=-1){
					isSel = true;
					anySel = false;
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
				subcatdoc.options[j+1] = new Option(dummyData[n][0], dummyData[n][1], dummyData[n][2], dummyData[n][3]);
				break;
			}
		}
	}
	if(anySel){
		subcatdoc.selectedIndex = 0;
	}

	/* ###########  Communication Type Implementation ############ */
    //if (window.location.host == "demo.competiscan.com" || window.location.host == "localhost") {
		var subCategoryIDs = $("#subCategoryID").children("select option:selected").map(function () {
	        return $(this).val();
	    }).get().join(',');
	    subCategoryIDs = subCategoryIDs.replace(/^,/, '');
	    $("#subCategoryIDs").attr('value',subCategoryIDs);
	//}
    /* ###########  Communication Type Implementation ############ */
}
function reorderSCSC(dummyData,dummySort,reordermap){
	var reorderArray = new Array();
	var reorderdata = new Array();
	var reorderlocs = new Array();
	for(var j=0;j<dummySort.length;j++){
		for(var n=0;n<dummyData.length;n++){
			if(dummySort[j]==dummyData[n][0]){
				if(in_array(dummyData[n][1],reordermap)!=-1){
					reorderdata[reorderdata.length] = dummyData[n][0];
					reorderArray[reorderArray.length] = dummyData[n][1];
					reorderlocs[reorderlocs.length] = j;
				}
				break;
			}
		}
	}
	if(reorderlocs.length>0){
		var firstj = 0;
		var currj = 0;
		for(j=0;j<reordermap.length;j++){
			currj = in_array(reordermap[j],reorderArray);
			if(currj!=-1){
				dummySort[reorderlocs[firstj]] = reorderdata[currj];
				firstj = firstj + 1;
			}
		}
	}
	return dummySort;
}
function showFT(){
	var term = document.forms.searchForm['tl_id[]'];
	var face = document.forms.searchForm['fa_id[]'];
	var issue = document.forms.searchForm['IssueTypeID_mult[]'];
	var objs = new Array(term,face,issue);
	var as = new Array(termArray,faceArray,issueArray);
	var aids = new Array(termArrayID,faceArrayID,issueArrayID);
	for(var j=0;j<objs.length;j++){
		var counter = 1;
		if(objs[j] && objs[j].length<=1){
			for(var i=0;i<as[j].length;i++){
				objs[j].options[counter++] = new Option(as[j][i], aids[j][i], false, false);
			}
		}
	}
}
function hideFT(){
	var term = document.forms.searchForm['tl_id[]'];
	var face = document.forms.searchForm['fa_id[]'];
	var issue = document.forms.searchForm['IssueTypeID_mult[]'];
	
	var objs = new Array(term,face,issue);
	for(var j=0;j<objs.length;j++){
		if(objs[j]){
			objs[j].selectedIndex = 0;
			objs[j].length = 1;
		}
	}
}
function getDeps(fieldname){
	var ac = document.forms.searchForm[fieldname];        
	if(ac){
		var mp = document.forms.searchForm['mPanelID[]'];
		var mpid = 0;// = mp.options[mp.selectedIndex].value;
		var selectedArray = new Array();
		var selectedMPArray = new Array();
		for(var j=0;j<mp.options.length;j++){
			if(mp.options[j].selected){
				selectedArray[selectedArray.length] = mp.options[j].value;
			}
		}
		for(var j=0;j<ac.options.length;j++){
			if(ac.options[j].selected){
				selectedMPArray[selectedMPArray.length] = ac.options[j].value;
			}
		}
		var sectorCatSub = new Array('sectorID[]','categoryID[]','subCategoryID[]');
		var selectedSArray = new Array();
		for(var n=0;n<sectorCatSub.length;n++){
			var sectordoc = document.forms.searchForm[sectorCatSub[n]];
			for(var j=0;j<sectordoc.options.length;j++){
				if(sectordoc.options[j].selected){
					selectedSArray[selectedSArray.length] = sectordoc.options[j].value;
				}
			}
		}
		var selectedMCArray = new Array();
		var mc = document.forms.searchForm.mChannelID;
		var mcid = 0;
		for(var j=0;j<mc.options.length;j++){
			if(mc.options[j].selected){
				selectedMCArray[selectedMCArray.length] = mc.options[j].value;
			}
		}
		ac.options.length = 0;
		var counter = 0;
		var optiontext = 'Any';
		var isSel = false;
		var anySel = true;
		var show1 = false;
		var show2 = false;
		var show3 = false;
		ac.options[counter++] = new Option(optiontext, '', false, isSel);
		for(var n=0;n<depsArrayID[fieldname].length;n++){
			show1 = false;
			show2 = false;
			show3 = false;
                        showRates=false;
			if(!depsArray[fieldname]){
				show1 = true;
			}
			else {
				for(var j=0;j<selectedArray.length;j++){                                       
					if(depsArray[fieldname][selectedArray[j]] && depsArray[fieldname][selectedArray[j]][depsArrayID[fieldname][n]]){
						show1 = true;
						break;
					}
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
				for(var j=0;j<selectedMCArray.length;j++){
					if(depsArrayM[fieldname][selectedMCArray[j]] && depsArrayM[fieldname][selectedMCArray[j]][depsArrayID[fieldname][n]]){
						show3 = true;
						break;
					}
				}
			}
                        // Changes for 'Rates' communication type
                        
                        //console.log('sector: '+depsArrayID[fieldname][n]);
                        //console.log('selected mp: '+selectedSArray);
                        if((depsArrayName[fieldname][n]=='Rates') && ((selectedSArray.indexOf('4')>=0) || (selectedSArray.indexOf('5')>=0) || (selectedSArray.indexOf('315')>=0) || (selectedArray.indexOf('6')>=0))){
                            showRates = true;
                            
                        }                       
                        
                        
                        if(!showRates){
                            if(show1 && show2 && show3){
                                optiontext = depsArrayName[fieldname][n];                                
                                if(in_array(depsArrayID[fieldname][n],selectedMPArray)!=-1){

                                    isSel = true;
                                    anySel = false;
                                }
                                else{ 
                                     isSel = false;
                                }                                   

                                ac.options[counter++] = new Option(optiontext, depsArrayID[fieldname][n], false, isSel);
                            }
                        }else{
                            if((show1 || show2) && show3){
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
                         //End for Changes 'Rates' communication type
		}
		if(anySel){
			ac.selectedIndex = 0;
		}

		/* ###########  Communication Type Implementation ############ */
		//if (window.location.host == "demo.competiscan.com" || window.location.host == "localhost") {
			var audienceIDs = $("#mPanelID").children("option:selected").map(function () {
		        return $(this).val();
		    }).get().join(',');
		    $("#audienceIDs").attr('value',audienceIDs);
		//}
	    /* ###########  Communication Type Implementation ############ */

	}
}
function getAffSubCats(){

	var affcats = document.forms.searchForm['AffinityCategoryID[]'];
        var affsubcats = document.forms.searchForm['AffinitySubCategoryID[]'];
        var cid = 0;//catdoc.options[catdoc.selectedIndex].value;
        var selectedCatArray = new Array();

        for(var j=0;j<affcats.options.length;j++){
                if(affcats.options[j].selected){
                        selectedCatArray[selectedCatArray.length] = affcats.options[j].value;
                }
        } //selectedCatArray holds all selected aff cats
	
	var selectedSubCatArray = new Array();
        for(var j=0;j<affsubcats.options.length;j++){
                if(affsubcats.options[j].selected){
                        if(affsubcats.options[j].value!=''){
                                selectedSubCatArray[selectedSubCatArray.length] = affsubcats.options[j].value;
                        }
                }
        } //selectSubCatArray holds all select aff subcats

	affsubcats.options.length = 0;
        var dummySort = new Array();
        var dummyData = new Array();
        var optiontext = 'Any';
        var isSel = false;
        var anySel = true;
        affsubcats.options[0] = new Option(optiontext, '', false, isSel);
        for(var k=0;k<selectedCatArray.length;k++){
                cid = selectedCatArray[k];
                if(affSubCategoryArray[cid]){
                        for(var i in affSubCategoryArray[cid]){
                                optiontext = affSubCategoryArray[cid][i];
                                if(in_array(i,selectedSubCatArray)!=-1){
                                        isSel = true;
                                        anySel = false;
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
                                affsubcats.options[j+1] = new Option(dummyData[n][0], dummyData[n][1], dummyData[n][2], dummyData[n][3]);
                                break;
                        }
                }
        }
        if(anySel){
                affsubcats.selectedIndex = 0;
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
function doEnhance(doenh){
	if(doenh){
		document.location.href = '#enh';
	}	
}
function showLook(iframe_div,iframe_link,iframe_id,focus_obj){
	var obj = document.getElementById(iframe_div);
	var obj2 = document.getElementById(iframe_link);
	if(obj){
		var ltext = '';
		if(obj.style.display!='none'){
			obj.style.display = 'none';
			ltext = 'Show Lookup';
			if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document && top.frames[iframe_id].document.forms && top.frames[iframe_id].document.forms.companyForm && top.frames[iframe_id].document.forms.companyForm.companylook){
				top.frames[iframe_id].document.forms.companyForm.companylook.value = '';
			}
			focus_obj.focus();
		}
		else{
			obj.style.display = 'block';
			ltext = 'Hide Lookup';
			
			if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document){
				top.frames[iframe_id].doSel();
			}
			
			window.setTimeout("doFocus('"+iframe_id+"')", 500);	
		}
		my_innerHTML_text(obj2,ltext);
	}
}
function doFocus(iframe_id){
	if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document && top.frames[iframe_id].document.forms && top.frames[iframe_id].document.forms.companyForm && top.frames[iframe_id].document.forms.companyForm.companylook){
		top.frames[iframe_id].document.forms.companyForm.companylook.focus();
	}
}
function checkLookup(iframe_id){
	if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document){
		top.frames[iframe_id].doSel();
	}
}
function changeWorksite(){
	var obj = document.getElementById('ws_id');
	var ws_val = document.forms.searchForm.worksiteVoluntary.value;
	if(ws_val==1){
		document.forms.searchForm.worksiteVoluntary.value = 2;
		my_innerHTML_text(obj,'Non-Worksite/Voluntary :');
	}
	else{
		document.forms.searchForm.worksiteVoluntary.value = 1;
		my_innerHTML_text(obj,'Worksite/Voluntary :');
	}
}
function changeCreditUnion(){
	var obj = document.getElementById('cu_id');
	var ws_val = document.forms.searchForm.creditUnion.value;
	if(ws_val==1){
		document.forms.searchForm.creditUnion.value = 2;
		my_innerHTML_text(obj,'Non-Credit Union :');
	}
	else{
		document.forms.searchForm.creditUnion.value = 1;
		my_innerHTML_text(obj,'Credit Union :');
	}
}
function changeCheckbox(eid,eobj,yestext,notext){
	var obj = document.getElementById(eid);
	var aa_val = eobj.value;
	if(aa_val==1){
		eobj.value = 2;
		my_innerHTML_text(obj,notext);
	}
	else{
		eobj.value = 1;
		my_innerHTML_text(obj,yestext);
	}
}
function checkAffCat(){
	if(affcat_id!=''){
		var obj = document.getElementById('div'+affcat_id);
		if(obj){
			if(document.forms.searchForm.affinityAssociation.checked && document.forms.searchForm.affinityAssociation.value==1){
				obj.style.display = 'block';
			}
			else{
				obj.style.display = 'none';
				//document.forms.searchForm.AffinityCategoryID.selectedIndex = 0;
			}
		}
	}
}
function checkAffSubCat(){
        if(affsubcat_id!=''){
                var obj = document.getElementById('div'+affsubcat_id);
                if(obj){
                        if(document.forms.searchForm.affinityAssociation.checked && document.forms.searchForm.affinityAssociation.value==1){
                                obj.style.display = 'block';
                        }
                        else{
				obj.style.display = 'none';
			}
                }
        }
}
function checkRewards(){
	if(rewarde_id!='' && reward_id!=''){
		var obj = document.getElementById('div'+rewarde_id);
		var obj2 = document.getElementById('div'+reward_id);
		if(obj && obj2){
			if(document.forms.searchForm.is_rewards.checked && obj2.style.display!='none'){
				obj.style.display = 'block';
			}
			else{
				obj.style.display = 'none';
			}
		}
	}
}
function checkMultic(){
	if(multic_id!=''){
		var obj = document.getElementById('div'+multic_id);
		if(obj){
			if(document.forms.searchForm.is_multicultural.checked && document.forms.searchForm.is_multicultural.value==1){
				obj.style.display = 'block';
			}
			else{
				obj.style.display = 'none';
			}
		}
	}
}
function mortgageCheckList(){
    var el = document.getElementById("sectorID");
    aTab = getSelectValues(el);
    
    aTab = aTab instanceof Array ? aTab : [aTab];
    var arrayLength = mortgage_arr.length;
    for (var i = 0; i < arrayLength; i++) {
        mortgage_id = mortgage_arr[i];
        var obj = document.getElementById('div'+mortgage_id);
        if(obj){ 
            if(aTab.indexOf("6") != -1){
                obj.style.display = 'block';
                
                document.getElementById('minmax').value='0-2000000';
                //document.getElementById('minmax-slider-range-min').innerHTML='0';
                //document.getElementById('minmax-slider-range-max').innerHTML='2000000';
                document.getElementById('minrangevalue').value='0';
                document.getElementById('maxrangevalue').value='2000000';
                $('#minmax-slider-range a').each(function (index) {
                    if (index == 0) {
                        // Set href for the first element
                        $(this).css('left', '0%');
                    } else if (index == 1) {
                        // Set href for the second element
                        $(this).css('left', '100%');
                    }
                    
                }); 
                
                
                
            }else{
                obj.style.display = 'none';
            }
        }
    }
}
function getSelectValues(select) {
  var result = [];
  var options = select && select.options;
  var opt;
  for (var i=0, iLen=options.length; i<iLen; i++) {
    opt = options[i];

    if (opt.selected) {
      result.push(opt.value || opt.text);
    }
  }
  return result;
}
function switchSearch(){
	if(document.searchForm.search_type.value=='ocr_fulltext2'){
		document.getElementById('ocr_fulltext_a').style.display = 'none';
		document.getElementById('ocr_fulltext_b').style.display = 'none';
		document.getElementById('ocr_fulltext_c').style.display = 'none';
		document.getElementById('fulltext_a').style.display = 'block';
		document.getElementById('ocr_a').style.display = 'block';
		if(document.searchForm.search_type_set[0].checked){
			document.searchForm.search_type.value = document.searchForm.search_type_set[0].value;
		}
		else{
			document.searchForm.search_type_set[1].checked = true;
			document.searchForm.search_type.value = document.searchForm.search_type_set[1].value;
		}
		document.searchForm.key2.value = '';
	}
	else{
		document.getElementById('ocr_fulltext_a').style.display = 'block';
		document.getElementById('ocr_fulltext_b').style.display = 'block';
		document.getElementById('ocr_fulltext_c').style.display = 'block';
		document.getElementById('fulltext_a').style.display = 'none';
		document.getElementById('ocr_a').style.display = 'none';
		document.searchForm.search_type.value = 'ocr_fulltext2';
	}
}
function sortDMA(){
	var dmasUse = new Array();
	if(dmaswitch=='Names'){
		dmasUse = dmaNames;
		dmasU = dmaNs;
		dmaswitch = 'Codes';
	}
	else{
		dmasUse = dmaCodes;
		dmasU = dmaCs;
		dmaswitch = 'Names';
	}
	var dmasel = document.searchForm['DMA_ID[]'];
	var selectedArray = new Array();
	for(var j=0;j<dmasel.options.length;j++){
		if(dmasel.options[j].selected){
			selectedArray[selectedArray.length] = dmasel.options[j].value;
		}
	}
	dmasel.options.length = 1;
	var j = 1;
	for(var k=0;k<dmasUse.length;k++){
		if(in_array(dmasUse[k],selectedArray)!=-1){
			isSel = true;
		}
		else{
			isSel = false;
		}
		dmasel.options[j] = new Option(dmasU[dmasUse[k]], dmasUse[k], false, isSel);
		j = j + 1;
	}
}

function showDigitalSource(){ 
    mchid = document.forms.searchForm['mChannelID[]'].value;
    
    var fld = document.getElementById('mChannelID');
    var chvalues = [];
    for (var i = 0; i < fld.options.length; i++) {
      if (fld.options[i].selected) {
        chvalues.push(fld.options[i].value);
      }
    }     
      if ( (chvalues.indexOf( '5' ) > -1) || (chvalues.indexOf( '9' ) > -1) || (chvalues.indexOf( '10' ) > -1)  ){           
           //document.getElementById('div0_1').style.display = 'block';
           var contents_dig = document.getElementsByClassName("digital_source_class");
            contents_dig[0].style.display = "block";
           
      }else{
          var contents_dig = document.getElementsByClassName("digital_source_class");
          contents_dig[0].style.display = "none";
          //document.getElementById('div0_1').style.display = 'none';
      }
    
}
function showPublicationName(){ 
    mchid = document.forms.searchForm['mChannelID[]'].value;
    
    var fld = document.getElementById('mChannelID');
    var chvalues = [];
    for (var i = 0; i < fld.options.length; i++) {
      if (fld.options[i].selected) {
        chvalues.push(fld.options[i].value);
      }
    }     
      if ( (chvalues.indexOf( '2' ) > -1) ){           
           //document.getElementById('div1_37').style.display = 'block';
           //document.getElementsByClassName('pub_name_class').style.display = 'block';
           var contents = document.getElementsByClassName("pub_name_class");
            contents[0].style.display = "block";
           document.getElementById('is_publication_name').value = 1;
           
      }else{
          //document.getElementById('div1_37').style.display = 'none';
          //document.getElementsByClassName('pub_name_class')[0].style.display = 'none';
          var contents = document.getElementsByClassName("pub_name_class");
            contents[0].style.display = "none";
          document.getElementById('is_publication_name').value = 0;
      }
    
}

function SortByName(x,y) {
  return ((x.type == y.type) ? 0 : ((x.type > y.type) ? 1 : -1 ));
}

/* ###########  Communication Type Implementation ############ */
$(document).ready(function(){
	//if (window.location.host == "demo.competiscan.com" || window.location.host == "localhost") {
		$("#agentCommunicationID").on('change', function(){
			var selectedCommunicationID = $("#agentCommunicationID option:selected").map(function () {
									        return $(this).val();
									    }).get().join(',');
			$("#agentCommunicationIDS").attr('value',selectedCommunicationID);
		});
		$("#sectorID, #mPanelID, #categoryID, #subCategoryID, #subSubCategoryID").on('click', function(){
			var audienceIDs = $("#audienceIDs").val();
			var sectorIDs = $("#sectorIDs").val();
			var categoryIDs = $("#categoryIDs").val();
			var subCategoryIDs = $("#subCategoryIDs").val();
			var subSubCategoryIDs = $("#subSubCategoryID").children("select option:selected").map(function () {
		        return $(this).val();
		    }).get().join(',');
		    subSubCategoryIDs = subSubCategoryIDs.replace(/^,/, '');
		    $("#subsubCategoryIDs").attr('value',subSubCategoryIDs);
		    var selectedCommunicationID = $("#agentCommunicationIDS").val();
			var ssid = $("input[name*='ssid']").val();
			if (window.location.host == "localhost") {
			    var origin = window.location.origin+'/competiscan.com/get-communication-type.php';
			} else {
			    var origin = window.location.origin+'/get-communication-type.php';
			}

			$.ajax({
				type: 'POST',
			    url: origin,
			    data: {audienceValue: audienceIDs, sectorValue: sectorIDs, catValue: categoryIDs, subCatValue: subCategoryIDs, 
			    	subSubCatValue: subSubCategoryIDs, searchID: ssid, selectedCommunicationID: selectedCommunicationID},
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

		var audienceIDs = $("#mPanelID").children("option:selected").map(function () {
	        return $(this).val();
	    }).get().join(',');
	    $("#audienceIDs").attr('value',audienceIDs);

	    var sectorIDs = $("#sectorID").children("select option:selected").map(function () {
	        return $(this).val();
	    }).get().join(',');
	    $("#sectorIDs").attr('value',sectorIDs);

	    var categoryIDs = $("#categoryID").children("select option:selected").map(function () {
	        return $(this).val();
	    }).get().join(',');
	    $("#categoryIDs").attr('value',categoryIDs);

	    var subCategoryIDs = $("#subCategoryID").children("select option:selected").map(function () {
	        return $(this).val();
	    }).get().join(',');
	    $("#subCategoryIDs").attr('value',subCategoryIDs);

	    var subSubCategoryIDs = $("#subSubCategoryID").children("select option:selected").map(function () {
	        return $(this).val();
	    }).get().join(',');
	    $("#subsubCategoryIDs").attr('value',subSubCategoryIDs);

	    var selectedCommunicationID = $("#agentCommunicationID option:selected").map(function () {
									        return $(this).val();
									    }).get().join(',');
		$("#agentCommunicationIDS").attr('value',selectedCommunicationID);

	    var ssid = $("input[name*='ssid']").val();

		if (window.location.host == "localhost") {
		    var origin = window.location.origin+'/competiscan.com/get-communication-type.php';
		} else {
		    var origin = window.location.origin+'/get-communication-type.php';
		}

		$.ajax({
			type: 'POST',
		    url: origin,
		    data: {audienceValue: audienceIDs, sectorValue: sectorIDs, catValue: categoryIDs, subCatValue: subCategoryIDs, 
		    	subSubCatValue: subSubCategoryIDs, searchID: ssid, selectedCommunicationID: selectedCommunicationID},
		    success: function (response) {
		    	if($.trim(response)){
			    	var json = $.parseJSON(response);
			    	if(json.communicationID != undefined){
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
				    }
			    }
		    },
		    error: function (data) {
		    	console.log("Something went wrong!");
		    },
		});
	//}
});
/* ###########  Communication Type Implementation ############ */
/*############################## Start Envelope/Postage Data Fields################*/
function doEnvelopePostageData(){ 
    mchid = document.forms.searchForm['mChannelID[]'].value;
    if(mchid==1){
    delmethid = document.forms.searchForm['delmethid_mult[]'].value;
    //alert(delmethid);
    var fld = document.getElementById('delivery_mehod');
    //alert(fld);
    var delmvalues = [];
    for (var i = 0; i < fld.options.length; i++) {
      if (fld.options[i].selected) {
        delmvalues.push(fld.options[i].value);
      }
    }     
      if ( (delmvalues.indexOf( '1' ) > -1) || (delmvalues.indexOf( '3' ) > -1) || (delmvalues.indexOf( '7' ) > -1) ){           
           var contents = document.getElementsByClassName("deliverytype_class");
            contents[0].style.display = "block";
            contents[1].style.display = "block";
            contents[2].style.display = "block";
            contents[3].style.display = "block";
      }else{
          var contents = document.getElementsByClassName("deliverytype_class");
            contents[0].style.display = "none";
            contents[1].style.display = "none";
            contents[2].style.display = "none";
            contents[3].style.display = "none";
         
      }
      
    }
    
}
/* ############################## End Envelope/Postage Data Fields################ */