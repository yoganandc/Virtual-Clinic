var testWindow =null;
var treatmentWindow = null;
var fileWindow = null;
var photoWindow = null;
var case_id = null;

window.addEventListener("load", function() {
	case_id = parseInt(document.getElementById("case").getAttribute("data-case-id"));

	if(document.getElementById("add-test"))
		document.getElementById("add-test").addEventListener("click", function(evt) { testHandler(); evt.preventDefault(); });
	if(document.getElementById("add-file"))
		document.getElementById("add-file").addEventListener("click", function(evt) { fileHandler(); evt.preventDefault(); });
	if(document.getElementById("add-photo"))
		document.getElementById("add-photo").addEventListener("click", function(evt) { photoHandler(); evt.preventDefault(); });
	if(document.getElementById("forward-link"))
		document.getElementById("forward-link").addEventListener("click", forwardHandler);

	document.getElementById("add-treatment").addEventListener("click", function(evt) { treatmentHandler(); evt.preventDefault(); });

	var testResultPictures = document.getElementsByClassName("fancybox");
	for(var i = 0; i < testResultPictures.length; i++) {
		testResultPictures[i].addEventListener("click", function(evt) {
			evt.preventDefault();
			var targetImage = evt.target.getAttribute("href");
			parent.$.fancybox([{href: targetImage, title: ""}]);
		});
	}
});

function testHandler() {
	if(testWindow == null || testWindow.closed)
		testWindow = window.open("technician/addtest.php?case_id="+case_id, "test-window", "left=50, top=50, width=790, height=262");
	else
		testWindow.focus();
}

function treatmentHandler() {
	if(treatmentWindow == null || treatmentWindow.closed)
		treatmentWindow = window.open("addprescription.php?case_id="+case_id, "treatment-window", "left=50, top=50, width=833, height=262");
	else
		treatmentWindow.focus();
}

function fileHandler() {
	if(fileWindow == null || fileWindow.closed)
		fileWindow = window.open("technician/addfile.php?case_id="+case_id, "file-window", "left=50, top=50, width=680, height=262");
	else
		fileWindow.focus();
}

function photoHandler() {
	if(photoWindow == null || photoWindow.closed)
		photoWindow = window.open("technician/addphoto.php?case_id="+case_id, "photo-window", "left=5, top=5, width=1339, height=650");
	else
		photoWindow.focus();
}

function forwardHandler(evt) {
	evt.preventDefault();

	if(evt.target.getAttribute("data-status") !== null) {
		if(evt.target.getAttribute("data-status") == "0") {
			var userId = evt.target.getAttribute("data-user-id");
			var assignedId = evt.target.getAttribute("data-assigned");
			var forwardId = evt.target.getAttribute("data-forward-id");
			evt.target.removeAttribute("href");
			while(evt.target.firstChild)
				evt.target.removeChild(evt.target.firstChild);
			evt.target.appendChild(document.createTextNode("Updating..."));
			function sendReturnCase() {
				if(parent.serverConnection.readyState == 1) {
					parent.serverConnection.send(JSON.stringify({'returnCase': case_id, 'returnCaseId': forwardId, 'returnUser': userId, 'returnAssigned': assignedId}));
				}
				else {
					setTimeout(sendReturnCase, 5);
				}
			}
			sendReturnCase();
		}
		else {
			var userId = evt.target.getAttribute("data-user-id");
			var forwardId = evt.target.getAttribute("data-forward-id");
			evt.target.removeAttribute("href");
			while(evt.target.firstChild)
				evt.target.removeChild(evt.target.firstChild);
			evt.target.appendChild(document.createTextNode("..."));
			function sendDismissCase() {
				if(parent.serverConnection.readyState == 1) {
					parent.serverConnection.send(JSON.stringify({'dismissCase': case_id, 'dismissCaseId': forwardId, 'dismissUser': userId}));
				}
				else {
					setTimeout(sendDismissCase, 5);
				}
			}
			sendDismissCase();
		}
	}
	else {
		var userId = evt.target.getAttribute("data-user-id");
		var assignedId = evt.target.getAttribute("data-assigned");
		evt.target.removeAttribute("href");
		while(evt.target.firstChild)
			evt.target.removeChild(evt.target.firstChild);
		evt.target.appendChild(document.createTextNode("Forwarding..."));
		function sendForwardCase() {
			if(parent.serverConnection.readyState == 1) {
				parent.serverConnection.send(JSON.stringify({'forwardCase': case_id, 'forwardUser': userId, 'forwardAssigned': assignedId}));
			}
			else {
				setTimeout(sendForwardCase, 5);
			}
		}
		sendForwardCase();
	}
}
