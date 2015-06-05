<?php 
	require_once('../../include/vclinic/usersession.php');

	$showerror = false;
	$error = "";
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

	if(isset($_GET['case_id']) && isset($_GET['patient_id'])) {
		$case_id = mysqli_real_escape_string($dbc, trim($_GET['case_id']));
		$patient_id = mysqli_real_escape_string($dbc, trim($_GET['patient_id']));
	}
	else if(isset($_POST['submit'])) {
		$case_id = mysqli_real_escape_string($dbc, trim($_POST['case_id']));
		$patient_id = mysqli_real_escape_string($dbc, trim($_POST['patient_id']));
	}
	else {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$edit_success = false;
	$query = "SELECT case_id FROM vc_case WHERE patient_id=".$patient_id." ORDER BY case_id DESC LIMIT 1";
	$data = mysqli_query($dbc, $query);
	$row = mysqli_fetch_array($data);
	if($case_id == $row['case_id']) 
		$back_url = "patient.php?patient_id=".$patient_id;
	else 
		$back_url = "case.php?case_id=".$case_id."&amp;patient_id=".$patient_id;

	if(isset($_GET['case_id']) && isset($_GET['patient_id'])) {
		$query = "SELECT patient_id FROM vc_case WHERE case_id=".$case_id;
		$data = mysqli_query($dbc, $query);
		if(mysqli_num_rows($data) != 1) {
			echo '<p>Some error occured.</p>';
			exit();
		}
		$row = mysqli_fetch_array($data);

		if($patient_id != $row['patient_id']) {
			header('Location: '.VC_LOCATION);
			exit();
		}

		$query = "SELECT edit_lock FROM vc_case WHERE case_id=".$case_id;
		$data = mysqli_query($dbc, $query);
		$row = mysqli_fetch_array($data);
		$edit_lock = intval($row['edit_lock']);

		if(!$edit_lock) {
			$query = "SELECT complaint_id, altname, chronic, patient_history, personal_history, past_history, family_history, examination FROM vc_case WHERE case_id=".$case_id;
			$data = mysqli_query($dbc, $query);
			$row = mysqli_fetch_array($data);

			$complaint = $row['complaint_id'];
			$alternate = $row['altname'];
			$complaint_type = $row['chronic'];
			$patient_history = $row['patient_history'];
			$personal_history = $row['personal_history'];
			$past_history = $row['past_history'];
			$family_history = $row['family_history'];
			$examination = $row['examination'];
		}
		else {
			$showerror = true;
			$error = "Someone else is editing this case right now. This is most probably your assigned ";
			if($_SESSION['type'] == VC_DOCTOR)
				$error .= "technician. ";
			else
				$error .= "doctor. ";
			$error .= "Please check. If not, please try editing this case at a later time.";
		}
	}
	else if(isset($_POST['submit'])) {
		$edit_lock = 0;

		if(isset($_POST['complaint_type']))
			$complaint_type = mysqli_real_escape_string($dbc, trim($_POST['complaint_type']));
		else 
			$complaint_type = "";
		$complaint = mysqli_real_escape_string($dbc, trim($_POST['complaint']));
		if(isset($_POST['alternate']))
			$alternate = mysqli_real_escape_string($dbc, trim($_POST['alternate']));
		else
			$alternate = "";
		$patient_history = preg_replace("/\\\\r\\\\n/", "\r\n", mysqli_real_escape_string($dbc, trim($_POST['patient_history'])));
		$past_history = preg_replace("/\\\\r\\\\n/", "\r\n", mysqli_real_escape_string($dbc, trim($_POST['past_history'])));
		$personal_history = preg_replace("/\\\\r\\\\n/", "\r\n", mysqli_real_escape_string($dbc, trim($_POST['personal_history'])));
		$family_history = preg_replace("/\\\\r\\\\n/", "\r\n", mysqli_real_escape_string($dbc, trim($_POST['family_history'])));
		$examination = preg_replace("/\\\\r\\\\n/", "\r\n", mysqli_real_escape_string($dbc, trim($_POST['examination'])));

		if(($complaint_type == "0") || ($complaint_type == "1")) {
			if(!empty($complaint)) {
				$query = "SELECT chronic_only FROM vc_complaint WHERE complaint_id=".$complaint;
				$data = mysqli_query($dbc, $query);
				if(mysqli_num_rows($data) != 1) {
					echo '<p class="error">Some error occured.</p>';
					exit();
				}
				$row = mysqli_fetch_array($data);

				if(!(($complaint_type == VC_ACUTE) && ($row['chronic_only'] == 1))) {
					if(!(($complaint == VC_COMPLAINT_UNLISTED) && (($alternate == "") || (strlen($alternate) > 40)))) {
						$query = "UPDATE vc_case SET complaint_id=".$complaint.", chronic=".$complaint_type.", patient_history='$patient_history', personal_history='$personal_history', past_history='$past_history', family_history='$family_history', ";
						if($complaint == VC_COMPLAINT_UNLISTED)
							$query .= "altname='$alternate'";
						else
							$query .= "altname=NULL";
						$query .= " WHERE case_id=".$case_id;

						if(mysqli_query($dbc, $query)) {
							$edit_success = true;
							/* mysqli_close($dbc);
							if($back_url[0] == "p")
								$url = $back_url;
							else
								$url = "case.php?case_id=".$case_id."&patient_id=".$patient_id;
							header("Location: ".$url);
							exit(); */
						}
						else {
							$showerror = true;
							$error = "Please check that patient, past, personal, family histories, and examination do not exceed 400 characters each.";
						}
					}
					else {
						$showerror = true;
						$error = "You must type in a complaint title (max. 40 characters) if the complaint is unlisted.";
					}
				}
				else {
					$showerror = true;
					$error = "You cannot select a chronic condition for an acute-type complaint.";
				}
			}
			else {
				$showerror = true;
				$error = "Chief complaint cannot be blank.";
			}
		}
		else {
			$showerror = true;
			$error = "Complaint type must be set to either acute or chronic.";
		}
	}

	$query = "SELECT vp.fname, vp.lname FROM vc_patient AS vp WHERE vp.patient_id=".$patient_id;
	$data = mysqli_query($dbc, $query);
	if(mysqli_num_rows($data) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$row = mysqli_fetch_array($data);

	if(!$edit_success) {
		$title = $row['fname'].' '.$row['lname'].' - Edit Case';
		$query = "SELECT * FROM vc_complaint";
		$data_complaints = mysqli_query($dbc, $query);
	}
	else {
		$title = $row['fname'].' '.$row['lname'].' - Updating Case...';
	}
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="stylesheets/user.css">
	<link rel="stylesheet" href="stylesheets/patient-sidebar.css">
	<link rel="stylesheet" href="stylesheets/editcase.css">
	<?php if(!$edit_success) { ?>
	<script src="scripts/editcase.js"></script>
</head>
<body>
	<div id="banner">
		<?php if($edit_lock) { ?><p><a title="Back" href="<?php echo $back_url; ?>">Back</a></p><?php } ?>
		<h2><?php echo $title; ?></h2>
	</div>

	<div id="main-content" data-case-id="<?php echo $case_id; ?>" data-edit-lock="<?php if($edit_lock) echo '1'; else echo '0'; ?>">
		<?php require_once(VC_INCLUDE.'patient-sidebar.php'); ?>
		<div id="content">
			<?php if($showerror) echo '<p class="error">'.$error.'</p>'."\n"; ?>
			<?php if(!$edit_lock) { ?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<input type="hidden" id="patient_id" name="patient_id" value="<?php if(isset($_GET['patient_id'])) echo $_GET['patient_id']; else echo $_POST['patient_id']; ?>">
				<input type="hidden" id="case_id" name="case_id" value="<?php if(isset($_GET['case_id'])) echo $_GET['case_id']; else echo $_POST['case_id']; ?>">
				<input type="hidden" id="altname" name="altname" value="<?php if(isset($alternate)) echo $alternate; ?>">
				<table id="case">
					<tr>
						<th>Type: </th>
						<td>
							<input type="radio" id="acute" name="complaint_type" value="0" <?php if(isset($complaint_type) && $complaint_type == VC_ACUTE) echo 'checked="checked"'; ?>><label for="acute">Acute</label>
							<input type="radio" id="chronic" name="complaint_type" value="1" <?php if(isset($complaint_type) && $complaint_type == VC_CHRONIC) echo 'checked="checked"'; ?>><label for="chronic">Chronic</label>
						</td>
					</tr>
					<tr id="complaint-row">
						<th><label for="complaint">Chief Complaint: </label></th>
						<td>
							<select id="complaint" name="complaint">
								<?php 
									while($row = mysqli_fetch_array($data_complaints)) { 
										$option = '<option id="option-'.$row['complaint_id'].'" value="'.$row['complaint_id'].'" ';
										if(!$row['chronic_only'])
											$option .= 'data-chronic_only="0"';
										else 
											$option .= 'data-chronic_only="1" disabled="disabled" ';
										if(!empty($complaint) && ($row['complaint_id'] == $complaint))
											$option .= 'selected="selected"';
										$option .= '>'.$row['complaint'].'</option>'."\n";
										echo $option;
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="patient_history">Patient History: </label></th>
						<td><textarea id="patient_history" name="patient_history" rows="10" cols="40"><?php if(!empty($patient_history)) echo $patient_history; ?></textarea></td>
					</tr>
					<tr>
						<th><label for="personal_history">Personal History: </label></th>
						<td><textarea id="personal_history" name="personal_history" rows="10" cols="40"><?php if(!empty($personal_history)) echo $personal_history; ?></textarea></td>
					</tr>
					<tr>
						<th><label for="past_history">Past History: </label></th>
						<td><textarea id="past_history" name="past_history" rows="10" cols="40"><?php if(!empty($past_history)) echo $past_history; ?></textarea></td>
					</tr>
					<tr>
						<th><label for="family_history">Family History: </label></th>
						<td><textarea id="family_history" name="family_history" rows="10" cols="40"><?php if(!empty($family_history)) echo $family_history; ?></textarea></td>
					</tr>
					<tr>
						<th><label for="examination">Examination: </label></th>
						<td><textarea id="examination" name="examination" rows="10" cols="40"><?php if(!empty($examination)) echo $examination; ?></textarea></td>
					</tr>
					<tr>
						<th></th>
						<td><input type="submit" id="submit" name="submit"><a href="<?php echo $back_url; ?>" class="back-link" title="Cancel">Cancel</a></td>
					</tr>
				</table>
			</form>
			<?php } ?>
		</div>
	</div>
	<?php } else { ?>
	<script src="scripts/sendreload.js"></script>
</head>
<body>
	<div id="banner">
		<h2><?php echo $title; ?></h2>
	</div>
	<div id="main-content" data-case-id="<?php echo $case_id; ?>" data-redirect="<?php echo $back_url; ?>">
		<?php require_once(VC_INCLUDE.'patient-sidebar.php'); ?>
		<div id="content">
			<p style="text-align: center">
				Please wait while the case is being updated
			</p>
		</div>
	</div>
	<?php } ?>

<?php require_once(VC_INCLUDE.'footer.php'); ?>