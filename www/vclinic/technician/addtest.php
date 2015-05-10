<?php 
	require_once('../../../include/vclinic/techniciansession.php');

	define('VC_TEST_UNLISTED', '4');
	define('VC_RESULT_LENGTH', '40');

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
			@unlink($test['file_src_location']);
		}
	}

	$showerror = false;
	$error = "";
	$hidden = "1";

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

		$available_tests = array();
		while($row_test = mysqli_fetch_array($data_tests))
			array_push($available_tests, $row_test);

		$pagetitle = "Add Test";
		$nochat = true;
		$tests[0]['test'] = 1;
		$tests[0]['result'] = "";
	}
	else if(isset($_POST['submit'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
		$case_id = mysqli_real_escape_string($dbc, trim($_POST['case_id']));
		$rows = intval(mysqli_real_escape_string($dbc, trim($_POST['num-rows'])));

		$tests = array();
		$count = 0;
		$i = 1;
		while($count < $rows) {
			$key = 'test-'.$i;
			if(isset($_POST[$key])) {
				$test['test'] = mysqli_real_escape_string($dbc, trim($_POST[$key]));
				if($test['test'] == VC_TEST_UNLISTED) {
					$hidden = "0";
					$test['altname'] = mysqli_real_escape_string($dbc, trim($_POST['altname-'.$i]));
				}
				$test['result'] = mysqli_real_escape_string($dbc, trim($_POST['result-'.$i]));
				$test['file_name'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['name']));
				$test['file_type'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['type']));
				$test['file_size'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['size']));
				$test['file_src_location'] = str_replace('\\\\', '\\', mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['tmp_name'])));
				$test['file_error'] = $_FILES['file-'.$i]['error'];
				array_push($tests, $test);
				$count++;
			}
			$i++;
		}

		$i = 0;
		foreach($tests as $test) {
			if(!empty($test['file_name'])) {
				if(is_valid_filetype($test['file_type'])) {
					if(($test['file_size'] > 0) && ($test['file_size'] <= VC_MAXFILESIZE)) {
						if($test['file_error'] == 0) {
							$folder_name = 'cases/case-'.$case_id;
							if(!file_exists('../'.$folder_name))
								mkdir('../'.$folder_name, 0777, true);
							$tests[$i]['file_uploaded'] = 1;
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
				$tests[$i]['file_uploaded'] = 0;
			}
			$i++;
		}

		if(!$showerror) {
			$i = 0;
			foreach($tests as $test) {
				if($test['test'] != VC_TEST_UNLISTED) {
					if((strlen($test['result']) > 0) && (strlen($test['result']) <= VC_RESULT_LENGTH)) {
						$tests[$i]['query'] = "INSERT INTO vc_test (test_name_id, case_id, altname, result, filename) VALUES (".$test['test'].", $case_id, NULL, '".$test['result']."', NULL)";
					}
					else {
						unlink_all_files($tests);
						$showerror = true;
						$error = "Result entered must be between 1 and 40 characters.";
					}
				}
				else {
					if((strlen($test['altname']) > 0) && (strlen($test['altname']) <= VC_RESULT_LENGTH)) {
						if((strlen($test['result']) > 0) && (strlen($test['result']) <= VC_RESULT_LENGTH)) {
							$tests[$i]['query'] = "INSERT INTO vc_test (test_name_id, case_id, altname, result, filename) VALUES (".$test['test'].", $case_id, '".$test['altname']."', '".$test['result']."', NULL)";
						}
						else {
							unlink_all_files($tests);
							$showerror = true;
							$error = "Result entered must be between 1 and 40 characters.";
						}
					}
					else {
						unlink_all_files($tests);
						$showerror = true;
						$error = "Name entered must be between 1 and 40 characters.";
					}
				}
				$i++;
			}
		}

		if(!$showerror) {
			foreach($tests as $test) {
				if(mysqli_query($dbc, $test['query'])) {
					if($test['file_uploaded']) {
						$query = "SELECT LAST_INSERT_ID() AS test_id";
						$data = mysqli_query($dbc, $query);
						if(mysqli_num_rows($data) == 1) {
							$row = mysqli_fetch_array($data);
							$filename = $row['test_id'].'-'.time().'.'.pathinfo($test['file_name'], PATHINFO_EXTENSION);
							$file_dest_location = $_SERVER['DOCUMENT_ROOT'].'/vclinic/'.$folder_name.'/'.$filename;
							if(move_uploaded_file($test['file_src_location'], $file_dest_location)) {
								$query = "UPDATE vc_test SET filename='$filename' WHERE test_id=".$row['test_id'];
								if(mysqli_query($dbc, $query)) {
									header('Location: '.VC_LOCATION.'technician/closewindow.php');
									exit();
								}
								else {
									unlink_all_files($tests);
									echo '<p class="error">Some error occured.</p>';
									exit();
								}
							}
							else {
								echo $test['file_src_location'];
								unlink_all_files($tests);
								echo '<p class="error">Some error occured.</p>';
								exit();
							}
						}
						else {
							unlink_all_files($tests);
							echo '<p class="error">Some error occured.</p>';
							exit();
						}
					}
					else {
						header('Location: '.VC_LOCATION.'technician/closewindow.php');
						exit();
					}
				}
				else {
					unlink_all_files($tests);
					echo '<p class="error">Some error occured.</p>';
					exit();
				}
			}
		}

		$query = "SELECT * FROM vc_test_name";
		$data_tests = mysqli_query($dbc, $query);
		if(!$data_tests) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}

		$available_tests = array();
		while($row_test = mysqli_fetch_array($data_tests))
			array_push($available_tests, $row_test);

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
						<th data-hidden="<?php echo $hidden; ?>" class="hidden-col"><label>Name</label></th>
						<th><label>Result</label></th>
						<th><label>File</label></th>
						<th></th>
					</tr>
					<?php $i = 1; foreach($tests as $test) { ?>
					<tr>
						<td>
							<select id="test-<?php echo $i; ?>" class="test" name="test-<?php echo $i; ?>">
								<?php 
									foreach($available_tests as $available_test) {
										if($test['test'] == $available_test['test_name_id'])
											echo '<option value="'.$available_test['test_name_id'].'" selected="selected">'.$available_test['name'].'</option>'."\n";
										else 
											echo '<option value="'.$available_test['test_name_id'].'">'.$available_test['name'].'</option>'."\n";
									}
								?>
							</select>
						</td>
						<td class="hidden-col"><input type="text" id="altname-<?php echo $i; ?>" name="altname-<?php echo $i; ?>" value="<?php if(isset($test['altname'])) echo $test['altname']; ?>" <?php if(!isset($test['altname'])) echo 'disabled="disabled"'; ?>></td>
						<td><input type="text" id="result-<?php echo $i; ?>" name="result-<?php echo $i; ?>" value="<?php if(!empty($test['result'])) echo $test['result']; ?>"></td>
						<td><input type="file" id="file-<?php echo $i; ?>" class="file" name="file-<?php echo $i; ?>"></td>
						<td><a id="remove-link-<?php echo $i; ?>" class="remove-link" title="Remove this test" href="#">Remove</a></td>
					</tr>
					<?php $i++; } ?>
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