<?php
	require_once('../../include/vclinic/usersession.php');

	if(!isset($_GET['case_id'])) {
		header('Location: '.VC_LOCATION);
		exit();
	}
	if(!isset($_GET['patient_id'])) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$case_id = mysqli_real_escape_string($dbc, trim($_GET['case_id']));
	$patient_id = mysqli_real_escape_string($dbc, trim($_GET['patient_id']));

	$query = "SELECT patient_id FROM vc_case WHERE case_id=".$case_id;
	$data = mysqli_query($dbc, $query);
	if(mysqli_num_rows($data) != 1) {
		echo '<p>Some error occured.</p>';
		exit();
	}

	$row = mysqli_fetch_array($data);

	if($patient_id != $row['patient_id']) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$query = "SELECT vp.fname, vp.lname FROM vc_patient AS vp WHERE vp.patient_id=".$patient_id;
	$data = mysqli_query($dbc, $query);

	if(mysqli_num_rows($data) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}

	$row = mysqli_fetch_array($data);
	$name = $row['fname'].' '.$row['lname'].' - View Case';
	$pagetitle = 'View Case';
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="stylesheets/user.css">
<link rel="stylesheet" href="stylesheets/patient-sidebar.css">
<link rel="stylesheet" href="stylesheets/viewcase.css">
<link rel="stylesheet" href="stylesheets/case.css">
<script src="scripts/viewcase.js"></script>
<link href="stylesheets/lightbox.css" rel="stylesheet">
<script src="scripts/jquery-1.11.0.min.js"></script>
<script src="scripts/lightbox.min.js"></script>

<?php require_once(VC_INCLUDE.'header.php'); ?>

<?php require_once(VC_INCLUDE.'chat.php'); ?>

<div id="banner">
	<h2><?php echo $name; ?></h2>
	<p><a title="Back to patient overview" href="patient.php?patient_id=<?php echo $patient_id; ?>">Back</a></p>
</div>

<div id="main-content">
	<?php require_once(VC_INCLUDE.'patient-sidebar.php'); ?>
	<div id="content">
		<?php require_once(VC_INCLUDE.'viewcase.php'); ?>
	</div>
</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>