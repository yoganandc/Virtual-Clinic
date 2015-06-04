<?php 
    session_start();

    require_once('appvars.php');
    require_once('dbvars.php');
    require_once('library.php');

    function update_status($dbc) {
        $current_time = time();
        $query = "UPDATE vc_user_status SET status=1 WHERE status_id=".$_SESSION['user_id'];
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

    if (!isset($_SESSION['user_id'])) {
        if (isset($_COOKIE['user_id']) && isset($_COOKIE['username']) && isset($_COOKIE['type'])) {
            $_SESSION['user_id'] = $_COOKIE['user_id'];
            $_SESSION['username'] = $_COOKIE['username'];
            $_SESSION['type'] = $_COOKIE['type'];
            $_SESSION['assigneduser_id'] = $_COOKIE['assigneduser_id'];
        }
        else {
            $url = VC_LOCATION.'login.php';
            header('Location: '.$url);
            exit();
        }
    }
    if(isset($_SESSION['user_id'])) {
        if($_SESSION['type'] != VC_ADMINISTRATOR) {
            $already_online = true;
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');
            $query = "SELECT status FROM vc_user_status WHERE status_id=".$_SESSION['user_id'];
            $data = mysqli_query($dbc, $query);
            if(mysqli_num_rows($data) != 1) {
                echo '<p class="error">Some error occured.</p>';
                exit();
            }
            $row = mysqli_fetch_array($data);
            if(!(intval($row['status']))) {
                $room = update_status($dbc);
                $_SESSION['room'] = $room;
                $already_online = false;
            }
            mysqli_close($dbc);
            unset($dbc);
        }
    }
?>
