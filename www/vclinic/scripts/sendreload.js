(function() {
	var SIGNAL_SERVER_LOCATION = "ws://"+window.location.hostname+":3434/";
	var serverConnection = new WebSocket(SIGNAL_SERVER_LOCATION);

	window.addEventListener("load", function() {
		if(document.getElementById("main-content")) {
			var caseId = Number(document.getElementById("main-content").getAttribute("data-case-id"));
			function sendMsg1() {
				if(serverConnection.readyState == 1) {
					parent.serverConnection.send(JSON.stringify({'forceReload': caseId}));
					var redirectUrl = document.getElementById("main-content").getAttribute("data-redirect");
					location.assign(redirectUrl);
				}
				else {
					setTimeout(sendMsg1, 5);
				}
			}
			sendMsg1();			
		}
		else if(document.getElementById("wrapper")) {
			var caseId = Number(document.getElementById("wrapper").getAttribute("data-case-id"));
			function sendMsg2() {
				if(serverConnection.readyState == 1) {
					serverConnection.send(JSON.stringify({'forceReload': caseId}));
					if(window.opener && !(window.opener.closed))
						window.close();
					else
						document.getElementById("success-message").textContent = "Uploaded Successfully.";
				}
				else {
					setTimeout(sendMsg2, 5);
				}
			}
			sendMsg2();
		}
	});
})();
