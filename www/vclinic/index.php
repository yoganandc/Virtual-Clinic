<?php 
    require_once('../../include/vclinic/startsession.php');

    if($_SESSION['type'] == VC_ADMINISTRATOR) {
        $url = VC_LOCATION.'administrator/';
        header('Location: '.$url);
        exit();
    }
    else {
        if($_SESSION['type'] == VC_TECHNICIAN)
            $url = 'technician/';
        else
            $url = 'doctor/';
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

        $pagetitle = 'Home';
    }
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

    <script src="<?php echo VC_LOCATION.'scripts/chat.js'; ?>"></script> 
    <script src="<?php echo VC_LOCATION.'scripts/index.js'; ?>"></script> 
    <script src="<?php echo VC_LOCATION.'scripts/jquery-1.11.0.min.js'; ?>"></script>
    <script src="<?php echo VC_LOCATION.'fancybox/jquery.fancybox.js'; ?>"></script>
    <link rel="stylesheet" href="<?php echo VC_LOCATION.'fancybox/jquery.fancybox.css'; ?>">
    <link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/chat.css'; ?>">
    <link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user.css'; ?>">
</head>
<body>
    <div id="header">
        <div id="header-right">
            <ul id="nav">
                <li><a title="Home" href="<?php echo VC_LOCATION.$url; ?>" target="vc-iframe">Home</a></li>
                <li>|</li>
                <?php if($_SESSION['type'] != VC_ADMINISTRATOR) { ?>
                <li><a title="My Account" href="<?php echo VC_LOCATION.'profile.php'; ?>" target="vc-iframe">My Account</a><?php echo '<span class="username"> ('.$_SESSION['username'].')</span>'; ?></li>
                <li>|</li>
                <?php } ?>
                <li><a title="Log out" href="<?php echo VC_LOCATION.'logout.php'; ?>">Log Out</a></li>
            </ul>
            <?php if($_SESSION['type'] != VC_ADMINISTRATOR) { ?>
            <form action="<?php echo VC_LOCATION.'search.php'; ?>" method="GET" target="vc-iframe">
                <input type="text" id="query" name="query" value="<?php if(isset($_GET['query'])) echo $_GET['query']; ?>">
                <input type="submit" id="search" value="Search">
            </form>
            <?php } ?>
        </div>
        <h1>Virtual Clinic</h1>
    </div>
    <div id="vc-wrapper">
        <?php
            $docname = "";
            if(!empty($_SESSION['assigneduser_id'])) {
                $query_chat = "SELECT fname, lname FROM vc_user WHERE user_id=".$_SESSION['assigneduser_id'];
                $data_docname = mysqli_query($dbc, $query_chat);
                if(mysqli_num_rows($data_docname) == 1) {
                    $result_chat = mysqli_fetch_array($data_docname);
                    $docname = $result_chat['fname'].' '.$result_chat['lname'];
                }
                $query_chat = "SELECT status FROM vc_user_status WHERE status_id=".$_SESSION['assigneduser_id'];
                $data_docstatus = mysqli_query($dbc, $query_chat);
                if(mysqli_num_rows($data_docstatus) == 1) {
                    $result_chat = mysqli_fetch_array($data_docstatus);
                    if($result_chat['status']) 
                        $online = true;
                    else 
                        $online = false;
                }
            }
        ?>
        <div id="chat-container" data-status="<?php if($docname == "") echo '0'; else echo '1'; ?>" data-user="<?php echo $_SESSION['user_id']; ?>" data-assigned="<?php echo $_SESSION['assigneduser_id']; ?>" data-room="<?php if(!empty($_SESSION['room'])) echo $_SESSION['room']; ?>">
            <div id="chat-details">
                <div id="details-right">
                </div>
                <p id="details-left">
                    <?php if($docname == "") echo 'No doctor assigned yet.'."\n"; else { if($_SESSION['type'] == VC_TECHNICIAN) echo 'Dr. '; echo $docname.' is '; if($online) echo '<span id="status" class="online">ONLINE</span>.'; else echo '<span id="status" class="offline">OFFLINE</span>.'; echo "\n"; }  ?>
                </p>
            </div>
            <div id="chat-body">
                <div id="video-chat">
                    <div id="remotevideo-container">
                        <video id="remoteVideo" autoplay></video>
                    </div>
                    <div id="localvideo-container">
                        <video id="localVideo" autoplay muted></video>
                    </div>
                </div>
                <div id="text-chat">
                    <div id="text-chat-main">
                        <div id="text-chat-panel">
                        </div>
                        <input type="text" id="send-text">
                        <input type="submit" id="send" value="Send">    
                    </div>
                </div>
            </div>
        </div>
        <div id="main">
            <iframe name="vc-iframe" id="vc-iframe" src="<?php echo VC_LOCATION.$url; ?>">
                <p>Your browser does not support iframes.</p>
            </iframe>
            <?php if(isset($dbc)) mysqli_close($dbc); ?>
        </div>
    </div>
</body>
</html>
