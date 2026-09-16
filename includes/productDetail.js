function doQA(pid){
	var chex = 0;
	if(document.qaform.qa.checked){
		chex = 1;
	}
	var done = processajax('update_qa.php', false, 'POST', 'chex='+chex+'&pid='+pid, '', '');
	if(done){
		//self.close();
	}
}

function sendColleague(ID,type) {
	var wind = window.open('sendLink.php?id='+ID+'&send_mode='+type,"coll"+ID,"left=20, top=20, scrollbars=yes, resizable=yes, width=625, height=475");
	wind.focus();
}
function productContent(ID) {
	var wind = window.open('pdfContentDetail.php?id='+ID,"text"+ID,"left=20, top=20, scrollbars=yes, resizable=yes");
	wind.focus();
}
var winy = '';
function printAll(pid,thepdf,id) {
	if(id==1){
		window.print();
	}
	else if (id==2 || id==3){
		if(thepdf!=''){
			winy = window.open(thepdf,"pdf"+pid,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes");
			winy.focus();
			//if (id==2){
			//	setTimeout('printing(winy)', 2000);
			//}
		}
	}
}
function printing(doc){
	doc.print();	
}
function doOp(objid,opstart,opend,speed){
	var obj = document.getElementById(objid);
	var op = opend;
	if(opend>opstart){
		op = opstart + speed;
	}
	else if(opend<opstart){
		op = opstart - speed;
	}
	
	obj.style.opacity = opstart;
	if(obj.filters){
		//obj.style.msFilter = '"progid:DXImageTransform.Microsoft.Alpha(Opacity='+Math.round(opstart*100)+')"';//-ms-filter
		obj.style.filter = 'alpha(opacity='+Math.round(opstart*100)+')';
	}
	obj.style.display = 'block';
	
	if(op>=1){
		op = 1;
		obj.style.opacity = 1;
		if(obj.filters){
			obj.style.removeAttribute('filter');
		}
	}
	else if(op<0.01){
		op = 0;
		obj.style.display = 'none';
	}
	
	if(op<1 && op>0){
		setTimeout("doOp('"+objid+"',"+op+","+opend+","+speed+")", 1);
	}
}
function showAddl(h){
	var obj = document.getElementById('theaddl');
	var obj2 = document.getElementById('theaddllink');
	
	var vtop = findPosY(obj2)-((h*28));
	if(vtop<60) {
		vtop = findPosY(obj2)-375;
		obj.style.height = '400px';
		var obj3 = document.getElementById('theaddl_inner');
		var obj4 = document.getElementById('theaddl_table');
		obj4.style.width = '270px';
		obj3.style.height = '360px';
		obj3.style.overflowY = 'scroll';
	}
	
	obj.style.left = findPosX(obj2)+'px';
	obj.style.top = vtop+'px';
	
	doOp('theaddl',0,1,0.1);
}
function hideAddl(){
	doOp('theaddl',1,0,0.1);
}
function showCalc(r){
	var obj = document.getElementById('thecalc');
	var obj2 = document.getElementById('thecalclink');
	
	var wid = winWidth();
	if(wid>0){
		obj.style.left = ((wid - 630)/2)+'px';
	}
	else{
		obj.style.left = '20px';
	}
	if(r>5){
		obj.style.height = '350px';
		var obj3 = document.getElementById('thecalc_inner');
		var obj4 = document.getElementById('thecalc_table');
		
		obj4.style.width = '590px';
		obj3.style.height = '310px';
		obj3.style.overflowY = 'scroll';
	}
	obj.style.top = findPosY(obj2)+'px';
	
	doOp('thecalc',0,1,0.1);
}
function hideCalc(){
	doOp('thecalc',1,0,0.1);
}
function showVarnt(h){
	var obj = document.getElementById('thevarnts');
	var obj2 = document.getElementById('thevarntlink');
	
	var vtop = findPosY(obj2)-((h*28)+26);
	if(h>11 || vtop<60) {
		vtop = findPosY(obj2)-375;
		obj.style.height = '400px';
		var obj3 = document.getElementById('thevarnts_inner');
		var obj4 = document.getElementById('thevarnts_table');
		obj4.style.width = '270px';
		obj3.style.height = '360px';
		obj3.style.overflowY = 'scroll';
	}
	
	obj.style.left = (findPosX(obj2)-80)+'px';
	obj.style.top = vtop+'px';
	
	doOp('thevarnts',0,1,0.1);
}
function hideVarnt(){
	doOp('thevarnts',1,0,0.1);
}
function showPublication(h){
	var obj = document.getElementById('thepubs');
	var obj2 = document.getElementById('thepublink');
	
	var vtop = findPosY(obj2)-((h*28)+26);
	if(h>11 || vtop<60) {
		vtop = findPosY(obj2)-375;
		obj.style.height = '400px';
		var obj3 = document.getElementById('thepubs_inner');
		var obj4 = document.getElementById('thepubs_table');
		obj4.style.width = '270px';
		obj3.style.height = '360px';
		obj3.style.overflowY = 'scroll';
	}
	
	obj.style.left = (findPosX(obj2)-80)+'px';
	obj.style.top = vtop+'px';
	
	doOp('thepubs',0,1,0.1);
}
function hidePublication(){
	doOp('thepubs',1,0,0.1);
}
function winWidth() {
	var myWidth = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		myWidth = window.innerWidth;
	} else if( document.documentElement && document.documentElement.clientWidth ) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
	} else if( document.body && document.body.clientWidth ) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
	}
	return myWidth;
}
function showSites(h){
	var obj = document.getElementById('thesites');
	var obj2 = document.getElementById('thesitelink');
	
	var vtop = findPosY(obj2)-((h*28)+26);
	if(h>11 || vtop<60) {
		vtop = findPosY(obj2)-375;
		obj.style.height = '400px';
		var obj3 = document.getElementById('thesites_inner');
		var obj4 = document.getElementById('thesites_table');
		obj4.style.width = '270px';
		obj3.style.height = '360px';
		obj3.style.overflowY = 'scroll';
	}
	
	obj.style.left = (findPosX(obj2)-80)+'px';
	obj.style.top = vtop+'px';
	
	doOp('thesites',0,1,0.1);
}
function hideSites(){
	doOp('thesites',1,0,0.1);
}
function showObservation(productID,sites_id,sp_observation){
	var wind = window.open('observationDetail.php?pid='+productID+'&sid='+sites_id+'&date='+sp_observation,"observ"+productID,"left=20, top=20, menubar=no, status=no, location=no, toolbar=no, scrollbars=yes, resizable=yes");
	wind.focus();
}
var saveArray = new Array();
function showCalcCos(pid){
	var obj = document.getElementById('thecalccos');
	var obj2 = document.getElementById('thecalccoslink'+pid);
	var obj3 = document.getElementById('thecalc');
	var obj4 = document.getElementById('thecalccos_inner');
	var obj5 = document.getElementById('thecalc_inner');
	
	var tmpleft = 0;
	if(obj3.style.left){
		tmpleft = parseInt(obj3.style.left)+10;
	}
	else{
		tmpleft = findPosX(obj3);
		if(tmpleft>100){
			tmpleft = tmpleft - 100;
		}
	}
	obj.style.left = tmpleft+'px';
	var tmptop = findPosY(obj2)-40;
	if(obj5 && obj5.scrollTop && obj5.scrollTop>0){
		tmptop = tmptop - obj5.scrollTop;
	}
	obj.style.top = tmptop+'px';
	
	var isthere = false;
	var objname = 'thecalccos_inner'+pid;
	for(var i=0;i<saveArray.length;i++){
		if(objname==saveArray[i]){
			isthere = true;
			document.getElementById(saveArray[i]).style.display = 'block';
		}
		else{
			document.getElementById(saveArray[i]).style.display = 'none';
		}
	}
	
	if(!isthere){
		var newdiv = document.createElement('div');
		newdiv.id = objname;
		saveArray[saveArray.length] = objname;
		var waitimg  = new Image(); //var waitimg = document.createElement('img');
		waitimg.style.border = 'none';
		waitimg.src = 'images/searching.gif';
		newdiv.appendChild(waitimg);
		obj4.appendChild(newdiv);
		processajax('panelist_info.php', true, 'POST', 'pid='+pid, newdiv, 'doResponseCalcCos');
	}
	
	doOp('thecalccos',0,1,0.1);
}
function doResponseCalcCos(response, obj){
	obj.innerHTML = response;
}
function hideCalcCos(){
	doOp('thecalccos',1,0,0.1);
}
var ValueScorepid = '';
function showValueScore(pid){
	var obj = document.getElementById('ValueScore');
	var obj2 = document.getElementById('ValueScorelink'+pid);
	var obj3 = document.getElementById('thecalc');
	var obj5 = document.getElementById('thecalc_inner');
	
	var tmpleft = 0;
	if(obj3.style.left){
		tmpleft = parseInt(obj3.style.left)+10;
	}
	else{
		tmpleft = findPosX(obj3);
		if(tmpleft>100){
			tmpleft = tmpleft - 100;
		}
	}
	obj.style.left = tmpleft+'px';
	var tmptop = findPosY(obj2)-40;
	if(obj5 && obj5.scrollTop && obj5.scrollTop>0){
		tmptop = tmptop - obj5.scrollTop;
	}
	obj.style.top = tmptop+'px';
	
	if(ValueScorepid!=pid){
		if(ValueScorepid!=''){
			var obj5 = document.getElementById('ValueScore_inner'+ValueScorepid);
			if(obj5){
				obj5.style.display = 'none';
			}
		}
		var obj4 = document.getElementById('ValueScore_inner'+pid);
		if(obj4){
			obj4.style.display = 'block';
			ValueScorepid = pid;
		}
	}
	
	doOp('ValueScore',0,1,0.1);
}
function hideValueScore(){
	doOp('ValueScore',1,0,0.1);
}
var pproductFICOpid = '';
function showpproductFICO(pid){
	var obj = document.getElementById('pproductFICO');
	var obj2 = document.getElementById('pproductFICOlink'+pid);
	var obj3 = document.getElementById('thecalc');
	var obj5 = document.getElementById('thecalc_inner');
	
	var tmpleft = 0;
	if(obj3.style.left){
		tmpleft = parseInt(obj3.style.left)+10;
	}
	else{
		tmpleft = findPosX(obj3);
		if(tmpleft>100){
			tmpleft = tmpleft - 100;
		}
	}
	obj.style.left = tmpleft+'px';
	var tmptop = findPosY(obj2)-40;
	if(obj5 && obj5.scrollTop && obj5.scrollTop>0){
		tmptop = tmptop - obj5.scrollTop;
	}
	obj.style.top = tmptop+'px';
	
	if(pproductFICOpid!=pid){
		if(pproductFICOpid!=''){
			var obj5 = document.getElementById('pproductFICO_inner'+pproductFICOpid);
			if(obj5){
				obj5.style.display = 'none';
			}
		}
		var obj4 = document.getElementById('pproductFICO_inner'+pid);
		if(obj4){
			obj4.style.display = 'block';
			pproductFICOpid = pid;
		}
	}
	
	doOp('pproductFICO',0,1,0.1);
}
function hidepproductFICO(){
	doOp('pproductFICO',1,0,0.1);
}
function showISO(pid){
	var obj = document.getElementById('ISO');
	var obj2 = document.getElementById('ISOlink1'+pid);
	if(!obj2){
		obj2 = document.getElementById('ISOlink2'+pid);	
	}
	var obj3 = document.getElementById('thecalc');
	var obj5 = document.getElementById('thecalc_inner');
	
	var tmpleft = 0;
	if(obj3.style.left){
		tmpleft = parseInt(obj3.style.left)+10;
	}
	else{
		tmpleft = findPosX(obj3);
		if(tmpleft>100){
			tmpleft = tmpleft - 100;
		}
	}
	obj.style.left = tmpleft+'px';
	var tmptop = findPosY(obj2)-40;
	if(obj5 && obj5.scrollTop && obj5.scrollTop>0){
		tmptop = tmptop - obj5.scrollTop;
	}
	obj.style.top = tmptop+'px';
	
	doOp('ISO',0,1,0.1);
}
function hideISO(){
	doOp('ISO',1,0,0.1);
}
function hideSWFDiv(){
	var obj = document.getElementById('flashContent');
	if(obj){
		var obj2 = document.getElementById('flashBlock');
		if(obj2){
			var blockleft = findPosX(obj);
			var blocktop = findPosY(obj);
			obj2.style.position = 'absolute';
			obj2.style.left = blockleft+'px';
			obj2.style.top = blocktop+'px';
			obj2.style.zIndex = 1000;
			obj2.style.opacity = 0;
			obj2.style.filter = 'alpha(opacity=0)';
			obj2.style.visibility = 'visible';
			obj2.style.display = 'block';
		}
	}
}
function findPosX(obj){
	var curleft = 0;
	if(obj.offsetParent){
		while(obj.offsetParent){
			curleft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	}
	else if(obj.x){
		curleft += obj.x;
	}
	return curleft;
}
function findPosY(obj){
	var curtop = 0;
	if(obj.offsetParent){
		while(obj.offsetParent){
			curtop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	}
	else if(obj.y){
		curtop += obj.y;
	}
	return curtop;
}

function showCalcCos_digital(pid){
	var obj = document.getElementById('thecalccos');
	var obj2 = document.getElementById('thecalccoslink'+pid);
	var obj3 = document.getElementById('thecalc');
	var obj4 = document.getElementById('thecalccos_inner');
	var obj5 = document.getElementById('thecalc_inner');
	
	var tmpleft = 0;
	if(obj3.style.left){
		tmpleft = parseInt(obj3.style.left)+10;
	}
	else{
		tmpleft = findPosX(obj3);
		if(tmpleft>100){
			tmpleft = tmpleft - 100;
		}
	}
	obj.style.left = tmpleft+'px';
	var tmptop = findPosY(obj2)-40;
	if(obj5 && obj5.scrollTop && obj5.scrollTop>0){
		tmptop = tmptop - obj5.scrollTop;
	}
	obj.style.top = tmptop+'px';
	
	var isthere = false;
	var objname = 'thecalccos_inner'+pid;
	for(var i=0;i<saveArray.length;i++){
		if(objname==saveArray[i]){
			isthere = true;
			document.getElementById(saveArray[i]).style.display = 'block';
		}
		else{
			document.getElementById(saveArray[i]).style.display = 'none';
		}
	}
	
	if(!isthere){
		var newdiv = document.createElement('div');
		newdiv.id = objname;
		saveArray[saveArray.length] = objname;
		var waitimg  = new Image(); //var waitimg = document.createElement('img');
		waitimg.style.border = 'none';
		waitimg.src = 'images/searching.gif';
		newdiv.appendChild(waitimg);
		obj4.appendChild(newdiv);
		processajax('panelist_info_digital.php', true, 'POST', 'pid='+pid, newdiv, 'doResponseCalcCos');
	}
	
	doOp('thecalccos',0,1,0.1);
}
function showIMPR(pid){//alert(pid);
    var obj = document.getElementById('IMPR');
    var obj2 = document.getElementById('IMPRlink');
    var obj3 = document.getElementById('IMPR_inner');
    var obj4 = document.getElementById('IMPR_table');
    
    obj4.innerHTML = "";
    var wid = winWidth();
    if(wid>0){
        obj.style.left = ((wid - 630)/2)+'px';
    }else{
        obj.style.left = '20px';
    }
    obj.style.top = findPosY(obj2)+'px';
    if(pid){
        var newdiv = obj4;
        var waitimg  = new Image(); //var waitimg = document.createElement('img');
        waitimg.style.border = 'none';
        waitimg.src = 'images/searching.gif';
        newdiv.appendChild(waitimg);
        obj3.appendChild(newdiv);
        processajax('panelist_impression_info.php', true, 'POST', 'pid='+pid, newdiv, 'doResponseCalcCos');
    }
        
    doOp('IMPR',0,1,0.1);
}
function hideIMPR(){
    doOp('IMPR',1,0,0.1);
}