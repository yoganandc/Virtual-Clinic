<?php
	require_once('../../include/vclinic/usersession.php');

	if(!isset($_GET['patient_id'])) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$patient_id = $_GET['patient_id'];

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$query = "SELECT vp.fname, vp.lname FROM vc_patient AS vp LEFT JOIN vc_address AS va USING (address_id) LEFT JOIN vc_address_state AS vas USING (state_id) WHERE vp.patient_id=".$_GET['patient_id'];
	$data = mysqli_query($dbc, $query);

	if(mysqli_num_rows($data) != 1) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$row = mysqli_fetch_array($data);
	$name = $row['fname'].' '.$row['lname'];
	$pagetitle = 'Patient Profile';
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="stylesheets/user.css">
<link rel="stylesheet" href="stylesheets/patient-sidebar.css">
<link rel="stylesheet" href="stylesheets/patient.css">

<?php require_once(VC_INCLUDE.'header.php'); ?>

<?php require_once(VC_INCLUDE.'chat.php'); ?>

<div id="banner">
	<h2><?php echo $name; ?></h2>
	<?php if($_SESSION['type'] == VC_TECHNICIAN) echo '<p><a title="Start New Case" href="'.VC_LOCATION.'technician/addcase.php?patient_id='.$_GET['patient_id'].'">Start New Case</a></p>'; ?>
</div>

<div id="main-content">
	<?php require_once(VC_INCLUDE.'patient-sidebar.php'); ?>
	<div id="content">
	</div>
</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
