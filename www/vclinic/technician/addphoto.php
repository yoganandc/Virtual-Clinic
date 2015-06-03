<?php
	require_once('../../../include/vclinic/techniciansession.php');

	$showerror = false;
	$error = "";
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

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
			<form method="POST" action="#"> <!-- <?php echo $_SERVER['PHP_SELF']; ?> -->
				<input type="hidden" id="encoded_picture" name="encoded_picture" value="">
				<input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id ?>">
				<table>
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

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>
