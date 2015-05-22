<?php 
	require_once('../../../include/vclinic/techniciansession.php');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

	$nochat= true;
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/site.css'; ?>">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/index1.css'; ?>">
</head>
<body>
	<div id="banner">
		<p><a title="Add New Patient" href="addpatient.php">Add New Patient</a></p>
		<h2><?php echo "Home"; ?></h2>
	</div>

	<div id="main-content">
		
	</div>
</body>
</html>
