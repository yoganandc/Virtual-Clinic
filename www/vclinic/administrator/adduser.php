<?php 
    require_once('../../../include/vclinic/administratorsession.php');

    define('VC_PATTERN_USERNAME', '/^\w{5,40}$/');
    define('VC_PATTERN_PASSWORD','/^[^\s]{5,40}$/');

    $error = "";
    $showerror = false;

    if(isset($_POST['submit'])) {
    	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');

    	$username = mysqli_real_escape_string($dbc, trim($_POST['username']));
    	$password = mysqli_real_escape_string($dbc, trim($_POST['password']));
    	$retype_password = mysqli_real_escape_string($dbc, trim($_POST['retype_password']));
    	if(isset($_POST['type']))
    		$type = mysqli_real_escape_string($dbc, trim($_POST['type']));
    	else
    		$type = NULL;

    	if(!empty($username) && !empty($password) && !empty($retype_password) && !empty($type)) {
            if(preg_match(VC_PATTERN_USERNAME, $username)) {
                if(preg_match(VC_PATTERN_PASSWORD, $password)) {
            		if($password == $retype_password) {
            			$query = "SELECT EXISTS (SELECT 1 FROM vc_user WHERE username='$username') AS result";
            			$data = mysqli_query($dbc, $query);
            			if(mysqli_num_rows($data) != 1) {
            				echo '<p class="error">Some error occured.</p>';
            				exit();
            			}
            			$row = mysqli_fetch_array($data);

            			if(!$row['result']) {
                            $query = "INSERT INTO vc_user_status (status_id) VALUES (0)";
                            if(mysqli_query($dbc, $query)) {
                                $query = "INSERT INTO vc_user (username, password, type) VALUES ('$username', SHA('$password'), '$type')";

                                if(mysqli_query($dbc, $query)) {
                                    mysqli_close($dbc);
                                    header('Location: '.VC_LOCATION);
                                    exit();
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
            			else {
            				$showerror = true;
            				$error = "This username is already taken.";
            			}
            		}
            		else {
            			$showerror = true;
            			$error = "The entered passwords do not match.";
            		}
                }
                else {
                    $showerror = true;
                    $error = "Password must contain between 5 and 40 non-whitespace characters.";
                }
            }
            else {
                $showerror = true;
                $error = "Username must be between 5 and 40 characters. It must only contain alphanumeric characters and underscore(_).";
            }
    	}
    	else {
    		$showerror = true;
    		$error = "All details are required for adding a new user.";
    	} 
    }

    $pagetitle = "Add New User";
    $url = 'administrator/';
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user1.css'; ?>">
<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/adduser.css'; ?>">

</head>
<body>
    <div id="header">
        <div id="header-right">
            <ul id="nav">
                <li><a title="Home" href="<?php echo VC_LOCATION.$url; ?>">Home</a></li>
                <li>|</li>
                <li><a title="Log out" href="<?php echo VC_LOCATION.'logout.php'; ?>">Log Out</a></li>
            </ul>
            </div>
        <h1>Virtual Clinic</h1>
    </div>
    <div id="vc-wrapper">
        <div id="main">
            <div id="banner">
            	<h2><?php echo $pagetitle; ?></h2>
            </div>
            <div id="main-content">
                <?php if($showerror) echo '<p class="error">'.$error.'</p>'."\n"; ?>
            	<div id="content">
            		<form action="#" method="POST">
            			<table>
            				<tr>
            					<th><label for="username">Username: </label></th>
            					<td><input type="text" id="username" name="username" value="<?php if(!empty($username)) echo $username; ?>"></td>
            				</tr>
            				<tr>
            					<th><label for="password">Password: </label></th>
            					<td><input type="password" id="password" name="password" value="<?php if(!empty($password)) echo $password; ?>"></td>
            				</tr>
            				<tr>
            					<th><label for="retype_password">Retype Password: </label></th>
            					<td><input type="password" id="retype_password" name="retype_password" value="<?php if(!empty($retype_password)) echo $retype_password; ?>"></td>
            				</tr>
            				<tr>
            					<th>Type: </th>
            					<td>
            						<input type="radio" id="type_t" name="type" value="t" <?php if(!empty($type) && $type == 't') echo 'checked="checked"'; ?>><label for="type_t">Technician</label>
            						<input type="radio" id="type_d" name="type" value="d" <?php if(!empty($type) && $type == 'd') echo 'checked="checked"'; ?>><label for="type_d">Doctor</label>
            					</td>
            				</tr>
            				<tr>
            					<th></th>
            					<td><input type="submit" id="submit" name="submit"><a href="index.php" class="back-link" title="Cancel">Cancel</a></td>
            				</tr>
            			</table>
            		</form>
            	</div>
            </div>
            <?php if(isset($dbc)) mysqli_close($dbc); ?>
        </div>
    </div>
</body>
</html>
