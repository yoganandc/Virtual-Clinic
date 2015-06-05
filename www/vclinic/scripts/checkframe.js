(function() {
	if((window == window.top) && (location.pathname != '/vclinic/') && (location.pathname != '/vclinic/index.php') && (location.pathname != '/vclinic/technician/addfile.php') && (location.pathname != '/vclinic/technician/addphoto.php') && (location.pathname != '/vclinic/technician/addtest.php') && (location.pathname != '/vclinic/addprescription.php')) {
		var form = document.createElement("form");
		form.setAttribute("method", "POST");
		form.setAttribute("action", "http://"+location.hostname+"/vclinic/index.php");
		var urlField = document.createElement("input");
		urlField.setAttribute("type", "hidden");
		urlField.setAttribute("name", "url");
		urlField.setAttribute("value", location.href);
		form.appendChild(urlField);
		window.addEventListener("DOMContentLoaded", function() {
			document.body.appendChild(form);
			form.submit();
		});
	}
})();