<?php
	require_once('../../include/vclinic/usersession.php');

	if(!isset($_GET['patient_id'])) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$patient_id = mysqli_real_escape_string($dbc, trim($_GET['patient_id']));
	$query = "SELECT vp.fname, vp.lname FROM vc_patient AS vp WHERE vp.patient_id=".$patient_id;
	$data = mysqli_query($dbc, $query);
	if(mysqli_num_rows($data) != 1) {
		echo '<p class="error">Some error occured here.</p>';
		exit();
	}
	$row = mysqli_fetch_array($data);
	$name = $row['fname'].' '.$row['lname'];

	$query = "SELECT case_id FROM vc_case WHERE patient_id=".$patient_id." ORDER BY case_id DESC LIMIT 1";
	$data = mysqli_query($dbc, $query);
	if(mysqli_num_rows($data) == 1) {
		$nocase =  false;
		$row = mysqli_fetch_array($data);
		$case_id = $row['case_id'];

		$query = "SELECT COUNT(*) AS count FROM vc_case WHERE patient_id=".$patient_id;
		$data = mysqli_query($dbc, $query);
		if(mysqli_num_rows($data) != 1) {
			echo '<p class="error">Some error occured here.</p>';
			exit();
		}
		$row = mysqli_fetch_array($data);
		$case_no = $row['count'];
	}
	else {
		$nocase = true;
	}

	$nothercases = true;

	if(!$nocase) {
		$query = "SELECT vc.case_id, vc.complaint_id, vc.altname, vs.complaint, DATE(vc.date_created) AS date_created FROM vc_case AS vc INNER JOIN vc_complaint AS vs USING (complaint_id) WHERE patient_id=".$patient_id." ORDER BY vc.case_id DESC";
		$data = mysqli_query($dbc, $query);
		$othercases = array();
		$othertreatment = array();
		if(mysqli_num_rows($data) > 1) {
			$nothercases = false;
			mysqli_fetch_array($data); //skip first row since it is already displayed in detail
			$i = 0;
			while($row = mysqli_fetch_array($data)) {
				$othercases[$i]['case_id'] = $row['case_id'];
				$othercases[$i]['complaint_id'] = $row['complaint_id'];
				if($othercases[$i]['complaint_id'] == VC_COMPLAINT_UNLISTED)
					$othercases[$i]['altname'] = $row['altname'];
				$othercases[$i]['complaint'] = $row['complaint'];
				$date_arr = explode('-', $row['date_created']);
				$date_arr = array_reverse($date_arr);
				$othercases[$i]['date_created'] = implode('-', $date_arr);
				$query = "SELECT treatment_name_id, altname, dosage, before_food, duration FROM vc_treatment WHERE case_id=".$othercases[$i]['case_id'];
				$data_other_treatment = mysqli_query($dbc, $query);
				if(mysqli_num_rows($data_other_treatment) < 1)
					$othertreatment[$i] = null;
				else {
					$j = 0;
					while($row1 = mysqli_fetch_array($data_other_treatment)) {
						$othertreatment[$i][$j]['treatment_name_id'] = $row1['treatment_name_id'];
						if($othertreatment[$i][$j]['treatment_name_id'] == VC_TREATMENT_UNLISTED)
							$othertreatment[$i][$j]['altname'] = $row1['altname'];
						$othertreatment[$i][$j]['dosage'] = $row1['dosage'];
						$othertreatment[$i][$j]['before_food'] = $row1['before_food'];
						$othertreatment[$i][$j]['duration'] = $row1['duration'];
						$j++;
					}
				}
				$i++;
			}
			$mednames = array();
			$query = "SELECT tt.initial, tn.name, tn.strength, tt.unit FROM vc_treatment_name AS tn INNER JOIN vc_treatment_type AS tt USING (treatment_type_id) ORDER BY tn.treatment_name_id";
			$data = mysqli_query($dbc, $query);
			$k = 0;
			while($row2 = mysqli_fetch_array($data)) {
				$row2['treatment'] = $row2['initial'].'. '.$row2['name'].' '.substr($row2['strength'],0,5).$row2['unit'];
				$mednames[$k] = $row2;
				$k++;
			}
		}
	}
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="stylesheets/user.css">
	<link rel="stylesheet" href="stylesheets/patient-sidebar.css">
	<link rel="stylesheet" href="stylesheets/patient.css">
	<?php if(!$nocase) { ?>
	<link rel="stylesheet" href="stylesheets/viewcase.css">
	<script src="scripts/viewcase.js"></script>
	<link href="fancybox/jquery.fancybox.css" rel="stylesheet">
	<script src="scripts/jquery-1.11.0.min.js"></script>
	<script src="fancybox/jquery.fancybox.js"></script>
	<?php } ?>
