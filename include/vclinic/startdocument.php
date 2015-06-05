<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php if(isset($pagetitle)) echo $pagetitle.' - '; ?>Virtual Clinic</title>
	<?php if((isset($_SESSION['type'])) && ($_SESSION['type'] != VC_ADMINISTRATOR)) { ?> <script src="<?php echo VC_LOCATION.'scripts/checkframe.js'; ?>"></script><?php } ?>
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/site.css'; ?>">
	