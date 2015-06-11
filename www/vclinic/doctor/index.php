<?php 
    require_once('../../../include/vclinic/doctorsession.php');

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

	$query = "SELECT assigneduser_id FROM vc_user WHERE user_id=".$_SESSION['user_id'];
	$data = mysqli_query($dbc, $query);
	$row = mysqli_fetch_array($data);
	if(!is_null($row['assigneduser_id'])) {
		$query = "SELECT vf.case_id, vp.patient_id, vp.fname, vp.lname, vp.gender, vp.birthdate, vc.complaint_id, vco.complaint, vc.altname FROM vc_forward AS vf INNER JOIN vc_case AS vc USING (case_id) INNER JOIN vc_complaint AS vco USING (complaint_id) INNER JOIN vc_patient AS vp USING (patient_id) WHERE vf.user_id=".$row['assigneduser_id']." AND status=0";
		$data = mysqli_query($dbc, $query);
		$forwards = array();
		$no_forwards = true;
		if(mysqli_num_rows($data) > 0) {
			$no_forwards = false;
			while($row = mysqli_fetch_array($data)) {
				if($row['complaint_id'] == VC_COMPLAINT_UNLISTED)
					$row['complaint'] = $row['altname'];
				array_push($forwards, $row);
			}
		}
	}
	else {
		$no_forwards = true;
	}	
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user.css'; ?>">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/t-index.css'; ?>">
</head>
<body>
	<div id="banner">
		<h2><?php echo "Home"; ?></h2>
	</div>
	<div id="main-content" data-user="<?php echo $_SESSION['user_id']; ?>">
		<h3 class="section-heading">Cases to be reviewed</h3>
		<div class="section-body">
			<?php if($no_forwards) { ?>
			<p class="nodata-message">No cases are under review or complete yet.</p>
			<?php } else { ?>
			<table id="forwards-table">
				<tr class="recents-heading">
					<th>Chief Complaint</th>
					<th class="middle-cell">Patient</th>
					<th class="middle-cell section-body">Sex</th>
					<th class="section-body">Birthdate</th>
				</tr>
				<?php foreach($forwards as $forward) { ?>
				<tr>
					<td><a title="<?php echo $forward['complaint']; ?>" href="<?php echo VC_LOCATION.'case.php?case_id='.$forward['case_id'].'&patient_id='.$forward['patient_id']; ?>"><?php echo $forward['complaint']; ?></a></td>
					<td class="middle-cell"><?php echo $forward['fname'].' '.$forward['lname']; ?></td>
					<td class="middle-cell section-body"><?php if(!empty($forward['gender'])) { if($forward['gender'] == 'm') echo 'M'; else echo 'F'; } else echo '-'; ?></td>
					<td class="section-body"><?php if(!empty($forward['birthdate'])) echo $forward['birthdate']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
				</tr>
				<?php } ?>
			</table>
			<?php } ?>
		</div>
		<br>
		<h3 class="section-heading">Recently Viewed Patient Profiles</h3>
		<div class="section-body">
			<?php if($no_recents) { ?>
			<p class="nodata-message">No patient profiles visited yet.</p>
			<?php } else { ?>
			<table id="recents-table">
				<tr class="recents-heading">
					<th id="width-1">Name</th>
					<th id="width-2" class="middle-cell section-body">Sex</th>
					<th id="width-3" class="middle-cell section-body">Birth Date</th>
					<th id="width-4" class="middle-cell">Occupation</th>
					<th id="width-5" class="middle-cell">Email</th>
					<th id="width-6">Phone</th>
				</tr>
				<?php for($i = $recents_pointer - 1; $i >= 0; $i--) { $recent = $recents[$i]; if(is_null($recent['patient_id'])) continue; else { ?>
				<tr>
					<td><a title="<?php echo $recent['fname'].' '.$recent['lname']; ?>" href="<?php echo VC_LOCATION; ?>patient.php?patient_id=<?php echo $recent['patient_id']; ?>"><?php echo $recent['fname'].' '.$recent['lname']; ?></a></td>
					<td class="middle-cell section-body"><?php if(!empty($recent['gender'])) { if($recent['gender'] == 'm') echo 'M'; else echo 'F'; } else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td class="middle-cell section-body"><?php if(!empty($recent['birthdate'])) echo $recent['birthdate']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td class="middle-cell"><?php if(!empty($recent['occupation'])) echo $recent['occupation']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td class="middle-cell"><?php if(!empty($recent['email'])) echo $recent['email']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['phone'])) echo $recent['phone']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
				</tr>
				<?php } } ?>
				<?php for($i = 9; $i >= $recents_pointer; $i--) { $recent = $recents[$i]; if(is_null($recent['patient_id'])) continue; else { ?>
				<tr>
					<td><a title="<?php echo $recent['fname'].' '.$recent['lname']; ?>" href="<?php echo VC_LOCATION; ?>patient.php?patient_id=<?php echo $recent['patient_id']; ?>"><?php echo $recent['fname'].' '.$recent['lname']; ?></a></td>
					<td class="middle-cell section-body"><?php if(!empty($recent['gender'])) { if($recent['gender'] == 'm') echo 'M'; else echo 'F'; } else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td class="middle-cell section-body"><?php if(!empty($recent['birthdate'])) echo $recent['birthdate']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td class="middle-cell"><?php if(!empty($recent['occupation'])) echo $recent['occupation']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td class="middle-cell"><?php if(!empty($recent['email'])) echo $recent['email']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
					<td><?php if(!empty($recent['phone'])) echo $recent['phone']; else echo '<span class="nulldata">Not Set.</span>'; ?></td>
				</tr>
				<?php } } ?>
			</table>
			<?php } ?>
		</div>
	</div>

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
