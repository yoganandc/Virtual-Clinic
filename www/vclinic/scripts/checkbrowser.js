(function() {
	window.addEventListener("load", function() {
		var message = document.getElementById("message");
		while(message.firstChild)
			message.removeChild(message.firstChild);
		message.appendChild(document.createTextNode("Checking your browser..."));

		if((navigator.cookieEnabled) && ('WebSocket' in window)) {
			setCookie("browser_supported", "1");
			location.assign('index.php');
		}
		else {
			while(message.firstChild)
				message.removeChild(message.firstChild);
			message.appendChild(document.createTextNode("Your browser is not supported."));
		}
	});

	function setCookie(cname, cvalue) {
	    document.cookie = cname + "=" + cvalue + "; path=/";
	}
})();