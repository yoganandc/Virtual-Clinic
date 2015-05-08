<?php 
	require_once('../../../include/vclinic/techniciansession.php');
	define('VC_TEST_UNLISTED', '4');

	function is_valid_filetype($file) {
		$filetypes = array("text/plain", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.oasis.opendocument.text", "application/vnd.oasis.opendocument.spreadsheet", "application/pdf", "image/jpeg", "image/pjpeg", "image/png", "image/bmp", "image/gif");
		foreach($filetypes as $filetype) {
			if($filetype == $file)
				return true;
		}
		return false;
	}

	function unlink_all_files($tests) {
		foreach($tests as $test) {
			unlink($test['file_src_location']);
		}
	}

	$showerror = false;
	$error = "";

	if(isset($_GET['case_id'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
		$case_id = mysqli_real_escape_string($dbc, trim($_GET['case_id']));
		$rows = 1;

		$query = "SELECT * FROM vc_test_name";
		$data_tests = mysqli_query($dbc, $query);
		if(!$data_tests) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}
		$pagetitle = "Add Test";
		$nochat = true;
	}
	else if(isset($_POST['submit'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
		$case_id = mysqli_real_escape_string($dbc, trim($_POST['case_id']));
		$rows = mysqli_real_escape_string($dbc, trim($_POST['num-rows']));

		$tests = array();
		$count = 0;
		$i = 1;
		while($count < $rows) {
			$key = 'test-'.$i;
			if(isset($_POST[$key])) {
				$test['test'] = mysqli_real_escape_string($dbc, trim($_POST[$key]));
				if($test['test'] == VC_TEST_UNLISTED) 
					$test['altname'] = mysqli_real_escape_string($dbc, trim($_POST['altname-'.$i]));
				$test['result'] = mysqli_real_escape_string($dbc, trim($_POST['result-'.$i]));
				$test['file_name'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['name']));
				$test['file_type'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['type']));
				$test['file_size'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['size']));
				$test['file_src_location'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['tmp_name']));
				$test['file_error'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['error']));
				array_push($tests, $test);
				$count++;
			}
			$i++;
		}

		foreach($tests as $test) {
			if(!empty($test['file_name'])) {
				if(is_valid_filetype($test['file_type'])) {
					if(($test['file_size'] > 0) && ($test['file_size'] <= VC_MAXFILESIZE)) {
						if($test['file_error'] == 0) {

						}
						else {
							unlink_all_files($tests);
							$showerror = true;
							$error = "Some error occured while uploading the attached files.";
						}
					}
					else {
						unlink_all_files($tests);
						$showerror = true;
						$error = "Maximum filesize is ".(VC_MAXFILESIZE/(1024*1024))."MB.";
					}
				}
				else {
					unlink_all_files($tests);
					$showerror = true;
					$error = "You can only upload files of the following types: .odt, .ods, .doc/.docx, .xls/.xlsx, .pdf, .jpg, .png, .bmp, & .gif";
				}
			}
			else {
				$test['file'] = null;
			}
		}

		$query = "SELECT * FROM vc_test_name";
		$data_tests = mysqli_query($dbc, $query);
		if(!$data_tests) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}
		$pagetitle = "Add Test";
		$nochat = true;
	}
	else {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}	
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/addtest.css'; ?>">
	<script src="<?php echo VC_LOCATION.'scripts/addtest.js'; ?>"></script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<p><a id="add-link" title="Add another test" href="#">Add&nbsp;Row</a></p>
			<h2>Add Test</h2>
		</div>
		<div id="wrapper-form">
			<?php if($showerror) echo '<p class="error">'.$error.'</p>'; ?>
			<form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id ?>">
				<input type="hidden" id="num-rows" name="num-rows" value="<?php echo $rows ?>">
				<table>
					<tr>
						<th><label>Test</label></th>
						<th data-hidden="1" class="hidden-col"><label>Name</label></th>
						<th><label>Result</label></th>
						<th><label>File</label></th>
						<th></th>
					</tr>
					<tr>
						<td>
							<select id="test-1" class="test" name="test-1">
								<?php 
									while($row_test = mysqli_fetch_array($data_tests)) {
										if(isset($test) && $test == $row_test['test_name_id'])
											echo '<option value="'.$row_test['test_name_id'].'" selected="selected">'.$row_test['name'].'</option>'."\n";
										else 
											echo '<option value="'.$row_test['test_name_id'].'">'.$row_test['name'].'</option>'."\n";
									}
								?>
							</select>
						</td>
						<td class="hidden-col"><input type="text" id="altname-1" name="altname-1" disabled="disabled" value="<?php if(isset($altname)) echo $altname; ?>"></td>
						<td><input type="text" id="result-1" name="result-1" value="<?php if(isset($result)) echo $result; ?>"></td>
						<td><input type="file" id="file-1" class="file" name="file-1"></td>
						<td><a id="remove-link-1" class="remove-link" title="Remove this test" href="#">Remove</a></td>
					</tr>
					<tr>
						<td id="submit-cell"><input type="submit" id="submit" name="submit"></td>
						<td><a id="cancel-test" href="#" title="Cancel">Cancel</a></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</form>
		</div>

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>