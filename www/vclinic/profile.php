<?php 
	require_once('../../include/vclinic/startsession.php');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

	if(isset($_GET['user_id'])) {
		$user_id = mysqli_real_escape_string($dbc, trim($_GET['user_id']));
	}
	else {
		if($_SESSION['type'] == VC_ADMINISTRATOR) {
			header('Location: '.VC_LOCATION);
			exit();
		}
		$user_id = $_SESSION['user_id'];
	}

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$query = "SELECT username, type, assigneduser_id, fname, lname, gender, birthdate, email, phone, picture FROM vc_user WHERE user_id=".$user_id;
	$data = mysqli_query($dbc, $query);

	if(mysqli_num_rows($data) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}

	$row = mysqli_fetch_array($data);

	$name = $row['fname'].' '.$row['lname'];
	$docname = "";

	if(!empty($row['assigneduser_id'])) {
		$query = "SELECT fname, lname FROM vc_user WHERE user_id=".$row['assigneduser_id'];
		$data = mysqli_query($dbc, $query);
		if(mysqli_num_rows($data) == 1) {
			$result = mysqli_fetch_array($data);
			$docname = $result['fname'].' '.$result['lname'];
		}
	}
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="stylesheets/user.css">
	<link rel="stylesheet" href="stylesheets/profile.css">
</head>
<body>
	<div id="banner">
		<?php if($user_id == $_SESSION['user_id']) echo '<p><a title="Edit Profile" href="editprofile.php">Edit Profile</a></p>'; ?>
		<h2><?php echo $name; ?></h2>
	</div>

	<div id="main-content">
		<div id="sidebar">
			<?php echo '<img src="'.VC_UPLOADPATH.$row['picture'].'" alt="Profile Picture">'."\n"; ?>
		</div>
		<div id="content">
			<table>
				<?php if($user_id == $_SESSION['user_id']) { ?>
				<tr>
					<th>Username: </th>
					<td><?php echo $row['username']; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<th>Account Type: </th>
					<td><?php if($row['type'] == VC_TECHNICIAN) echo 'Technician'; else echo 'Doctor'; ?></td>
				</tr>
				<tr>
					<th><?php  if($row['type'] == VC_TECHNICIAN) echo 'Assigned Doctor: '; else echo 'Assigned Technician: '; ?></th>
					<td><?php if($docname == '') echo '<span class="nulldata">No doctor assigned yet.</span>'; else echo '<a title="'.$docname.'" href="profile.php?user_id='.$row['assigneduser_id'].'">'.$docname.'</a>'; ?></td>
				</tr>
				<tr>
					<th>Gender: </th>
					<td><?php if(empty($row['gender'])) echo '<span class="nulldata">Not set.</span>'; else if($row['gender'] == 'm') echo 'Male'; else echo 'Female'; ?></td>
				</tr>
				<tr>
					<th>Birth Date: </th>
					<td><?php if(empty($row['birthdate'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['birthdate']; ?></td>
				</tr>
				<tr>
					<th>Email: </th>
					<td><?php if(empty($row['email'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['email']; ?></td>
				</tr>
				<tr>
					<th>Phone No.: </th>
					<td><?php if(empty($row['phone'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['phone']; ?></td>
				</tr>
			</table>
		</div>
	</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
