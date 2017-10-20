var SelectedIndex = 0;
var ParentTab = null;

function GetDiv(s,i) {
	var div;
	if (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.charAt(0) < 5) {
		div = document.all.item(s+i);
	} else {
		div = document.getElementById(s+i);
	}
	return div;
}

// TODO: refactor this to take an ID and adjust for sub tabs, etc. etc.
function TabSelect(index) {
	// Loop through the DIV containers and set the Z-Index to the back except
	// for the one that was clicked, set that 
	SelectedIndex = index;
	ParentTab = null;
	var CurrentTab = GetDiv("tab", index);
	var CurrentPanel = GetDiv("panel", index);
	if (!CurrentTab.parentNode && !CurrentTab.parentNode.id) {
		// There's not a parent element (i.e. DIV/SPAN), so more than likely
		// there are no parent tabs, so just loop through and set accordingly
		var TabCount = 2; // Set this accordingly
		for (var i = 0; i < TabCount; ++i) {
			var Tab = GetDiv("tab", i);
			var Panel = GetDiv("panel", i);
			if (i == index) {
				Tab.className = "tabSelected";
				Panel.className = "panel";
				Tab.style.zIndex = (TabCount + 1);
				Panel.style.zIndex = (TabCount + 1);
			} else {
				Tab.className = "tab";
				Panel.className = "panelHidden";
				Tab.style.zIndex = (TabCount - i);
				Panel.style.zIndex = (TabCount - i);
			}
		}
	}
	var Parent = CurrentTab.parentNode;
	var Divs = Parent.getElementsByTagName("DIV");
	var Panels = new Array(); var P = 0;
	var Tabs = new Array(); var T = 0;
	var Div = null; var ClassName = '';
	var IsTab = false; var IsPanel = false;
	for (var i = 0; i < Divs.length; ++i) {
		Div = Divs[i];
		IsTab = (Div.className.toLowerCase().indexOf("tab") != -1);
		IsPanel = (Div.className.toLowerCase().indexOf("panel") != -1);
		if (!IsTab && !IsPanel) { continue; }
		if (IsTab) {
			if (Div.className.toLowerCase() == "tab-pane") { continue; }
			Tabs[T++] = Div;
		} else {
			Panels[P++] = Div;
		}	
	}
	var TabCount = Tabs.length;
	for (var i = 0; i < TabCount; ++i) {
		Tab = Tabs[i];
		if (Tab.id == CurrentTab.id) { 
			Tab.className = "tabSelected";
			Tab.style.zIndex = (TabCount + 1);
		} else {
			Tab.className = "tab";
			Tab.style.zIndex = (TabCount - i);
		}
	}
	for (var i = 0; i < TabCount; i ++) {
		Panel = Panels[i];
		if (Panel.id == CurrentPanel.id) {
			Panel.className = "panel";
			Panel.style.zIndex = (TabCount + 1);
		} else {
			Panel.className = "panelHidden";
			Panel.style.zIndex = (TabCount - i);
		}
	}
}

function TabMouseOver(index) {
	var Tab = GetDiv("tab", index);
	if (Tab.className == "tabSelected") { return; }
	Tab.className = "tabHover";
}

function TabMouseLeave(index) {
	var Tab = GetDiv("tab", index);
	if (Tab.className == "tabSelected") { return; }
	Tab.className = "tab";
}