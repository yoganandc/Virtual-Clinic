(function() {
	var timer;

	window.addEventListener("load", function() {
		var message = document.getElementById("message");
		while(message.firstChild)
			message.removeChild(message.firstChild);
		message.appendChild(document.createTextNode("Checking your browser..."));

		if((navigator.cookieEnabled) && ('WebSocket' in window)) {
			var SIGNAL_SERVER_LOCATION = 'ws://'+window.location.hostname+":3434/";
			var connection = new WebSocket(SIGNAL_SERVER_LOCATION);
			function checkConnection() {
				if(connection.readyState == 1) {
					setCookie("browser_supported", "1");
					location.assign('index.php');
				}
				else {
					timer = setTimeout(checkConnection, 5);
				}
			}
			checkConnection();
			setTimeout(onFail, 2000);
		}
		else {
			while(message.firstChild)
				message.removeChild(message.firstChild);
			message.appendChild(document.createTextNode("Your browser is not supported."));
		}
	});

	function onFail() {
		clearInterval(timer);
		while(message.firstChild)
			message.removeChild(message.firstChild);
		message.appendChild(document.createTextNode("Could not connect to Node server."));
	}

	function setCookie(cname, cvalue) {
	    document.cookie = cname + "=" + cvalue + "; path=/";
	}
})();