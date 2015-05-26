<?php 
	require_once('../../../include/vclinic/techniciansession.php');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php');?>

	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user.css'; ?>">
</head>
<body>
	<div id="banner">
		<p><a title="Add New Patient" href="addpatient.php">Add New Patient</a></p>
		<h2><?php echo "Home"; ?></h2>
	</div>

	<div id="main-content">

	</div>
		
<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