</head>
<body>
	<div id="banner">
		<?php if($_SESSION['type'] == VC_TECHNICIAN) echo '<p><a title="Start New Case" href="'.VC_LOCATION.'technician/addcase.php?patient_id='.$_GET['patient_id'].'">Start New Case</a></p>'; ?>
		<h2><?php echo $name; ?></h2>
	</div>

	<div id="main-content">
		<?php require_once(VC_INCLUDE.'patient-sidebar.php'); ?>
		<div id="content">
			<?php if(!$nocase) require_once(VC_INCLUDE.'viewcase.php'); else echo '<div id="nocase">Click on <span class="username">Start New Case</span> to get started.</div>'; ?>
			<?php if(!$nothercases) { ?>
				<div id="othercases">
					<div id="othercases-top">
						<h3 id="othercases-heading">Previous Cases</h3>
					</div>
					<table>
						<tr id="othercases-heading-row">
							<th id="oc-width-6">Date</th>
							<th id="oc-width-1">Complaint</th>
							<th id="oc-width-2">Medicine</th>
							<th id="oc-width-3" class="treatment-center">Dosage</th>
							<th id="oc-width-4" class="treatment-center">Intake</th>
							<th id="oc-width-5" class="treatment-center">Length</th>
						</tr>
						<?php $othercases_color = false; ?>
						<?php for($i = 0; $i < count($othercases); $i++) { ?>
						<tr <?php if($othercases_color) echo 'class="othercases-color"'; ?>>
							<?php if($othertreatment[$i] == null) { $notreatment = true; $rowspan = 1; } else { $notreatment = false; $rowspan = count($othertreatment[$i]); } ?>
							<td rowspan="<?php echo $rowspan; ?>"><?php echo $othercases[$i]['date_created']; ?></td>
							<td rowspan="<?php echo $rowspan; ?>"><a href="case.php?case_id=<?php echo $othercases[$i]['case_id']; ?>&amp;patient_id=<?php echo $patient_id; ?>"><?php if($othercases[$i]['complaint_id'] == VC_COMPLAINT_UNLISTED) echo $othercases[$i]['altname']; else echo $othercases[$i]['complaint']; ?></a></td>
							<?php if($notreatment) { ?>
							<td colspan="4"><span class="nulldata">None prescribed.</span></td>
							<?php } else { $firstrow = true; foreach($othertreatment[$i] as $med) { ?>
							<?php if(!$firstrow) { if($othercases_color) echo '</tr><tr class="othercases-color">'; else echo '</tr><tr>'; } else $firstrow = false; ?>
							<td><?php if($med['treatment_name_id'] == VC_TREATMENT_UNLISTED) echo $med['altname']; else echo $mednames[$med['treatment_name_id']]['treatment']; ?></td>
							<td class="treatment-center"><?php echo $med['dosage']; ?></td>
							<td class="treatment-center"><?php if($med['before_food'] == "1") echo 'B/F'; else echo 'A/F'; ?></td>
							<td class="treatment-center"><?php echo $med['duration']; ?></td>
							<?php } } ?>
						</tr>
						<?php $othercases_color = !$othercases_color; } ?>
					</table>
				</div>
			<?php } ?>
		</div>
	</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
