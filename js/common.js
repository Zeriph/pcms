// TODO: seperate out this better .. javascript .. ugh :/

function JSTrim(s) {
    var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
    return s.replace(rtrim, '');
}

function Validate(id, msg) {
	var val = Trim(document.getElementById(id).value);
	if (val == "") { alert(msg); return false; }
	return true;
}

function CheckInput(id) {
	var val = document.getElementById(id).value;
	var divname = "div_" + id;
	if (val == "") {
		document.getElementById(divname).style.display = 'block';
	} else {
		document.getElementById(divname).style.display = 'none';
	}
}

function ShowHideDiv(div) {
	if (document.getElementById(div).style.display == 'none') {
		document.getElementById(div).style.display = 'block';
	} else {
		document.getElementById(div).style.display = 'none';
	}
}

function CheckPass(nce) {
    var n = document.getElementById("new_pass");
    var c = document.getElementById("con_pass");
    var e = document.getElementById(nce);
    if (n.value != c.value) {
        e.style.backgroundColor = "#FF5555";
    } else {
        n.style.backgroundColor = "";
        c.style.backgroundColor = "";
    }
}

function CheckValue(id) {
    var n = document.getElementById(id);
    if (n != null) {
        if (n.value == null || n.value == "") {
            n.style.backgroundColor = "#FF5555";
        } else {
            n.style.backgroundColor = "";
        }
    }
}

function IsNullOrEmpty(str) {
    return (str == null || JSTrim(str) == "");
}

function IsValidDate(nm) {
    if (IsNullOrEmpty(nm)) { return true; }
    if (nm.length > 22) { return false; }
    var re = /([0-9][0-9][0-9][0-9])[-/\s]?([0-9][0-9])[-/\s]?([0-9][0-9])[\s]*([0-9][0-9])[:\s]?([0-9][0-9])[:\s]?([0-9][0-9])[\s]*([AaPp][Mm])?/;
    return !isNaN(Date.parse(nm)) || nm.match(re);
}

function IsValidEmail(nm) {
    if (IsNullOrEmpty(nm)) { return true; }
    var re = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    return re.test(nm) && nm.length < 255;
}

function IsValidPhone(nm) {
    if (IsNullOrEmpty(nm)) { return true; }
    var m = /^[xX0-9\~\`\!\@\#\$\%\^\&\*\(\)\-\_\+\|\}\{\"\:\?\>\<\\\]\[\'\;\/\.\,\s]+$/g;
    return nm.match(m) && nm.length <= 40;
}

function IsValidUserName(nm) {
    if (IsNullOrEmpty(nm)) { return false; }
    if (nm == "[Add User]" || nm == "[New User]") { return false; }
    var m = /^[0-9a-zA-Z\"\'\.\s]+$/g;
    return nm.match(m) && nm.length <= 128;
}

function IsValidLogin(nm) {
    if (IsNullOrEmpty(nm)) { return false; }
    var m = /^[0-9a-zA-Z\.\_]+$/g;
    return nm.match(m) && nm.length <= 128;
}

function IsValidProjectName(nm) {
    if (IsNullOrEmpty(nm) || nm == "[Add Project]" || nm == "[New Project]" || nm.length > 255) { return false; }
    return true;
}

function IsValidProjectDesc(nm) {
    if (nm != null) { return nm.length <= 4096; }
    return true;
}

function CheckUserName(id) {
    var n = document.getElementById(id).value;
    if (!IsValidUserName(n)) {
        document.getElementById(id).style.background = "#FF5555";
    } else {
        document.getElementById(id).style.background = "";
    }
}

function CheckProjectName(id) {
    var n = document.getElementById(id).value;
    if (!IsValidProjectName(n)) {
        document.getElementById(id).style.background = "#FF5555";
    } else {
        document.getElementById(id).style.background = "";
    }
}

function CheckProjectDesc(id) {
    var n = document.getElementById(id).value;
    if (!IsValidProjectDesc(n)) {
        document.getElementById(id).style.background = "#FF5555";
    } else {
        document.getElementById(id).style.background = "";
    }
}

function CheckDate(id) {
    var n = document.getElementById(id).value;
    if (!IsValidDate(n)) {
        document.getElementById(id).style.background = "#FF5555";
    } else {
        document.getElementById(id).style.background = "";
    }
}

function CheckLogin(id) {
    var n = document.getElementById(id).value;
    if (!IsValidLogin(n)) {
        document.getElementById(id).style.background = "#FF5555";
    } else {
        document.getElementById(id).style.background = "";
    }
}

function CheckEmail(id) {
    var e = document.getElementById(id).value;
    if (!IsValidEmail(e)) {
        document.getElementById(id).style.background = "#FF5555";
    } else {
        document.getElementById(id).style.background = "";
    }
}

function CheckPhone(id) {
    var e = document.getElementById(id).value;
    if (!IsValidPhone(e)) {
        document.getElementById(id).style.background = "#FF5555";
    } else {
        document.getElementById(id).style.background = "";
    }
}

function DoTextCheck(id, charDivId, maxlen) {
    var cval = document.getElementById(charDivId);
    var ival = document.getElementById(id).value;
    if (ival != null  && ival.length < maxlen) {
        document.getElementById(id).style.background = "";
    } else {
        document.getElementById(id).style.background = "#FF5555";
    }
    cval.innerHTML = ((maxlen - ival.length) + " characters left");
}

function ScrollIntoView(id) {
    var elm = document.getElementById(id);
    if (elm != null && elm.scrollIntoView) {
       elm.scrollIntoView();
    }
}

function ScrollTo(id, parent) {
    var elm = document.getElementById(id);
    if (elm != null) {
        var tnode = elm.parentNode;
        if (parent != null) { tnode = document.getElementById(parent); }
        if (tnode != null) { tnode.scrollTop = elm.offsetTop; }
    }
}

function GetDate() {
    var t = new Date(); 
    return t.toDateString();
}

function GetTime() {
    var t = new Date(); 
    return t.toLocaleTimeString();
}

function GetDateTime() {
    return GetDate() + ' ' + GetTime();
}

function SetCurrentTime() {
    var tm = document.getElementById('jstime');
    if (tm) {
        var dt = GetDateTime();
        tm.innerHTML = dt;
    }
}

function StartTimers(userCallback) {
    SetCurrentTime();
    setInterval(SetCurrentTime, 1000);
    if (userCallback && (typeof userCallback == "function")) { userCallback(); }
}
