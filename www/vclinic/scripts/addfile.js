var rows = null;
var finalRows = null;

window.addEventListener("load", function() {
	rows = document.getElementById("num-rows").value;
	finalRows = rows;

	var offsetX = window.outerWidth - window.innerWidth;
	var offsetY = window.outerHeight - window.innerHeight;
	windowWidth = 680 + offsetX;
	windowHeight = 231 + (parseInt(rows, 10) * 31) + offsetY;
	window.resizeTo(windowWidth, windowHeight);

	document.getElementById("cancel-test").addEventListener("click", function(evt) { window.close(); evt.preventDefault(); });
	document.getElementById("add-link").addEventListener("click", function(evt) { addHandler(); evt.preventDefault(); });

	var removeLinks = document.getElementsByClassName("remove-link");
	var i = 0;
	if(rows == 1)
		i = 1;
	for(; i<removeLinks.length; i++) {
		removeLinks[i].addEventListener("click", removeHandler);
	}
});

function addHandler() {
	if(finalRows == 1) {
		var firstRemoveLink = document.getElementsByTagName("table")[0].rows[1].cells[2].firstChild;
		firstRemoveLink.addEventListener("click", removeHandler);
	}
	rows++;
	finalRows++;
	document.getElementById("num-rows").value = finalRows;
	window.resizeBy(0, 31);
	var row = document.getElementsByTagName("table")[0].insertRow(finalRows);

	var titleEl = document.createElement("input");
	titleEl.setAttribute("type", "text");
	titleEl.setAttribute("id", "title-"+rows);
	titleEl.setAttribute("name", "title-"+rows);
	row.insertCell(0).appendChild(titleEl);

	var fileEl = document.createElement("input");
	fileEl.setAttribute("type", "file");
	fileEl.setAttribute("id", "file-"+rows);
	fileEl.setAttribute("class", "file");
	fileEl.setAttribute("name", "file-"+rows);
	row.insertCell(1).appendChild(fileEl);

	var removeLink = document.createElement("a");
	removeLink.setAttribute("id", "remove-link-"+rows);
	removeLink.className = "remove-link";
	removeLink.setAttribute("href", "#");
	removeLink.setAttribute("title", "Remove this file");
	removeLink.appendChild(document.createTextNode("Remove"));
	removeLink.addEventListener("click", removeHandler);
	row.insertCell(2).appendChild(removeLink);
}

function removeHandler(evtSrc) {
	evtSrc.preventDefault();
	var id = evtSrc.target.getAttribute("id");
	var tableRows = document.getElementsByTagName("table")[0].rows;
	var row;
	for(row=1; row<tableRows.length; row++) {
		if(tableRows[row].cells[2].firstChild.getAttribute("id") == id)
			break;
	}
	window.resizeBy(0, -31);
	document.getElementsByTagName("table")[0].deleteRow(row);
	finalRows--;
	document.getElementById("num-rows").value = finalRows;
	if(finalRows == 1) {
		var firstRemoveLink = document.getElementsByTagName("table")[0].rows[1].cells[2].firstChild;
		firstRemoveLink.removeEventListener("click", removeHandler);
	}
}
