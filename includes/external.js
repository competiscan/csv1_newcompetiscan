//Specify speed of scroll. Larger=faster (ie: 5)
var scrollspeed = 2;
var cache = scrollspeed;
//Specify intial delay before scroller starts scrolling (in miliseconds):
var initialdelay = 1000;
var dataobj;

function stop(){
	scrollspeed = 0;
}
function go(){
	scrollspeed = cache;
}
function initializeScroller(){
	dataobj = document.all? document.all.datacontainer : document.getElementById("datacontainer");
	dataobj.style.top = "5px";
	setTimeout("scrollDiv()", initialdelay);
}
function scrollDiv(){
	var thelength = dataobj.offsetHeight;
	var change = parseInt(dataobj.style.top)-scrollspeed;
	if (change<thelength*(-1)){
		dataobj.style.top = "5px";
		setTimeout("scrollDiv()", initialdelay);
	}
	else{
		dataobj.style.top = change + "px";
		setTimeout("scrollDiv()",40);
	}
}