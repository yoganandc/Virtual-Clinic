<?php
    session_start();

    require_once('../../include/vclinic/appvars.php');

    if (isset($_SESSION['user_id'])) {

        $user = "";

        if(!empty($_SESSION['assigneduser_id'])) {
            $user = $_SESSION['user_id'];
            $assigned = $_SESSION['assigneduser_id'];
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
    <script src="scripts/logout.js"></script>
</head>
<body>
    <div id="wrapper"<?php if(!empty($user)) { ?> data-user="<?php echo $user; ?>" data-assigned="<?php echo $assigned; ?>"<?php } ?>>
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
