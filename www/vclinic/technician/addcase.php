<?php 
	require_once('../../../include/vclinic/techniciansession.php');

	$showerror = false;
	$error = "";

	if(isset($_GET['patient_id'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
		$patient_id = mysqli_real_escape_string($dbc, trim($_GET['patient_id']));
		$query = "SELECT vp.fname, vp.lname FROM vc_patient AS vp WHERE vp.patient_id=".$patient_id;
		$data = mysqli_query($dbc, $query);

		if(mysqli_num_rows($data) != 1) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}

		$row = mysqli_fetch_array($data);
		$name = $row['fname'].' '.$row['lname'];

		$query = "SELECT * FROM vc_complaint";
		$data_complaints = mysqli_query($dbc, $query);

		if(mysqli_num_rows($data_complaints) < 1) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}
	}
	else if(isset($_POST['submit'])) {
		$patient_id = $_POST['patient_id'];

		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
		$query = "SELECT vp.fname, vp.lname FROM vc_patient AS vp LEFT JOIN vc_address AS va USING (address_id) LEFT JOIN vc_address_state AS vas USING (state_id) WHERE vp.patient_id=".$patient_id;
		$data = mysqli_query($dbc, $query);

		if(mysqli_num_rows($data) != 1) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}

		$row = mysqli_fetch_array($data);
		$name = $row['fname'].' '.$row['lname'];

		$query = "SELECT * FROM vc_complaint";
		$data_complaints = mysqli_query($dbc, $query);

		if(mysqli_num_rows($data_complaints) < 1) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}

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
					if(!(($complaint == VC_COMPLAINT_UNLISTED) && ($alternate == ""))) {
						$query = "INSERT INTO vc_case (patient_id, complaint_id, altname, chronic, patient_history, past_history, personal_history, family_history, examination) VALUES (".$patient_id.", ".$complaint.", ";
						if($complaint == VC_COMPLAINT_UNLISTED)
							$query .= "'".$alternate."', ";
						else
							$query .= "NULL, ";
						$query .= $complaint_type.", '$patient_history', '$past_history', '$personal_history', '$family_history', '$examination')";
						if(mysqli_query($dbc, $query)) {
							mysqli_close($dbc);
							$url = VC_LOCATION.'patient.php?patient_id='.$patient_id;
							header("Location: ".$url);
							exit();
						}
						else {
							$showerror = true;
							$error = "Please check that patient, past, personal, family histories, and examination do not exceed 400 characters each.";
						}
					}
					else {
						$showerror = true;
						$error = "You must type in a complaint title if the complaint is unlisted.";
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
	else {
		header('Location: '.VC_LOCATION);
		exit();
	}
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user.css'; ?>">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/patient-sidebar.css'; ?>">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/addcase.css'; ?>">
	<script src="<?php echo VC_LOCATION.'scripts/addcase.js'; ?>"></script>
</head>
<body>
	<div id="banner">
		<h2><?php echo $name.' - Add Case'; ?></h2>
	</div>

	<div id="main-content">
		<?php require_once('../'.VC_INCLUDE.'patient-sidebar.php'); ?>
		<div id="content">
			<?php if($showerror) echo '<p class="error">'.$error.'</p>'."\n"; ?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<input type="hidden" id="patient_id" name="patient_id" value="<?php if(isset($_GET['patient_id'])) echo $_GET['patient_id']; else echo $_POST['patient_id']; ?>">
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
						<td><input type="submit" id="submit" name="submit"><a href="<?php echo VC_LOCATION.'patient.php?patient_id='.$patient_id; ?>" class="back-link" title="Cancel">Cancel</a></td>
					</tr>
				</table>
			</form>
		</div>
	</div>

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
