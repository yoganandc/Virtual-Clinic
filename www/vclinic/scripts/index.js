var previousPage;
var prevCase;
var edit_lock;
var elIframe;

if(typeof chatIncluded === 'undefined') {
	var userSet = true;
	var serverConnection = null;
	var SIGNAL_SERVER_LOCATION = 'ws://'+window.location.hostname+":3434/";
	serverConnection = new WebSocket(SIGNAL_SERVER_LOCATION);

	serverConnection.onmessage = function(message) {
		var signal = JSON.parse(message.data);
		if(signal.reload)
			elIframe.contentWindow.location.reload();
	}
}

function checkSize() {
	if(elIframe.contentWindow.document.body !== null) {
		if(elIframe.style.height != (elIframe.contentWindow.document.body.offsetHeight + 30))
			elIframe.style.height = (elIframe.contentWindow.document.body.offsetHeight + 30) + "px";
	}
	setTimeout(checkSize, 200);
}

window.addEventListener("load", function() {
	previousPage = null;
	edit_lock = null;
	elIframe = document.getElementById("vc-iframe");
	checkSize();
	iframeLoad();

	elIframe.addEventListener("load", function() {
		iframeLoad();
	});
});

function iframeLoad() {
	
	//send LOCKCASEID
	if(elIframe.contentWindow.location.pathname == "/vclinic/editcase.php") {
		if(elIframe.contentWindow.document.getElementById("main-content").getAttribute("data-edit-lock")) {
			edit_lock = Number(elIframe.contentWindow.document.getElementById("main-content").getAttribute("data-edit-lock"));
			if(!edit_lock) {
				var caseId = elIframe.contentWindow.document.getElementById("main-content").getAttribute("data-case-id");
				function sendLock() {
					if(serverConnection.readyState == 1) {
						serverConnection.send(JSON.stringify({'lockCaseId': caseId}));
					}
					else {
						setTimeout(sendLock, 5);
					}
				}
				sendLock();
			}
		}
	}

	//send UNLOCKCASEID
	if((elIframe.contentWindow.location.pathname != "/vclinic/editcase.php") && (previousPage == "/vclinic/editcase.php") && (!edit_lock)) {
		serverConnection.send(JSON.stringify({'unlockCaseId': true}))
	}

	//send REGISTERINDEX
	if((elIframe.contentWindow.location.pathname == "/vclinic/technician/") || (elIframe.contentWindow.location.pathname == "/vclinic/technician/index.php") || (elIframe.contentWindow.location.pathname == "/vclinic/doctor/") || (elIframe.contentWindow.location.pathname == "/vclinic/doctor/index.php")) {
		if((previousPage != "/vclinic/technician/") && (previousPage != "/vclinic/technician/index.php") && (previousPage != "/vclinic/doctor/") && (previousPage != "/vclinic/doctor/index.php")) {
			var indexId = elIframe.contentWindow.document.getElementById("main-content").getAttribute("data-user");
			function sendIndex() {
				if((serverConnection.readyState == 1) && (userSet)) {
					serverConnection.send(JSON.stringify({'registerIndex': indexId}));
				}
				else {
					setTimeout(sendIndex, 5);
				}
			}
			sendIndex();
		}
	}

	//send UNREGISTERINDEX
	if((elIframe.contentWindow.location.pathname != "/vclinic/technician/") && (elIframe.contentWindow.location.pathname != "/vclinic/technician/index.php") && (elIframe.contentWindow.location.pathname != "/vclinic/doctor/") && (elIframe.contentWindow.location.pathname != "/vclinic/doctor/index.php")) {
		if((previousPage == "/vclinic/technician/") || (previousPage == "/vclinic/technician/index.php") || (previousPage == "/vclinic/doctor/") || (previousPage == "/vclinic/doctor/index.php")) {
			serverConnection.send(JSON.stringify({'unregisterIndex': true}));
		}
	}

	//send REGISTERCASEID
	if((elIframe.contentWindow.location.pathname == "/vclinic/patient.php") || (elIframe.contentWindow.location.pathname == "/vclinic/case.php")) {
		if((previousPage != "/vclinic/patient.php") && (previousPage != "/vclinic/case.php")) {
			var caseId = elIframe.contentWindow.document.getElementById("case").getAttribute("data-case-id");
			var caseUser = elIframe.contentWindow.document.getElementById("case").getAttribute("data-case-user");
			prevCase = caseId;
			function sendCase() {
				if(serverConnection.readyState == 1) {
					serverConnection.send(JSON.stringify({'registerCaseId': caseId, 'registerCaseUser': caseUser}));
				}
				else {
					setTimeout(sendCase, 5);
				}
			}
			sendCase();
		}
	}

	//send UNREGISTER (old) & REGISTER (new)
	if((elIframe.contentWindow.location.pathname == "/vclinic/patient.php") || (elIframe.contentWindow.location.pathname == "/vclinic/case.php")) {
		if((previousPage == "/vclinic/patient.php") || (previousPage == "/vclinic/case.php")) {
			var caseId = elIframe.contentWindow.document.getElementById("case").getAttribute("data-case-id");
			var caseUser = elIframe.contentWindow.document.getElementById("case").getAttribute("data-case-user");
			if(caseId != prevCase) {
				prevCase = caseId;
				serverConnection.send(JSON.stringify({'unregisterCaseId': true}));
				function sendRegister() {
					serverConnection.send(JSON.stringify({'registerCaseId': caseId, 'registerCaseUser': caseUser}));
				}
				setTimeout(sendRegister, 100);
			}
		}
	}

	//send UNREGISTERCASEID
	if((elIframe.contentWindow.location.pathname != "/vclinic/patient.php") && (elIframe.contentWindow.location.pathname != "/vclinic/case.php")) {
		if((previousPage == "/vclinic/patient.php") || (previousPage == "/vclinic/case.php")) {
			serverConnection.send(JSON.stringify({'unregisterCaseId': true}));
		}
	}

	previousPage = elIframe.contentWindow.location.pathname;
}
