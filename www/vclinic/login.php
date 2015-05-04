<?php 
	require_once('../../include/vclinic/appvars.php');
	require_once(VC_INCLUDE.'dbvars.php');

	function check_and_assign_doctor($dbc) {
		if(empty($_SESSION['assigneduser_id'])) {
			$query = "SELECT user_id FROM vc_user WHERE assigneduser_id IS NULL AND type = 'd' LIMIT 1";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) == 1) {
				$row = mysqli_fetch_array($data);
				$query = "UPDATE vc_user SET assigneduser_id=".$row['user_id']." WHERE user_id=".$_SESSION['user_id'];
				if(mysqli_query($dbc, $query)) {
					$_SESSION['assigneduser_id'] = $row['user_id'];
					return true;
				}
				else {
					echo '<p class="error">Some error occured.</p>';
					exit();
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	function check_and_assign_technician($dbc) {
		if(empty($_SESSION['assigneduser_id'])) {
			$query = "SELECT user_id FROM vc_user WHERE assigneduser_id=".$_SESSION['user_id'];
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) == 1) {
				$row = mysqli_fetch_array($data);
				$query = "UPDATE vc_user SET assigneduser_id=".$row['user_id']." WHERE user_id=".$_SESSION['user_id'];
				if(mysqli_query($dbc, $query)) {
					$_SESSION['assigneduser_id'] = $row['user_id'];
					return true;
				}
				else {
					echo '<p class="error">Some error occured.</p>';
					exit();
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	function check_and_assign($dbc) {
		if($_SESSION['type'] == VC_TECHNICIAN)
			return check_and_assign_doctor($dbc);
		else
			return check_and_assign_technician($dbc);
	}

	define('VC_ADMIN_ID', '0');
	define('VC_ADMIN_NAME', 'admin');

	session_start();

	$error_msg = "";

	if(!isset($_SESSION['user_id'])) {
		if(isset($_POST['submit'])) {
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

			$form_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
			$form_password = mysqli_real_escape_string($dbc, trim($_POST['password']));

			if(!empty($form_username) && !empty($form_password)) {
				if($form_username == VC_ADMIN_NAME) {
					$query = "SELECT * FROM vc_admin WHERE password = SHA('$form_password')";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data) == 1) {
						$_SESSION['user_id'] = VC_ADMIN_ID;
						$_SESSION['username'] = VC_ADMIN_NAME;
						$_SESSION['type'] = VC_ADMINISTRATOR;
						setcookie('user_id', VC_ADMIN_ID, time() + (60 * 60 * 24 * 30));
						setcookie('username', VC_ADMIN_NAME, time() + (60 * 60 * 24 * 30));
						setcookie('type', VC_ADMINISTRATOR, time() + (60 * 60 * 24 * 30));
						mysqli_close($dbc);
						$url = VC_LOCATION;
						header('Location: '.$url);
						exit();
					}
					else {
						$error_msg = "Incorrect username or password.";
					}
				}
				else {
					$query = "SELECT user_id, username, type, assigneduser_id FROM vc_user WHERE username = '$form_username' AND password = SHA('$form_password')";
					$data = mysqli_query($dbc, $query);
					
					if(mysqli_num_rows($data) == 1) {
						$row = mysqli_fetch_array($data);

						$_SESSION['user_id'] = $row['user_id'];
	          			$_SESSION['username'] = $row['username'];
	          			$_SESSION['type'] = $row['type'];
	          			$_SESSION['assigneduser_id'] = $row['assigneduser_id'];
						setcookie('user_id', $row['user_id'], time() + (60 * 60 * 24 * 30));
						setcookie('username', $row['username'], time() + (60 * 60 * 24 * 30));
						setcookie('type', $row['type'], time() + (60 * 60 * 24 * 30));
						setcookie('assigneduser_id', $row['assigneduser_id'], time() + (60 * 60 * 24 * 30));

						if(check_and_assign($dbc))
							$url = VC_LOCATION.'profile.php';
						else 
							$url = VC_LOCATION;

						mysqli_close($dbc);

						header('Location: '.$url);
						exit();
					}
					else {
						$error_msg = "Incorrect username or password.";
					}
				}	
			}
			else {
				$error_msg = "Enter your username and password to continue.";
			}
		}
	}

	$pagetitle = "Login";
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
			<?php
				if(!isset($_SESSION['user_id'])) {
					echo '<p class="error">'.$error_msg.'</p>'."\n";
			?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
					<table>
						<tr>
							<th><label for="username">Username: </label></th>
							<td><input type="text" id="username" name="username" value="<?php if(!empty($form_username)) echo $form_username; ?>"></td>
						</tr>
						<tr>
							<th><label for="password">Password: </label></th>
							<td><input type="password" id="password" name="password"></td>
						</tr>
						<tr>
							<th></th>
							<td><input type="submit" id="submit" name ="submit" value="Login"></td>
						</tr>
					</table>
				</form>
			<?php 
				}
				else {
					echo '<p>You are logged in as: '.$_SESSION['username'].'</p>';
				}
			?>
		</div>
	</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
