<?php
	require_once('../../../include/vclinic/ajaxsession.php');

	if(isset($_POST['message'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');
		$message = mysqli_real_escape_string($dbc, trim($_POST['message']));
		if(!empty($message)) {
			$query = "INSERT INTO vc_messages (assigneduser_id, user_id, message) VALUES (".$_SESSION['assigneduser_id'].", ".$_SESSION['user_id'].", '$message')";
			mysqli_query($dbc, $query);
		}	
		mysqli_close($dbc);	
	}
?>
