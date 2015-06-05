(function() {
	window.addEventListener("load", function() {
		var caseId = Number(document.getElementById("main-content").getAttribute("data-case-id"));
		parent.serverConnection.send(JSON.stringify({'forceReload': caseId}));
		var redirectUrl = document.getElementById("main-content").getAttribute("data-redirect");
		location.assign(redirectUrl);
	});
})();
