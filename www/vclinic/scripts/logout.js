(function() {
	window.addEventListener("load", function() {
		if(document.getElementById("wrapper").getAttribute("data-user") !== null) {
			var serverConnection = new WebSocket('ws://'+window.location.hostname+":3434/");
			var userId = document.getElementById("wrapper").getAttribute("data-user");
			var assignedId = document.getElementById("wrapper").getAttribute("data-assigned");

			function sendMsg() {
				if(serverConnection.readyState == 1) {
					serverConnection.send(JSON.stringify({'logout': userId, 'logoutAssigned': assignedId}));
				}
				else {
					setTimeout(sendMsg, 5);
				}
			}
			sendMsg();
		}
	});
})();
