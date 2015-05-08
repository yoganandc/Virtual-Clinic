<?php
	$query_case = "SELECT complaint_id, altname, chronic, patient_history, past_history, personal_history, family_history, examination FROM vc_case WHERE case_id=".$case_id;
	$data_case = mysqli_query($dbc, $query_case);
	if(mysqli_num_rows($data_case) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$row_case = mysqli_fetch_array($data_case);

	$query_case = "SELECT complaint, chronic_only FROM vc_complaint WHERE complaint_id=".$row_case['complaint_id'];
	$data_case = mysqli_query($dbc, $query_case);
	if(mysqli_num_rows($data_case) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$row_temp = mysqli_fetch_array($data_case);
	$row_case['complaint'] = $row_temp['complaint'];
	$row_case['chronic_only'] = $row_temp['chronic_only'];

	if($row_case['complaint_id'] == VC_COMPLAINT_UNLISTED)
		$title = $row_case['altname'];
	else 
		$title = $row_case['complaint'];

	if($row_case['chronic'] == '0')
		$type = "Acute";
	else
		$type = "Chronic";

	$row_case['patient_history'] = preg_replace("/\r\n/", "<br>", $row_case['patient_history']);
	$row_case['past_history'] = preg_replace("/\r\n/", "<br>", $row_case['past_history']);
	$row_case['personal_history'] = preg_replace("/\r\n/", "<br>", $row_case['personal_history']);
	$row_case['family_history'] = preg_replace("/\r\n/", "<br>", $row_case['family_history']);
	$row_case['examination'] = preg_replace("/\r\n/", "<br>", $row_case['examination']);

	$query_case = "SELECT COUNT(*) AS count_test FROM vc_test WHERE case_id=".$case_id;
	$data_case = mysqli_query($dbc, $query_case);
	if(mysqli_num_rows($data_case) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$row_temp = mysqli_fetch_array($data_case);
	$row_case['count_test'] = $row_temp['count_test'];

	$query_case = "SELECT COUNT(*) AS count_treatment FROM vc_treatment WHERE case_id=".$case_id;
	$data_case = mysqli_query($dbc, $query_case);
	if(mysqli_num_rows($data_case) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$row_temp = mysqli_fetch_array($data_case);
	$row_case['count_treatment'] = $row_temp['count_treatment'];
?>
<div class="case">
	<a class="case-edit" title="Edit Case" href="#">Edit Case</a>
	<h3 class="case-heading"><?php if(isset($case_no)) echo '#'.$case_no.' '; echo $title; ?></h3>
	<div class="case-content">
		<table>
			<tr>
				<th>Type: </th>
				<td><?php echo $type; ?></td>
			</tr>
		</table>
		<hr>
		<table>
			<tr>
				<th>Patient History: </th>
				<td><?php if(empty($row_case['patient_history'])) echo '<span class="nulldata">Not entered.</span>'; else echo $row_case['patient_history']; ?></td>
			</tr>
			<tr>
				<th>Past History: </th>
				<td><?php if(empty($row_case['past_history'])) echo '<span class="nulldata">Not entered.</span>'; else echo $row_case['past_history']; ?></td>
			</tr>
			<tr>
				<th>Personal History: </th>
				<td><?php if(empty($row_case['personal_history'])) echo '<span class="nulldata">Not entered.</span>'; else echo $row_case['personal_history']; ?></td>
			</tr>
			<tr>
				<th>Family History: </th>
				<td><?php if(empty($row_case['family_history'])) echo '<span class="nulldata">Not entered.</span>'; else echo $row_case['family_history']; ?></td>
			</tr>
		</table>
		<br>
		<hr>
		<br>
		<table>
			<tr>
				<th>Examination: </th>
				<td><?php if(empty($row_case['examination'])) echo '<span class="nulldata">Not entered.</span>'; else echo $row_case['examination']; ?></td>
			</tr>
		</table>
		<br>
		<hr>
		<table>
			<tr>
				<th class="test-heading">Tests: </th>
				<td>
					<ul>
						<?php
							if(($row_case['count_test'] == "0") && ($_SESSION['type'] == VC_DOCTOR)) {
								echo '<li><span class="nulldata">No tests conducted.</span></li>';
							}
							else {

							}
						?>
						<?php if($_SESSION['type'] == VC_TECHNICIAN) echo '<li><a id="add-test" title="Add Test" href="#">Add Test</a></li>'; ?>
					</ul>
				</td>
			</tr>
		</table>
		<hr>
		<table>	
			<tr>
				<th class="test-heading">Treatment: </th>
				<td>
					<ul>
						<?php
							if($row_case['count_treatment'] != "0") {
								
							}
						?>
						<?php echo '<li><a id="add-treatment" title="Add Prescription" href="#">Add Prescription</a></li>'; ?>
					</ul>
				</td>
			</tr>
		</table>
	</div>
</div>
