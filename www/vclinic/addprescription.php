<?php 
	require_once('../../include/vclinic/usersession.php');

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

	$query = "SELECT * FROM vc_treatment_name";
	$data = mysqli_query($dbc, $query);
	if(!$data) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$available_meds = array();
	while($row = mysqli_fetch_array($data)) {
		if($row['treatment_name_id'] != VC_TREATMENT_UNLISTED)
			$row['name'] = $available_types[$row['treatment_type_id']]['initial'].'. '.$row['name'].' '.substr($row['strength'],0,5).$available_types[$row['treatment_type_id']]['unit'];
		array_push($available_meds, $row);
	}

	$pagetitle = "Add Prescription";
	$nochat = true;
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="stylesheets/addtest.css">
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<p><a id="add-link" title="Add row" href="#">Add&nbsp;Row</a></p>
			<h2>Prescription</h2>
		</div>
		<div id="wrapper-form">
			<?php if($showerror) echo '<p class="error">'.$error.'</p>'; ?>
			<form method="POST" action="#">
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
						<td class="hidden-col"><input type="text" class="altname" id="altname-<?php echo $i; ?>" name="altname-<?php echo $i; ?>" value="<?php if(isset($med['altname'])) echo $med['altname']; ?>"></td>
						<td><input type="text" class="dosage" id="dosage-<?php echo $i; ?>" name="dosage-<?php echo $i; ?>" value="<?php if(isset($med['dosage'])) echo $med['dosage']; ?>" size="5" maxlength="5"></td>
						<td>
							<input type="radio" id="before-<?php echo $i; ?>" name="before-<?php echo $i; ?>" value="1" <?php if((isset($med['before'])) && ($med['before'] == "1")) echo 'checked="checked"'; ?>>Before
							<input type="radio" id="after-<?php echo $i; ?>" name="before-<?php echo $i; ?>" value="0" <?php if((isset($med['before'])) && ($med['before'] == "0")) echo 'checked="checked"'; ?>>After
						</td>
						<td><input type="text" class="duration" id="duration-<?php echo $i; ?>" name="name-<?php echo $i; ?>" value="<?php if(isset($med['duration'])) echo $med['duration']; ?>" size="2" maxlength="2">&nbsp;days</td>
						<td><a class="remove-link" id="remove-link-<?php echo $i; ?>" href="#" title="Remove this row">Remove</a></td>
					</tr>
					<?php $i++; } ?>
					<tr>
						<td id="submit-cell"><input type="submit" id="submit" name="submit"></td>
						<td><a id="cancel-test" href="#" title="Cancel">Cancel</a></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</form>
		</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
