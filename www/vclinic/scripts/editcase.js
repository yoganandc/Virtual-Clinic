(function() {
	var COMPLAINT_UNLISTED = 8;
	var previousComplaint = null;

	window.addEventListener("load", readyForm);

	function readyForm() {
		if(Number(document.getElementById("main-content").getAttribute("data-edit-lock"))) 
			return;
		
		if(document.getElementById("complaint").value == COMPLAINT_UNLISTED) {
			addAlternateRow();
			document.getElementById("alternate").value = document.getElementById("altname").value;
		}
		if(document.getElementById("chronic").checked)
			radioHandler(1);

		previousComplaint = document.getElementById("complaint").value;

		document.getElementById("acute").addEventListener("click", function() { radioHandler(0); });
		document.getElementById("chronic").addEventListener("click", function() { radioHandler(1); });
		document.getElementById("complaint").addEventListener("change", listHandler);
	}

	function radioHandler(type) {
		if(type == 0) {
			var name = "option-"+document.getElementById("complaint").value;
			var optionSelected = document.getElementById(name);
			if(optionSelected.getAttribute("data-chronic_only") == 1)
				document.getElementById("complaint").value = 1;
		}
		var complaintList = document.getElementById("complaint").options;
		var chronicOnly;
		for(var i=0; i<complaintList.length; i++) {
			chronicOnly = complaintList[i].getAttribute("data-chronic_only");
			if(chronicOnly == "1") {
				if(type)
					complaintList[i].disabled = false;
				else 
					complaintList[i].disabled = true;
			}
		}	
	}

	function addAlternateRow() {
		var table = document.getElementById("case");
		var row = document.createElement("tr");
		row.setAttribute("id", "alternate-row");
		var complaintList = document.getElementById("complaint-row");
		complaintList.parentNode.insertBefore(row, complaintList.nextSibling);
		var label = document.createElement("label");
		label.setAttribute("for", "alternate");
		label.appendChild(document.createTextNode("Complaint Name: "));
		var heading = document.createElement("th");
		heading.appendChild(label);
		row.appendChild(heading);		
		var input = document.createElement("input");
		input.setAttribute("type", "text");
		input.setAttribute("id", "alternate");
		input.setAttribute("name", "alternate");
		row.insertCell(1).appendChild(input);
	}

	function listHandler() {
		var value = document.getElementById("complaint").value;
		if(previousComplaint == COMPLAINT_UNLISTED) {
			document.getElementById("case").deleteRow(2);
			previousComplaint = document.getElementById("complaint").value;
		}
		if(value == COMPLAINT_UNLISTED) {
			addAlternateRow();
			previousComplaint = COMPLAINT_UNLISTED;
		}
	}
})();
