<?php
	require_once('../../../include/vclinic/techniciansession.php');

	define('VC_TITLE_LENGTH', '40');

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

		$files[0]['title'] = "";
	}
	else if(isset($_POST['submit'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
		$case_id = mysqli_real_escape_string($dbc, trim($_POST['case_id']));
		$rows = intval(mysqli_real_escape_string($dbc, trim($_POST['num-rows'])));

		$files = array();
		$count = 0;
		$i = 1;
		while($count < $rows) {
			$key = 'title-'.$i;
			if(isset($_POST[$key])) {
				$file['title'] = mysqli_real_escape_string($dbc, trim($_POST[$key]));
				$file['file_name'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['name']));
				$file['file_type'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['type']));
				$file['file_size'] = mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['size']));
				$file['file_src_location'] = str_replace('\\\\', '\\', mysqli_real_escape_string($dbc, trim($_FILES['file-'.$i]['tmp_name'])));
				$file['file_error'] = $_FILES['file-'.$i]['error'];
				array_push($files, $file);
				$count++;
			}
			$i++;
		}

		$i = 0;
		foreach($files as $file) {
			if((!empty($file['title'])) && (strlen($file['title']) <= VC_TITLE_LENGTH)) {
				if(!empty($file['file_name'])) {
					if(is_valid_filetype($file['file_type'])) {
						if(($file['file_size'] > 0) && ($file['file_size'] <= VC_MAXFILESIZE)) {
							if($file['file_error'] == 0) {
								$folder_name = 'cases/case-'.$case_id;
								if(!file_exists('../'.$folder_name))
									mkdir('../'.$folder_name, 0777, true);
								$query = "INSERT INTO vc_case_file (case_id, title, filename) VALUES (".$case_id.", '".$file['title']."', 'placeholder')";
								mysqli_query($dbc, $query);
								$query = "SELECT LAST_INSERT_ID() AS case_file_id";
								$data = mysqli_query($dbc, $query);
								$row = mysqli_fetch_array($data);
								$filename = $row['case_file_id'].'-'.time().'.'.pathinfo($file['file_name'], PATHINFO_EXTENSION);
								$file_dest_location = $_SERVER['DOCUMENT_ROOT'].'/vclinic/'.$folder_name.'/'.$filename;
								if(move_uploaded_file($file['file_src_location'], $file_dest_location)) {
									$query = "UPDATE vc_case_file SET filename='$filename' WHERE case_file_id=".$row['case_file_id'];
									if(mysqli_query($dbc, $query)) {
										continue;
									}
									else {
										unlink_all_files($files);
										echo '<p class="error">Some error occured.</p>';
										exit();
									}
								}
								else {
									unlink_all_files($files);
									echo '<p class="error">Some error occured.</p>';
									exit();
								}
							}
							else {
								unlink_all_files($files);
								$showerror = true;
								$error = "Some error occured while uploading the attached files.";
							}
						}
						else {
							unlink_all_files($files);
							$showerror = true;
							$error = "Maximum filesize is ".(VC_MAXFILESIZE/(1024*1024))."MB.";
						}
					}
					else {
						unlink_all_files($files);
						$showerror = true;
						$error = "You can only upload files of the following types: .odt, .ods, .doc/.docx, .xls/.xlsx, .pdf, .jpg, .png, .bmp, & .gif";
					}
				}
				else {
					unlink_all_files($files);
					$showerror = true;
					$error = "Every entry must have a file attached.";
				}
			}
			else {
				unlink_all_files($files);
				$showerror = true;
				$error = "Every entry must have a title/description of max. 40 characters.";
			}
		}
		if(!$showerror) {
			header('Location: '.VC_LOCATION.'closewindow.php');
			exit();
		}
	}
	else {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/addtest.css'; ?>">
	<script src="<?php echo VC_LOCATION.'scripts/addfile.js'; ?>"></script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<p><a id="add-link" title="Add another file" href="#">Add&nbsp;Row</a></p>
			<h2>Add File</h2>
		</div>
		<div id="wrapper-form">
			<?php if($showerror) echo '<p class="error">'.$error.'</p>'; ?>
			<form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
				<input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id ?>">
				<input type="hidden" id="num-rows" name="num-rows" value="<?php echo $rows ?>">
				<table>
					<tr>
						<th>Title / Description</th>
						<th>Location</th>
						<th></th>
					</tr>
					<?php $i=1; foreach($files as $file) { ?>
					<tr>
						<td><input type="text" id="title-<?php echo $i; ?>" name="title-<?php echo $i; ?>" value="<?php echo $file['title']; ?>"></td>
						<td><input type="file" class="file" id="file-<?php echo $i; ?>" name="file-<?php echo $i; ?>"></td>
						<td><a id="remove-link-<?php echo $i; ?>" class="remove-link" title="Remove this file" href="#">Remove</a></td>
					</tr>
					<?php } ?>
					<tr>
						<td id="submit-cell"><input type="submit" id="submit" name="submit"></td>
						<td><a id="cancel-test" href="#" title="Cancel">Cancel</a></td>
						<td></td>
					</tr>
				</table>
			</form>
		</div>

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
