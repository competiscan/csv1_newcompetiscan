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

function my_innerHTML_text(obj,newtext){
	var newnode = document.createTextNode(newtext);
	if(obj.childNodes && obj.childNodes.length>0){
		var kid = obj.childNodes;
		obj.replaceChild(newnode, kid[0]);
	}
	else{
		obj.appendChild(newnode);
	}
}

function getxmlhttp(){
	var xmlhttp = false;

	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} 
	catch(e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} 
		catch(e) {
			xmlhttp = false;
		}
	}

	if(!xmlhttp && typeof XMLHttpRequest!='undefined'){
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function processajax(serverPage, asy, getOrPost, str, obj, functioncall){
	var xmlhttp = getxmlhttp();
	if(xmlhttp){
		if(getOrPost=='GET'){
			xmlhttp.open("GET",serverPage, asy);
			if(asy){
				xmlhttp.onreadystatechange = function() {
					if(xmlhttp.readyState==4 && xmlhttp.status==200){
						if(functioncall){
							if(eval('typeof '+functioncall)=='function'){
								eval(functioncall+'(xmlhttp.responseText, obj);');
							}
						}
					}
				}
			}
			xmlhttp.send(null);
		}
		else {
			xmlhttp.open("POST",serverPage, asy);
			xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
			if(asy){
				xmlhttp.onreadystatechange = function() {
					if(xmlhttp.readyState==4 && xmlhttp.status==200){
						if(functioncall){
							if(eval('typeof '+functioncall)=='function'){
								eval(functioncall+'(xmlhttp.responseText, obj);');
							}
						}
					}
				}
			}
			xmlhttp.send(str);
		}
		if(!asy){
			if(functioncall){
				if(eval('typeof '+functioncall)=='function'){
					eval(functioncall+'(xmlhttp.responseText, obj);');
				}
			}
			else return xmlhttp.responseText;
		}
		return true;
	}
	return false;
}

var timer = false;
var delay = 300;
function startTimer(functiontext){
	if(timer){
		window.clearTimeout(timer);
	}
	timer = window.setTimeout(functiontext, delay);	
}

function hideBlock(obj){
	obj.style.display = 'none';
}

function doResponse(response, obj){
	if(response!=''){
		//obj.innerHTML = response;
		my_innerHTML_text(obj,response);
		obj.style.left = findPosX(document.forms.prodForm.productHeadline)+'px';
		obj.style.top = (findPosY(document.forms.prodForm.productHeadline)+60)+'px';
		obj.style.display = 'block';
	}
	else{
		hideHeads();
	}
}

function showHeads(page){
	var wholeval = document.forms.prodForm.productHeadline.value;
	if(wholeval.length>0){
		processajax(page, true, 'POST', 'findval='+encodeURIComponent(wholeval), document.getElementById('showbox'), 'doResponse');	
	}
	else{
		hideHeads();
	}
}
function hideHeads(){
	document.getElementById('showbox').style.display = 'none';
}

function doResponseCos(response, obj){
	if(response!=''){
		obj.innerHTML = response;
		//my_innerHTML_text(obj,response);
		obj.style.left = findPosX(document.forms.prodForm.company)+'px';
		obj.style.top = (findPosY(document.forms.prodForm.company)+20)+'px';
		obj.style.display = 'block';
		ie6Hide(obj);
	}
	else{
		hideCos();
	}
}

function checkIE6(){
	var agt=navigator.userAgent.toLowerCase();
	
	var is_major = parseInt(navigator.appVersion);
	var is_minor = parseFloat(navigator.appVersion);
	
	var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
	if(is_ie){
		if(is_major < 4){
			return true;
		}
		if((is_major == 4) && (agt.indexOf("msie 4")!=-1)){
			return true;
		}
		if((is_major == 4) && (agt.indexOf("msie 5.0")!=-1)){
			return true;
		}
		if((is_major == 4) && (agt.indexOf("msie 5.5") !=-1)){
			return true;
		}
		if((is_major == 4) && (agt.indexOf("msie 6.")!=-1) ){
			return true;
		}
	}
	
	return false;
}

function ie6Hide(obj){
	if(checkIE6()){
		var ieframe_id = document.getElementById('ieframe');
		if(ieframe_id){
			ieframe_id.style.left = obj.style.left;
			ieframe_id.style.top =  obj.style.top;
			ieframe_id.style.height = obj.offsetHeight + 'px';
			ieframe_id.style.width = obj.offsetWidth + 'px';
			ieframe_id.style.display = 'block';
		}
	}
}
function ie6Show(){
	if(checkIE6()){
		var ieframe_id = document.getElementById('ieframe');
		if(ieframe_id){
			ieframe_id.style.display = 'none';
		}
	}
}

function showCos(page){
	var wholeval = document.forms.prodForm.company.value;
	if(wholeval.length>0){
		processajax(page, true, 'POST', 'findval='+encodeURIComponent(wholeval), document.getElementById('showbox_cos'), 'doResponseCos');	
	}
	else{
		hideCos();
	}
}
function hideCos(){
	ie6Show();
	document.getElementById('showbox_cos').style.display = 'none';
}

function doResponseCos2(response, obj){
	if(response!=''){
		obj.innerHTML = response;
		//my_innerHTML_text(obj,response);
		obj.style.left = findPosX(document.forms.prodForm.secondCompany)+'px';
		obj.style.top = (findPosY(document.forms.prodForm.secondCompany)+20)+'px';
		obj.style.display = 'block';
		ie6Hide(obj);
	}
	else{
		hideCos();
	}
}

function showCos2(page){
	var wholeval = document.forms.prodForm.secondCompany.value;
	if(wholeval.length>0){
		processajax(page, true, 'POST', 'findval='+encodeURIComponent(wholeval), document.getElementById('showbox_cos'), 'doResponseCos2');	
	}
	else{
		hideCos();
	}
}

function showFiles(page,obj){
	processajax(page, true, 'GET', '', obj, 'doFileList');
}

function doFileList(response, obj){
	if(response!=''){
		obj.innerHTML = response;
	}
}
var currentMatchObj = false;
function showMatch(page,fobj){
	currentMatchObj = fobj;
	var wholeval = fobj.value;
	if(wholeval.length>0){
		processajax(page, true, 'POST', 'findval='+encodeURIComponent(wholeval), document.getElementById('showbox_cos'), 'doResponseMatch');	
	}
	else{
		hideCos();
	}
}
function doResponseMatch(response, obj){
	if(response!=''){
		obj.innerHTML = response;
		var xval = '20px';
		var yval = '20px';
		if(currentMatchObj){
			xval = findPosX(currentMatchObj)+'px';
			yval = (findPosY(currentMatchObj)+20)+'px';
		}
		obj.style.left = xval;
		obj.style.top = yval;
		obj.style.display = 'block';
		ie6Hide(obj);
	}
	else{
		hideCos();
	}
}