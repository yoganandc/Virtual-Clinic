var rows = null;

window.addEventListener("load", function() {
	rows = document.getElementById("num-rows").value;
	document.getElementById("cancel-test").addEventListener("click", function(evt) { window.close(); evt.preventDefault(); });
	document.getElementById("add-link").addEventListener("click", function(evt) { addHandler(); evt.preventDefault(); });
});

function addHandler() {
	var row = document.getElementsByTagName("table")[0].insertRow(++rows);
	document.getElementById("num-rows").value = rows;

	var selectEl = document.createElement("select");
	selectEl.setAttribute("id", "test-"+rows);
	selectEl.setAttribute("name", "test-"+rows);
	var selectOptions = document.getElementsByTagName("select")[0].options;
	var value = null;
	var optionText = null;
	for(var i=0; i<selectOptions.length; i++) {
		value = selectOptions[i].value;
		optionText = selectOptions[i].firstChild.nodeValue;
		var option = document.createElement("option");
		option.value = value;
		option.appendChild(document.createTextNode(optionText));
		selectEl.appendChild(option);
	}
	row.insertCell(0).appendChild(selectEl);

	var inputAltNameEl = document.createElement("input");
	inputAltNameEl.setAttribute("type", "text");
	inputAltNameEl.setAttribute("id", "altname-"+rows);
	inputAltNameEl.setAttribute("name", "altname-"+rows);
	inputAltNameEl.disabled = true;
	var altNameCell = row.insertCell(1);
	altNameCell.appendChild(inputAltNameEl);
	altNameCell.className = "hidden-col";

	var resultEl = document.createElement("input");
	resultEl.setAttribute("type", "text");
	resultEl.setAttribute("id", "result-"+rows);
	resultEl.setAttribute("name", "result-"+rows);
	row.insertCell(2).appendChild(resultEl);

	var fileEl = document.createElement("input");
	fileEl.setAttribute("type", "file");
	fileEl.setAttribute("id", "file-"+rows);
	fileEl.setAttribute("class", "file");
	fileEl.setAttribute("name", "file-"+rows);
	row.insertCell(3).appendChild(fileEl);

	var addLink = document.getElementById("add-link");
	var parentLink = addLink.parentNode;
	parentLink.removeChild(addLink);
	var removeLink = document.createElement("a");
	removeLink.setAttribute("id", "remove-link-"+rows);
	removeLink.setAttribute("href", "#");
	removeLink.setAttribute("title", "Remove this test");
	removeLink.appendChild(document.createTextNode("Remove"));
	removeLink.addEventListener("click", function(evt) { removeHandler(evt); evt.preventDefault(); });
	parentLink.appendChild(removeLink);

	var newAddLink = document.createElement("a");
	newAddLink.setAttribute("id", "add-link");
	newAddLink.setAttribute("href", "#");
	newAddLink.setAttribute("title", "Add another test");
	newAddLink.appendChild(document.createTextNode("Add"));
	newAddLink.addEventListener("click", function(evt) { addHandler(); evt.preventDefault(); });
	row.insertCell(4).appendChild(newAddLink);
}

function removeHandler(evtSrc) {
	var id = evtSrc.target.getAttribute("id");
	var tableRows = document.getElementsByTagName("table")[0].rows;
	var row;
	for(row=1; row<tableRows.length; row++) {
		if(tableRows[row].cells[4].firstChild.getAttribute("id") == id)
			break;
	}
	document.getElementsByTagName("table")[0].deleteRow(row);
	document.getElementById("num-rows").value = --rows;
}
