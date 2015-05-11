<?php
	require_once('../../include/vclinic/usersession.php');

	if(!isset($_GET['patient_id'])) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$patient_id = mysqli_real_escape_string($dbc, trim($_GET['patient_id']));
	$query = "SELECT vp.fname, vp.lname FROM vc_patient AS vp WHERE vp.patient_id=".$patient_id;
	$data = mysqli_query($dbc, $query);
	if(mysqli_num_rows($data) != 1) {
		echo '<p class="error">Some error occured here.</p>';
		exit();
	}
	$row = mysqli_fetch_array($data);
	$name = $row['fname'].' '.$row['lname'];

	$query = "SELECT case_id FROM vc_case WHERE patient_id=".$patient_id." ORDER BY case_id DESC LIMIT 1";
	$data = mysqli_query($dbc, $query);
	if(mysqli_num_rows($data) == 1) {
		$nocase =  false;
		$row = mysqli_fetch_array($data);
		$case_id = $row['case_id'];

		$query = "SELECT COUNT(*) AS count FROM vc_case WHERE patient_id=".$patient_id;
		$data = mysqli_query($dbc, $query);
		if(mysqli_num_rows($data) != 1) {
			echo '<p class="error">Some error occured here.</p>';
			exit();
		}
		$row = mysqli_fetch_array($data);
		$case_no = $row['count'];
	}
	else {
		$nocase = true;
	}
	

	$pagetitle = 'Patient Profile';
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="stylesheets/user.css">
<link rel="stylesheet" href="stylesheets/patient-sidebar.css">
<link rel="stylesheet" href="stylesheets/patient.css">
<?php if(!$nocase) { ?>
<link rel="stylesheet" href="stylesheets/viewcase.css">
<script src="scripts/viewcase.js"></script>
<link href="stylesheets/lightbox.css" rel="stylesheet">
<script src="scripts/jquery-1.11.0.min.js"></script>
<script src="scripts/lightbox.min.js"></script>
<?php } ?>

<?php require_once(VC_INCLUDE.'header.php'); ?>

<?php require_once(VC_INCLUDE.'chat.php'); ?>

<div id="banner">
	<h2><?php echo $name; ?></h2>
	<?php if($_SESSION['type'] == VC_TECHNICIAN) echo '<p><a title="Start New Case" href="'.VC_LOCATION.'technician/addcase.php?patient_id='.$_GET['patient_id'].'">Start New Case</a></p>'; ?>
</div>

<div id="main-content">
	<?php require_once(VC_INCLUDE.'patient-sidebar.php'); ?>
	<div id="content">
		<?php if(!$nocase) require_once(VC_INCLUDE.'viewcase.php'); else echo '<div id="nocase">Click on <span class="username">Start New Case</span> to get started.</div>'; ?>
	</div>
</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
