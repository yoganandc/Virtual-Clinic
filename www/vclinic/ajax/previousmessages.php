<?php 
	require_once('../../../include/vclinic/ajaxsession.php');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$query = "SELECT q.message_id, q.message, q.user_id FROM (SELECT message_id, message, user_id FROM vc_messages WHERE assigneduser_id=".$_SESSION['user_id']." OR user_id=".$_SESSION['user_id']." ORDER BY message_id LIMIT 100) q ORDER BY q.message_id";
	$data = mysqli_query($dbc, $query);
	$json_string = '{"success":"';
	$messages_array = array();
	if(mysqli_num_rows($data) > 0) {
		$json_string .= '1","messages":[';
		while($row = mysqli_fetch_array($data)) {
			$message = '{"from":"';
			if($row['user_id'] == $_SESSION['user_id'])
				$message .= '1",';
			else {
				$message .= '0",';
			}
			$message .= '"message":"'.$row['message'].'"}';
			array_push($messages_array, $message);
		}
		$messages = implode($messages_array, ',');
		$json_string .= $messages."]}";
	}
	else {
		$json_string .= '0"}';
	}
	mysqli_close($dbc);
	echo $json_string;
?>
