window.addEventListener("load", function() {
	if(window.opener && !(window.opener.closed)) {
		window.opener.location.reload(true);
		window.close();
	}
});