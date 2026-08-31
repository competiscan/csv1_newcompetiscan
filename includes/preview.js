var from_admin = '';
var is_admin = false;
var pdfsearch = '';
var lastpid = '0';
var zind = 0;
var delayms = 500;
var timeridArray = new Array();
var growArray = new Array();
var widthadd = 12;
var heightadd = 36;
var initinc = 10;
var xadd = 154;
var isIE6 = checkIE6();
var maxwidth = 400;
var maxheight = 400;
var sample_types = new Array();
var sample_widths = new Array();
var sample_heights = new Array();

function hidePreview(pid){
	if(timeridArray[pid]){
		window.clearTimeout(timeridArray[pid]);
		timeridArray[pid] = false;
	}
	else{
		//doShrinkBox(pid);	
	}
}
function showPreview(pid,ppage,isdig){
        isdig = isdig || '';
	if(pid!=lastpid){
		timeridArray[pid] = window.setTimeout(doPreview(pid,ppage,isdig), delayms);
	}
}

function doPreview(pid,ppage,isdig){
        isdig = isdig || '';
	var sample_type = '';
	if(sample_types[pid]){
		sample_type = sample_types[pid];
	}
	timeridArray[pid] = false;
	if(pid!=lastpid){
		if(lastpid!='0'){
			doShrinkBox(lastpid);
		}
		
		var testobj = document.getElementById('pdiv'+pid);
		if(!testobj){
			growArray[pid] = 0;
			lastpid = pid;
			var obj = document.getElementById('pcontainer');
			var obj2 = document.getElementById('pimg'+pid);
			
			var divleft = (xadd+findPosX(obj2))+'px';
			var divtop = findPosY(obj2)+'px';
			
			var waitimg  = new Image(); //var waitimg = document.createElement('img');
			waitimg.style.border = 'none';
			waitimg.src = from_admin+'images/searching.gif';
			
			var waitdiv = document.createElement('div');
			waitdiv.style.display = 'none';
			waitdiv.style.position = 'absolute';
			waitdiv.style.border = 'solid 1px #000000';
			waitdiv.style.background = '#ffffff';
			waitdiv.style.padding = '4px';
			waitdiv.style.zIndex = 99+zind;
			waitdiv.id = 'wimg'+pid;
			waitdiv.style.left = divleft;
			waitdiv.style.top = divtop;
			
			waitdiv.appendChild(waitimg);
			obj.appendChild(waitdiv);
			showWait('wimg'+pid);
	
			var newimg = new Image(); //var newimg = document.createElement('img');
			newimg.onload = new Function("doGrowBox('"+pid+"'); return true;");
			newimg.style.border = 'none';
			newimg.style.verticalAlign = 'middle';
			newimg.id = 'dimg'+pid;                        
			if(is_admin && sample_type!='flash' && sample_type!='image'){
				newimg.onclick = new Function("doPDFSample('"+pid+"'); return true;");
				newimg.style.cursor = 'pointer';
			}
			
			var newdiv = document.createElement('div');
			newdiv.style.overflow = 'hidden';
			newdiv.style.display = 'none';
			newdiv.style.position = 'absolute';
			newdiv.style.border = 'solid 2px #000000';
			newdiv.style.background = '#ffffff';
			newdiv.style.padding = '4px';
			newdiv.style.zIndex = 100+zind;
			newdiv.id = 'pdiv'+pid;
			newdiv.style.left = divleft;
			newdiv.style.top = divtop;
			
			var innerdiv = document.createElement('div');
			innerdiv.style.cssFloat = 'left';
			innerdiv.style.styleFloat = 'left';
			innerdiv.style.visibility = 'hidden';
			innerdiv.id = 'del'+pid;
			
			var innerlink = document.createElement('a');
			innerlink.href = '#';
			innerlink.onclick = new Function("doShrinkBox('"+pid+"'); return false;");
			
			var innerimg = new Image(); //document.createElement('img');
			innerimg.style.border = 'none';
			innerimg.style.verticalAlign = 'bottom';
			innerimg.src = from_admin+'images/drop.png';
			
			var innerdiv_right = document.createElement('div');
			innerdiv_right.style.cssFloat = 'right';
			innerdiv_right.style.styleFloat = 'right';
			innerdiv_right.style.visibility = 'hidden';
			innerdiv_right.id = 'pdf'+pid;
                        var showpdftext=''
                        if(isdig=='1'){
                            showpdftext='View in full screen';
                        }else{
                            showpdftext='View PDF Content';
                            
                        }
			var newnode = document.createTextNode(showpdftext);
			
			//var newnode = document.createTextNode('View PDF Content ');
			
			var innerlink_right = document.createElement('a');
			innerlink_right.href = '#';
			innerlink_right.className = 'bluelink';
			innerlink_right.onclick = new Function("pdfWin('"+pid+"'); return false;");
			
			var innerimg_right = new Image(); //document.createElement('img');
			innerimg_right.style.border = 'none';
			innerimg_right.style.verticalAlign = 'bottom';
                        
                        if(isdig=='1'){
                          // innerimg_right.src = from_admin+'images/pdf.jpg';
                        }else{
                            innerimg_right.src = from_admin+'images/pdf.jpg';
                            
                        }
                        
			//innerimg_right.src = from_admin+'images/pdf.jpg';
			
			var cleardiv = document.createElement('div');
			cleardiv.style.clear = 'both';
			cleardiv.style.paddingTop = '2px';
			cleardiv.style.textAlign = 'center';
			cleardiv.id = 'sample_'+pid;
			
			innerlink.appendChild(innerimg);
			innerdiv.appendChild(innerlink);
			newdiv.appendChild(innerdiv);
			
			if(sample_type=='flash'){
				var bannerlink = document.createElement('a');
				bannerlink.href = 'http://www.adobe.com/go/getflashplayer';
				bannerlink.target = '_blank';
				bannerlink.appendChild(newimg);
				cleardiv.appendChild(bannerlink);
			}
			else{
				if(sample_type!='image'){
					innerlink_right.appendChild(newnode);
					innerlink_right.appendChild(innerimg_right);
					innerdiv_right.appendChild(innerlink_right);
					newdiv.appendChild(innerdiv_right);
				}
				cleardiv.appendChild(newimg);
			}
                        
			newdiv.appendChild(cleardiv);
			
			obj.appendChild(newdiv);
			
			p_ie6Frame(pid,obj);
			
			if(sample_type=='flash'){
				newimg.src = 'http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif';
			}
			else if(sample_type=='image'){
				newimg.src = from_admin+'productDocuments.php?id='+pid+'&did=2';
				if(sample_widths[pid]){
					newimg.width = sample_widths[pid];
				}
				if(sample_heights[pid]){
					newimg.height = sample_heights[pid];
				}
			}
			else{
				newimg.src = from_admin+'pdfSample.php?id='+pid+'&new='+ppage;
			}
			
			zind++;
                        
		}
		else if(testobj.style.display=='none'){
			growArray[pid] = 0;
			lastpid = pid;
			doGrowBox(pid);
		}
	}
	return true;
}
function getImgWidth(obj){
	var tmpwidth = maxwidth;
	if(obj){
		if(obj.width && obj.width>0 && obj.width<maxwidth){
			tmpwidth = obj.width;
		}
	}
	return tmpwidth;
}
function getImgHeight(obj){
	var tmpheight = maxheight;
	if(obj){
		if(obj.height && obj.height>0 && obj.height<maxheight){
			tmpheight = obj.height;
		}
	}
	return tmpheight;
}
function doGrowBox(pid){
	var sample_type = '';
	if(sample_types[pid]){
		sample_type = sample_types[pid];
	}
	growArray[pid] = 1;
	if(sample_type=='flash'){
		var fwidth = maxwidth;
		var fheight = maxheight;
		if(sample_widths[pid]){
			fwidth = sample_widths[pid];
		}
		if(sample_heights[pid]){
			fheight = sample_heights[pid];
		}
		growBox(pid,0,0,fwidth,fheight + heightadd);
		hideWait('wimg'+pid);
		var flashvars = {};
		var params = {
			loop: "true",
			menu: "false",
			quality: "high",
			wmode: "transparent"
		};
		var attributes = {};
		swfobject.embedSWF(from_admin+"productDocuments.php?id="+pid+"&did=2", 'sample_'+pid, fwidth, fheight, "9.0.0","includes/expressInstall.swf", flashvars, params, attributes);
	}
	else{
		var obj = document.getElementById('dimg'+pid);
		if(obj){
			var gwidth = getImgWidth(obj)+widthadd;
			if(gwidth<150){
				gwidth = 150;
			}
			growBox(pid,0,0,gwidth,(getImgHeight(obj)+heightadd));
			hideWait('wimg'+pid);
		}
	}
}
function doShrinkBox(pid){
	var sample_type = '';
	if(sample_types[pid]){
		sample_type = sample_types[pid];
	}
	growArray[pid] = -1;
	var obj = document.getElementById('dimg'+pid);
	if(obj || sample_type=='flash'){
		var obj2 = document.getElementById('del'+pid);
		if(obj2){
			obj2.style.visibility = 'hidden';
		}
		var obj3 = document.getElementById('pdf'+pid);
		if(obj3){
			obj3.style.visibility = 'hidden';
		}
		if(pid==lastpid){
			lastpid = '0';
		}
		p_ie6Show(pid);
		var startw = maxwidth;
		var starth = maxheight;
		if(sample_type=='flash'){
			if(sample_widths[pid]){
				startw = sample_widths[pid];
			}
			if(sample_heights[pid]){
				starth = sample_heights[pid];
			}
		}
		else{
			startw = getImgWidth(obj);
			starth = getImgHeight(obj);
		}
		startw = startw+widthadd;
		starth = starth+heightadd;
		shrinkBox(pid,startw,starth,startw,starth);
	}
}
function hideSWFDiv(pid,blockwidth,blockheight){
	var obj = document.getElementById('bdiv'+pid);
	if(obj){
		obj.style.visibility = 'visible';
	}
	else{
		var obj2 = document.getElementById('pdiv'+pid);
		var obj3 = document.getElementById('pcontainer');
		if(obj2 && obj3){
			var blockdiv = document.createElement('div');
			blockdiv.id = 'bdiv'+pid;
			blockdiv.style.position = 'absolute';
			var left_int = parseInt(obj2.style.left) + 4;
			var top_int = parseInt(obj2.style.top) + (heightadd/2);
			blockheight = blockheight - heightadd + 4;
			blockwidth = blockwidth + 4;
			blockdiv.style.left = left_int+'px';
			blockdiv.style.top = top_int+'px';
			blockdiv.style.width = blockwidth+'px';
			blockdiv.style.height = blockheight+'px';
			blockdiv.style.zIndex = 1000;
			blockdiv.style.opacity = 0;
			blockdiv.style.filter = 'alpha(opacity=0)';
			blockdiv.style.visibility = 'visible';
			obj3.appendChild(blockdiv);
		}
	}
}
function showSWFDiv(pid){
	var obj = document.getElementById('bdiv'+pid);
	if(obj){
		obj.style.visibility = 'hidden';
	}
}
function growBox(pid,widthstart,heightstart,widthend,heightend){
	var sample_type = '';
	if(sample_types[pid]){
		sample_type = sample_types[pid];
	}
	var objid = 'pdiv'+pid;
	var obj = document.getElementById(objid);
	obj.style.display = 'block';
	var inc = initinc;
	if(widthstart<widthend){
		obj.style.width = widthstart+'px';
	}
	else if((widthstart-inc)<widthend){
		obj.style.width = widthend+'px';
	}
	if(heightstart<heightend){
		obj.style.height = heightstart+'px';
	}
	else if((heightstart-inc)<heightend){
		obj.style.height = heightend+'px';
	}
	
	doOp(obj,widthstart,heightstart,widthend,heightend);
	
	if(widthstart<widthend || heightstart<heightend){
		if(growArray[pid]==1){
			setTimeout("growBox('"+pid+"',"+(widthstart+inc)+","+(heightstart+inc)+","+widthend+","+heightend+")", 1);
		}
	}
	else{
		growArray[pid] = 0;
		var obj2 = document.getElementById('del'+pid);
		if(obj2){
			obj2.style.visibility = 'visible';
		}
		var obj3 = document.getElementById('pdf'+pid);
		if(obj3){
			obj3.style.visibility = 'visible';
		}
		p_ie6Hide(pid,obj);
	}
	if(sample_type=='flash'){
		hideSWFDiv(pid,widthend,heightend);
	}
}
function shrinkBox(pid,widthstart,heightstart,widthend,heightend){
	var sample_type = '';
	if(sample_types[pid]){
		sample_type = sample_types[pid];
	}
	var objid = 'pdiv'+pid;
	var obj = document.getElementById(objid);
	var incw = initinc;
	var inch = initinc;
	
	if(widthstart>heightstart){
		inch = 0;
		if(widthstart-incw<heightstart){
			incw = widthstart - heightstart;
		}
	}
	else if(widthstart<heightstart){
		incw = 0;
		if(heightstart-inch<widthstart){
			inch = heightstart - widthstart;
		}
	}
	
	if(widthstart>0){
		obj.style.width = widthstart+'px';
	}
	if(heightstart>0){
		obj.style.height = heightstart+'px';
	}
		
	doOp(obj,widthstart,heightstart,widthend,heightend);
	
	if(widthstart>0 || heightstart>0){
		if(growArray[pid]==-1){
			setTimeout("shrinkBox('"+pid+"',"+(widthstart-incw)+","+(heightstart-inch)+","+widthend+","+heightend+")", 1);
		}
	}
	else{
		obj.style.display = 'none';
		growArray[pid] = 0;
	}
	if(sample_type=='flash'){
		showSWFDiv(pid);
	}
}
function doOp(obj,widthstart,heightstart,widthend,heightend){
	var op = 1;
	if(widthend>0 && widthend>heightend){
		op = widthstart/widthend;
	}
	else if(heightend>0){
		op = heightstart/heightend;
	}
	if(op>1){
		op = 1;
	}
	else if(op<0.01){
		op = 0;
	}
	
	obj.style.opacity = op;
	if(obj.filters){
		if(op==1){
			obj.style.removeAttribute('filter');
		}
		else{
			obj.style.filter = 'alpha(opacity='+Math.round(op*100)+')';
		}
	}
}
function showWait(objid){
	var obj = document.getElementById(objid);
	obj.style.display = 'block';
}
function hideWait(objid){
	var obj = document.getElementById(objid);
	obj.style.display = 'none';
}
function pdfWin(pid) {
	var winy = window.open(from_admin+'productDocuments.php?id='+pid+pdfsearch,"pdf"+pid,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes");
	winy.focus();
}

function p_ie6Hide(pid,obj){
	if(isIE6){
		var ieframe_id = document.getElementById('ieframe'+pid);
		if(ieframe_id){
			ieframe_id.style.left = obj.style.left;
			ieframe_id.style.top =  obj.style.top;
			ieframe_id.style.height = obj.offsetHeight + 'px';
			ieframe_id.style.width = obj.offsetWidth + 'px';
			ieframe_id.style.display = 'block';
		}
	}
}
function p_ie6Show(pid){
	if(isIE6){
		var ieframe_id = document.getElementById('ieframe'+pid);
		if(ieframe_id){
			ieframe_id.style.display = 'none';
		}
	}
}
function p_ie6Frame(pid,obj){
	if(isIE6){
		var ieframe = document.createElement('iframe');
		ieframe.id = 'ieframe'+pid;
		ieframe.style.display = 'none';
		ieframe.style.position = 'absolute';
		ieframe.style.border = 'none';
		obj.appendChild(ieframe);
	}
}
function showIns(pid){
	var objid = 'Ins'+pid;
	var obj = document.getElementById(objid);
	var obj2 = document.getElementById('Inslink'+pid);
	
	var vtop = findPosY(obj2);
	if(vtop<0) {
		vtop = 0;
	}
	
	obj.style.left = (findPosX(obj2)-20)+'px';
	obj.style.top = vtop+'px';
	
	doOp2(objid,0,1,0.1);
	obj.style.display = 'block';
}
function hideIns(pid){
	var objid = 'Ins'+pid;
	doOp2(objid,1,0,0.1);
}
function doOp2(objid,opstart,opend,speed){
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
		setTimeout("doOp2('"+objid+"',"+op+","+opend+","+speed+")", 1);
	}
}
function doPDFSample(pid) {
	if(is_admin){
		//var winy = window.open(from_admin+"admin/managepdfSample.php?productID="+pid,null,"scrollbars=yes, resizable=yes, height=450,width=650,status=yes,toolbar=no,menubar=no,location=no,addressbar=yes");
		var winy = window.open("https://cs.competiscan.com/managepdf/"+pid,null,"scrollbars=yes, resizable=yes, height=450,width=650,status=yes,toolbar=no,menubar=no,location=no,addressbar=yes");
		winy.focus();
	}
}








