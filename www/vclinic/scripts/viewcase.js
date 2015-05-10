var testWindow =null;
var case_id = null;

window.addEventListener("load", function() {
	case_id = parseInt(document.getElementsByClassName("case")[0].getAttribute("data-case_id"));
	document.getElementById("add-test").addEventListener("click", function(evt) { testHandler(); evt.preventDefault(); });
});

function testHandler() {
	if(testWindow == null || testWindow.closed)
		testWindow = window.open("technician/addtest.php?case_id="+case_id, "test-window", "left=50, top=50, width=790, height=262");
	else
		testWindow.focus();
}
