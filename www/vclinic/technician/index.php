<?php 
	require_once('../../../include/vclinic/techniciansession.php');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$query = "SELECT vc_recents_pointer FROM vc_user WHERE user_id=".$_SESSION['user_id'];
	$data = mysqli_query($dbc, $query);
	$row = mysqli_fetch_array($data);

	$recents_pointer = $row['vc_recents_pointer'];

	if(is_null($recents_pointer)) {
		$query = "INSERT INTO vc_recents (user_id) VALUES ";
		for($i = 0; $i < 9; $i++) {
			$query .= "(".$_SESSION['user_id']."), ";
		}
		$query .= "(".$_SESSION['user_id'].")";
		mysqli_query($dbc, $query);
		$query = "UPDATE vc_user SET vc_recents_pointer=0 WHERE user_id=".$_SESSION['user_id'];
		mysqli_query($dbc, $query);
		$recents_pointer = "0";
	}

	$query = "SELECT vr.patient_id, vp.fname, vp.lname, vp.gender, vp.birthdate, vp.occupation, vp.email, vp.phone FROM vc_recents AS vr LEFT JOIN vc_patient AS vp USING (patient_id) WHERE vr.user_id=".$_SESSION['user_id'];
	$data = mysqli_query($dbc, $query);

	$recents = array();
	$no_recents = true;
	while($row = mysqli_fetch_array($data)) {
		if(!(is_null($row['patient_id'])))
			$no_recents = false;
		array_push($recents, $row);
	}
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php');?>

	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user.css'; ?>">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/t-index.css'; ?>">
</head>
<body>
	<div id="banner">
		<p><a title="Add New Patient" href="addpatient.php">Add New Patient</a></p>
		<h2><?php echo "Home"; ?></h2>
	</div>

	<div id="main-content">
		<h3 class="section-heading">Reviewed Cases</h3>
		<div class="section-body">

		</div>
		<h3 class="section-heading">Recently Viewed Patient Profiles</h3>
		<div class="section-body">
			<?php if($no_recents) { ?>
			<p class="nodata-message">No patient profiles visited yet.</p>
			<?php } else { ?>
			<table>
				<tr>
					<th>Name</th>
					<th>Sex</th>
					<th>Birth Date</th>
					<th>Occupation</th>
					<th>Email</th>
					<th>Phone</th>
				</tr>
				<?php for($i = $recents_pointer - 1; $i >= 0; $i--) { $recent = $recents[$i]; if(is_null($recent['patient_id'])) continue; else { ?>
				<tr>
					<td><a title="<?php echo $recent['fname'].' '.$recent['lname']; ?>" href="<?php echo VC_LOCATION; ?>patient.php?patient_id=<?php echo $recent['patient_id']; ?>"><?php echo $recent['fname'].' '.$recent['lname']; ?></a></td>
					<td><?php if(!empty($recent['gender'])) { if($recent['gender'] == 'm') echo 'M'; else echo 'F'; } else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['birthdate'])) echo $recent['birthdate']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['occupation'])) echo $recent['occupation']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['email'])) echo $recent['email']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['phone'])) echo $recent['phone']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
				</tr>
				<?php } } ?>
				<?php for($i = 9; $i >= $recents_pointer; $i--) { $recent = $recents[$i]; if(is_null($recent['patient_id'])) continue; else { ?>
				<tr>
					<td><a title="<?php echo $recent['fname'].' '.$recent['lname']; ?>" href="<?php echo VC_LOCATION; ?>patient.php?patient_id=<?php echo $recent['patient_id']; ?>"><?php echo $recent['fname'].' '.$recent['lname']; ?></a></td>
					<td><?php if(!empty($recent['gender'])) { if($recent['gender'] == 'm') echo 'M'; else echo 'F'; } else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['birthdate'])) echo $recent['birthdate']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['occupation'])) echo $recent['occupation']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['email'])) echo $recent['email']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['phone'])) echo $recent['phone']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
				</tr>
				<?php } } ?>
			</table>
			<?php } ?>
		</div>
	</div>
		
<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
