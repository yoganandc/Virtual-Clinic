<?php
    session_start();

    require_once('../../include/vclinic/appvars.php');
    require_once(VC_INCLUDE.'dbvars.php');
    require_once(VC_INCLUDE.'library.php');

    if (isset($_SESSION['user_id'])) {

        if($_SESSION['type'] != VC_ADMINISTRATOR) {
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
            set_status_offline($dbc, $_SESSION['user_id']);
            mysqli_close($dbc);
            unset($dbc);
        }
        
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600);
        }

        @session_destroy();

        setcookie('user_id', '', time() - 3600);
        setcookie('username', '', time() - 3600);
        setcookie('type', '', time() - 3600);
        setcookie('assigneduser_id', '', time() - 3600);
    }
    else {
        $url = VC_LOCATION.'login.php';
        header('Location: ' . $url);
        exit();
    } 

    $pagetitle = "Logout";
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

    <link rel="stylesheet" href="stylesheets/accountcontrol.css">
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>Virtual Clinic</h1>
        </div>
        <div id="wrapper-form">
            <p>
                You have successfully logged out.
                <br>
                <br>
                <a title="Login" href="login.php">Login</a> Again?
            </p>
        </div>
    </div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
