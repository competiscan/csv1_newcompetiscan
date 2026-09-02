var defArray = new Array();
var defArrayID = new Array();
var bidArray = new Array();
var bidArrayID = new Array();
var currbid = '';
var currbname = '';
var do_doDis = true;

function chk_search_name(searchName) {
	if(trimspace(searchName)=='') {
		alert('Search Name cannot be blank');
		document.nameForm.searchName.focus();
		return false;
	}
	return true;
}
function checkNew(){
	var sel = document.basketer.basketid.selectedIndex;
	var bnobj = document.basketer.basket_name;
	if(sel<0){
		sel = 0;
	}
	if(bnobj){
		if(document.basketer.basketid.options[sel].value=='-2'){
			bnobj.style.display = 'inline';
			bnobj.focus();
		}
		else{
			bnobj.style.display = 'none';
		}
	}
}
function checkChex(){
	var sel = document.basketer.basket_action.selectedIndex;
	if(sel>=0){
		var selval = document.basketer.basket_action.options[sel].value;
		
		if(selval==1 || selval==6 || selval==7 || selval==8 || selval==9 || selval==10) {//Change Basket Name || Save Annotation || Copy To/Add page || Copy To/Add all
			return true;
		}
		else if(selval==2 || selval==4) {//Copy Selected To/Add Selected To || Move Selected To
			return doDis();
		}
		else if(selval==3) {//Delete Basket
			return doDel();
		}
		else if(selval==5) {//Remove Selected
			return doRem();
		}
		else {
			return true;
		}
	}
	return true;
}
var prev_sel = -1;
function checkAction(){
	var sel = document.basketer.basket_action.selectedIndex;
	if(sel>=0){
		var selval = document.basketer.basket_action.options[sel].value;
		var nobj = document.basketer.curr_basket_name;
		var bnobj = document.basketer.basket_name;
		var bobj = document.basketer.basketid;
		var bbutton = document.basketer.basketbutton_submit;
		
		if(selval==0){
			hideActions(bbutton,bnobj,bobj,nobj);
		}
		else if(selval==1) {//Change Basket Name
			hideActions(bbutton,bnobj,bobj,nobj);
			nobj.style.display = 'inline';
			nobj.focus();
			bbutton.style.display = 'inline';
		}
		else if((selval==2 || selval==4 || selval==7 || selval==8 || selval==9 || selval==10) && (prev_sel!=2 && prev_sel!=4 && prev_sel!=7 && prev_sel!=8 && prev_sel!=9 && prev_sel!=10)) {//Copy Selected To/Add Selected To || Move Selected To || page || all
			hideActions(bbutton,bnobj,bobj,nobj);
			fixBasket(1);
			bobj.style.display = 'inline';
			bobj.focus();
			checkNew();
			bbutton.style.display = 'inline';
		}
		else if(selval==3) {//Delete Basket
			hideActions(bbutton,bnobj,bobj,nobj);
			fixBasket(0);
			bobj.style.display = 'inline';
			bobj.focus();
			bbutton.style.display = 'inline';
		}
		else if((selval==5 || selval==6) && (prev_sel!=5 && prev_sel!=6)) {//Remove Selected || Save Annotation
			hideActions(bbutton,bnobj,bobj,nobj);
			bbutton.style.display = 'inline';
		}
		prev_sel = selval;
	}
}
function hideActions(bbutton,bnobj,bobj,nobj){
	if(bbutton){
		bbutton.style.display = 'none';
	}
	if(bnobj){
		bnobj.style.display = 'none';
	}
	if(bobj){
		bobj.style.display = 'none';
	}
	if(nobj){
		nobj.style.display = 'none';
	}
}
function doDis(){
	if(do_doDis){
		var ischecked = false;
		if(document.basketer['basket[]'].length){
			for(var i=0;i<document.basketer['basket[]'].length;i++){
				if(document.basketer['basket[]'][i].checked){
					ischecked = true;
					break;	
				}	
			}
		}
		else if(document.basketer['basket[]'].checked){
			ischecked = true;
		}
		if(!ischecked){
			alert('Please select at least one product');
			return false;	
		}
	}
	return true;
}
function doCheck(bcount){
	var obj;
	if(document.basketer['basket[]'].length){
		obj = document.basketer['basket[]'][bcount];
	}
	else{
		obj = document.basketer['basket[]'];
	}
	if(obj.checked){
		obj.checked = false;
	}
	else{
		obj.checked = true;
	}
	doExportBasketSelect(bcount);
}
function doExportBasketSelect(bcount){
	var remove = '1';
	var obj;
	if(document.basketer['basket[]'].length){
		obj = document.basketer['basket[]'][bcount];
	}
	else{
		obj = document.basketer['basket[]'];
	}
	if(obj.checked){
		remove = '0';
	}
	processajax('add_basket.php', true, 'POST', 'selected_productID='+obj.value+'&remove='+remove, '', '');
}
function doRem(){
	if(doDis()) { 
		return confirm('Remove Selected?');
	}
	else return false;
}
function doDel(){
	return confirm('Delete Basket?');
}
function fixBasket(doAll){
	var bobj = document.basketer.basketid;
	bobj.options.length = 0;
	var counter = 0;
	if(doAll){
		for(var j=0;j<defArray.length;j++){
			bobj.options[counter++] = new Option(defArray[j], defArrayID[j], false, false);
		}
	}
	else if(currbid!=''){
		bobj.options[counter++] = new Option(currbname, currbid, false, false);
	}
	for(var j=0;j<bidArray.length;j++){
		bobj.options[counter++] = new Option(bidArray[j], bidArrayID[j], false, false);
	}
}
function productDescription(ID,pdf_key) {
	var win = window.open('productDetail.php?id='+ID+pdf_key,"detail","toolbar=no, menubar=no, location=no, status=yes, scrollbars=yes, resizable=yes, width=700, height=600");
	win.focus();
}

function productDescription_digital(ID,pdf_key) {
	var win = window.open('productDetail.php?id='+ID+pdf_key,"detail","toolbar=no, menubar=no, location=no, status=yes, scrollbars=yes, resizable=yes, width=700, height=600");
	win.focus();
}