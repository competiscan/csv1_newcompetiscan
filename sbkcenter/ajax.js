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

function processajax(serverPage, asy, getOrPost, str, functioncall, obj){
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


function sendPan(val,dbfield){
	if(val.length>0){
		processajax('contacts_pre.php', true, 'POST', 'dbfield='+dbfield+'&val='+escape(val), false, false);	
	}
}