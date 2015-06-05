<?php
	require_once('../../../include/vclinic/techniciansession.php');

	define('VC_TITLE_LENGTH', '40');

	$showerror = false;
	$error = "";
	$photo_success = false;

	if(isset($_GET['case_id'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
		$case_id = mysqli_real_escape_string($dbc, trim($_GET['case_id']));
	}
	else if(isset($_POST['submit'])) {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
		$case_id = mysqli_real_escape_string($dbc, trim($_POST['case_id']));

		if(!empty($_POST['title'])) {
			$title = mysqli_real_escape_string($dbc, trim($_POST['title']));
			if(strlen($title) > VC_TITLE_LENGTH) {
				$showerror = true;
				$error = "Title / description must be limited to 40 characters.";
			}
		}
		else {
			$showerror = true;
			$error = "You must specify a title / description.";
		}

		if(!$showerror) {
			if(!empty($_POST['encoded_picture'])) {
				$photo = mysqli_real_escape_string($dbc, trim($_POST['encoded_picture']));
				$decoded_photo = base64_decode($photo);
			}
			else {
				$showerror = true;
				$error = "You have not taken a photo yet.";
			}
		}

		if(!$showerror) {
			$folder_name = 'cases/case-'.$case_id;
			if(!file_exists('../'.$folder_name))
				mkdir('../'.$folder_name, 0777, true);

			$query = "INSERT INTO vc_case_file (case_id, title, filename) VALUES (".$case_id.", '".$title."', 'placeholder')";
			mysqli_query($dbc, $query);
			$query = "SELECT LAST_INSERT_ID() AS case_file_id";
			$data = mysqli_query($dbc, $query);
			$row = mysqli_fetch_array($data);

			$filename = $row['case_file_id'].'-'.time().'.png';
			$file_dest_location = $_SERVER['DOCUMENT_ROOT'].'/vclinic/'.$folder_name.'/'.$filename;
			$fp = fopen($file_dest_location, 'w');
			fwrite($fp, $decoded_photo);
			fclose($fp);

			$query = "UPDATE vc_case_file SET filename='$filename' WHERE case_file_id=".$row['case_file_id'];
			mysqli_query($dbc, $query);

			$photo_success = true;
		}
	}
	else {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

	<?php if(!$photo_success) { ?>
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/addtest.css'; ?>">
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/addphoto.css'; ?>">
	<script src="<?php echo VC_LOCATION.'scripts/addphoto.js'; ?>"></script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<h2>Add Photo</h2>
		</div>
		<div id="wrapper-form">
			<?php if($showerror) echo '<p class="error">'.$error.'</p>'; ?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="hidden" id="encoded_picture" name="encoded_picture" value="">
				<input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id ?>">
				<table>
					<tr>
						<th style="text-align: right">Title/Description: </th>
						<td><input type="text" id="title" name="title" value="<?php if(isset($title)) echo $title; ?>"></td>
					</tr>
					<tr>
						<td>
							<div id="camera">
							    <video id="video" autoplay>Video stream not available.</video>
							    <button id="startbutton">Take photo</button>
							</div>
							<canvas id="canvas">
							</canvas>
						</td>
						<td>
							<div id="output">
							    <img id="photo" alt="Photo taken will appear here." src="#">
							</div>
						</td>
					</tr>
					<tr>
						<td id="submit-cell"><input type="submit" id="submit" name="submit"></td>
						<td><a id="cancel-test" href="#" title="Cancel">Cancel</a></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<?php } else { ?>
	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/closewindow.css'; ?>">
	<script src="<?php echo VC_LOCATION.'scripts/sendreload.js'; ?>"></script>
</head>
<body>
	<div id="wrapper" data-case-id="<?php echo $case_id; ?>">
		<div id="wrapper-form">
			<p id="success-message">Uploading...</p>
		</div>
	</div>
	<?php } ?>

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
