<?php 
	require_once('../../../include/vclinic/techniciansession.php');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

	$pagetitle = 'Home';
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user.css'; ?>">

<?php require_once('../'.VC_INCLUDE.'header.php'); ?>

<div id="banner">
	<h2><?php echo $pagetitle; ?></h2>
	<p><a title="Add New Patient" href="addpatient.php">Add New Patient</a></p>
</div>

<?php require_once('../'.VC_INCLUDE.'chat.php'); ?>
<div id="main-content">
	
</div>

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
