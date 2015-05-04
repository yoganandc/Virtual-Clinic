<?php 
	require_once('startsession.php');

  	if($_SESSION['type'] != VC_TECHNICIAN) {
  		$url = VC_LOCATION;
    	header('Location: '.$url);
    	exit();
  	}
?>
