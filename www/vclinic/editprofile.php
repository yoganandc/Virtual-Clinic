<?php 
	require_once('../../include/vclinic/usersession.php');
	require_once(VC_INCLUDE.'library.php');

	$showerror = false;
	$error = "";

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

	$query = "SELECT fname, lname, picture FROM vc_user WHERE user_id=".$_SESSION['user_id'];
	$data = mysqli_query($dbc, $query);

	if(mysqli_num_rows($data) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}

	$row = mysqli_fetch_array($data);

	$display_fname = $row['fname'];
	$display_lname = $row['lname'];
	$display_picture = $row['picture'];

	if(isset($_POST['submit'])) {
		$fname = mysqli_real_escape_string($dbc, trim($_POST['fname']));
		$lname = mysqli_real_escape_string($dbc, trim($_POST['lname']));
		$gender = mysqli_real_escape_string($dbc, trim($_POST['gender']));
		$birthdate = mysqli_real_escape_string($dbc, trim($_POST['birthdate']));
		$email = mysqli_real_escape_string($dbc, trim($_POST['email']));
		$phone = mysqli_real_escape_string($dbc, trim($_POST['phone']));
		$picture_name = mysqli_real_escape_string($dbc, trim($_FILES['picture']['name']));
		$picture_type = mysqli_real_escape_string($dbc, trim($_FILES['picture']['type']));
		$picture_size = mysqli_real_escape_string($dbc, trim($_FILES['picture']['size']));
		$picture_src_location = mysqli_real_escape_string($dbc, trim($_FILES['picture']['tmp_name']));

		if(!empty($fname) && !empty($lname)) {
			if(!preg_match(VC_PATTERN_NAME, $fname)) {
				$showerror = true;
				$error = "First name must be between 2 and 40 lowercase or uppercase letters.";
			}
			if(!preg_match(VC_PATTERN_NAME, $lname)) {
				$showerror = true;
				$error = "Last name must be between 2 and 40 lowercase or uppercase letters.";
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

								$picture = $_SESSION['user_id'].'-'.time().'-u'.'.png';
								$picture_dest_location = VC_UPLOADPATH.$picture;

								imagepng($image_resampled, $picture_dest_location, 0);

								unlink($picture_src_location);
								if($display_picture != "default.png")
									unlink(VC_UPLOADPATH.$display_picture);
							}
							else {
								unlink($picture_src_location);
								$showerror = true;
								$error = "An error related to the uploaded picture occured.";
							}
						}
						else {
							unlink($picture_src_location);
							$showerror = true;
							$error = "Picture size cannot exceed ".(VC_MAXFILESIZE/(1024*1024))."MB.";
						}
					}
					else {
						unlink($picture_src_location);
						$showerror = true;
						$error = "Picture format can only be JPG or PNG.";
					} 
				}
				else {
					$picture = $display_picture;
				}
			}
			
			if(!$showerror) {
				$query = "UPDATE vc_user SET fname='$fname', lname='$lname', ";

				if($gender == 'n')
					$query .= "gender=NULL, ";
				else 
					$query .= "gender='$gender', ";

				if(empty($birthdate))
					$query .= "birthdate=NULL, ";
				else
					$query .= "birthdate='$birthdate', ";

				if(empty($email))
					$query .= "email=NULL, ";
				else
					$query .= "email='$email', ";

				if(empty($phone))
					$query .= "phone=NULL, ";
				else
					$query .= "phone='$phone', ";

				$query .= "picture='$picture' WHERE user_id=".$_SESSION['user_id'];

				if(mysqli_query($dbc, $query)) {
					mysqli_close($dbc);
					$url = VC_LOCATION.'profile.php';
					header('Location: '.$url);
					exit();
				}
				else {
					$showerror = true;
					$error = "Birth date must be formatted as YYYY-MM-DD. Set it to blank if you do not want to enter a birth date.";
				}
			}
		}
		else {
			$showerror = true;
			$error = "First name and last name fields cannot be blank.";
		}
	}
	else {
		$query = "SELECT gender, birthdate, email, phone FROM vc_user WHERE user_id=".$_SESSION['user_id'];
		$data = mysqli_query($dbc, $query);

		if(mysqli_num_rows($data) != 1) {
			echo '<p class="error">Some error occured.</p>';
			exit();
		}

		$row = mysqli_fetch_array($data);

		$fname = $display_fname;
		$lname = $display_lname;
		$gender = $row['gender'];
		$birthdate = $row['birthdate'];
		$email = $row['email'];
		$phone = $row['phone'];
	}
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

	<link rel="stylesheet" href="stylesheets/user.css">
	<link rel="stylesheet" href="stylesheets/profile.css">
</head>
<body>
	<div id="banner">
		<p><a title="Change Password" href="#">Change Password</a></p>
		<h2><?php echo $display_fname.' '.$display_lname; ?></h2>
	</div>

	<div id="main-content">
		<div id="sidebar">
			<?php echo '<img src="'.VC_UPLOADPATH.$display_picture.'" alt="Profile Picture">'."\n"; ?>
		</div>
		<div id="content">
			<?php if($showerror) echo '<p class="error">'.$error.'</p>'."\n"; ?>
			<form enctype="multipart/form-data" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
						<th><label for="gender">Gender: </label></th>
						<td>
							<select id="gender" name="gender">
								<option value="m" <?php if(!empty($gender) && $gender == 'm') echo 'selected="selected"'; ?>>Male</option>
								<option value="f" <?php if(!empty($gender) && $gender == 'f') echo 'selected="selected"'; ?>>Female</option>
								<option value="n" <?php if(empty($gender)) echo 'selected="selected"'; ?>>Prefer not to say</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="birthdate">Birthdate: </label></th>
						<td><input type="text" id="birthdate" name="birthdate" value="<?php if(!empty($birthdate)) echo $birthdate; else echo 'YYYY-MM-DD'; ?>"></td>
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
						<td><input type="submit" id="submit" name="submit"><a href="profile.php" class="back-link" title="Cancel">Cancel</a></td>
					</tr>
				</table>
			</form>
		</div>
	</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
