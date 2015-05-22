<?php 
	require_once('../../include/vclinic/usersession.php');
	define('VC_ALTNAME_LENGTH', '40');

	$showerror = false;
	$error = "";

	$hidden = "1";

	if(isset($_GET['case_id'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');
		$case_id = mysqli_real_escape_string($dbc, trim($_GET['case_id']));
		$rows = 1;

		$meds = array();
		array_push($meds, null);
	}
	else if(isset($_POST['submit'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');
		$case_id = mysqli_real_escape_string($dbc, trim($_POST['case_id']));
		$rows = mysqli_real_escape_string($dbc, trim($_POST['num-rows']));

		$meds = array();
		$count = 0;
		$i = 1;
		while($count < $rows) {
			$key = 'treatment-'.$i;
			if(isset($_POST[$key])) {
				$med['treatment'] = mysqli_real_escape_string($dbc, trim($_POST[$key]));
				if($med['treatment'] == VC_TREATMENT_UNLISTED) {
					$med['altname'] = mysqli_real_escape_string($dbc, trim($_POST['altname-'.$i]));
					$hidden = "0";
				}
				$med['dosage'] = mysqli_real_escape_string($dbc, trim($_POST['dosage-'.$i]));
				if(isset($_POST['before-'.$i]))
					$med['before'] = mysqli_real_escape_string($dbc, trim($_POST['before-'.$i]));
				else 
					$med['before'] = "";
				$med['duration'] = mysqli_real_escape_string($dbc, trim($_POST['duration-'.$i]));
				array_push($meds, $med);
				$count++;
				unset($med['altname']);
			}
			$i++;
		}

		foreach($meds as $med) {
			if($med['treatment'] == VC_TREATMENT_UNLISTED) {
				if((empty($med['altname'])) || (strlen($med['altname']) > VC_ALTNAME_LENGTH)) {
					$showerror = true;
					$error = "Name must be entered for all unlisted medicines and cannot exceed 40 characters.";
				} 
			}
			if(empty($med['dosage']) || !preg_match('/^[01]-[01]-[01]$/', $med['dosage'])) {
				$showerror = true;
				$error = "Dosage cannot be empty and must be entered in the format: X-X-X. For example, 0-0-1.";
			}
			if(($med['before'] != "0") && ($med['before'] != "1")) {
				$showerror = true;
				$error = "Enter whether medicine should be taken before or after food.";
			}
			if(empty($med['duration']) || !is_numeric($med['duration']) || intval($med['duration']) == 0) {
				$showerror = true;
				$error = "Entered duration cannot be empty and must be a number between 1 and 99 days.";
			}
		}

		if(!$showerror) {
			foreach($meds as $med) {
				$med['dosage'] = str_replace('-', '', $med['dosage']);
				if($med['treatment'] != VC_TREATMENT_UNLISTED)
					$query = "INSERT INTO vc_treatment (treatment_name_id, case_id, altname, dosage, before_food, duration) VALUES (".$med['treatment'].", $case_id, NULL, '".$med['dosage']."', ".$med['before'].", ".$med['duration'].")";
				else 
					$query = "INSERT INTO vc_treatment (treatment_name_id, case_id, altname, dosage, before_food, duration) VALUES (".$med['treatment'].", $case_id, '".$med['altname']."', '".$med['dosage']."', ".$med['before'].", ".$med['duration'].")";
				if(!mysqli_query($dbc, $query)) {
					echo '<p class="error">Some error occured.</p>';
					exit();
				}
			}
			header('Location: '.VC_LOCATION.'closewindow.php');
			exit();
		}
	}
	else {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}

	$query = "SELECT initial, unit FROM vc_treatment_type";
	$data = mysqli_query($dbc, $query);
	if(!$data) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$available_types = array();
	array_push($available_types, null);
	while($row = mysqli_fetch_array($data)) {
		array_push($available_types, $row);
	}

	$query = "SELECT * FROM vc_treatment_name ORDER BY name";
	$data = mysqli_query($dbc, $query);
	if(!$data) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$available_meds = array();
	while($row = mysqli_fetch_array($data)) {
		if($row['treatment_name_id'] != VC_TREATMENT_UNLISTED)
			$row['name'] = $row['name'].' '.substr($row['strength'],0,5).$available_types[$row['treatment_type_id']]['unit'].' ('.$available_types[$row['treatment_type_id']]['initial'].')';
		array_push($available_meds, $row);
	}

	$pagetitle = "Add Prescription";
	$nochat = true;
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="stylesheets/addtest.css">
	<script src="scripts/addprescription.js"></script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<p><a id="add-link" title="Add row" href="#">Add&nbsp;Row</a></p>
			<h2>Prescription</h2>
		</div>
		<div id="wrapper-form">
			<?php if($showerror) echo '<p class="error">'.$error.'</p>'; ?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id ?>">
				<input type="hidden" id="num-rows" name="num-rows" value="<?php echo $rows ?>">
				<table>
					<tr>
						<th>Medicine</th>
						<th data-hidden="<?php echo $hidden; ?>" class="hidden-col"><label>Name</label></th>
						<th>Dosage</th>
						<th>Before/After Food</th>
						<th>Duration</th>
						<th></th>
					</tr>
					<?php $i=1; foreach($meds as $med) { ?>
					<tr>
						<td>
							<select id="treatment-<?php echo $i; ?>" name="treatment-<?php echo $i; ?>" class="treatment">
								<?php
									foreach($available_meds as $available_med) {
										if((isset($med['treatment'])) && ($med['treatment'] == $available_med['treatment_name_id']))
											echo '<option value="'.$available_med['treatment_name_id'].'" selected="selected">'.$available_med['name'].'</option>'."\n";
										else
											echo '<option value="'.$available_med['treatment_name_id'].'">'.$available_med['name'].'</option>'."\n";
									}
								?>
							</select>
						</td>
						<td class="hidden-col"><input type="text" class="altname" id="altname-<?php echo $i; ?>" name="altname-<?php echo $i; ?>" value="<?php if(isset($med['altname'])) echo $med['altname']; ?>" <?php if(!isset($med['altname'])) echo 'disabled="disabled"'; ?>></td>
						<td><input type="text" class="dosage" id="dosage-<?php echo $i; ?>" name="dosage-<?php echo $i; ?>" value="<?php if(isset($med['dosage'])) echo $med['dosage']; ?>" size="5" maxlength="5"></td>
						<td>
							<input type="radio" id="before-<?php echo $i; ?>" name="before-<?php echo $i; ?>" value="1" <?php if((isset($med['before'])) && ($med['before'] == "1")) echo 'checked="checked"'; ?>>Before
							<input type="radio" id="after-<?php echo $i; ?>" name="before-<?php echo $i; ?>" value="0" <?php if((isset($med['before'])) && ($med['before'] == "0")) echo 'checked="checked"'; ?>>After
						</td>
						<td><input type="text" class="duration" id="duration-<?php echo $i; ?>" name="duration-<?php echo $i; ?>" value="<?php if(isset($med['duration'])) echo $med['duration']; ?>" size="2" maxlength="2">&nbsp;days</td>
						<td><a class="remove-link" id="remove-link-<?php echo $i; ?>" href="#" title="Remove this row">Remove</a></td>
					</tr>
					<?php $i++; } ?>
					<tr>
						<td id="submit-cell"><input type="submit" id="submit" name="submit"></td>
						<td><a id="cancel-treatment" href="#" title="Cancel">Cancel</a></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</form>
		</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
