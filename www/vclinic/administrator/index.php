<?php 
    require_once('../../../include/vclinic/administratorsession.php');

    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');

    $query = "SELECT user_id, username, fname, lname, type, assigneduser_id FROM vc_user";
    $data = mysqli_query($dbc, $query);

    if(!$data) {
    	echo '<p class="error">Some error occured.</p>';
    	exit();
    }

    $pagetitle = "Home";
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user.css'; ?>">
<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/adduser.css'; ?>">
<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/a-index.css'; ?>">

<?php require_once('../'.VC_INCLUDE.'header.php'); ?>

<div id="banner">
	<h2><?php echo $pagetitle; ?></h2>
	<p><a title="Add New User" href="adduser.php">Add New User</a></p>
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

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
