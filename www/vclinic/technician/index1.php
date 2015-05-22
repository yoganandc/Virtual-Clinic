<?php 
	require_once('../../../include/vclinic/techniciansession.php');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

	$nochat= true;
	$pagetitle = 'Home';
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

<script src="<?php echo VC_LOCATION.'scripts/simplewebrtc.bundle.js'; ?>"></script>
<script src="<?php echo VC_LOCATION.'scripts/chat.js'; ?>"></script> 
<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/index1.css'; ?>">

<?php require_once('../'.VC_INCLUDE.'header1.php'); ?>

<iframe id="vc-iframe" src="thome.php"></iframe>

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>