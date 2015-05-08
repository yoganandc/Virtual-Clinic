window.addEventListener("load", function() {
	document.getElementById("add-test").addEventListener("click", function(evt) { testHandler(); evt.preventDefault(); });
});

function testHandler() {
	window.open("technician/addtest.php", "test-window", "left=50, top=50, width=790, height=230");
}
