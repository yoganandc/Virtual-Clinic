<?php
	require_once('../../../include/vclinic/techniciansession.php');
	require_once('../'.VC_INCLUDE.'library.php');

	$showerror = false;
	$error = "";

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database');
	$query = "SELECT state_id, name FROM vc_address_state ORDER BY name";
	$data = mysqli_query($dbc, $query);

	if(!$data) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}

	$states = array();
	while($result = mysqli_fetch_array($data)) 
	array_push($states, $result);

	if(isset($_GET['patient_id'])) {
		$query = "SELECT vp.fname, vp.lname, vp.gender, vp.birthdate, vp.occupation, va.line1, va.line2, va.city, va.district, va.state_id, va.pincode, vp.email, vp.phone, vp.picture FROM vc_patient AS vp LEFT JOIN vc_address AS va USING (address_id) WHERE vp.patient_id=".$_GET['patient_id'];
		$data = mysqli_query($dbc, $query);

		if(mysqli_num_rows($data) != 1) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}

		$row = mysqli_fetch_array($data);

		$fname = $row['fname'];
		$lname = $row['lname'];
		$gender = $row['gender'];
		$birthdate = $row['birthdate'];
		$occupation = $row['occupation'];
		$line1 = $row['line1'];
		$line2 = $row['line2'];
		$city = $row['city'];
		$district = $row['district'];
		$state = $row['state_id'];
		$pincode = $row['pincode'];
		$email = $row['email'];
		$phone = $row['phone'];
		$picture = $row['picture'];

		$display_fname = $row['fname'];
		$display_lname = $row['lname'];
		$display_picture = $row['picture'];		
	}
	else if(isset($_POST['submit'])) {
		$query = "SELECT fname, lname, picture FROM vc_patient WHERE patient_id=".$_POST['patient_id'];
		$data = mysqli_query($dbc, $query);
		if(mysqli_num_rows($data) != 1) {
			echo '<p class="error">Some error occured</p>';
			exit();
		}
		$row = mysqli_fetch_array($data);

		$display_fname = $row['fname'];
		$display_lname = $row['lname'];
		$display_picture = $row['picture'];

		$fname = mysqli_real_escape_string($dbc, trim($_POST['fname']));
		$lname = mysqli_real_escape_string($dbc, trim($_POST['lname']));
		if(isset($_POST['gender']))
			$gender = mysqli_real_escape_string($dbc, trim($_POST['gender']));
		else 
			$gender = NULL;
		$birthdate = mysqli_real_escape_string($dbc, trim($_POST['birthdate']));
		$occupation = mysqli_real_escape_string($dbc, trim($_POST['occupation']));
		$line1 = mysqli_real_escape_string($dbc, trim($_POST['line1']));
		$line2 = mysqli_real_escape_string($dbc, trim($_POST['line2']));
		$city = mysqli_real_escape_string($dbc, trim($_POST['city']));
		$district = mysqli_real_escape_string($dbc, trim($_POST['district']));
		$state = mysqli_real_escape_string($dbc, trim($_POST['state']));
		$pincode = mysqli_real_escape_string($dbc, trim($_POST['pincode']));
		$email = mysqli_real_escape_string($dbc, trim($_POST['email']));
		$phone = mysqli_real_escape_string($dbc, trim($_POST['phone']));

		$picture_name = mysqli_real_escape_string($dbc, trim($_FILES['picture']['name']));
		$picture_type = mysqli_real_escape_string($dbc, trim($_FILES['picture']['type']));
		$picture_size = mysqli_real_escape_string($dbc, trim($_FILES['picture']['size']));
		$picture_src_location = mysqli_real_escape_string($dbc, trim($_FILES['picture']['tmp_name']));

		if(!empty($fname) && !empty($lname)) {
			if(!preg_match(VC_PATTERN_NAME, $fname)) {
				$showerror = true;
				$error = "First name must contain between 2 and 40 letters only.";
			}
			if(!preg_match(VC_PATTERN_NAME, $lname)) {
				$showerror = true;
				$error = "Last name must contain between 2 and 40 letters only.";
			}
			if(!preg_match(VC_PATTERN_OCCUPATION, $occupation)) {
				$showerror = true;
				$error = "Occupation field exceeds 40 characters.";
			}
			if(!preg_match(VC_PATTERN_CITY, $city)) {
				$showerror = true;
				$error = "Entered city must contain a maximum of 40 letters only.";
			}
			if(!preg_match(VC_PATTERN_CITY, $district)) {
				$showerror = true;
				$error = "Entered district must contain a maximum of 40 letters only.";
			}
			if(!empty($pincode)) {
				if(!preg_match(VC_PATTERN_PINCODE, $pincode)) {
					$showerror = true;
					$error = "Pincode must consist of six numbers exactly.";
				}
			}
			if(!preg_match(VC_PATTERN_ADDRESS, $line1)) {
				$showerror = true;
				$error = "Line 1 of address exceed 80 characters.";
			}
			if(!preg_match(VC_PATTERN_ADDRESS, $line2)) {
				$showerror = true;
				$error = "Line 2 of address exceeds 80 characters.";
			}
			if(!check_email($email)) {
				$showerror = true;
				$error = "You have not entered a valid email address.";
			}
			if(!preg_match(VC_PATTERN_PHONE, $phone)) {
				$showerror = true;
				$error = "You have not entered a valid phone number.";
			}
			if(!$showerror) {
				if(!empty($picture_name)) {
					if(($picture_type == 'image/png') || ($picture_type == 'image/pjpeg') || ($picture_type == 'image/jpeg')) {
						if(($picture_size > 0) && ($picture_size <= VC_MAXFILESIZE)) {
							if($_FILES['picture']['error'] == 0) {
								list($width, $height) = getimagesize($picture_src_location);

								if ($width < $height) 
									$side = $width;
								else
									$side = $height;
								$image_resampled = imagecreatetruecolor(VC_DPWIDTH, VC_DPHEIGHT);
								if(($picture_type == 'image/jpeg') || ($picture_type == 'image/pjpeg')) 
									$image_original = imagecreatefromjpeg($picture_src_location);
								else
									$image_original = imagecreatefrompng($picture_src_location);

								imagecopyresampled($image_resampled, $image_original, 0, 0, 0, 0, VC_DPWIDTH, VC_DPHEIGHT, $side, $side);

								$picture = $_SESSION['user_id'].'-'.time().'-p.png';
								$picture_dest_location = '../'.VC_UPLOADPATH.$picture;

								imagepng($image_resampled, $picture_dest_location, 0);

								unlink($picture_src_location);
								if($display_picture != "default.png")
									unlink(VC_UPLOADPATH.$display_picture);
							}
							else {
								$showerror = true;
								$error = "An error related to the uploaded picture occured.";
							}
						}
						else {
							$showerror = true;
							$error = "Picture size cannot exceed ".(VC_MAXFILESIZE/(1024*1024))."MB.";
						}
					}
					else {
						$showerror = true;
						$error = "Picture format can only be JPG or PNG.";
					} 
				}
				else {
					$picture = $display_picture;
				}
			}
			if(!$showerror) {
				$query = "SELECT address_id FROM vc_patient WHERE patient_id=".$_POST['patient_id'];
				$data = mysqli_query($dbc, $query);
				if(mysqli_num_rows($data) != 1) {
					echo '<p class="error">Some error occured.</p>';
					exit();
				}
				$row = mysqli_fetch_array($data);
				if(empty($row['address_id'])) {
					if(empty($line1) && empty($line2) && empty($city) && empty($district) && empty($state) && empty($pincode))
						$query_address = "address_id=NULL, ";
					else {
						$query = "INSERT INTO vc_address (line1, line2, city, district, state_id, pincode) VALUES (";

						if(empty($line1))
							$query .= "NULL, ";
						else
							$query .= "'$line1', ";

						if(empty($line2))
							$query .= "NULL, ";
						else
							$query .= "'$line2', ";

						if(empty($city))
							$query .= "NULL, ";
						else
							$query .= "'$city', ";

						if(empty($district))
							$query .= "NULL, ";
						else
							$query .= "'$district', ";

						if($state == '0')
							$query .= "NULL, ";
						else 
							$query .= "'$state', ";

						if(empty($pincode))
							$query .= "NULL)";
						else
							$query .= "'$pincode')";

						//echo $query;

						if(mysqli_query($dbc, $query)) {
							$query = "SELECT LAST_INSERT_ID()";
							$data = mysqli_query($dbc, $query);
							if(mysqli_num_rows($data) != 1) {
								echo '<p class="error">Some error occured.</p>';
								exit();
							}
							$row = mysqli_fetch_array($data);
							$query_address = "address_id='".$row['LAST_INSERT_ID()']."', ";
						}
						else {
							echo '<p class="error">Some error occured.</p>';
							exit();
						}
					}
				}
				else {
					$query = "UPDATE vc_address SET ";

					if(empty($line1))
						$query .= "line1=NULL, ";
					else
						$query .= "line1='$line1', ";

					if(empty($line2))
						$query .= "line2=NULL, ";
					else
						$query .= "line2='$line2', ";

					if(empty($city))
						$query .= "city=NULL, ";
					else
						$query .= "city='$city', ";

					if(empty($district))
						$query .= "district=NULL, ";
					else
						$query .= "district='$district', ";

					if($state == '0')
						$query .= "state_id=NULL, ";
					else 
						$query .= "state_id='$state', ";

					if(empty($pincode))
						$query .= "pincode=NULL ";
					else
						$query .= "pincode='$pincode' ";

					$query .= "WHERE address_id=".$row['address_id'];
					if(mysqli_query($dbc, $query)) 
						$query_address = "address_id='".$row['address_id']."', ";
					else {
						echo '<p class="error">Some error occured.</p>';
						exit();
					}
				}
			
				$query = "UPDATE vc_patient SET fname ='$fname', lname='$lname', ";

				if(empty($gender))
					$query .= "gender=NULL, ";
				else 
					$query .= "gender='$gender', ";

				if(empty($birthdate))
					$query .= "birthdate=NULL, ";
				else
					$query .= "birthdate='$birthdate', ";

				if(empty($occupation))
					$query .= "occupation=NULL, ";
				else
					$query .= "occupation='$occupation', ";

				$query .= $query_address;				

				if(empty($email))
					$query .= "email=NULL, ";
				else
					$query .= "email='$email', ";

				if(empty($phone))
					$query .= "phone=NULL, ";
				else
					$query .= "phone='$phone', ";

				$query .= "picture='$picture' WHERE patient_id=".$_POST['patient_id'];

				if(mysqli_query($dbc, $query)) {
					mysqli_close($dbc);
					$url = VC_LOCATION.'patient.php?patient_id='.$_POST['patient_id'];
					header('Location: '.$url);
					exit();
				}
				else {
					echo '<p class="error">Some error occured.</p>';
					exit();
				}
			}
		}
		else {
			$showerror = true;
			$error = "First name and last name fields cannot be blank.";
		}
	}
	else {
		mysqli_close($dbc);
		header('Location: '.$VC_LOCATION);
		exit();
	}

	$pagetitle = 'Edit Details';
