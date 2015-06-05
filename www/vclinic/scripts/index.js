var SIGNAL_SERVER_LOCATION = 'ws://'+window.location.hostname+":3434/";
var previousPage;
var edit_lock;

window.addEventListener("load", function() {
	previousPage = null;
	edit_lock = null;
	var elIframe = document.getElementById("vc-iframe");

	elIframe.addEventListener("load", function() {
		elIframe.style.height = (elIframe.contentWindow.document.body.offsetHeight + 30) + "px";

		if(elIframe.contentWindow.location.pathname == "/vclinic/editcase.php") {
			if(elIframe.contentWindow.document.getElementById("main-content").getAttribute("data-edit-lock")) {
				edit_lock = Number(elIframe.contentWindow.document.getElementById("main-content").getAttribute("data-edit-lock"));
				if(!edit_lock) {
					var caseId = elIframe.contentWindow.document.getElementById("main-content").getAttribute("data-case-id");
					serverConnection.send(JSON.stringify({'editCaseId': caseId}));
				}
			}
		}

		if((elIframe.contentWindow.location.pathname != "/vclinic/editcase.php") && (previousPage == "/vclinic/editcase.php") && (!edit_lock)) {
			serverConnection.send(JSON.stringify({'unlockCaseId': true}))
		}

		previousPage = elIframe.contentWindow.location.pathname;
	});
});