<?php

define('VC_PATTERN_EMAIL', '/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@/');
define('VC_EMAIL_LENGTH', '40');
define('VC_ROOM_PHRASELENGTH', '40');
define('VC_NUM_ALLOWEDCHARS', '36');

function update_status($dbc) {
    $current_time = time();
    $query = "UPDATE vc_user_status SET status=1, lastseen='$current_time' WHERE status_id=".$_SESSION['user_id'];
    if(mysqli_query($dbc, $query)) {
        $query = "SELECT assigneduser_id FROM vc_user WHERE user_id=".$_SESSION['user_id'];
        $data = mysqli_query($dbc, $query);
        if(mysqli_num_rows($data) == 1) {
            $row = mysqli_fetch_array($data);
            if(!empty($row['assigneduser_id'])) {
                $query = "SELECT status, room FROM vc_user_status WHERE status_id=".$row['assigneduser_id'];
                $data = mysqli_query($dbc, $query);
                if(mysqli_num_rows($data) == 1) {
                    $row = mysqli_fetch_array($data);
                    if($row['status']) {
                        $query = "UPDATE vc_user_status SET room='".$row['room']."' WHERE status_id=".$_SESSION['user_id'];
                        if(mysqli_query($dbc, $query)) {
                            return $row['room'];
                        }
                        else {
                            echo '<p class="error">Some error occured.</p>';
                            exit();
                        }
                    }
                    else {
                        $allowed_chars = "abcdefghijklmnopqrstuvwxyz1234567890";
                        $roomphrase = "";
                        for($i = 0; $i < VC_ROOM_PHRASELENGTH; $i++) {
                            $x = rand(0, (VC_NUM_ALLOWEDCHARS - 1));
                            $roomphrase .= $allowed_chars[$x];
                        }
                        $query = "UPDATE vc_user_status SET room='".$roomphrase."' WHERE status_id=".$_SESSION['user_id'];
                        if(mysqli_query($dbc, $query)) {
                            return $roomphrase;
                        }
                        else {
                            echo '<p class="error">Some error occured.</p>';
                            exit();
                        }
                    }
                }
                else {
                    echo '<p class="error">Some error occured.</p>';
                    exit();
                }
            }
            else {
                return null;
            }
        }
        else {
            echo '<p class="error">Some error occured.</p>';
            exit();
        }
    }
    else {
        echo '<p class="error">Some error occured.</p>';
        exit();
    }
}

function win_checkdnsrr($domain, $rectype='') {
	if(!empty($domain)) {
		if($rectype=='')
			$rectype = 'MX';
		exec("nslookup -type=$rectype $domain", $output);
		foreach($output as $line) {
			if(preg_match("/^$domain/", $line))
				return true;
		}
		return false;
	}
	return false;
}
function check_email($email) {
	if(empty($email))
		return true;
	if(!preg_match(VC_PATTERN_EMAIL, $email))
		return false;
	$domain = preg_replace(VC_PATTERN_EMAIL, '', $email);
	if(!win_checkdnsrr($domain))
		return false;
	if(!(strlen($email) <= VC_EMAIL_LENGTH))
		return false;
	return true;
}
function set_status_offline($dbc, $id) {
	$query = "UPDATE vc_user_status SET status=0, room=NULL, lastseen=NULL WHERE status_id=".$id;
	mysqli_query($dbc, $query);
}
?>