?>

<?php require_once('../'.VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/user.css'; ?>">
<link rel="stylesheet" href="<?php echo VC_LOCATION.'stylesheets/profile.css'; ?>">

<?php require_once('../'.VC_INCLUDE.'header.php'); ?>


<div id="banner">
	<h2><?php echo $display_fname.' '.$display_lname; ?></h2>
</div>
<?php require_once('../'.VC_INCLUDE.'chat.php'); ?>
<div id="main-content">
	<div id="sidebar">
		<?php echo '<img src="'.VC_LOCATION.VC_UPLOADPATH.$display_picture.'" alt="Profile Picture">'."\n"; ?>
	</div>
	<div id="content">
		<?php if($showerror) echo '<p class="error">'.$error.'</p>'."\n"; ?>
		<form enctype="multipart/form-data" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" id="patient_id" name="patient_id" value="<?php if(isset($_GET['patient_id'])) echo $_GET['patient_id']; else echo $_POST['patient_id']; ?>">
			<table>
				<tr>
					<th><label for="fname">First Name: </label></th>
					<td><input type="text" id="fname" name="fname" value="<?php if(!empty($fname)) echo $fname; ?>"></td>
				</tr>
				<tr>
					<th><label for="lname">Last Name: </label></th>
					<td><input type="text" id="lname" name="lname" value="<?php if(!empty($lname)) echo $lname; ?>"></td>
				</tr>
				<tr>
					<th>Gender: </th>
					<td>
						<input type="radio" id="gender_m" name="gender" value="m" <?php if(!empty($gender) && $gender == 'm') echo 'checked="checked"'; ?>><label for="gender_m">Male </label>
						<input type="radio" id="gender_f" name="gender" value="f" <?php if(!empty($gender) && $gender == 'f') echo 'checked="checked"'; ?>><label for="gender_f">Female </label>
					</td>
				</tr>
				<tr>
					<th><label for="birthdate">Birth Date: </label></th>
					<td><input type="text" id="birthdate" name="birthdate" value="<?php if(!empty($birthdate)) echo $birthdate; ?>"></td>
				</tr>
				<tr>
					<th><label for="occupation">Occupation: </label></th>
					<td><input type="text" id="occupation" name="occupation" value="<?php if(!empty($occupation)) echo $occupation; ?>"></td>
				</tr>
				<tr>
					<th>Address: </th>
					<td>
						<table>
							<tr>
								<th><label for="line1">Line 1: </label></th>
								<td><input type="text" id="line1" name="line1" value="<?php if(!empty($line1)) echo $line1; ?>"></td>
							</tr>
							<tr>
								<th><label for="line2">Line 2: </label></th>
								<td><input type="text" id="line2" name="line2" value="<?php if(!empty($line2)) echo $line2; ?>"></td>
							</tr>
							<tr>
								<th><label for="city">City: </label></th>
								<td><input type="text" id="city" name="city" value="<?php if(!empty($city)) echo $city; ?>"></td>
							</tr>
							<tr>
								<th><label for="district">District: </label></th>
								<td><input type="text" id="district" name="district" value="<?php if(!empty($district)) echo $district; ?>"></td>
							</tr>
							<tr>
								<th><label for="state">State: </label></th>
								<td>
									<select id="state" name="state">
										<?php
											echo '<option value="0">Do not set.</option>'."\n";
											foreach($states as $currentstate) {
												if(!empty($state) && $state == $currentstate['state_id'])
													echo '<option value="'.$currentstate['state_id'].'" selected="selected">'.$currentstate['name'].'</option>'."\n";
												else
													echo '<option value="'.$currentstate['state_id'].'">'.$currentstate['name'].'</option>'."\n";
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="pincode">Pincode: </label></th>
								<td><input type="text" id="pincode" name="pincode" value="<?php if(!empty($pincode)) echo $pincode; ?>"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<th><label for="email">Email: </label></th>
					<td><input type="text" id="email" name="email" value="<?php if(!empty($email)) echo $email; ?>"></td>
				</tr>
				<tr>
					<th><label for="phone">Phone: </label></th>
					<td><input type="text" id="phone" name="phone" value="<?php if(!empty($phone)) echo $phone; ?>"></td>
				</tr>
				<tr>
					<th><label for="picture">Picture: </label></th>
					<td><input type="file" id="picture" name="picture"></td>
				</tr>
				<tr>
					<th></th>
					<td><input type="submit" id="submit" name="submit"></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<?php require_once('../'.VC_INCLUDE.'footer.php');
