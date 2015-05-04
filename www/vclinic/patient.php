<?php
	require_once('../../include/vclinic/usersession.php');

	if(!isset($_GET['patient_id'])) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');
	$query = "SELECT vp.fname, vp.lname, vp.gender, vp.birthdate, vp.occupation, va.line1, va.line2, va.city, va.district, vas.code, va.pincode, vp.email, vp.phone, vp.picture FROM vc_patient AS vp LEFT JOIN vc_address AS va USING (address_id) LEFT JOIN vc_address_state AS vas USING (state_id) WHERE vp.patient_id=".$_GET['patient_id'];
	$data = mysqli_query($dbc, $query);

	if(mysqli_num_rows($data) != 1) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	$row = mysqli_fetch_array($data);

	$name = $row['fname'].' '.$row['lname'];
	$pagetitle = 'Patient Profile';
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="stylesheets/user.css">
<link rel="stylesheet" href="stylesheets/profile.css">
<link rel="stylesheet" href="stylesheets/patient.css">

<?php require_once(VC_INCLUDE.'header.php'); ?>

<div id="banner">
	<h2><?php echo $name; ?></h2>
	<?php if($_SESSION['type'] == VC_TECHNICIAN) echo '<p><a title="Start New Case" href="#">Start New Case</a></p>'; ?>
</div>
<?php require_once(VC_INCLUDE.'chat.php'); ?>
<div id="main-content">
	<div id="sidebar">
		<?php echo '<img src="'.VC_UPLOADPATH.$row['picture'].'" alt="Profile Picture">'."\n"; ?>
		<div id="details">
			<table>
				<tr>
					<th>Gender: </th>
					<td><?php if(empty($row['gender'])) echo '<span class="nulldata">Not set.</span>'; else if($row['gender'] == 'm') echo 'Male'; else echo 'Female'; ?></td>
				</tr>
				<tr>
					<th>Birth Date: </th>
					<td><?php if(empty($row['birthdate'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['birthdate']; ?></td>
				</tr>
				<tr>
					<th>Phone No.: </th>
					<td><?php if(empty($row['phone'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['phone']; ?></td>
				</tr>
			</table>
			<div id="details-inner-1">
				<?php
					echo '<span class="heading">Email: </span>';
					if(empty($row['email'])) 
						echo '<span class="nulldata lastline">Not set.</span>'; 
					else 
						echo '<span class="lastline">'.$row['email'].'</span>';

					echo '<span class="heading">Occupation: </span>';
					if(empty($row['occupation'])) 
						echo '<span class="nulldata lastline">Not set.</span>'; 
					else 
						echo '<span class="lastline">'.$row['occupation'].'</span>';

					echo '<span class="heading">Address: </span>'."\n";
					if(empty($row['line1']) && empty($row['line2']) && empty($row['city']) && empty($row['district']) && empty($row['code']) && empty($row['pincode']))
						echo '<span class="nulldata">Not set.</span>'."\n";
					else {
						if(!empty($row['line1']))
							echo $row['line1'].'<br>';
						if(!empty($row['line2']))
							echo $row['line2'].'<br>';
						if(!empty($row['city']))
							echo $row['city'].'<br>';
						if(!empty($row['district']))
							echo $row['district'].'<br>';
						echo '<span class="lastline">';
						if(!empty($row['code']))
							echo $row['code'].' ';
						if(!empty($row['pincode']))
							echo $row['pincode'];
						echo '</span>'."\n";
					} 
				?>
			</div>
			<?php
				if($_SESSION['type'] == VC_TECHNICIAN) {
			?>		
			<div id="details-inner-2">
				<a title="Edit Details" href="<?php echo VC_LOCATION.'technician/editpatient.php?patient_id='.$_GET['patient_id']; ?>">Edit Details</a>
			</div>
			<?php
				}
			?>
		</div>
	</div>
	<div id="content">

	</div>
</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
