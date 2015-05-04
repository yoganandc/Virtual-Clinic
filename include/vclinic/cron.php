<?php
	require_once('appvars.php');
	require_once('dbvars.php');

	define('VC_TIMEOUT', '20');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$query = "SELECT status_id, lastseen FROM vc_user_status WHERE status=1";
	$data = mysqli_query($dbc, $query);
	if(mysqli_num_rows($data) > 0) {
		while($row = mysqli_fetch_array($data)) {
			$current_time = time();
			if(($current_time - $row['lastseen']) > VC_TIMEOUT) {
				$query = "UPDATE vc_user_status SET status=0, room=NULL, lastseen=NULL WHERE status_id=".$row['status_id'];
				mysqli_query($dbc, $query);
			}
		}
	}
	mysqli_close($dbc);
?>
