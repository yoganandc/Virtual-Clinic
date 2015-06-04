<?php 
    require_once('../../../include/vclinic/administratorsession.php');

    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');

    $query = "SELECT user_id, username, fname, lname, type, assigneduser_id FROM vc_user";
    $data = mysqli_query($dbc, $query);

    if(!$data) {
    	echo '<p class="error">Some error occured.</p>';
    	exit();
    }

    $url = 'administrator/';
    $pagetitle = "Home";
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user1.css'; ?>">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/adduser.css'; ?>">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/a-index.css'; ?>">
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
				<p><a title="Add New User" href="adduser.php">Add New User</a></p>
				<h2><?php echo $pagetitle; ?></h2>
			</div>
			<div id="main-content">
				<div id="content">
					<table>
						<tr>
							<th>ID</th>
							<th>Username</th>
							<th>Name</th>
							<th>Type</th>
							<th>Assigned?</th>
							<th></th>
							<th></th>
						</tr>
						<?php
							while($row = mysqli_fetch_array($data)) {
						?>
						<tr>
							<td><?php echo $row['user_id']; ?></td>
							<td><span class="username"><?php echo $row['username']; ?></span></td>
							<td><?php echo $row['fname'].' '.$row['lname']; ?></td>
							<td><?php if(($row['type']) == VC_TECHNICIAN) echo 'Technician'; else echo 'Doctor'; ?></td> 
							<td class="centerify"><?php if(empty($row['assigneduser_id'])) echo 'N'; else echo 'Y'; ?></td>
							<td><a title="<?php echo $row['fname'].' '.$row['lname']; ?>" href="<?php echo VC_LOCATION.'profile.php?user_id='.$row['user_id']; ?>">View Profile</a></td>
							<td><a title="Remove" href="#">Remove</a></td>
						</tr>
						<?php
							}
						?>
					</table>
				</div>
			</div>
			<?php if(isset($dbc)) mysqli_close($dbc); ?>
		</div>
	</div>
</body>
</html>
