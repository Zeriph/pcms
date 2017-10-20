function addEvents() { activateTree(document.getElementById("classtree")); }

// This function traverses the list and add links to nested list items
function activateTree(uls) {
    /*for (var i=0; i < uls.getElementsByTagName("ul").length; i++) {
        uls.getElementsByTagName("ul")[i].style.display="none";
    }*/
    // Add the click-event handler to the list items
    if (uls.addEventListener) {
        uls.addEventListener("click", toggleBranch, false);
    } else if (uls.attachEvent) { // For IE
        uls.attachEvent("onclick", toggleBranch);
    }
    // Make the nested items look like links
    addLinksToBranches(uls);
}

// This is the click-event handler
function toggleBranch(event) {
    var b, sb;
    if (event.target) {
        b = event.target;
    } else if (event.srcElement) { // For IE
        b = event.srcElement;
    }
    sb = b.getElementsByTagName("ul");
    if (sb.length > 0) {
        if (sb[0].style.display == "block") {
            sb[0].style.display = "none";
        } else {
            sb[0].style.display = "block";
        }
    }
}

// This function makes nested list items look like links
function addLinksToBranches(uls) {
    var lis = uls.getElementsByTagName("li");
    var i, n, sb;
    if (lis.length > 0) {
        for (i=0, n = lis.length; i < n; i++) {
            sb = lis[i].getElementsByTagName("ul");
            if (sb.length > 0) {
                addLinksToBranches(sb[0]);
                lis[i].className = "min";
            }
        }
    }
}