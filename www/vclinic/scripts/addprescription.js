var TREATMENT_UNLISTED = 38;

var rows = null;
var finalRows = null;
var hidden = null;
var isPreviousValueUnlisted = [];

window.addEventListener("load", function() {
	if(document.getElementsByTagName("th")[1].getAttribute("data-hidden") == "1")
		hidden = true;
	else
		hidden = false;

	if(!hidden) {
		var tableRows = document.getElementsByTagName("table")[0].rows;
		for(var i=0; i<tableRows.length; i++) {
			tableRows[i].cells[1].style.display = "table-cell";
		}
	}

	rows = document.getElementById("num-rows").value;
	finalRows = rows;

	var offsetX = window.outerWidth - window.innerWidth;
	var offsetY = window.outerHeight - window.innerHeight;
	windowWidth = 833 + offsetX;
	windowHeight = 231 + (parseInt(rows, 10) * 31) + offsetY;
	window.resizeTo(windowWidth, windowHeight);
	
	document.getElementById("cancel-treatment").addEventListener("click", function(evt) { window.close(); evt.preventDefault(); });
	document.getElementById("add-link").addEventListener("click", function(evt) { addHandler(); evt.preventDefault(); });
	var removeLinks = document.getElementsByClassName("remove-link");

	var i = 0;
	if(rows == 1)
		i = 1;
	for(; i<removeLinks.length; i++) {
		removeLinks[i].addEventListener("click", removeHandler);
	}
	var selectElements = document.getElementsByClassName("treatment");
	for(var i=0; i<selectElements.length; i++) {
		if(selectElements[i].value == TREATMENT_UNLISTED)
			isPreviousValueUnlisted.push(true);
		else
			isPreviousValueUnlisted.push(false);
		selectElements[i].addEventListener("change", function(evt) { selectHandler(evt); });
	}
});

function addHandler() {
	if(finalRows == 1) {
		var firstRemoveLink = document.getElementsByTagName("table")[0].rows[1].cells[5].firstChild;
		firstRemoveLink.addEventListener("click", removeHandler);
	}
	rows++;
	finalRows++;
	document.getElementById("num-rows").value = finalRows;
	window.resizeBy(0, 31);
	var row = document.getElementsByTagName("table")[0].insertRow(finalRows);

	var selectEl = document.createElement("select");
	selectEl.setAttribute("id", "treatment-"+rows);
	selectEl.setAttribute("name", "treatment-"+rows);
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
	selectEl.addEventListener("change", function(evt) { selectHandler(evt); });
	var selectCell = row.insertCell(0);
	selectCell.appendChild(document.createTextNode(""));
	selectCell.appendChild(selectEl);

	var inputAltNameEl = document.createElement("input");
	inputAltNameEl.setAttribute("type", "text");
	inputAltNameEl.setAttribute("id", "altname-"+rows);
	inputAltNameEl.setAttribute("name", "altname-"+rows);
	inputAltNameEl.disabled = true;
	var altNameCell = row.insertCell(1);
	altNameCell.appendChild(inputAltNameEl);
	altNameCell.className = "hidden-col";
	if(!hidden)
		altNameCell.style.display = "table-cell";

	var dosageEl = document.createElement("input");
	dosageEl.setAttribute("type", "text");
	dosageEl.setAttribute("id", "dosage-"+rows);
	dosageEl.setAttribute("name", "dosage-"+rows);
	dosageEl.setAttribute("size", "5");
	dosageEl.setAttribute("maxlength", "5");
	row.insertCell(2).appendChild(dosageEl);

	var beforeEl1 = document.createElement("input");
	beforeEl1.setAttribute("type", "radio");
	beforeEl1.setAttribute("id", "before-"+rows);
	beforeEl1.setAttribute("name", "before-"+rows);
	beforeEl1.value = 1;
	var beforeEl2 = document.createElement("input");
	beforeEl2.setAttribute("type", "radio");
	beforeEl2.setAttribute("id", "after-"+rows);
	beforeEl2.setAttribute("name", "before-"+rows);
	beforeEl2.value = 0;
	var beforeCell = row.insertCell(3);
	beforeCell.appendChild(beforeEl1);
	beforeCell.appendChild(document.createTextNode("Before "));
	beforeCell.appendChild(beforeEl2);
	beforeCell.appendChild(document.createTextNode("After"));

	var durationEl = document.createElement("input");
	durationEl.setAttribute("type", "text");
	durationEl.setAttribute("id", "duration-"+rows);
	durationEl.setAttribute("name", "duration-"+rows);
	durationEl.setAttribute("size", "2");
	durationEl.setAttribute("maxlength", "2");
	var durationCell = row.insertCell(4);
	durationCell.appendChild(durationEl);
	durationCell.appendChild(document.createTextNode(" days"));

	var removeLink = document.createElement("a");
	removeLink.setAttribute("id", "remove-link-"+rows);
	removeLink.className = "remove-link";
	removeLink.setAttribute("href", "#");
	removeLink.setAttribute("title", "Remove this row");
	removeLink.appendChild(document.createTextNode("Remove"));
	removeLink.addEventListener("click", removeHandler);
	row.insertCell(5).appendChild(removeLink);

	isPreviousValueUnlisted.push(false);
}

function removeHandler(evtSrc) {
	evtSrc.preventDefault();
	var id = evtSrc.target.getAttribute("id");
	var tableRows = document.getElementsByTagName("table")[0].rows;
	var row;
	for(row=1; row<tableRows.length; row++) {
		if(tableRows[row].cells[5].firstChild.getAttribute("id") == id)
			break;
	}
	window.resizeBy(0, -31);
	document.getElementsByTagName("table")[0].deleteRow(row);
	finalRows--;
	document.getElementById("num-rows").value = finalRows;

	isPreviousValueUnlisted.splice(row, 1);
	if(finalRows == 1) {
		var firstRemoveLink = document.getElementsByTagName("table")[0].rows[1].cells[5].firstChild;
		firstRemoveLink.removeEventListener("click", removeHandler);
	}
}

function selectHandler(evtSrc) {
	var tableRows = document.getElementsByTagName("table")[0].rows;

	if((event.target.value == TREATMENT_UNLISTED) && hidden) {
		for(var i=0; i<tableRows.length; i++) {
			tableRows[i].cells[1].style.display = "table-cell";
		}
		hidden = false;
	}
	if((event.target.value != TREATMENT_UNLISTED) && !hidden) {
		var j = null;
		for(j=1; j<tableRows.length-1; j++) {
			if(tableRows[j].cells[0].firstChild.nextSibling.value == TREATMENT_UNLISTED)
				break;
		}
		if(j == tableRows.length-1) {
			for(j=0; j<tableRows.length-1; j++) {
				tableRows[j].cells[1].style.display = "none";
			}
			hidden = true;
		}
	}

	var id = evtSrc.target.getAttribute("id");
	var row;
	for(row=1; row<(tableRows.length-1); row++) {
		if(tableRows[row].cells[0].firstChild.nextSibling.getAttribute("id") == id) {
			if(isPreviousValueUnlisted[row]) {
				isPreviousValueUnlisted[row] = false;
				tableRows[row].cells[1].firstChild.disabled = true;
			}
			if(tableRows[row].cells[0].firstChild.nextSibling.value == TREATMENT_UNLISTED) {
				isPreviousValueUnlisted[row]= true;
				tableRows[row].cells[1].firstChild.disabled = false;
			}
		}
	}
}
