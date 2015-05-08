<?php 
	require_once('../../../include/vclinic/techniciansession.php');

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$query = "SELECT * FROM vc_test_name";
	$data_tests = mysqli_query($dbc, $query);
	if(!$data_tests) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}

	$pagetitle = "Add Test";
	$nochat = true;

	$rows = 1;
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/addtest.css'; ?>">
	<script src="<?php echo VC_LOCATION.'scripts/addtest.js'; ?>"></script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<h2>Add Test</h2>
		</div>
		<div id="wrapper-form">
			<form method="POST" enctype="multipart/form-data" action="#">
				<input type="hidden" id="num-rows" name="num-rows" value="<?php echo $rows ?>">
				<table>
					<tr>
						<th><label for="test">Test</label></th>
						<th class="hidden-col"><label for="altname">Name</label></th>
						<th><label for="result">Result</label></th>
						<th><label for="file">File</label></th>
					</tr>
					<tr>
						<td>
							<select id="test-1" name="test-1">
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
						<td><a id="add-link" title="Add another test" href="#">Add</a></td>
					</tr>
					<tr>
						<td id="submit-cell"><input type="submit" id="submit" name="submit"></td>
						<td><a id="cancel-test" href="#" title="Cancel">Cancel</a></td>
					</tr>
				</table>
			</form>
		</div>

<?php require_once('../'.VC_INCLUDE.'footer.php'); ?>