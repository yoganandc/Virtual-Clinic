<?php
	function is_filetype_image($file) {
		$filetypes = array("image/jpeg", "image/pjpeg", "image/png", "image/bmp", "image/gif");
		foreach($filetypes as $filetype) {
			if($filetype == $file)
				return true;
		}
		return false;
	}

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

	if($row_case['count_test'] > 0) {
		$query_case = "SELECT * FROM vc_test WHERE case_id=".$case_id;
		$data_case = mysqli_query($dbc, $query_case);
		if(mysqli_num_rows($data_case) < 1) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}
		$data_case_tests = array();
		while($row_temp = mysqli_fetch_array($data_case)) {
			array_push($data_case_tests, $row_temp);
		}
	}

	$query_case = "SELECT COUNT(*) AS count_treatment FROM vc_treatment WHERE case_id=".$case_id;
	$data_case = mysqli_query($dbc, $query_case);
	if(mysqli_num_rows($data_case) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$row_temp = mysqli_fetch_array($data_case);
	$row_case['count_treatment'] = $row_temp['count_treatment'];

	$query_case = "SELECT * FROM vc_test_name";
	$data_case = mysqli_query($dbc, $query_case);
	if(mysqli_num_rows($data_case) < 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
	$data_test_names = array();
	$i = 1;
	while($row_temp = mysqli_fetch_array($data_case)) {
		$data_test_names[$i] = $row_temp['name'];
		$i++;
	}
?>
<div class="case" data-case_id="<?php echo $case_id; ?>">
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
		<br>
		<?php
			if(($row_case['count_test'] == "0") && ($_SESSION['type'] == VC_DOCTOR)) {
				echo '<ul><li><span class="nulldata">No tests conducted.</span></li></ul>';
			}
			else { 	
		?>
		<table>
			<tr>
				<th>Name</th>
				<th>Result</th>
				<th>File</th>
			</tr>
			<?php foreach($data_case_tests as $data_case_test) { ?>
			<tr>
				<td><?php $x = $data_case_test['test_name_id']; echo $data_test_names[$x]; ?></td>
				<td><?php echo $data_case_test['result']; ?></td>
				<td></td>
			</tr>
			<?php } ?>
		</table>
		<?php } if($_SESSION['type'] == VC_TECHNICIAN) echo '<ul><li><a id="add-test" title="Add Test" href="#">Add Test</a></li></ul>'; ?>
		<hr>
		<table>	
			<tr>
				<th class="test-heading">Treatment: </th>
				<td>
					<?php
						if($row_case['count_treatment'] != "0") {
							
						}
					?>
					<?php echo '<ul><li><a id="add-treatment" title="Add Prescription" href="#">Add Prescription</a></li></ul>'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