function setAdmin(){
	from_admin = '../';
	xadd = 17;
	is_admin = true;
}

function showPreview_digital(pid,ppage,prevtyp,filetyp,isdig,isadmin){    
    isdig = isdig || '';
    isadmin = isadmin || '';
    if(pid!=lastpid){ 
//		timeridArray[pid] = window.setTimeout('doPreview_digital('+pid+','+ppage+','+prevtyp+','+filetyp+','+isdig+','+isadmin+')', delayms);
                timeridArray[pid] = window.setTimeout(doPreview_digital(pid,ppage,prevtyp,filetyp,isdig,isadmin), delayms);
	
    }
}
function doPreview_digital(pid,ppage,prevtyp,filetyp,isdig,isadmin){   
        isdig = isdig || '';
        isadmin = isadmin || '';
	var sample_type = '';
	if(sample_types[pid]){
		sample_type = sample_types[pid];
	}
	timeridArray[pid] = false;
	if(pid!=lastpid){  
            if(lastpid!='0'){
                    doShrinkBox(lastpid);
             }
		
		var testobj = document.getElementById('pdiv'+pid);
		if(!testobj){ 
                    growArray[pid] = 0;
                    lastpid = pid;
                    var obj = document.getElementById('pcontainer');
                    var obj2 = document.getElementById('pimg'+pid);

                    var divleft = (xadd+findPosX(obj2))+'px';
                    var divtop = findPosY(obj2)+'px';

                    var waitimg  = new Image(); //var waitimg = document.createElement('img');
                    waitimg.style.border = 'none';
                    waitimg.src = from_admin+'images/searching.gif';


                    var waitdiv = document.createElement('div');
                    waitdiv.style.display = 'none';
                    waitdiv.style.position = 'absolute';
                    waitdiv.style.border = 'solid 1px #000000';
                    waitdiv.style.background = '#ffffff';
                    waitdiv.style.padding = '4px';
                    waitdiv.style.zIndex = 99+zind;
                    waitdiv.id = 'wimg'+pid;
                    waitdiv.style.left = divleft;
                    waitdiv.style.top = divtop;

                    waitdiv.appendChild(waitimg);
                    obj.appendChild(waitdiv);
                    showWait('wimg'+pid);

                    var newimg = new Image(); //var newimg = document.createElement('img');
                    newimg.onload = new Function("doGrowBox_digital('"+pid+"'); return true;");
                    newimg.style.border = 'none';
                    newimg.style.verticalAlign = 'middle';
                    newimg.id = 'dimg'+pid;
                    if(is_admin && sample_type!='flash' && sample_type!='image'){
                            newimg.onclick = new Function("doPDFSample('"+pid+"'); return true;");
                            newimg.style.cursor = 'pointer';
                    }
                       
			
                    var newdiv = document.createElement('div');
                    newdiv.style.overflow = 'hidden';
                    //newdiv.style.display = 'none';
                    newdiv.style.position = 'absolute';
                    newdiv.style.border = 'solid 2px #000000';
                    newdiv.style.background = '#ffffff';
                    newdiv.style.padding = '4px';
                    newdiv.style.zIndex = 100+zind;
                    newdiv.id = 'pdiv'+pid;
                    newdiv.style.left = divleft;
                    newdiv.style.top = divtop;


                    var innerdiv = document.createElement('div');
                    innerdiv.style.cssFloat = 'left';
                    innerdiv.style.styleFloat = 'left';
                    //innerdiv.style.visibility = 'hidden';
                    innerdiv.id = 'del'+pid;

                    var innerlink = document.createElement('a');
                    innerlink.href = '#';
                    innerlink.onclick = new Function("doShrinkBox('"+pid+"'); return false;");

                    var innerimg = new Image(); //document.createElement('img');
                    innerimg.style.border = 'none';
                    innerimg.style.verticalAlign = 'bottom';
                    innerimg.src = from_admin+'images/drop.png';

                    var innerdiv_right = document.createElement('div');
                    innerdiv_right.style.cssFloat = 'right';
                    innerdiv_right.style.styleFloat = 'right';
                    //innerdiv_right.style.visibility = 'hidden';
                    innerdiv_right.id = 'pdf'+pid;

                    var newnode = document.createTextNode('View in full screen');

                    var innerlink_right = document.createElement('a');
                    innerlink_right.href = '#';
                    innerlink_right.className = 'bluelink';
                    var isadmins=','+isadmin;
                    
                    
                    innerlink_right.onclick = new Function("pdfWin_digital('"+pid+"','"+isadmin+"'); return false;");

                    var innerimg_right = new Image(); //document.createElement('img');
                    innerimg_right.style.border = 'none';
                    innerimg_right.style.verticalAlign = 'bottom';
                    //innerimg_right.src = from_admin+'images/pdf.jpg';

                    var cleardiv = document.createElement('div');
                    cleardiv.style.clear = 'both';
                    cleardiv.style.paddingTop = '2px';
                    cleardiv.style.textAlign = 'center';
                    cleardiv.id = 'sample_'+pid;

                    innerlink.appendChild(innerimg);

                    innerdiv.appendChild(innerlink);
                    newdiv.appendChild(innerdiv);

                    if(sample_type=='flash'){
                        var bannerlink = document.createElement('a');
                        bannerlink.href = 'http://www.adobe.com/go/getflashplayer';
                        bannerlink.target = '_blank';
                        bannerlink.appendChild(newimg);
                        cleardiv.appendChild(bannerlink);
                    }else{ 
                        if(sample_type!='image'){ 
                            innerlink_right.appendChild(newnode);
                            innerlink_right.appendChild(innerimg_right);
                            innerdiv_right.appendChild(innerlink_right);
                            newdiv.appendChild(innerdiv_right);
                        }
                            cleardiv.appendChild(newimg);
                    }
                        newdiv.appendChild(cleardiv);

                        obj.appendChild(newdiv);

                        p_ie6Frame(pid,obj);

                    if(sample_type=='flash'){
                            newimg.src = 'http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif';
                    }
                    else if(sample_type=='image'){ 
                        newimg.src = from_admin+'productDocuments.php?id='+pid+'&did=2';
                        if(sample_widths[pid]){
                            newimg.width = sample_widths[pid];
                        }
                        if(sample_heights[pid]){
                            newimg.height = sample_heights[pid];
                        }
                    }else{ 
                           newimg.src = from_admin+'pdfSample.php?id='+pid+'&new='+ppage;
                            var abc= from_admin+'pdfSample.php?id='+pid+'&new='+ppage; 
                            //alert(abc);
                            document.getElementById('dimg'+pid).style.display= "none";
                            var divs = document.getElementById('sample_'+pid);
                         // document.getElementById('pdiv'+pid).setAttribute("style","width:400px !important; height:400px !important; overflow:hidden; display:block; position:absolute;z-index:100; left:290px; top:488px; opacity:1; ");
                           // border:2px solid rgb(0, 0, 0);padding: 4px; background:rgb(255, 255, 255);
//                       var element = document.getElementById('pdiv'+pid);
//
//                            element.style.width = null;
//                            element.style.height = null;                          
                            
                            
                            setTimeout(function(){ getiframeimg(pid,prevtyp,filetyp,isadmin); }, 300);   
                    }			
			zind++;
		}
		else if(testobj.style.display=='none'){ 
                    growArray[pid] = 0;
                    lastpid = pid;
                    
                    var originalStyle = document.getElementById('pdiv'+pid).getAttribute('style'); 

                            var regex = new RegExp(/(width:|height:).+?(;[\s]?|$)/g);
                            //Replace matches with null
                            var modStyle = originalStyle.replace(regex, ""); 
//alert(modStyle);
                        //Set the modified style value to element using it's Id
                        document.getElementById('pdiv'+pid).setAttribute('style', modStyle); 
doGrowBox_digital(pid);
                    
		}
	}
	return true;
}

