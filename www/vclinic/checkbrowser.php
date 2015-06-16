<?php
	require_once('../../include/vclinic/appvars.php');
	require_once(VC_INCLUDE.'dbvars.php');

	$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	mysqli_close($dbc);
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Browser Check</title>
	<script src="scripts/checkbrowser.js"></script>
</head>
<body>
	<noscript>
		<p>Your web browser is not javascript-capable. To view this website, either turn on javascript or use another web browser.</p>
	</noscript>
	<p id="message"></p>
</body>
</html>
