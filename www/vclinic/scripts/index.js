window.addEventListener("load", function() {
	document.getElementById("vc-iframe").addEventListener("load", function() {
		document.getElementById("vc-iframe").style.height = (document.getElementById("vc-iframe").contentWindow.document.body.scrollHeight + 30) + "px";
	});
});