function getiframeimg(pid,prevtyp,filetyp,isadmin){
        isadmin = isadmin || '';
    
        if (window.XMLHttpRequest) {                                
            ajaxRequest = new XMLHttpRequest();
        }else {
            ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
        }             
					
        ajaxRequest.onreadystatechange = function(){
            if(ajaxRequest.readyState == 4){
              var ajaxDisplay = document.getElementById('sample_'+pid);

              var innerDiv3 = document.createElement('div');
                innerDiv3.className = 'block-2';
                innerDiv3.id = 'block_'+pid;
                innerDiv3.style.height = '150px;';                                    
                ajaxDisplay.appendChild(innerDiv3);
                var ajaxDisplay2 = document.getElementById('block_'+pid);                                                                     
                ajaxDisplay2.innerHTML = ajaxRequest.responseText;
                
                var originalStyle = document.getElementById('pdiv'+pid).getAttribute('style'); 

                            var regex = new RegExp(/(width:|height:).+?(;[\s]?|$)/g);
                            //Replace matches with null
                            var modStyle = originalStyle.replace(regex, ""); 
//alert(modStyle);
                        //Set the modified style value to element using it's Id
                        document.getElementById('pdiv'+pid).setAttribute('style', modStyle); 
                if(prevtyp=='2' && filetyp=='2'){        
                document.getElementById('wimg'+pid).style.display='none';
                }
                
            }
        }
        var queryString="id=" + pid;        
        queryString +=  "&prevtyp=" + prevtyp + "&filetyp=" + filetyp;
        if(isadmin==1){ 
            ajaxRequest.open("GET", "../fetchiframe.php?"+queryString, true);
        }else{ 
            ajaxRequest.open("GET", "fetchiframe.php?"+queryString, true);
        }        
        ajaxRequest.send(null); 
}


