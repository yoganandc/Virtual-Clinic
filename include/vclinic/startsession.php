<?php 
    session_start();

    require_once('appvars.php');
    require_once('dbvars.php');
    require_once('library.php');

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
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');
            $query = "SELECT status FROM vc_user_status WHERE status_id=".$_SESSION['user_id'];
            $data = mysqli_query($dbc, $query);
            if(mysqli_num_rows($data) != 1) {
                echo '<p class="error">Some error occured.</p>';
                exit();
            }
            $row = mysqli_fetch_array($data);
            if(!$row['status']) {
                $room = update_status($dbc);
                $_SESSION['room'] = $room;
            }
            else {
                $current_time = time();
                $query = "UPDATE vc_user_status SET lastseen='$current_time' WHERE status_id=".$_SESSION['user_id'];
                if(!mysqli_query($dbc, $query)) {
                    echo '<p class="error">Some error occured.</p>';
                    exit();
                } 
            }
            mysqli_close($dbc);
            unset($dbc);
        }
    }
?>
