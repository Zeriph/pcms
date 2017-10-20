// Variables
var InternetExporer = document.all;
var NetscapeNavigator6 = document.getElementById && !document.all;
var IsDragging = false;
var x, y;
var mx, my;
var DocObject;
var idName;

// Functions

function MouseMove(e) { // Move the form when the mouse moves (adjust position for screen res)
	if( IsDragging ) {
		DocObject.style.left = NetscapeNavigator6 ? tmpX + e.clientX - x : tmpX + event.clientX - x;
		DocObject.style.top  = NetscapeNavigator6 ? tmpY + e.clientY - y : tmpY + event.clientY - y;
		return false;
	} else {
		mx = e.pageX;
		my = e.pageY;
	}
}

function MouseDown(e) { // When the mouse goes down on the form, adjust form
	var FormObj = NetscapeNavigator6 ? e.target : event.srcElement;
	var TopElement = NetscapeNavigator6 ? "HTML" : "BODY";
	while (FormObj.tagName != TopElement && FormObj.className != "dragme") {
		FormObj = NetscapeNavigator6 ? FormObj.parentNode : FormObj.parentElement;
	}
	if (FormObj.className == "dragme") {
		IsDragging = true;
		DocObject = document.getElementById(idName);
		tmpX = parseInt(DocObject.style.left);
		tmpY = parseInt(DocObject.style.top);
		x = NetscapeNavigator6 ? e.clientX : event.clientX;
		y = NetscapeNavigator6 ? e.clientY : event.clientY;
		document.onmousemove = MouseMove;
		return false;
	}
}

function MenuShow(idName, mainWindow) {
	var Divs = document.body.getElementsByTagName("DIV");
	var Index;
	var MaxIndex = 0;
	// Bring menu to front
	for (var i = 0; i < Divs.length; i++) {
		var item = Divs[i];
		if (item ==  document.getElementById(idName) || item.style.zIndex == '') { continue; }
		Index = parseInt(item.style.zIndex);
		if (MaxIndex < Index) { MaxIndex = Index; }
	}
	document.getElementById(idName).zIndex = MaxIndex + 1;
	// Set menu location
	//var offset = document.getElementById(mainWindow).offsetWidth;
	document.getElementById(idName).style.display  = "block";
	var toploc = document.getElementById(mainWindow).clientHeight - document.getElementById(idName).clientHeight + 6;
	document.getElementById(idName).style.left = 9;
	document.getElementById(idName).style.top = toploc;
}

function PopupShow(id_Name) {
	idName = id_Name
	var Divs = document.body.getElementsByTagName("DIV");
	var Index;
	var MaxIndex = 0;
	for (var i = 0; i < Divs.length; i++) {
		var item = Divs[i];
		if (item ==  document.getElementById(idName) || item.style.zIndex == '') { continue; }
		Index = parseInt(item.style.zIndex);
		if (MaxIndex < Index) { MaxIndex = Index; }
	}
	document.getElementById(idName).zIndex = MaxIndex + 1;
	var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth;
	var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight;
	document.getElementById(idName).style.display  = "block";
	var NewX = -100;
	var NewY = -100;
	if (navigator.appName == "Microsoft Internet Explorer") {
		NewX = 150;
		NewY = 350;
	}
	//document.getElementById(idName).style.left = (document.documentElement.scrollLeft + (width - document.getElementById(idName).clientWidth) / 2 + NewY) + 'px';
	//document.getElementById(idName).style.top = (document.documentElement.scrollTop + (height - document.getElementById(idName).clientHeight) / 2 + NewX) + 'px';
	// bottom is for mouse pos
	document.getElementById(idName).style.left = (document.documentElement.scrollLeft + mx - 10) + 'px';
	document.getElementById(idName).style.top  = (document.documentElement.scrollTop + my - 10) + 'px';
}

function PopupClose(id_Name) {
	document.getElementById(id_Name).style.display = "none";
}

// Event handlers
document.onmousedown = MouseDown; // The event when the mouse goes down
document.onmouseup = new Function("IsDragging=false"); // The event when the mouse goes up
document.onmousemove = GetMouseCoordinates;

function GetMouseCoordinates(event)
{
	ev = (event ? event : window.event);
	mx = ev.clientX;
	my = ev.clientY;
}