1`	`
function pdfWin_digital(pid,isadmin) {
        isadmin = isadmin || '';
        if (window.XMLHttpRequest) {                                
            ajaxRequest = new XMLHttpRequest();
        }else {
            ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
        }             
					
        ajaxRequest.onreadystatechange = function(){
            if(ajaxRequest.readyState == 4){                                     
                var link_iframe = ajaxRequest.responseText;
                //if(isadmin=='1'){
                   var from_admin='';
               // }
               // alert(link_iframe);
               //commented for displaying image at productDocument page.
               
                //var winy = window.open(from_admin+link_iframe,"pdf"+pid,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes");
                //winy.focus();
                
                //added for displaying image at productDocument page.
               /* Changes for S3 implementation */
                var winy = window.open('../productDocuments.php?id='+pid+pdfsearch,"pdf"+pid,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes");
                winy.focus();              
                
                
            }
        }
        var queryString="id=" + pid;
        var prevtyps='';
        var filetyps='';
        queryString +=  "&prevtyp=" + prevtyps + "&filetyp=" + filetyps;
        if(isadmin=='1'){
            ajaxRequest.open("GET", "../fetchiframe.php?"+queryString, true);
        }else{
            ajaxRequest.open("GET", "fetchiframe.php?"+queryString, true);
        }       
        //ajaxRequest.open("GET", "fetchiframe.php?"+queryString, true);
        ajaxRequest.send(null);     
    
	
}
function doGrowBox_digital(pid){
	var sample_type = '';
	if(sample_types[pid]){
		sample_type = sample_types[pid];
	}
	growArray[pid] = 1;
	if(sample_type=='flash'){
		var fwidth = maxwidth;
		var fheight = maxheight;
		if(sample_widths[pid]){
			fwidth = sample_widths[pid];
		}
		if(sample_heights[pid]){
			fheight = sample_heights[pid];
		}
		growBox(pid,0,0,fwidth,fheight + heightadd);
		hideWait('wimg'+pid);
		var flashvars = {};
		var params = {
			loop: "true",
			menu: "false",
			quality: "high",
			wmode: "transparent"
		};
		var attributes = {};
		swfobject.embedSWF(from_admin+"productDocuments.php?id="+pid+"&did=2", 'sample_'+pid, fwidth, fheight, "9.0.0","includes/expressInstall.swf", flashvars, params, attributes);
	}
	else{
		var obj = document.getElementById('dimg'+pid);
		if(obj){
			var gwidth = getImgWidth(obj)+widthadd;
			if(gwidth<150){
				gwidth = 150;
			}
			growBox_digital(pid,0,0,gwidth,(getImgHeight(obj)+heightadd));
			hideWait('wimg'+pid);
		}
	}
}

function growBox_digital(pid,widthstart,heightstart,widthend,heightend){
	var sample_type = '';
	if(sample_types[pid]){
		sample_type = sample_types[pid];
	}
	var objid = 'pdiv'+pid;
	var obj = document.getElementById(objid);
	obj.style.display = 'block';
	var inc = initinc;
	if(widthstart<widthend){
		//obj.style.width = widthstart+'px';
	}
	else if((widthstart-inc)<widthend){
		//obj.style.width = widthend+'px';
	}
	if(heightstart<heightend){
		//obj.style.height = heightstart+'px';
	}
	else if((heightstart-inc)<heightend){
		//obj.style.height = heightend+'px';
	}
	
	doOp(obj,widthstart,heightstart,widthend,heightend);
	
	if(widthstart<widthend || heightstart<heightend){
		if(growArray[pid]==1){
			setTimeout("growBox_digital('"+pid+"',"+(widthstart+inc)+","+(heightstart+inc)+","+widthend+","+heightend+")", 1);
		}
	}
	else{
		growArray[pid] = 0;
		var obj2 = document.getElementById('del'+pid);
		if(obj2){
			obj2.style.visibility = 'visible';
		}
		var obj3 = document.getElementById('pdf'+pid);
		if(obj3){
			obj3.style.visibility = 'visible';
		}
		p_ie6Hide(pid,obj);
	}
	if(sample_type=='flash'){
		hideSWFDiv(pid,widthend,heightend);
	}
}

function htmlWin(pid) {
	var winy = window.open(from_admin+'processed-html.php?id='+pid,"pdf"+pid,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes, width=900, height=600");
	winy.focus();
}