function showTrend(selname,fromlink){
	var trend_id = document.searchForm[selname].options[document.searchForm[selname].selectedIndex].value;
	if(trend_id!=''){
		document.location.href = 'trend_report.php?trend_id='+trend_id;
	}
	else if(fromlink){
		alert('Please select a Trend Report');	
	}
}
function showTrend_id(trend_id){
	document.location.href = 'trend_report.php?trend_id='+trend_id;
}

function clearTrendSearch(){
	document.forms.searchForm.trend_date_from.value = '';
	document.forms.searchForm.trend_date_to.value = '';
	document.forms.searchForm['sectorID[]'].selectedIndex=-1;
	getCat();
}

var categoryArray = new Array();
var sectorArray = new Array();
var subCategoryArray = new Array();

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
		93: Payment Cards – Credit Cards
		92: Payment Cards – Charge Cards
		212: Payment Cards – Business Cards
		102: Payment Cards – Private Label Cards
		103: Payment Cards - Prepaid Cards
		91: Payment Cards – Corporate Cards
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
function in_array(val,ar){
	for(var i=0;i<ar.length;i++){
		if(val==ar[i]){
			return i;
		}
	}
	return -1;
}