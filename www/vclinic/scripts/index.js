window.addEventListener("load", function() {
	document.getElementById("vc-iframe").addEventListener("load", function() {
		document.getElementById("vc-iframe").style.height = (document.getElementById("vc-iframe").contentWindow.document.body.offsetHeight + 30) + "px";
	});
});