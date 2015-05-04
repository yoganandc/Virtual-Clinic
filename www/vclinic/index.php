<?php 
    require_once('../../include/vclinic/startsession.php');

    if($_SESSION['type'] == 'a') {
        $url = VC_LOCATION.'administrator/';
    }
    else if ($_SESSION['type'] == 't') {
        $url = VC_LOCATION.'technician/';
    }
    else {
        $url = VC_LOCATION.'doctor/';
    }
    header('Location: '.$url);
    exit();
?>
