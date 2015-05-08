<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $pagetitle; ?> - Virtual Clinic</title>
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/site.css'; ?>">
	<?php if(isset($_SESSION['type']) && $_SESSION['type'] != VC_ADMINISTRATOR && (!isset($nochat))) { ?>
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/chat.css'; ?>">
	<script src="<?php echo VC_LOCATION.'scripts/simplewebrtc.bundle.js'; ?>"></script>
	<script src="<?php echo VC_LOCATION.'scripts/chat.js'; ?>"></script> 
	<?php } ?>
	