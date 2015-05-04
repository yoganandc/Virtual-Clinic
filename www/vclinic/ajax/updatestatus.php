<?php
    require_once('../../../include/vclinic/ajaxsession.php');

    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
    $query = "SELECT status FROM vc_user_status WHERE status_id=".$_SESSION['assigneduser_id'];
    $data = mysqli_query($dbc, $query);
    if(mysqli_num_rows($data) != 1) {
        $status = null;
    }
    $row = mysqli_fetch_array($data);
    $status = $row['status'];
    
    if($status == null)
        echo '{"status":"0"}';
    else if($status) {
        $query = "SELECT message_id, message FROM vc_messages WHERE assigneduser_id=".$_SESSION['user_id']." AND received=0";
        $data = mysqli_query($dbc, $query);
        $query = "UPDATE vc_messages SET received=1 WHERE ";
        $where_array = array();
        $json_messages = array();
        if(mysqli_num_rows($data) > 0) {
            while($row = mysqli_fetch_array($data)) {
                $where = "message_id=".$row['message_id'];
                array_push($where_array, $where);
                $message = '"'.$row['message'].'"';
                array_push($json_messages, $message);
            }
            $where_clause = implode(' OR ', $where_array);
            $query .= $where_clause;
            mysqli_query($dbc, $query);
            $json_string = implode(',', $json_messages);
            $json_string = '{"messages":['.$json_string.'], "status":"2"}';
            echo $json_string;
        }
        else {
            echo '{"status":"2"}';
        }
    }  
    else 
        echo '{"status":"1"}';
    mysqli_close($dbc);
?>
