
function MM_findObj(n, d) { //v4.01
	var p,i,x;
	if(!d){
		d = document;
	}
	if((p = n.indexOf("?"))>0 && parent.frames.length) {
		d = parent.frames[n.substring(p+1)].document;
		n = n.substring(0,p);
	}
	if(!(x = d[n]) && d.all) {
		x = d.all[n];
	}
	for (i=0; !x && i<d.forms.length; i++) {
		x = d.forms[i][n];
	}
	for(i=0;!x && d.layers && i<d.layers.length; i++) {
		x = MM_findObj(n,d.layers[i].document);
	}
	if(!x && d.getElementById) {
		x = d.getElementById(n);
	}
	
	return x;
}

// checking for the valid email address.
function checkmail(eAddr) {
	var usEmail = true;
	var lenSuffix = (usEmail) ? 3 : 2;
	var result = false;
	var ndxAt  = eAddr.indexOf("@");
	var ndxDot = eAddr.indexOf(".");
	var ndxDot2 = eAddr.lastIndexOf(".");
	if ((ndxDot < 0) || (ndxAt < 0)) {
		return false;
	}
	else if ( (ndxDot2 - 2) <= ndxAt) {
		return false;
	}
	else if (eAddr.length < ndxDot2 + lenSuffix) {
		return false;
	}
	else {
		return true;
	}
}

function trimspace(str) {
	var len = str.length;
	if (len>0) {
		for(var i=0;i<len;i++) {
			if(str.indexOf(" ")==0){
				str = str.substring(1,len);
				len--;
			}
			else {
				break;	
			}
		}
		for(var i=len;i>=1;i--) {
			if(str.substring(len-1,len)==' '){
				str = str.substring(0,len-1);
				len--;
			}
			else {
				break;	
			}
		}
	}
	
	return str;
}
